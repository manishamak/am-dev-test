<?php
/**
 * Class ApiInvoker file.
 */

namespace ManishaMakhija;

use ManishaMakhija\TransientCache;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Main Class
 */
class ApiInvoker {

	/**
	 * API URL
	 *
	 * @var string
	 */
	public const API_URL = 'https://miusage.com/v1/challenge/1/';

	/**
	 * Namespace for the registered custom route
	 *
	 * @var string
	 */
	public const ROUTE_NAMESPACE = 'manisha/v1';

	/**
	 * Base slug for the registered route
	 *
	 * @var string
	 */
	public const CUSTOM_ENDPOINT = '/miusage';

	/**
	 * Meta key for caching api data.
	 *
	 * @var string
	 */
	public static $transient_key = 'manisha_miusage_data';

	/**
	 * Init function at the time of plugin loads.
	 */
	public function init() {
		add_action( 'rest_api_init', array( $this, 'register_ajax_endpoint' ) );
		load_plugin_textdomain( 'manisha-makhija', false, MM_PLUGIN_PATH . '/languages' );

		// WPCLI command init.
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			\WP_CLI::add_command( 'mm', 'ManishaMakhija\WP_CLI\MmWPCLI' );
		}

		// Admin Page init.
		$admin_page_obj = new Admin\AdminPage();
		$admin_page_obj->init();

		// Gutenberg Block.
		$g_block = new Block\APIDataDisplayBlock();
		$g_block->init();
	}


	/**
	 * Register Ajax Endpoint.
	 */
	public function register_ajax_endpoint() {
		register_rest_route(
			self::ROUTE_NAMESPACE,
			self::CUSTOM_ENDPOINT,
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'permission_callback' => '__return_true',
				'callback'            => array( $this, 'rest_custom_endpoint_callback' ),
			)
		);
	}

	/**
	 * Callback function for register ajax endpoint.
	 *
	 * @return WP_REST_Response  API response data
	 */
	public function rest_custom_endpoint_callback() {
		// Get data from the cache.
		$transient_obj = new TransientCache( self::$transient_key );
		$cache_data    = $transient_obj->receive_transient_data();
		if ( false === $cache_data ) {
			$api_response = $this->retrieve_remote_data( self::API_URL );
			if ( is_wp_error( $api_response ) ) {
				return $api_response;
			}
			$transient_obj->add_transient_data( $api_response );
			$result = $this->decode_api_data( $api_response );
			return rest_ensure_response( $result );
		}
		$decoded_cache_data = $this->decode_api_data( $cache_data );
		return rest_ensure_response( $decoded_cache_data );
	}

	/**
	 * Retrieve response from API.
	 *
	 * @param  string $api_url      API URL.
	 *
	 * @return string|WP_Error API json data|Error object
	 */
	public static function retrieve_remote_data( string $api_url ) {
		$response = wp_remote_get( $api_url );
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		$api_data = wp_remote_retrieve_body( $response );
		if ( '' === $api_data || empty( $api_data ) ) {
			return new WP_Error( '400', esc_html__( 'No data found', 'manisha-makhija' ), array( 'status' => 400 ) );
		}
		return $api_data; // API data response.
	}

	/**
	 * Decode API response body.
	 *
	 * @param  string $json_data     JSON string.
	 *
	 * @return array  Decoded data
	 */
	public static function decode_api_data( string $json_data ): array {
		return (array) json_decode( $json_data );
	}

	/**
	 * Get decoded response from API.
	 *
	 * @param  string $api_url      API URL.
	 *
	 * @return array|WP_Error  API data|Error object
	 */
	public static function get_accessible_api_data( $api_url ) {
		$api_data = self::retrieve_remote_data( $api_url );
		if ( is_wp_error( $api_data ) ) {
			return $api_data;
		}

		$result = self::decode_api_data( $api_data );
		return $result;
	}

}
