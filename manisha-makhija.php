<?php
/**
Plugin Name: Manisha Makhija
Description: API based plugin challenge
Author: Manisha Makhija
Author URI: https://profiles.wordpress.org/manishamakhija/
Text Domain: manisha-makhija
Domain Path: /languages/
Version: 1.0
 */

// Check abspath exists or not.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'MM_PLUGIN_FILE' ) ) {
	define( 'MM_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'MM_PLUGIN_PATH' ) ) {
	define( 'MM_PLUGIN_PATH', plugin_dir_path( MM_PLUGIN_FILE ) );
}

if ( ! defined( 'MM_PLUGIN_URL' ) ) {
	define( 'MM_PLUGIN_URL', plugin_dir_url( MM_PLUGIN_FILE ) );
}

if ( ! defined( 'MM_API_DATA_VERSION' ) ) {
	define( 'MM_API_DATA_VERSION', 1.0 );
}

// Inclusion of main class.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) && ! class_exists( 'ManishaMakhija\ApiInvoker' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}
if ( class_exists( 'ManishaMakhija\ApiInvoker' ) ) {
	$apinvoker_obj = new ManishaMakhija\ApiInvoker();
	$apinvoker_obj->init();
}
