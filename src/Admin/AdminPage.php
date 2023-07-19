<?php
/**
 * Class AdminPage file.
 */

namespace ManishaMakhija\Admin;

use ManishaMakhija\Admin\MMApiData;
use ManishaMakhija\Admin\MMApiDataTab;

/**
 * Admin Page for showing API data.
 */
class AdminPage {

	/**
	 * Init function.
	 */
	public function init() {
		add_action( 'admin_init', array( $this, 'mm_check_dependent_plugin' ) );
		add_filter( 'wp_mail_smtp_admin_area_get_parent_pages', array( $this, 'mm_add_api_response_data_page' ), 10, 1 );
		add_action( 'wp_ajax_mm_api_data_refresh_ajax', array( $this, 'mm_refresh_api_data_ajax' ) );
	}

	/**
	 * Ajax callback for refresh button functionality.
	 */
	public function mm_refresh_api_data_ajax() {
		check_ajax_referer( 'mm-api-data', 'nonce' );
		$obj = new MMApiDataTab();
		ob_start();
		$obj->display();
		wp_send_json( ob_get_clean() );
	}

	/**
	 * Insertion of new page in WP SMTP mail plugin.
	 *
	 * @param  array $pages WP SMTP Mail plugin pages details.
	 *
	 * @return array $pages WP SMTP Mail plugin pages details.
	 */
	public function mm_add_api_response_data_page( $pages ) {
		array_push( \WPMailSMTP\Admin\Area::$pages_registered, 'manisha-makhija' );
		$pages['manisha-makhija'] = new MMApiData(
			array(
				'manisha-makhija' => MMApiDataTab::class,
			)
		);
		return $pages;
	}

	/**
	 * Check if dependent plugin is active.
	 */
	public function mm_check_dependent_plugin() {

		if ( ! defined( 'WPMS_PLUGIN_VER' ) ) {
			add_action( 'admin_notices', array( $this, 'mm_display_admin_notices' ) );
		}
	}

	/**
	 * Display admin notice for non-activation of dependent plugin.
	 */
	public function mm_display_admin_notices() {
		$notice = sprintf(
			/* translators: 1: wp mail smtp plugin url */
			__( 'Some of the features of <strong> Manisha Makhija </strong> is dependent on WP Mail SMTP. In order to get the benefits of those features, please activate <a href="%1$s">WP Mail SMTP</a>.', 'manisha-makhija' ),
			'https://wordpress.org/plugins/wp-mail-smtp/'
		);

		echo '<div class="notice notice-warning"><p>' . wp_kses_post( $notice ) . '</p></div>';
	}
}
