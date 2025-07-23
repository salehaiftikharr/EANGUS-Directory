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
 * Plugin activation hook - creates the database table
 */
register_activation_hook(__FILE__, 'sdp_create_database_table');

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
        rank varchar(50) DEFAULT NULL,
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
    
    // Optional: Add version option to track database version
    add_option('sdp_db_version', '1.0');
}

/**
 * Plugin deactivation hook - optionally clean up
 */
register_deactivation_hook(__FILE__, 'sdp_deactivate_plugin');

/**
 * Clean up on plugin deactivation (optional)
 */
function sdp_deactivate_plugin() {
    // Optional: You can choose to keep the data or remove it
    // Uncomment the lines below if you want to remove the table on deactivation
    
    /*
    global $wpdb;
    $table_name = $wpdb->prefix . 'eangus_directory';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
    delete_option('sdp_db_version');
    */
}

/**
 * Check for database updates on plugin updates
 */
add_action('plugins_loaded', 'sdp_update_db_check');

function sdp_update_db_check() {
    $current_version = get_option('sdp_db_version', '0');
    
    if ($current_version != '1.0') {
        sdp_create_database_table();
    }
}

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

/**
 * Add admin notice if database table creation fails
 */
add_action('admin_notices', 'sdp_admin_notices');

function sdp_admin_notices() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'eangus_directory';
    
    // Check if table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        echo '<div class="notice notice-error"><p>';
        echo '<strong>State Directory Plugin:</strong> Database table could not be created. Please check your database permissions.';
        echo '</p></div>';
    }
}