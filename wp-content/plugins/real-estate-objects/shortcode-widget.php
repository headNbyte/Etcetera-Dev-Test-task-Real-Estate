<?php
/**
 * Shortcode and Widget functionality for Real Estate Objects
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Real Estate Filter Widget
 */
class Real_Estate_Filter_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'real_estate_filter_widget',
            __('Real Estate Filter', 'real-estate-objects'),
            array('description' => __('Filter for real estate objects', 'real-estate-objects'))
        );
    }

    /**
     * Widget front-end display
     *
     * @param array $args     Widget arguments
     * @param array $instance Saved values from database
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        // Output the filter form
        echo real_estate_objects_filter_form();

        echo $args['after_widget'];
    }

    /**
     * Widget backend form
     *
     * @param array $instance Previously saved values from database
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Real Estate Filter', 'real-estate-objects');
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'real-estate-objects'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved
     *
     * @param array $new_instance Values just sent to be saved
     * @param array $old_instance Previously saved values from database
     * @return array Updated safe values to be saved
     */
    public function update($instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }
}

/**
 * Register the widget
 */
function real_estate_objects_register_widget() {
    register_widget('Real_Estate_Filter_Widget');
}
add_action('widgets_init', 'real_estate_objects_register_widget');

/**
 * Register the shortcode
 */
function real_estate_objects_register_shortcode() {
    add_shortcode('real_estate_filter', 'real_estate_objects_shortcode_callback');
}
add_action('init', 'real_estate_objects_register_shortcode');

/**
 * Shortcode callback
 *
 * @param array $atts Shortcode attributes
 * @return string Shortcode output
 */
function real_estate_objects_shortcode_callback($atts) {
    $atts = shortcode_atts(array(
        'title' => __('Real Estate Filter', 'real-estate-objects'),
    ), $atts, 'real_estate_filter');

    ob_start();

    if (!empty($atts['title'])) {
        echo '<h3>' . esc_html($atts['title']) . '</h3>';
    }

    // Output the filter form
    echo real_estate_objects_filter_form();

    return ob_get_clean();
}

/**
 * Generate the filter form HTML
 *
 * @return string Filter form HTML
 */
