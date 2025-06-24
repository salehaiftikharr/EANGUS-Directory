<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// Delete both custom tables
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}annual_conference");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}executive_officers");
