<?php
/**
 * Plugin Name: SEO Meta Bulk Editor
 * Description: Bulk edit SEO titles and meta descriptions for WordPress.
 * Version: 0.1.0
 * Author: Abhishek Soni
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load Required Files
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/parser.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/validator.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/mapper.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/updater.php';

/**
 * Add Admin Menu
 */
add_action( 'admin_menu', 'smbe_admin_menu' );

function smbe_admin_menu() {

	add_menu_page(
		'SEO Meta Bulk Editor',
		'SEO Meta Bulk Editor',
		'manage_options',
		'seo-meta-bulk-editor',
		'smbe_dashboard',
		'dashicons-edit-page',
		80
	);

}

/**
 * Load Dashboard
 */
function smbe_dashboard() {

	require_once plugin_dir_path( __FILE__ ) . 'admin/dashboard.php';

}