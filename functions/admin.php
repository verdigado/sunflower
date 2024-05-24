<?php
/**
 * Functions for backend admins.
 *
 * @package sunflower
 */

/**
 * Replace the WordPress logo and replace with link to sunflower settings.
 *
 * @param \WP_Admin_Bar $wp_admin_bar The WP Admin Bar object.
 */
function sunflower_admin_bar( $wp_admin_bar ) {

	// Remove WP Logo on the left.
	$wp_admin_bar->remove_node( 'wp-logo' );

	$args = array(
		'id'    => 'sunflower',
		'title' => 'Sunflower',
		'href'  => home_url() . '/wp-admin/admin.php?page=sunflower_admin',
		'meta'  => array(
			'class' => 'sunflower-icon',
			'title' => esc_attr__( 'Sunflower Settings Page', 'sunflower' ),
		),
	);

	// Add the link to Sunflower settings to the admin bar.
	$wp_admin_bar->add_node( $args );
}

add_action( 'admin_bar_menu', 'sunflower_admin_bar', 999 );

/**
 * Show sunflower note.
 */
function sunflower_notice() {
	?>
	<div id="sunflower-plugins" class="notice notice-info notice-large sunflower-plugins is-dismissible">
		<?php wp_nonce_field( 'sunflower_update_notice' ); ?>
		<p>
		<?php
			$linkgithub    = "<a href='https://github.com/verdigado/sunflower' target='_blank'>open source</a>";
			$linkverdigado = "<a href='https://www.verdigado.com/' target='_blank' title='verdigado eG'>
                <img src='" . get_template_directory_uri() . "/assets/img/verdigado-logo.png' alt='Logo of verdigado eG' /></a>";
		printf(
		/* translators: %1$s and %2$s are replaced with links */
			esc_attr__( 'Thank you for using sunflower theme. Sunflower is %1$s and maintained by %2$s.', 'sunflower' ),
			wp_kses_post( $linkgithub ),
			wp_kses_post( $linkverdigado )
		);
		printf(
			'<br /><a href="admin.php?page=sunflower_admin">%s</a>.',
			esc_attr__( 'More information can be found on the sunflower theme settings page', 'sunflower' )
		);
		?>
		</p>
	</div>
	<?php
}

if ( empty( get_option( 'sunflower-plugins-dismissed' ) ) ) {
	add_action( 'admin_notices', 'sunflower_notice' );
}

/**
 * Show sunflower note about deprecated PHP version.
 */
function sunflower_notice_php() {

	?>
	<div id="sunflower-plugins-php-82" class="notice notice-error update-nag sunflower-plugins is-dismissible">
		<?php wp_nonce_field( 'sunflower_update_notice' ); ?>
		<p class="h3"><?php esc_attr_e( 'PHP Version End of Life', 'sunflower' ); ?></p>
		<p>
		<?php
			$phpversion = 'Current PHP version: ' . phpversion();
		$linkverdigado  = "<a href='https://www.verdigado.com/' target='_blank' title='verdigado eG'>
                <img src='" . get_template_directory_uri() . "/assets/img/verdigado-logo.png' alt='Logo of verdigado eG' /></a>";

		printf(
			wp_kses_post(
			/* translators: %1$s is replace with current PHP version and %2$s is replaced with link */
				__(
					'<p>You are using PHP <strong>%1$s</strong> which <a href="https://www.php.net/supported-versions.php">is not supported</a> anymore!</p>
                <p>Please note, that Sunflower theme <strong>will require at least PHP 8.2+</strong> as of release 2.1.0. <br />
                If you want to continue receiving updates, you must update your server or contact your
                web hosting service.</p>
                <p>If you are looking for a web hosting service with attitute, have look at %2$s.</p>',
					'sunflower'
				)
			),
			esc_attr( phpversion() ),
			esc_url( $linkverdigado )
		);
		?>
		</p>
	</div>
	<?php
}

if ( empty( get_option( 'sunflower-plugins-php-82-dismissed' ) ) && version_compare( phpversion(), '8.2', '<' ) ) {
	add_action( 'admin_notices', 'sunflower_notice_php' );
}

/**
 * Sunflower note about the new terms of use setting.
 */
function sunflower_notice_terms() {

	?>
	<div id="sunflower-notice-terms" class="notice notice-error update-nag sunflower-plugins is-dismissible">
		<?php wp_nonce_field( 'sunflower_update_notice' ); ?>
		<p class="h3"><?php esc_attr_e( 'New Terms and Use Settings', 'sunflower' ); ?></p>
		<p>
			<?php
			echo wp_kses_post(
				__(
					"There is a new option on the <a href='admin.php?page=sunflower_admin'>Sunflower->First Steps</a> page.<br />
            If you continue to use the sunflower icons in menue, footer and as favicon, please read and accept the terms of use.",
					'sunflower'
				)
			);
			?>
		</p>
	</div>
	<?php
}

