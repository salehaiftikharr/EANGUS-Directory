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
add_shortcode('executive_officers', ['StateDirectoryShortcode', 'render_executive_officers']);
add_shortcode('annual_conferences', ['StateDirectoryShortcode', 'render_annual_conferences']);
add_shortcode('area_chairs', ['StateDirectoryShortcode', 'render_area_chairs']);
add_shortcode('executive_council', ['StateDirectoryShortcode', 'render_executive_council']);
add_shortcode('committees', ['StateDirectoryShortcode', 'render_committees']);
add_shortcode('auxiliary_exec_board', ['StateDirectoryShortcode', 'render_auxiliary_exec_board']);
add_shortcode('past_presidents', ['StateDirectoryShortcode', 'render_past_presidents']);
add_shortcode('state_directory', ['StateDirectoryShortcode', 'render_all']);

/**
 * Enqueue Tailwind CSS and custom JS for frontend display
 */
add_action('wp_enqueue_scripts', function () {
    // Load Tailwind-generated CSS
    wp_enqueue_style(
        'sdp-tailwind',
        plugin_dir_url(__FILE__) . 'assets/css/output.css',
        [],
        filemtime(plugin_dir_path(__FILE__) . 'assets/css/output.css') // Cache busting
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
