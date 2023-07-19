<?php
/**
 * Class MMApiDataTab file.
 */

namespace ManishaMakhija\Admin;

use WPMailSMTP\Admin\PageAbstract;
use ManishaMakhija\ApiInvoker;

/**
 * Displays remote API data in tab.
 */
class MMApiDataTab extends PageAbstract {

	/**
	 * Part of the slug of a tab.
	 *
	 * @var string
	 */
	protected $slug = 'manisha-makhija';

	/**
	 * Tab priority.
	 *
	 * @var int
	 */
	protected $priority = 10;

	/**
	 * Link label of a tab.
	 *
	 * @return string
	 */
	public function get_label() {

		return esc_html__( 'Manisha Makhija', 'manisha-makhija' );
	}

	/**
	 * Title of a tab.
	 *
	 * @return string
	 */
	public function get_title() {

		return $this->get_label();
	}

	/**
	 * Register hooks.
	 */
	public function hooks() {

		add_action( 'wp_mail_smtp_admin_area_enqueue_assets', array( $this, 'enqueue_assets' ) );

	}

	/**
	 * Enqueue required JS and CSS.
	 */
	public function enqueue_assets() {

		wp_enqueue_style(
			'mm-admin-api-data',
			MM_PLUGIN_URL . '/assets/css/mm-admin-api-data.min.css',
			array( 'wp-mail-smtp-admin' ),
			MM_API_DATA_VERSION
		);

		wp_enqueue_script(
			'mm-admin-api-data',
			MM_PLUGIN_URL . '/assets/js/mm-admin-api-data.min.js',
			array( 'wp-mail-smtp-admin' ),
			MM_API_DATA_VERSION,
			false
		);

		$settings = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'mm-api-data' ),
			'loading'  => esc_html__( 'Loading', 'manisha-makhija' ),
		);

		wp_localize_script(
			'mm-admin-api-data',
			'mmApiData',
			$settings
		);

	}


	/**
	 * Output HTML of the API response data.
	 */
	public function display() {
		$data     = ApiInvoker::get_accessible_api_data( ApiInvoker::API_URL );
		$is_error = is_wp_error( $data ); ?>
		<div class="mm-admin-api-data-section mm-admin-api-data-section-squashed">
			<h1 class="centered">
				<strong>
					<?php
					if ( $is_error ) {
						?>
						<span class="mm-error">
						<?php
							printf( /* Translators: %s - error message. */
								esc_html__( 'Error! %s', 'manisha-makhija' ),
								esc_html( $data->get_error_message() )
							);
						?>
						</span> 
						<?php
					} else {
						echo esc_html( $data['title'] );
					}
					?>
				</strong>
			</h1>
		</div>

		<?php if ( ! $is_error ) { ?>
			<div class="mm-admin-api-data-section mm-admin-api-data-section-squashed mm-admin-api-data-section-hero mm-admin-api-data-section-table">
			<?php
			if ( isset( $data['data']->headers ) && ! empty( $data['data']->headers ) ) {
				?>
					<div class="mm-admin-api-data-section-hero-main mm-admin-columns"> 
					<?php
					foreach ( $data['data']->headers as $headers ) {
						?>
							<div class="mm-admin-column-20">
								<h3 class="no-margin">
									<?php echo esc_html( $headers ); ?>
								</h3>
							</div>
							<?php
					}
					?>
					</div>
					<?php
			}

			if ( isset( $data['data']->rows ) && ! empty( $data['data']->rows ) ) {
				?>
					<div class="mm-admin-api-data-section-hero-extra no-padding mm-admin-columns">
						<table>
						<?php
						foreach ( $data['data']->rows as $rows ) {
							?>
								<tr class="mm-admin-columns">
									<td class="mm-admin-column-20">
										<p><?php echo esc_html( $rows->id ); ?></p>
									</td>
									<td class="mm-admin-column-20">
										<p><?php echo esc_html( $rows->fname ); ?></p>
									</td>
									<td class="mm-admin-column-20">
										<p><?php echo esc_html( $rows->lname ); ?></p>
									</td>
									<td class="mm-admin-column-20">
										<p><?php echo esc_html( $rows->email ); ?></p>
									</td>
									<td class="mm-admin-column-20">
										<p><?php echo esc_html( date( 'j F, Y', $rows->date ) ); ?></p>
									</td>
								</tr>
								<?php
						}
						?>
						</table>
					</div>
					<?php
			}
			?>
			</div>
			<a href="javascript:;" class="wp-mail-smtp-btn wp-mail-smtp-btn-upgrade wp-mail-smtp-btn-orange mm-api-data-refresh-btn">
					<?php esc_html_e( 'Refresh', 'manisha-makhija' ); ?>
			</a>
			<?php
		}
	}
}


