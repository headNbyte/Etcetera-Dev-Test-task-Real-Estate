<?php
/**
 * REST API functionality for Real Estate Objects
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Real_Estate_REST_API {

    /**
     * Constructor
     */
    public function __construct() {
        // Register REST API routes
        add_action('rest_api_init', array($this, 'register_routes'));

        // Add XML support
        add_filter('rest_pre_serve_request', array($this, 'serve_xml_request'), 10, 4);
    }

    /**
     * Register REST API routes
     */
    public function register_routes() {
        register_rest_route('real-estate/v1', '/properties', array(
            array(
                'methods'  => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_properties'),
                'permission_callback' => '__return_true',
                'args'     => array(
                    'district' => array(
                        'description' => 'Filter by district slug',
                        'type'        => 'string',
                    ),
                    'building_type' => array(
                        'description' => 'Filter by building type (panel, brick, foam_block)',
                        'type'        => 'string',
                        'enum'        => array('panel', 'brick', 'foam_block'),
                    ),
                    'min_floors' => array(
                        'description' => 'Minimum number of floors',
                        'type'        => 'integer',
                        'minimum'     => 1,
                        'maximum'     => 20,
                    ),
                    'max_floors' => array(
                        'description' => 'Maximum number of floors',
                        'type'        => 'integer',
                        'minimum'     => 1,
                        'maximum'     => 20,
                    ),
                    'min_eco_rating' => array(
                        'description' => 'Minimum eco-rating',
                        'type'        => 'integer',
                        'minimum'     => 1,
                        'maximum'     => 5,
                    ),
                    'page' => array(
                        'description' => 'Page number',
                        'type'        => 'integer',
                        'default'     => 1,
                        'minimum'     => 1,
                    ),
                    'per_page' => array(
                        'description' => 'Items per page',
                        'type'        => 'integer',
                        'default'     => 10,
                        'minimum'     => 1,
                        'maximum'     => 100,
                    ),
                ),
            ),
            array(
                'methods'  => WP_REST_Server::CREATABLE,
                'callback' => array($this, 'create_property'),
                'permission_callback' => array($this, 'check_admin_permission'),
            ),
        ));

        register_rest_route('real-estate/v1', '/properties/(?P<id>\d+)', array(
            array(
                'methods'  => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_property'),
                'permission_callback' => '__return_true',
                'args'     => array(
                    'id' => array(
                        'validate_callback' => function($param) {
                            return is_numeric($param);
                        },
                    ),
                ),
            ),
            array(
                'methods'  => WP_REST_Server::EDITABLE,
                'callback' => array($this, 'update_property'),
                'permission_callback' => array($this, 'check_admin_permission'),
                'args'     => array(
                    'id' => array(
                        'validate_callback' => function($param) {
                            return is_numeric($param);
                        },
                    ),
                ),
            ),
            array(
                'methods'  => WP_REST_Server::DELETABLE,
                'callback' => array($this, 'delete_property'),
                'permission_callback' => array($this, 'check_admin_permission'),
                'args'     => array(
                    'id' => array(
                        'validate_callback' => function($param) {
                            return is_numeric($param);
                        },
                    ),
                ),
            ),
        ));
    }

    /**
     * Check if user has admin permission
     *
     * @return bool
     */
    public function check_admin_permission() {
        return current_user_can('manage_options');
    }

    /**
     * Get properties
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function get_properties($request) {
        $args = array(
            'post_type'      => 'real_estate_object',
            'posts_per_page' => $request['per_page'] ?: 10,
            'paged'          => $request['page'] ?: 1,
            'meta_query'     => array(),
            'tax_query'      => array(),
        );

        // Filter by district
        if (!empty($request['district'])) {
            $args['tax_query'][] = array(
                'taxonomy' => 'district',
                'field'    => 'slug',
                'terms'    => $request['district'],
            );
        }

        // Filter by building type
        if (!empty($request['building_type'])) {
            $args['meta_query'][] = array(
                'key'     => 'building_type',
                'value'   => $request['building_type'],
                'compare' => '=',
            );
        }

        // Filter by floors
        if (!empty($request['min_floors'])) {
            $args['meta_query'][] = array(
                'key'     => 'floors',
                'value'   => $request['min_floors'],
                'compare' => '>=',
                'type'    => 'NUMERIC',
            );
        }

        if (!empty($request['max_floors'])) {
            $args['meta_query'][] = array(
                'key'     => 'floors',
                'value'   => $request['max_floors'],
                'compare' => '<=',
                'type'    => 'NUMERIC',
            );
        }

        // Filter by eco-rating
        if (!empty($request['min_eco_rating'])) {
            $args['meta_query'][] = array(
                'key'     => 'eco_rating',
                'value'   => $request['min_eco_rating'],
                'compare' => '>=',
                'type'    => 'NUMERIC',
            );
        }

        // Get posts
        $query = new WP_Query($args);
        $properties = array();

        foreach ($query->posts as $post) {
            $properties[] = $this->prepare_property_for_response($post);
        }

        // Return response with pagination
        $response = new WP_REST_Response($properties);
        $response->header('X-WP-Total', $query->found_posts);
        $response->header('X-WP-TotalPages', $query->max_num_pages);

        return $response;
    }

    /**
     * Get single property
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function get_property($request) {
        $post = get_post($request['id']);

        if (!$post || $post->post_type !== 'real_estate_object') {
            return new WP_Error('not_found', 'Property not found', array('status' => 404));
        }

        return $this->prepare_property_for_response($post);
    }

    /**
     * Create property
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function create_property($request) {
        $params = $request->get_params();

        // Log the incoming parameters for debugging
        error_log("Creating property with parameters: " . print_r($params, true));

        // Create post
        $post_data = array(
            'post_type'    => 'real_estate_object',
            'post_title'   => sanitize_text_field($params['title']),
            'post_content' => wp_kses_post($params['content'] ?? ''),
            'post_status'  => 'publish',
        );

        $post_id = wp_insert_post($post_data, true);

        if (is_wp_error($post_id)) {
            error_log("Error creating property: " . $post_id->get_error_message());
            return $post_id;
        }

        // Set district taxonomy
        if (!empty($params['district'])) {
            wp_set_object_terms($post_id, $params['district'], 'district');
        }

        // Set ACF fields
        if (!empty($params['building_name'])) {
            update_field('building_name', sanitize_text_field($params['building_name']), $post_id);
        }

        if (!empty($params['coordinates'])) {
            update_field('coordinates', sanitize_text_field($params['coordinates']), $post_id);
        }

        if (!empty($params['floors'])) {
            update_field('floors', intval($params['floors']), $post_id);
        }

        if (!empty($params['building_type'])) {
            update_field('building_type', sanitize_text_field($params['building_type']), $post_id);
        }

        if (!empty($params['eco_rating'])) {
            update_field('eco_rating', intval($params['eco_rating']), $post_id);
        }

        // Handle premises if provided
        if (!empty($params['premises']) && is_array($params['premises'])) {
            $premises_data = array();

            foreach ($params['premises'] as $premise) {
                $premises_data[] = array(
                    'area'     => sanitize_text_field($premise['area'] ?? ''),
                    'rooms'    => intval($premise['rooms'] ?? 1),
                    'balcony'  => sanitize_text_field($premise['balcony'] ?? 'no'),
                    'bathroom' => sanitize_text_field($premise['bathroom'] ?? 'yes'),
                );
            }

            update_field('premises', $premises_data, $post_id);
        }

        // Create a proper WP_REST_Request object with the correct ID parameter
        $request_get = new WP_REST_Request('GET', '/real-estate/v1/properties/' . $post_id);
        $request_get->set_param('id', $post_id);

        // Return the newly created property
        return $this->prepare_property_for_response(get_post($post_id));
    }

    /**
     * Update property
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function update_property($request) {
        $post = get_post($request['id']);

        if (!$post || $post->post_type !== 'real_estate_object') {
            return new WP_Error('not_found', 'Property not found', array('status' => 404));
        }

        $params = $request->get_params();

        // Update post
        $post_data = array(
            'ID' => $post->ID,
        );

        if (!empty($params['title'])) {
            $post_data['post_title'] = sanitize_text_field($params['title']);
        }

        if (isset($params['content'])) {
            $post_data['post_content'] = wp_kses_post($params['content']);
        }

        wp_update_post($post_data);

        // Update district taxonomy
        if (!empty($params['district'])) {
            wp_set_object_terms($post->ID, $params['district'], 'district');
        }

        // Update ACF fields
        if (!empty($params['building_name'])) {
            update_field('building_name', sanitize_text_field($params['building_name']), $post->ID);
        }

        if (!empty($params['coordinates'])) {
            update_field('coordinates', sanitize_text_field($params['coordinates']), $post->ID);
        }

        if (!empty($params['floors'])) {
            update_field('floors', intval($params['floors']), $post->ID);
        }

        if (!empty($params['building_type'])) {
            update_field('building_type', sanitize_text_field($params['building_type']), $post->ID);
        }

        if (!empty($params['eco_rating'])) {
            update_field('eco_rating', intval($params['eco_rating']), $post->ID);
        }

        // Handle premises if provided
        if (!empty($params['premises']) && is_array($params['premises'])) {
            $premises_data = array();

            foreach ($params['premises'] as $premise) {
                $premises_data[] = array(
                    'area'     => sanitize_text_field($premise['area'] ?? ''),
                    'rooms'    => intval($premise['rooms'] ?? 1),
                    'balcony'  => sanitize_text_field($premise['balcony'] ?? 'no'),
                    'bathroom' => sanitize_text_field($premise['bathroom'] ?? 'yes'),
                );
            }

            update_field('premises', $premises_data, $post->ID);
        }

        return $this->get_property($request);
    }

    /**
     * Delete property
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function delete_property($request) {
        $post = get_post($request['id']);

        if (!$post || $post->post_type !== 'real_estate_object') {
            return new WP_Error('not_found', 'Property not found', array('status' => 404));
        }

        $result = wp_delete_post($post->ID, true);

        if (!$result) {
            return new WP_Error('delete_failed', 'Failed to delete property', array('status' => 500));
        }

        return new WP_REST_Response(array(
            'deleted'  => true,
            'previous' => $this->prepare_property_for_response($post),
        ));
    }

    /**
     * Prepare property for response
     *
     * @param WP_Post $post
     * @return array
     */
    private function prepare_property_for_response($post) {
        $districts = wp_get_object_terms($post->ID, 'district');
        $district_data = array();

        foreach ($districts as $district) {
            $district_data[] = array(
                'id'   => $district->term_id,
                'name' => $district->name,
                'slug' => $district->slug,
            );
        }

        // Get ACF fields
        $building_name = get_field('building_name', $post->ID);
        $coordinates = get_field('coordinates', $post->ID);
        $floors = get_field('floors', $post->ID);
        $building_type = get_field('building_type', $post->ID);
        $eco_rating = get_field('eco_rating', $post->ID);
        $premises = get_field('premises', $post->ID);

        // Format premises data
        $premises_data = array();
        if ($premises) {
            foreach ($premises as $premise) {
                $premise_data = array(
                    'area'     => $premise['area'],
                    'rooms'    => $premise['rooms'],
                    'balcony'  => $premise['balcony'],
                    'bathroom' => $premise['bathroom'],
                );

                $premises_data[] = $premise_data;
            }
        }

        // Prepare response
        $response = array(
            'id'            => $post->ID,
            'title'         => $post->post_title,
            'content'       => $post->post_content,
            'link'          => get_permalink($post->ID),
            'districts'     => $district_data,
            'building_name' => $building_name,
            'coordinates'   => $coordinates,
            'floors'        => $floors,
            'building_type' => $building_type,
            'eco_rating'    => $eco_rating,
            'premises'      => $premises_data,
            'note'          => 'Images should be added manually through WordPress admin'
        );

        return $response;
    }

    /**
     * Serve XML response if requested
     *
     * @param bool $served Whether the request has already been served
     * @param WP_REST_Response $result The response object
     * @param WP_REST_Request $request The request object
     * @param WP_REST_Server $server The REST server
     * @return bool Whether the request has been served
     */
    public function serve_xml_request($served, $result, $request, $server) {
        $headers = $request->get_headers();

        // Check if client accepts XML
        if (isset($headers['accept']) && strpos($headers['accept'][0], 'application/xml') !== false) {
            // Convert response data to XML
            $xml = $this->convert_to_xml($result->get_data());

            // Set headers and output XML
            $server->send_header('Content-Type', 'application/xml; charset=' . get_option('blog_charset'));
            echo $xml;

            return true; // Request has been served
        }

        return $served; // Let WordPress handle the request
    }

    /**
     * Convert data to XML
     *
     * @param mixed $data The data to convert
     * @param SimpleXMLElement $xml The XML element to add to
     * @param string $root_tag The root tag name
     * @return string XML string
     */
    private function convert_to_xml($data, $xml = null, $root_tag = 'response') {
        if ($xml === null) {
            $xml = new SimpleXMLElement("<?xml version=\"1.0\"?><{$root_tag}></{$root_tag}>");
        }

        foreach ($data as $key => $value) {
            // Handle numeric array keys
            if (is_numeric($key)) {
                $key = 'item';
            }

            // Clean up key name
            $key = preg_replace('/[^a-z0-9_]/i', '', $key);

            // Handle different value types
            if (is_array($value) || is_object($value)) {
                $child = $xml->addChild($key);
                $this->convert_to_xml($value, $child, $key);
            } else {
                // Handle CDATA for text content
                if (is_string($value) && (strpos($value, '<') !== false || strpos($value, '>') !== false)) {
                    $child = $xml->addChild($key);
                    $child_node = dom_import_simplexml($child);
                    $cdata = $child_node->ownerDocument->createCDATASection($value);
                    $child_node->appendChild($cdata);
                } else {
                    $xml->addChild($key, htmlspecialchars((string)$value));
                }
            }
        }

        if ($root_tag === 'response') {
            return $xml->asXML();
        }

        return $xml;
    }
}

// Initialize the class
$real_estate_rest_api = new Real_Estate_REST_API();
