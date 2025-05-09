<?php
/**
 * Understrap Child Theme functions and definitions
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Enqueue scripts and styles
 */
function understrap_child_enqueue_styles() {
    // Get the theme data
    $the_theme = wp_get_theme();

    wp_enqueue_style( 'understrap-styles', get_template_directory_uri() . '/css/theme.min.css', array(), $the_theme->get( 'Version' ) );
    wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', array(), '5.15.4' );
    wp_enqueue_style( 'understrap-child-styles', get_stylesheet_directory_uri() . '/style.css', array('understrap-styles'), $the_theme->get( 'Version' ) );

    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'understrap-scripts', get_template_directory_uri() . '/js/theme.min.js', array(), $the_theme->get( 'Version' ), true );
}
add_action( 'wp_enqueue_scripts', 'understrap_child_enqueue_styles' );

/**
 * Register navigation menus
 */
function understrap_child_register_menus() {
    register_nav_menus(
        array(
            'primary' => __( 'Primary Menu', 'understrap-child' ),
            'footer' => __( 'Footer Menu', 'understrap-child' ),
        )
    );
}
add_action( 'after_setup_theme', 'understrap_child_register_menus' );

/**
 * Custom template for pages
 */
function understrap_child_page_template($template) {
    if (is_page()) {
        $new_template = locate_template(array('page-custom.php'));
        if ('' != $new_template) {
            return $new_template;
        }
    }
    return $template;
}
add_filter('template_include', 'understrap_child_page_template', 99);

/**
 * Custom template for single posts
 */
function understrap_child_single_template($template) {
    if (is_single() && 'post' == get_post_type()) {
        $new_template = locate_template(array('single-custom.php'));
        if ('' != $new_template) {
            return $new_template;
        }
    }
    return $template;
}
add_filter('template_include', 'understrap_child_single_template', 99);
