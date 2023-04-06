<?php
namespace FetchApiData\WP_CLI;

use FetchApiData\CreateEndpoint;
use FetchApiData\TransientCache;

/**
 * Class MmWPCLI file.
 *
 * WP_CLI class for creating cli commands
 */
class MmWPCLI {

	/**
	 * On-demand clearing transient cache
	 */
	public function clear_cache() {
		$transientObj = new TransientCache( CreateEndpoint::$transient_key );
		$is_deleted   = $transientObj->removeTransientData();
		if ( $is_deleted ) {
			\WP_CLI::success( __( 'Cache has been cleared.', 'manisha-makhija' ) );
		} else {
			\WP_CLI::error( __( 'Either there is no cache or an error has occurred.', 'manisha-makhija' ) );
		}
	}
}
