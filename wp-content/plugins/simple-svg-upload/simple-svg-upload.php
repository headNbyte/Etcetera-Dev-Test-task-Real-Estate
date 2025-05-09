<?php
/**
 * Plugin Name: Simple SVG Upload
 * Plugin URI:
 * Description: A simple plugin to enable SVG file uploads in WordPress
 * Version: 1.0
 * Author: Ihor Nemyrovskyi
 * Author URI:
 * License:
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add SVG to allowed mime types
 */
function simple_svg_upload_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'simple_svg_upload_mime_types');

/**
 * Fix SVG file upload bug in WordPress
 */
function simple_svg_upload_fix_mime_type_detection($data, $file, $filename, $mimes) {
    $ext = pathinfo($filename, PATHINFO_EXTENSION);

    if ($ext === 'svg') {
        $data['type'] = 'image/svg+xml';
        $data['ext'] = 'svg';
        $data['proper_filename'] = $filename;
    }

    return $data;
}
add_filter('wp_check_filetype_and_ext', 'simple_svg_upload_fix_mime_type_detection', 10, 4);

/**
 * Basic SVG sanitization on upload
 */
function simple_svg_upload_sanitize($file) {
    if ($file['type'] === 'image/svg+xml') {
        // Read the file
        $content = file_get_contents($file['tmp_name']);

        // Remove script tags
        $content = preg_replace('/<script[\s\S]*?>[\s\S]*?<\/script>/i', '', $content);

        // Remove onclick and other event handlers
        $content = preg_replace('/\s+on\w+=["\'][^"\']*["\']/', '', $content);

        // Write the sanitized content back to the file
        file_put_contents($file['tmp_name'], $content);
    }

    return $file;
}
add_filter('wp_handle_upload_prefilter', 'simple_svg_upload_sanitize');

/**
 * Display SVG thumbnails in media library
 */
function simple_svg_upload_display_thumbs() {
    echo '<style>
        table.media .column-title .media-icon img[src$=".svg"] {
            width: 100%;
            height: auto;
        }
    </style>';
}
add_action('admin_head', 'simple_svg_upload_display_thumbs');

/**
 * Add admin page
 */
function simple_svg_upload_add_admin_menu() {
    add_submenu_page(
        'upload.php',
        'SVG Upload Settings',
        'SVG Upload',
        'manage_options',
        'simple-svg-upload',
        'simple_svg_upload_admin_page'
    );
}
add_action('admin_menu', 'simple_svg_upload_add_admin_menu');

/**
 * Admin page content
 */
function simple_svg_upload_admin_page() {
    ?>
    <div class="wrap">
        <h1>Simple SVG Upload</h1>
        <div class="card">
            <h2>About</h2>
            <p>This plugin allows you to upload SVG files to your WordPress media library.</p>
            <p>SVG files are vector-based graphics that maintain their quality at any size.</p>
        </div>

        <div class="card" style="margin-top: 20px;">
            <h2>Security</h2>
            <p>This plugin includes basic sanitization for SVG files:</p>
            <ul style="list-style-type: disc; margin-left: 20px;">
                <li>Removes &lt;script&gt; tags from SVG files</li>
                <li>Removes event handlers (like onclick attributes)</li>
            </ul>
            <p><strong>Note:</strong> While this provides some protection, it is still recommended to only allow trusted users to upload files.</p>
        </div>

        <div class="card" style="margin-top: 20px;">
            <h2>Usage</h2>
            <p>Simply upload SVG files like you would any other image through the WordPress media library.</p>
        </div>
    </div>
    <?php
}
