<?php

namespace FetchApiData\Admin;

use WPMailSMTP\Admin\PageAbstract;
use FetchApiData\CreateEndpoint;

/**
 * Class MMApiDataTab file
 * Displays remote API data.
 */
class MMApiDataTab extends PageAbstract {

	/**
	 * Part of the slug of a tab.
	 *
	 * @var string
	 */
	protected $slug = 'mm-api-data';

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

		return esc_html__( 'Miusage Data', 'manisha-makhija' );
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
			MM_PLUGIN_URL . '/assets/css/mm-admin-api-data.css',
			array( 'wp-mail-smtp-admin' ),
			MM_API_DATA_VERSION
		);

		wp_enqueue_script(
			'mm-admin-api-data',
			MM_PLUGIN_URL . '/assets/js/mm-admin-api-data.js',
			array( 'jquery', 'wp-mail-smtp-admin' ),
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
		$data     = CreateEndpoint::get_accessible_API_data( CreateEndpoint::API_URL );
		$is_error = is_wp_error( $data ); ?>
		<div class="mm-admin-api-data-section mm-admin-api-data-section-squashed">
			<h1 class="centered">
				<strong>
					<?php
					if ( $is_error ) { ?>
						<span class="mm-error"><?php 
							printf( /* Translators: %s - error message. */ 
								__( 'Error! %s', 'manisha-makhija' ), esc_html( $data->get_error_message() )
							); ?>
						</span> 
															 <?php
																// printf(
																// wp_kses( /* Translators: %s - error message. */
																// __( '<span class="mm-error">Error! %s </span>', 'manisha-makhija' ),
																// [
																// 'span' => [
																// 'class' => [],
																// ],
																// ]
																// ),
																// esc_html($data['message'])
																// );
					} else {
						echo esc_html( $data['title'] );
					}
					// printf(
					// /* translators: %s - plugin current license type. */
					// esc_html__( 'The amazing Table', 'manisha-makhija' )

					// );
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
										<p><?php echo date( 'j F, Y', $rows->date ); ?></p>
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
		?>


			<!-- <div class="wp-mail-smtp-admin-about-section wp-mail-smtp-admin-about-section-hero">
				<div class="wp-mail-smtp-admin-about-section-hero-main no-border">
					<h3 class="call-to-action centered">
						<a href="<?php echo esc_url( wp_mail_smtp()->get_upgrade_link( 'lite-vs-pro' ) ); ?>" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Get WP Mail SMTP Pro Today and Unlock all of these Powerful Features', 'wp-mail-smtp' ); ?>
						</a>
					</h3>

					<p class="centered">
						<?php
						printf(
							wp_kses( /* Translators: %s - discount value $50. */
								__( 'Bonus: WP Mail SMTP Lite users get <span class="price-off">%s off regular price</span>, automatically applied at checkout.', 'wp-mail-smtp' ),
								array(
									'span' => array(
										'class' => array(),
									),
								)
							),
							'$50'
						);
						?>
					</p>
				</div>
			</div> -->
		<?php
	}


	// public function getAPIData(){
	// $remoteAPIObj = new CreateEndpoint();
	// $apiData = $remoteAPIObj->retrieveRemoteData(CreateEndpoint::API_URL);
	// if ( !is_array($apiData) ){
	// $apiDataArray = $remoteAPIObj->decodeApiData($apiData);
	// return $apiDataArray;
	// }
	// return $apiData;
	// }
}


