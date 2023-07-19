<?php
/**
 * Class MmWPCLI file.
 */

namespace ManishaMakhija\WP_CLI;

use ManishaMakhija\ApiInvoker;
use ManishaMakhija\TransientCache;

/**
 * WP_CLI class for creating cli commands.
 */
class MmWPCLI {

	/**
	 * On-demand clearing transient cache.
	 */
	public function clear_cache() {
		$transient_obj = new TransientCache( ApiInvoker::$transient_key );
		$is_deleted    = $transient_obj->remove_transient_data();
		if ( $is_deleted ) {
			\WP_CLI::success( esc_html__( 'Cache has been cleared.', 'manisha-makhija' ) );
		} else {
			\WP_CLI::error( esc_html__( 'Either there is no cache or an error has occurred.', 'manisha-makhija' ) );
		}
	}
}
