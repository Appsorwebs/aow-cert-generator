<?php
// If uninstall not called from WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Drop custom tables created by the plugin (optional - only if you want to purge data)
global $wpdb;
$certs_table = $wpdb->prefix . 'aow_certificates';
$jobs_table = $wpdb->prefix . 'aow_jobs';

// Only drop tables if the administrator has explicitly opted in via option 'aow_drop_on_uninstall'
$drop = get_option('aow_drop_on_uninstall', false);
if ( $drop ) {
    $wpdb->query( "DROP TABLE IF EXISTS {$certs_table}" );
    $wpdb->query( "DROP TABLE IF EXISTS {$jobs_table}" );
}

// Optionally remove plugin options
delete_option('aow_primary_color');
delete_option('aow_secondary_color');
delete_option('aow_capability_slug');