function real_estate_objects_filter_form() {
    // Enqueue scripts and styles
    wp_enqueue_script('jquery');
    wp_enqueue_script('real-estate-filter', REAL_ESTATE_OBJECTS_URL . 'js/real-estate-filter.js', array('jquery'), '1.0', true);
    wp_enqueue_style('real-estate-filter', REAL_ESTATE_OBJECTS_URL . 'css/real-estate-filter.css');

    // Localize script with ajax url
    wp_localize_script('real-estate-filter', 'real_estate_filter', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('real_estate_filter_nonce'),
    ));

    // Get all districts
    $districts = get_terms(array(
        'taxonomy'   => 'district',
        'hide_empty' => false,
    ));

    ob_start();
    ?>
    <div class="real-estate-filter">
        <form id="real-estate-filter-form" class="real-estate-filter-form">
            <!-- First row: District, Building Type, Min Floors -->
            <div class="filter-row">
                <div class="filter-field">
                    <label for="district"><?php _e('Район', 'real-estate-objects'); ?></label>
                    <select name="district" id="district">
                        <option value=""><?php _e('Всі райони', 'real-estate-objects'); ?></option>
                        <?php foreach ($districts as $district) : ?>
                            <option value="<?php echo esc_attr($district->slug); ?>"><?php echo esc_html($district->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-field">
                    <label for="building_type"><?php _e('Тип будівлі', 'real-estate-objects'); ?></label>
                    <select name="building_type" id="building_type">
                        <option value=""><?php _e('Всі типи', 'real-estate-objects'); ?></option>
                        <option value="panel"><?php _e('Панель', 'real-estate-objects'); ?></option>
                        <option value="brick"><?php _e('Цегла', 'real-estate-objects'); ?></option>
                        <option value="foam_block"><?php _e('Піноблок', 'real-estate-objects'); ?></option>
                    </select>
                </div>

                <div class="filter-field">
                    <label for="min_floors"><?php _e('Мін. поверхів', 'real-estate-objects'); ?></label>
                    <select name="min_floors" id="min_floors">
                        <option value=""><?php _e('Будь-яка', 'real-estate-objects'); ?></option>
                        <?php for ($i = 1; $i <= 20; $i++) : ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <!-- Second row: Max Floors, Min Ecology, Rooms -->
            <div class="filter-row">
                <div class="filter-field">
                    <label for="max_floors"><?php _e('Макс. поверхів', 'real-estate-objects'); ?></label>
                    <select name="max_floors" id="max_floors">
                        <option value=""><?php _e('Будь-яка', 'real-estate-objects'); ?></option>
                        <?php for ($i = 1; $i <= 20; $i++) : ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="filter-field">
                    <label for="min_eco_rating"><?php _e('Мін. екологічність', 'real-estate-objects'); ?></label>
                    <select name="min_eco_rating" id="min_eco_rating">
                        <option value=""><?php _e('Будь-яка', 'real-estate-objects'); ?></option>
                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="filter-field">
                    <label for="rooms"><?php _e('Кількість кімнат', 'real-estate-objects'); ?></label>
                    <select name="rooms" id="rooms">
                        <option value=""><?php _e('Будь-яка', 'real-estate-objects'); ?></option>
                        <?php for ($i = 1; $i <= 10; $i++) : ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <!-- Third row: Balcony, Bathroom -->
            <div class="filter-row">
                <div class="filter-field">
                    <label for="balcony"><?php _e('Балкон', 'real-estate-objects'); ?></label>
                    <select name="balcony" id="balcony">
                        <option value=""><?php _e('Не важливо', 'real-estate-objects'); ?></option>
                        <option value="yes"><?php _e('Так', 'real-estate-objects'); ?></option>
                        <option value="no"><?php _e('Ні', 'real-estate-objects'); ?></option>
                    </select>
                </div>

                <div class="filter-field">
                    <label for="bathroom"><?php _e('Санвузол', 'real-estate-objects'); ?></label>
                    <select name="bathroom" id="bathroom">
                        <option value=""><?php _e('Не важливо', 'real-estate-objects'); ?></option>
                        <option value="yes"><?php _e('Так', 'real-estate-objects'); ?></option>
                        <option value="no"><?php _e('Ні', 'real-estate-objects'); ?></option>
                    </select>
                </div>

                <div class="filter-field">
                    <!-- Empty field for alignment -->
                </div>
            </div>

            <div class="filter-row filter-actions-row">
                <div class="filter-actions">
                    <button type="submit" class="filter-button">
                        <i class="fas fa-search"></i> <span><?php _e('Пошук', 'real-estate-objects'); ?></span>
                    </button>
                    <button type="reset" class="filter-reset">
                        <i class="fas fa-redo-alt"></i> <span><?php _e('Скинути', 'real-estate-objects'); ?></span>
                    </button>
                </div>
            </div>
        </form>

        <div id="real-estate-results" class="real-estate-results">
            <!-- Results header with sorting and view options -->
            <div class="results-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="sorting-options d-flex align-items-center">
                            <label for="sort-selector" class="mr-2 mb-0 text-nowrap"><?php _e( 'Сортувати за:', 'real-estate-objects' ); ?></label>
                            <div class="select-wrapper flex-grow-1">
                                <select id="sort-selector" class="form-control sort-selector">
                                    <option value="ecology-high" selected><?php _e( 'Спочатку вища екологічність', 'real-estate-objects' ); ?></option>
                                    <option value="ecology-low"><?php _e( 'Спочатку нижча екологічність', 'real-estate-objects' ); ?></option>
                                    <option value="price-low"><?php _e( 'Спочатку нижча ціна', 'real-estate-objects' ); ?></option>
                                    <option value="price-high"><?php _e( 'Спочатку вища ціна', 'real-estate-objects' ); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-right">
                        <div class="view-options d-flex align-items-center justify-content-md-end">
                            <span class="mr-2 mb-0 text-nowrap"><?php _e( 'Вигляд:', 'real-estate-objects' ); ?></span>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm view-option btn-secondary" data-view="blocks" data-view-active="true" title="<?php _e( 'Вигляд плиткою', 'real-estate-objects' ); ?>">
                                    <i class="fas fa-th"></i>
                                </button>
                                <button type="button" class="btn btn-sm view-option" data-view="list" title="<?php _e( 'Вигляд списком', 'real-estate-objects' ); ?>">
                                    <i class="fas fa-list"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="results-loading" style="display: none;">
                <?php _e('Завантаження...', 'real-estate-objects'); ?>
            </div>
            <div class="results-container"></div>
            <div class="results-pagination"></div>
            <div class="attribution">
                <a href="https://storyset.com/building" target="_blank" rel="noopener noreferrer">Building illustrations by Storyset</a>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * AJAX handler for the filter
 */
function real_estate_objects_filter_ajax() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'real_estate_filter_nonce')) {
        wp_send_json_error('Invalid nonce');
    }

    $args = array(
        'post_type'      => 'real_estate_object',
        'posts_per_page' => 5,
        'paged'          => isset($_POST['page']) ? intval($_POST['page']) : 1,
        'meta_query'     => array('relation' => 'AND'),
        'tax_query'      => array(),
        'orderby'        => array(
            'ID' => 'ASC' // Add a secondary, consistent ordering by ID to prevent duplicates
        ),
    );

    // Filter by district
    if (!empty($_POST['district'])) {
        $args['tax_query'][] = array(
            'taxonomy' => 'district',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($_POST['district']),
        );
    }

    // Filter by building type
    if (!empty($_POST['building_type'])) {
        $args['meta_query'][] = array(
            'key'     => 'building_type',
            'value'   => sanitize_text_field($_POST['building_type']),
            'compare' => '=',
        );
    }

    // Filter by floors
    if (!empty($_POST['min_floors'])) {
        $args['meta_query'][] = array(
            'key'     => 'floors',
            'value'   => intval($_POST['min_floors']),
            'compare' => '>=',
            'type'    => 'NUMERIC',
        );
    }

    if (!empty($_POST['max_floors'])) {
        $args['meta_query'][] = array(
            'key'     => 'floors',
            'value'   => intval($_POST['max_floors']),
            'compare' => '<=',
            'type'    => 'NUMERIC',
        );
    }

    // Filter by eco-rating
    if (!empty($_POST['min_eco_rating'])) {
        $args['meta_query'][] = array(
            'key'     => 'eco_rating',
            'value'   => intval($_POST['min_eco_rating']),
            'compare' => '>=',
            'type'    => 'NUMERIC',
        );
    }

    // Add premises filters if needed
    $premises_meta_query = array('relation' => 'AND');
    $has_premises_filter = false;

    // Filter by rooms
    if (!empty($_POST['rooms'])) {
        $premises_meta_query[] = array(
            'key'     => 'premises_%_rooms',
            'value'   => intval($_POST['rooms']),
            'compare' => '=',
        );
        $has_premises_filter = true;
    }

    // Filter by balcony
    if (!empty($_POST['balcony'])) {
        $premises_meta_query[] = array(
            'key'     => 'premises_%_balcony',
            'value'   => sanitize_text_field($_POST['balcony']),
            'compare' => '=',
        );
        $has_premises_filter = true;
    }

    // Filter by bathroom
    if (!empty($_POST['bathroom'])) {
        $premises_meta_query[] = array(
            'key'     => 'premises_%_bathroom',
            'value'   => sanitize_text_field($_POST['bathroom']),
            'compare' => '=',
        );
        $has_premises_filter = true;
    }

    // Add premises meta query if any premises filter is applied
    if ($has_premises_filter) {
        $args['meta_query'][] = $premises_meta_query;
    }

    // Sort results if requested
    $sort = isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : 'ecology-high';

    switch ($sort) {
        case 'ecology-high':
            // Sort by ecology rating (highest first)
            $args['meta_key'] = 'eco_rating';
            $args['orderby'] = array(
                'meta_value_num' => 'DESC',
                'ID' => 'ASC'
            );
            break;

        case 'ecology-low':
            // Sort by ecology rating (lowest first)
            $args['meta_key'] = 'eco_rating';
            $args['orderby'] = array(
                'meta_value_num' => 'ASC',
                'ID' => 'ASC'
            );
            break;

        case 'price-low':
            // Sort by price (lowest first)
            $args['meta_key'] = 'price';
            $args['orderby'] = array(
                'meta_value_num' => 'ASC',
                'ID' => 'ASC'
            );
            break;

        case 'price-high':
            // Sort by price (highest first)
            $args['meta_key'] = 'price';
            $args['orderby'] = array(
                'meta_value_num' => 'DESC',
                'ID' => 'ASC'
            );
            break;
    }

    // Get posts
    $query = new WP_Query($args);
    $results = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            $building_image = get_field('building_image');
            $image_url = !empty($building_image) ? $building_image['url'] : '';

            $results[] = array(
                'id'            => get_the_ID(),
                'title'         => get_the_title(),
                'excerpt'       => get_the_excerpt(),
                'link'          => get_permalink(),
                'image_url'     => $image_url,
                'building_name' => get_field('building_name'),
                'floors'        => get_field('floors'),
                'building_type' => get_field('building_type'),
                'eco_rating'    => get_field('eco_rating'),
                'price'         => get_field('price') ? get_field('price') : 0,
            );
        }

        wp_reset_postdata();
    }

    // Prepare pagination
    $pagination = array(
        'total'        => $query->max_num_pages,
        'current'      => isset($_POST['page']) ? intval($_POST['page']) : 1,
        'per_page'     => 5,
        'total_items'  => $query->found_posts,
    );

    wp_send_json_success(array(
        'results'    => $results,
        'pagination' => $pagination,
    ));
}
add_action('wp_ajax_real_estate_filter', 'real_estate_objects_filter_ajax');
add_action('wp_ajax_nopriv_real_estate_filter', 'real_estate_objects_filter_ajax');
