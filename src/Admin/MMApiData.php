<?php
namespace FetchApiData\Admin;

use WPMailSMTP\Admin\ParentPageAbstract;

/**
 * Class MMApiData.
 */
class MMApiData extends ParentPageAbstract {

	/**
	 * Page default tab slug.
	 *
	 * @var string
	 */
	protected $default_tab = 'mm-api-data';

	/**
	 * Slug of a page.
	 *
	 * @var string
	 */
	protected $slug = 'mm-api-data';

	/**
	 * Link label of a page.
	 *
	 * @return string
	 */
	public function get_label() {

		return esc_html__( 'Miusage Data', 'manisha-makhija' );
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
