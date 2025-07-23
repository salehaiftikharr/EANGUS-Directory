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
 * Enqueue plugin assets
 */
add_action('wp_enqueue_scripts', 'sdp_enqueue_assets');

function sdp_enqueue_assets() {
    wp_enqueue_style(
        'sdp-styles',
        plugin_dir_url(__FILE__) . 'assets/css/styles.css',
        [],
        '1.1'
    );

    wp_enqueue_script(
        'sdp-script',
        plugin_dir_url(__FILE__) . 'assets/js/script.js',
        [],
        '1.1',
        true
    );
}