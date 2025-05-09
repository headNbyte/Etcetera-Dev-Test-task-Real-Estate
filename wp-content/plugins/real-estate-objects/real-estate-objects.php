<?php
/**
 * Plugin Name: Real Estate Objects
 * Plugin URI:
 * Description: Custom plugin that initializes a new post type "Об'єкт нерухомості" and taxonomy "Район"
 * Version: 1.0
 * Author: Ihor Nemyrovskyi
 * Author URI:
 * Text Domain: real-estate-objects
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('REAL_ESTATE_OBJECTS_PATH', plugin_dir_path(__FILE__));
define('REAL_ESTATE_OBJECTS_URL', plugin_dir_url(__FILE__));

// Include ACF fields registration
require_once REAL_ESTATE_OBJECTS_PATH . 'acf-fields.php';

// Include Real Estate Sorter class
require_once REAL_ESTATE_OBJECTS_PATH . 'class-real-estate-sorter.php';

// Include REST API functionality
require_once REAL_ESTATE_OBJECTS_PATH . 'rest-api.php';

// Include Shortcode and Widget functionality
require_once REAL_ESTATE_OBJECTS_PATH . 'shortcode-widget.php';


// Register Custom Post Type and Taxonomy on init
add_action('init', 'real_estate_objects_init');

function real_estate_objects_init() {
    // Register Custom Post Type: "Об'єкт нерухомості" (Real Estate Object)
    register_post_type('real_estate_object',
        array(
            'labels' => array(
                'name'               => __('Об\'єкти нерухомості', 'real-estate-objects'),
                'singular_name'      => __('Об\'єкт нерухомості', 'real-estate-objects'),
                'menu_name'          => __('Об\'єкти нерухомості', 'real-estate-objects'),
                'add_new'            => __('Додати новий', 'real-estate-objects'),
                'add_new_item'       => __('Додати новий об\'єкт', 'real-estate-objects'),
                'edit_item'          => __('Редагувати об\'єкт', 'real-estate-objects'),
                'new_item'           => __('Новий об\'єкт', 'real-estate-objects'),
                'view_item'          => __('Переглянути об\'єкт', 'real-estate-objects'),
                'search_items'       => __('Шукати об\'єкти', 'real-estate-objects'),
                'not_found'          => __('Об\'єктів не знайдено', 'real-estate-objects'),
                'not_found_in_trash' => __('В кошику об\'єктів не знайдено', 'real-estate-objects'),
            ),
            'public'              => true,
            'has_archive'         => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-building',
            'capability_type'     => 'post',
            'hierarchical'        => false,
            'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'rewrite'             => array('slug' => 'real-estate'),
            'query_var'           => true,
            'show_in_rest'        => true, // Enable Gutenberg editor
            'rest_base'           => 'real-estate', // Custom REST API base
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        )
    );

    // Register Custom Taxonomy: "Район" (District)
    register_taxonomy('district', 'real_estate_object',
        array(
            'labels' => array(
                'name'                       => __('Райони', 'real-estate-objects'),
                'singular_name'              => __('Район', 'real-estate-objects'),
                'search_items'               => __('Шукати райони', 'real-estate-objects'),
                'popular_items'              => __('Популярні райони', 'real-estate-objects'),
                'all_items'                  => __('Всі райони', 'real-estate-objects'),
                'parent_item'                => __('Батьківський район', 'real-estate-objects'),
                'parent_item_colon'          => __('Батьківський район:', 'real-estate-objects'),
                'edit_item'                  => __('Редагувати район', 'real-estate-objects'),
                'update_item'                => __('Оновити район', 'real-estate-objects'),
                'add_new_item'               => __('Додати новий район', 'real-estate-objects'),
                'new_item_name'              => __('Назва нового району', 'real-estate-objects'),
                'separate_items_with_commas' => __('Розділіть райони комами', 'real-estate-objects'),
                'add_or_remove_items'        => __('Додати або видалити райони', 'real-estate-objects'),
                'choose_from_most_used'      => __('Вибрати з найбільш використовуваних районів', 'real-estate-objects'),
                'menu_name'                  => __('Райони', 'real-estate-objects'),
            ),
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'district'),
            'show_in_rest'      => true, // Enable Gutenberg editor
        )
    );
}

// Add ACF fields to REST API
add_action('rest_api_init', 'real_estate_objects_register_acf_rest_fields');

function real_estate_objects_register_acf_rest_fields() {
    // Make sure ACF is active
    if (!function_exists('get_field')) {
        return;
    }

    // Register ACF fields to show in REST API
    register_rest_field('real_estate_object', 'acf', array(
        'get_callback' => 'real_estate_objects_get_acf_fields',
        'update_callback' => 'real_estate_objects_update_acf_fields',
        'schema' => null,
    ));
}

/**
 * Get ACF fields for REST API
 *
 * @param array $object The object from the response
 * @return array ACF fields
 */
function real_estate_objects_get_acf_fields($object) {
    $post_id = $object['id'];

    // Get all ACF fields
    $fields = get_fields($post_id);

    // Process premises field to ensure it's properly formatted
    if (isset($fields['premises']) && is_array($fields['premises'])) {
        foreach ($fields['premises'] as $key => $premise) {
            // Ensure image is properly formatted
            if (isset($premise['image']) && is_array($premise['image'])) {
                $fields['premises'][$key]['image'] = array(
                    'id' => $premise['image']['ID'] ?? '',
                    'url' => $premise['image']['url'] ?? '',
                    'alt' => $premise['image']['alt'] ?? '',
                );
            }
        }
    }

    return $fields;
}

/**
 * Update ACF fields from REST API
 *
 * @param array $fields The fields to update
 * @param object $object The object from the request
 * @param string $field_name The name of the field
 * @return bool|int
 */
function real_estate_objects_update_acf_fields($fields, $object, $field_name) {
    if (!$fields || !is_array($fields)) {
        return false;
    }

    foreach ($fields as $field_key => $value) {
        update_field($field_key, $value, $object->ID);
    }

    return true;
}