$sunflower_first_steps_options = get_option( 'sunflower_first_steps_options' ) ?? array();
if ( empty( get_option( 'sunflower-notice-terms-dismissed' ) ) && ( $sunflower_first_steps_options['sunflower_terms_of_use'] ?? false ) !== 'checked' ) {
	add_action( 'admin_notices', 'sunflower_notice_terms' );
}

/**
 * Load admin scripts.
 */
function sunflower_load_admin_scripts() {
	wp_enqueue_script(
		'sunflower-admin',
		get_template_directory_uri() . '/assets/js/admin.js',
		array( 'jquery' ),
		SUNFLOWER_VERSION,
		true
	);

	wp_localize_script(
		'sunflower-admin',
		'sunflower',
		array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'title'   => get_the_title(),
		)
	);
}

add_action( 'admin_enqueue_scripts', 'sunflower_load_admin_scripts' );

/**
 * Update Dismissed option of the given option.
 *
 * The option id is send via ajax-call from admin.js
 */
function sunflower_update_notice() {
	// Do not save, if nonce is invalid.
	if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'sunflower_update_notice' ) ) {
		return;
	}

	$id              = $_POST['id'] ?? '';
	$dissmiss_option = empty( $id ) ? 'sunflower-plugins-dismissed' : $id . '-dismissed';

	update_option( $dissmiss_option, 1 );
}

add_action( 'wp_ajax_sunflower_plugins_dismiss', 'sunflower_update_notice' );

/**
 * Delete dissmissed option for admins.
 */
function sunflower_admin() {
	// phpcs:ignore
	// delete_option( 'sunflower-plugins-dismissed' );
}

add_action( 'admin_init', 'sunflower_admin' );

/**
 * Add CSS file for sunflower admin style.
 */
function sunflower_admin_style() {
	wp_enqueue_style(
		'sunflower-admin-styles',
		get_template_directory_uri() . '/assets/css/admin.css',
		null,
		'2.1.0'
	);
}

add_action( 'admin_enqueue_scripts', 'sunflower_admin_style' );

/**
 * Add footer note to all backend pages.
 */
function sunflower_change_admin_footer() {
	echo wp_kses_post(
		'<span id="footer-note"><a href="https://sunflower-theme.de/" target="_blank">Sunflower</a> wurde programmiert von <a href="https://github.com/codeispoetry" target="_blank">Tom Rose</a> für <a href="https://www.verdigado.com/" target="_blank">
    <img src="' . get_template_directory_uri() . '/assets/img/verdigado-logo.png" /></a></span>'
	);
}

add_filter( 'admin_footer_text', 'sunflower_change_admin_footer' );

/**
 * Add sunflower widget to dashboard.
 */
function sunflower_add_custom_dashboard_widgets() {
	wp_add_dashboard_widget(
		'sunflower_dashboard_widget', // Widget slug.
		__( 'Sunflower', 'sunflower' ), // Title.
		'sunflower_dashboard_widget_content', // Display function.
		null,
		null,
		'column-4'
	);
}

add_action( 'wp_dashboard_setup', 'sunflower_add_custom_dashboard_widgets' );

/**
 * Create the function to output the contents of your Dashboard Widget.
 */
function sunflower_dashboard_widget_content() {
	echo 'Schön, dass Du Sunflower benutzt. Eine ausführliche Hilfe gibt es unter <a href="https://sunflower-theme.de/documentation" target="_blank">https://sunflower-theme.de/documentation</a>.';
}

/**
 * Add help to sunflower menus.
 */
function sunflower_setup_help_tab() {
	$screen = get_current_screen();

	if ( 'sunflower_event' === $screen->post_type ) {
		get_current_screen()->add_help_tab(
			array(
				'id'      => 'sunflower-event',
				'title'   => __( 'Help for sunflowers events', 'sunflower' ),
				'content' => '<strong>Sunflower-Termine.</strong>
        <p>Du kannst Termine hier anlegen oder einen iCal-Kalender importieren. Mehr Infos dazu gibt es auf
        <a href="https://sunflower-theme.de/documentation/events/" target="_blank">https://sunflower-theme.de/documentation/events/</a></p>
    ',
			)
		);
	}
}

add_action( 'admin_head', 'sunflower_setup_help_tab' );

/**
 * Show sunflower icon on login screen.
 */
function sunflower_login_logo() {
	?>
	<style type="text/css">
		#login h1 a, .login h1 a {
			background-image: url(<?php echo esc_url( get_template_directory_uri() ); ?>/assets/img/logo-theme.svg);
			padding-bottom: 30px;
		}
	</style>
	<?php
}

add_action( 'login_enqueue_scripts', 'sunflower_login_logo' );

/**
 * Fix event location.
 */
function sunflower_fix_event_location() {
	if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'sunflower_location' ) ) {
		return;
	}
	$transient = sprintf( 'sunflower_geocache_%s', $_POST['transient'] );
	$lon       = $_POST['lon'];
	$lat       = $_POST['lat'];
	set_transient( $transient, array( $lon, $lat ) );
}

add_action( 'wp_ajax_sunflower_fix_event_location', 'sunflower_fix_event_location' );
