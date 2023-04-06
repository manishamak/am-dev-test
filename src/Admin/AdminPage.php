<?php
namespace FetchApiData\Admin;

use FetchApiData\Admin\MMApiData;
use FetchApiData\Admin\MMApiDataTab;

class AdminPage {

	public function init() {
		// if ( has_filter('wp_mail_smtp_admin_area_get_parent_pages') ){
		// if ( function_exists( 'wp_mail_smtp' ) ) {
		// if ( defined( 'WPMS_PLUGIN_VER' ) ) {
		// include_once ABSPATH . 'wp-admin/includes/plugin.php';
		// var_dump(is_plugin_active( 'plugins/wp-mail-smtp/wp-mail-smtp.php' ));
		// if ( is_plugin_active( MM_PLUGIN_FILE ) ) {
		// Plugin is active
		// }
		add_action( 'admin_init', array( $this, 'mm_check_dependent_plugin' ) );
		add_filter( 'wp_mail_smtp_admin_area_get_parent_pages', array( $this, 'mm_add_api_response_data_page' ), 10, 1 );
		add_action( 'wp_ajax_mm_api_data_refresh_ajax', array( $this, 'mm_refresh_api_data_ajax' ) );
		add_action( 'wp_ajax_no_priv_mm_api_data_refresh_ajax', array( $this, 'mm_refresh_api_data_ajax' ) );

	}

	public function mm_refresh_api_data_ajax() {
		check_ajax_referer( 'mm-api-data', 'nonce' );
		$obj = new MMApiDataTab();
		ob_start();
		$obj->display();
		wp_send_json( ob_get_clean() );
	}

	public function mm_add_api_response_data_page( $pages ) {

		array_push( \WPMailSMTP\Admin\Area::$pages_registered, 'mm-api-data' );
		$pages['mm-api-data'] = new MMApiData(
			array(
				'mm-api-data' => MMApiDataTab::class,
			)
		);
		return $pages;
	}

	public function mm_check_dependent_plugin() {

		if ( ! defined( 'WPMS_PLUGIN_VER' ) ) {
			add_action( 'admin_notices', array( $this, 'mm_display_admin_notices' ) );
		}
	}


	public function mm_display_admin_notices() {
		$notice = sprintf(
			/* translators: 1: wp mail smtp plugin url */
			__( 'Some of the features of <strong> Manisha Makhija </strong> is dependent on WP Mail SMTP. In order to get the benefits of those features, please activate <a href="%1$s">WP Mail SMTP</a>.', 'manisha-makhija' ),
			'https://wordpress.org/plugins/wp-mail-smtp/'
		);

		echo '<div class="notice notice-warning"><p>' . wp_kses_post( $notice ) . '</p></div>';
	}
}
