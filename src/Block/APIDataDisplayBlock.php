<?php
/**
 * Class APIDataDisplayBlock file
 */

namespace ManishaMakhija\Block;

use ManishaMakhija\ApiInvoker;

/**
 * Front-side display of Gutenberg Block.
 */
class APIDataDisplayBlock {

	/**
	 * Init functions
	 */
	public function init() {
		add_action( 'init', array( $this, 'mm_register_block_type' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'mm_init_block_editor_assets' ) );
	}

	/**
	 * Register Gutenberg Block.
	 */
	public function mm_register_block_type() {
		register_block_type(
			MM_PLUGIN_PATH . 'build\Block',
			array(
				'render_callback' => array( $this, 'mm_display_api_data' ),
			)
		);
	}

	/**
	 * Callback function for frontend block.
	 *
	 * @param array $attr attributes of block.
	 *
	 * @return string $html_structure HTML structure of frontside
	 */
	public function mm_display_api_data( $attr ) {
		$html_structure = '';
		$custom_api_url = get_rest_url( null, ApiInvoker::ROUTE_NAMESPACE . ApiInvoker::CUSTOM_ENDPOINT );
		$api_data       = ApiInvoker::get_accessible_api_data( $custom_api_url );
		$data_headers   = $api_data['data']->headers;
		$data_rows      = $api_data['data']->rows;

		if ( empty( $data_headers ) || empty( $data_rows ) ) {
			return '<p>' . esc_html__( 'No data found in API', 'manisha-makhija' ) . '</p>';
		}

		$attr_wrap_class = ! empty( $attr['className'] ) ? esc_html( $attr['className'] ) : '';
		$html_structure .=
		'<div class="manisha-makhija-table-data-wrap ' . $attr_wrap_class . '">
			<table border="1" style="width:100%; text-align:center;">';
			$table_title = apply_filters( 'mm_api_table_title', $api_data['title'] );
		if ( '' !== $table_title ) {
			$html_structure .= '<tr><td colspan="5" style="font-size:xx-large; padding:25px;">' . $table_title . '</td></tr>';
		}

			$html_structure .= '<tr>';
		foreach ( $data_headers as $header ) {
			$attr_key        = str_replace( ' ', '_', strtolower( $header ) );
			$html_structure .= $attr[ $attr_key ] ? '<th>' . esc_html( $header ) . '</th>' : '';
		}
			$html_structure .= '</tr>';

		foreach ( $data_rows as $key => $record ) {
			$html_structure .= '<tr>';
			$html_structure .= $attr['id'] ? '<td>' . esc_html( $record->id ) . '</td>' : '';
			$html_structure .= $attr['first_name'] ? '<td>' . esc_html( $record->fname ) . '</td>' : '';
			$html_structure .= $attr['last_name'] ? '<td>' . esc_html( $record->lname ) . '</td>' : '';
			$html_structure .= $attr['email'] ? '<td>' . esc_html( $record->email ) . '</td>' : '';
			$html_structure .= $attr['date'] ? '<td>' . esc_html( date( 'n/j/Y', $record->date ) ) . '</td>' : '';
			$html_structure .= '</tr>';
		}
			$html_structure .= '</table></div>';

		return apply_filters( 'mm_api_data_display_content', $html_structure );
	}

	/**
	 * Enqueue assets during block registration.
	 */
	public function mm_init_block_editor_assets() {

		wp_set_script_translations(
			'manisha-makhija-api-data-display-editor-script',
			'manisha-makhija'
		);

		wp_localize_script(
			'manisha-makhija-api-data-display-editor-script',
			'mmApiDataDisplayBlock',
			array(
				'custom_endpoint' => ApiInvoker::ROUTE_NAMESPACE . ApiInvoker::CUSTOM_ENDPOINT,
			)
		);
	}
}
