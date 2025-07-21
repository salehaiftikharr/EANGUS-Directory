<?php
/*
Plugin Name: State Directory Plugin
Description: A plugin to manage and display a directory of EANGUS leadership data.
Version: 1.1
Author: Saleha Iftikhar
*/

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Load core plugin files (Admin and Shortcodes)
 */
require_once plugin_dir_path(__FILE__) . 'includes/class-sdp-admin.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-sdp-shortcode.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-sdp-renderer.php';

/**
 * Add plugin to WordPress admin dashboard menu
 */
add_action('admin_menu', ['StateDirectoryAdmin', 'add_menu']);

/**
 * Register shortcodes for each leadership section
 */
add_shortcode('state_directory', ['StateDirectoryShortcode', 'render_all']);

/**
 * Enqueue custom CSS and JavaScript (no Tailwind)
 */
add_action('wp_enqueue_scripts', function () {
    // Load custom static CSS (not Tailwind)
    wp_enqueue_style(
        'sdp-styles',
        plugin_dir_url(__FILE__) . 'assets/css/styles.css',
        [],
        filemtime(plugin_dir_path(__FILE__) . 'assets/css/styles.css')
    );

    // Load JavaScript for toggles and filters
    wp_enqueue_script(
        'sdp-script',
        plugin_dir_url(__FILE__) . 'assets/js/script.js',
        [],
        '1.1',
        true
    );
});
