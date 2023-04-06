<?php
/**
 * Class CreateEndpoint file.
 */

namespace FetchApiData;

use FetchApiData\TransientCache;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Main Class
 */
class CreateEndpoint {

	/**
	 * API URL
	 *
	 * @var string
	 */
	public const API_URL = 'https://miusage.com/v1/challenge/1/';

	/**
	 * Namespace for the registered route
	 *
	 * @var string
	 */
	public const route_namespace = 'manisha/v1';

	/**
	 * Base URL for the registered route
	 *
	 * @var string
	 */
	public const endpoint = '/miusage';

	/**
	 * meta key for caching api data.
	 *
	 * @var string
	 */
	public static $transient_key = 'manisha_miusage_data';

	/**
	 * Init functions at the time of plugin loads
	 */
	public function init() {
		add_action( 'rest_api_init', array( $this, 'register_ajax_endpoint' ) );
		load_plugin_textdomain( 'manisha-makhija', false, MM_PLUGIN_PATH . '/languages' );

		// WPCLI commands init
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			\WP_CLI::add_command( 'mm', 'FetchApiData\WP_CLI\MmWPCLI' );
		}

		// Admin Page init
		$adminPageObj = new Admin\AdminPage();
		$adminPageObj->init();

		// Gutenberg Block
		$gBlock = new Block\APIDataDisplayBlock();
		$gBlock->init();
	}


	/**
	 * Register Ajax Endpoint.
	 */
	public function register_ajax_endpoint() {
		register_rest_route(
			self::route_namespace,
			self::endpoint,
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
		$transientObj = new TransientCache( self::$transient_key );
		$cacheData    = $transientObj->receiveTransientData();
		if ( false === $cacheData ) {
			// $response = $this->remoteResponse( self::API_URL );
			// // Check API response is valid or not.
			// if ( is_wp_error( $response ) ) {
			// $error_response = $this->receiveErrorJson( $response );
			// return rest_ensure_response( $error_response );
			// }
			// $apiData = $this->fetchResponseBody( $response, $transientObj );
			$apiResponse = $this->retrieveRemoteData( self::API_URL );
			if ( is_wp_error( $apiResponse ) ) {
				return $apiResponse;
			}

			// if ( !is_wp_error( $apiResponse ) ){
			// // if (!is_array($apiResponse)){
			$transientObj->addTransientData( $apiResponse );
			$result = $this->decodeApiData( $apiResponse );
			return rest_ensure_response( $result );
			// }
			// return $apiResponse;

		}
		$decodedCacheData = $this->decodeApiData( $cacheData );
		return rest_ensure_response( $decodedCacheData );
	}

	/**
	 * Call remote API.
	 *
	 * @param  string $apiurl  Full API URL
	 *
	 * @return array  $response Get API response
	 */
	// public function remoteResponse( string $apiUrl ): array {
	// $response = (array) wp_remote_get( $apiUrl );
	// return $response;
	// }

	/**
	 * Retrieve error details.
	 *
	 * @param  array  $errorObj        Error object
	 * @param  string $customErrorMsg  Error message
	 *
	 * @return array  Error message.
	 */
	// public function receiveErrorJson( $errorObj = null, $customErrorMsg = null ): array {
	// $errorObjMessage = ! empty( $errorObj ) ? get_error_message( $errorObj ) : '';
	// $errorMsg        = ! empty( $customErrorMsg ) ? $customErrorMsg : $errorObjMessage;
	// return array(
	// 'is_error' => $errorMsg,
	// );
	// }

	/**
	 * Retrieve response body from API.
	 *
	 * @param  array          $response      Response body
	 * @param  TransientCache $transientObj  Instance of TransientCache
	 *
	 * @return array          API data|Error
	 */
	// public function fetchResponseBody( array $response, TransientCache $transientObj ): array {
	// $apiData = wp_remote_retrieve_body( $response );
	// if ( is_wp_error( $apiData ) ) {
	// return $this->receiveErrorJson( $apiData );
	// }
	// if ( empty( $apiData ) ) {
	// return $this->receiveErrorJson( '', __( 'No data found', 'manisha-makhija' ) );
	// }
	// $transientObj->addTransientData( $apiData );
	// $result = $this->decodeApiData( $apiData );
	// return $result;
	// }

	public static function retrieveRemoteData( string $apiUrl ): string|WP_Error {
		$response = wp_remote_get( $apiUrl );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$apiData = wp_remote_retrieve_body( $response );
		if ( $apiData == '' || empty( $apiData ) ) {
			return new WP_Error( '400', esc_html__( 'No data found', 'manisha-makhija' ), array( 'status' => 400 ) );
		}
		return $apiData; // API data response

		// if ( !is_wp_error( $response ) ){
		// $apiData = wp_remote_retrieve_body( $response );
		// if ( $apiData == '' || empty( $apiData ) ){
		// return new WP_Error( '400', esc_html__( 'No data found', 'manisha-makhija' ), array( 'status' => 400 ) );
		// }
		// return $apiData; // API data response
		// }
		// return $response;  // wp_error

		// Check API response is valid or not.
			// if ( is_wp_error( $response ) ) {
			// return $this->receiveErrorJson( $response );
			// rest_ensure_response( $error_response );
			// }
			// $apiData = wp_remote_retrieve_body( $response );
			// if ( is_wp_error( $apiData ) ) {
			// return $this->receiveErrorJson( $apiData );
			// }
			// if ( empty( $apiData ) ) {
			// return $this->receiveErrorJson( '', __( 'No data found', 'manisha-makhija' ) );
			// }
			// return $apiData;
	}

	 /**
	  * Retrieve response code from API.
	  *
	  * @param  array   $response       Response body
	  *
	  * @return int     $responseCode   Response code
	  */
	// public function fetchResponseCode(array $response): int
	// {
	// $responseCode = wp_remote_retrieve_response_code($response);
	// return $responseCode;
	// }

	/**
	 * Decode API response body.
	 *
	 * @param  string $jsonData     JSON string
	 *
	 * @return array  Decoded data
	 */
	public static function decodeApiData( string $jsonData ): array {
		return (array) json_decode( $jsonData );
	}


	public static function get_accessible_API_data( $api_url ): array|WP_Error {
		// $remoteAPIObj = new CreateEndpoint();
		$apiData = self::retrieveRemoteData( $api_url );

		if ( is_wp_error( $apiData ) ) {
			return $apiData;
		}

		// if ( !is_wp_error( $apiResponse ) ){
		// // if (!is_array($apiResponse)){
		// $transientObj->addTransientData( $apiResponse );
		$result = self::decodeApiData( $apiData );
		return $result;

		// if ( !is_array($apiData) ){
		// $apiDataArray = $remoteAPIObj->decodeApiData($apiData);
		// return $apiDataArray;
		// }
		// return $apiData;
	}

}
