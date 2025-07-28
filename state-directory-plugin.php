<?php
/*
Plugin Name: State Directory Plugin
Description: A plugin to manage and display a directory of EANGUS leadership data.
Version: 1.2
Author: Saleha Iftikhar
*/

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Load core plugin files
 */
require_once plugin_dir_path(__FILE__) . 'includes/class-sdp-admin.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-sdp-shortcode.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-sdp-renderer.php';

/**
 * Plugin activation hook - creates the database table
 */
register_activation_hook(__FILE__, 'sdp_create_database_table');

/**
 * Plugin deactivation hook
 */
register_deactivation_hook(__FILE__, 'sdp_deactivate_plugin');

/**
 * Plugin uninstall hook - removes all plugin data
 */
register_uninstall_hook(__FILE__, 'sdp_uninstall_plugin');

/**
 * Create the eangus_directory table on plugin activation
 */
function sdp_create_database_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'eangus_directory';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id int(11) NOT NULL AUTO_INCREMENT,
        type varchar(50) NOT NULL,
        area varchar(10) DEFAULT NULL,
        state varchar(50) DEFAULT NULL,
        position varchar(100) DEFAULT NULL,
        `rank` varchar(50) DEFAULT NULL,
        first_name varchar(100) DEFAULT NULL,
        last_name varchar(100) DEFAULT NULL,
        address text DEFAULT NULL,
        email varchar(200) DEFAULT NULL,
        phone_office varchar(50) DEFAULT NULL,
        phone_fax varchar(50) DEFAULT NULL,
        phone_home varchar(50) DEFAULT NULL,
        phone_mobile varchar(50) DEFAULT NULL,
        term_start year DEFAULT NULL,
        term_end year DEFAULT NULL,
        edition varchar(20) DEFAULT NULL,
        location text DEFAULT NULL,
        date_range varchar(100) DEFAULT NULL,
        group_key varchar(100) DEFAULT NULL,
        created_at timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_type (type),
        KEY idx_area (area),
        KEY idx_state (state),
        KEY idx_term_start (term_start)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    add_option('sdp_db_version', '1.1');
}

/**
 * Plugin deactivation - clean up temporary data
 */
function sdp_deactivate_plugin() {
    // Clear any cached data or temporary options if needed
    wp_cache_flush();
}

/**
 * Plugin uninstall - remove all plugin data
 */
function sdp_uninstall_plugin() {
    global $wpdb;
    
    // Remove database table
    $table_name = $wpdb->prefix . 'eangus_directory';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
    
    // Remove plugin options
    delete_option('sdp_db_version');
    
    // Clear any cached data
    wp_cache_flush();
}

/**
 * Check for database updates on plugin load
 */
add_action('plugins_loaded', 'sdp_update_db_check');

function sdp_update_db_check() {
    $current_version = get_option('sdp_db_version', '0');
    
    if (version_compare($current_version, '1.1', '<')) {
        sdp_create_database_table();
    }
}

/**
 * Initialize plugin functionality
 */
add_action('init', 'sdp_init');

function sdp_init() {
    // Add admin menu
    add_action('admin_menu', ['StateDirectoryAdmin', 'add_menu']);
    
    // Register shortcode
    add_shortcode('state_directory', ['StateDirectoryShortcode', 'render_all']);
}

/**
 * Enhanced asset enqueuing with higher priority and better theme override protection
 */
add_action('wp_enqueue_scripts', 'sdp_enqueue_assets', 25); // Higher priority to load after theme

function sdp_enqueue_assets() {
    // Only enqueue on pages that contain our shortcode or if we detect it's needed
    global $post;
    
    $should_enqueue = false;
    
    // Check if current page/post contains the shortcode
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'state_directory')) {
        $should_enqueue = true;
    }
    
    // Also enqueue if we're on a page where the shortcode might be used via widgets, etc.
    if (is_page() || is_single()) {
        $should_enqueue = true;
    }
    
    if ($should_enqueue) {
        // Enqueue styles with dependencies on common theme stylesheets to ensure loading order
        wp_enqueue_style(
            'sdp-styles',
            plugin_dir_url(__FILE__) . 'assets/css/styles.css',
            array(), // You can add theme stylesheet handles here if known
            '1.1.1', // Increment version when updating CSS
            'all'
        );

        // Add inline CSS for extra protection against theme overrides
        $inline_css = '
        .sdp-container * {
            box-sizing: border-box !important;
        }
        .sdp-container {
            isolation: isolate;
            contain: layout style;
        }';
        
        wp_add_inline_style('sdp-styles', $inline_css);

        wp_enqueue_script(
            'sdp-script',
            plugin_dir_url(__FILE__) . 'assets/js/script.js',
            array('jquery'), // Add jQuery dependency if needed
            '1.1.1',
            true
        );
    }
}

/**
 * Add CSS with very high priority to override theme styles
 */
add_action('wp_head', 'sdp_add_critical_css', 999);

function sdp_add_critical_css() {
    global $post;
    
    // Only add on pages with our shortcode
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'state_directory')) {
        echo '<style id="sdp-critical-css">
        .sdp-container {
            font-family: "Segoe UI", Roboto, -apple-system, BlinkMacSystemFont, sans-serif !important;
            line-height: 1.6 !important;
            color: #1f2937 !important;
        }
        .sdp-container * {
            box-sizing: border-box !important;
        }
        </style>';
    }
}

/**
 * Add custom body class when our shortcode is present
 */
add_filter('body_class', 'sdp_add_body_class');

function sdp_add_body_class($classes) {
    global $post;
    
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'state_directory')) {
        $classes[] = 'has-sdp-directory';
    }
    
    return $classes;
}