<?php
/**
 * Class MMApiData.
 */

namespace ManishaMakhija\Admin;

use WPMailSMTP\Admin\ParentPageAbstract;

/**
 * For adding API data page in WP SMTP Mail Plugin.
 */
class MMApiData extends ParentPageAbstract {

	/**
	 * Page default tab slug.
	 *
	 * @var string
	 */
	protected $default_tab = 'manisha-makhija';

	/**
	 * Slug of a page.
	 *
	 * @var string
	 */
	protected $slug = 'manisha-makhija';

	/**
	 * Link label of a page.
	 *
	 * @return string
	 */
	public function get_label() {

		return esc_html__( 'Manisha Makhija', 'manisha-makhija' );
	}

	/**
	 * Title of a page.
	 *
	 * @return string
	 */
	public function get_title() {

		return $this->get_label();
	}
}
