<?php
/**
 * Class for sorting real estate objects by eco-rating
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Real_Estate_Sorter {

    /**
     * Constructor
     */
    public function __construct() {
        // Hook into pre_get_posts to modify the query for real estate objects
        add_action('pre_get_posts', array($this, 'sort_by_eco_rating'));
    }

    /**
     * Sort real estate objects by eco-rating
     *
     * @param WP_Query $query The WordPress query object
     */
    public function sort_by_eco_rating($query) {
        // Only modify main queries for real estate object post type
        if (!is_admin() && $query->is_main_query() &&
            (is_post_type_archive('real_estate_object') ||
             is_tax('district') ||
             (isset($query->query['post_type']) && $query->query['post_type'] === 'real_estate_object'))) {

            // Add meta query for eco_rating
            $query->set('meta_key', 'eco_rating');
            $query->set('orderby', 'meta_value_num');
            $query->set('order', 'DESC'); // Higher eco-rating first
        }
    }
}

// Initialize the class
$real_estate_sorter = new Real_Estate_Sorter();
