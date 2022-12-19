<?php
function sunflower_admin_bar( $wp_admin_bar ) {
	 $wp_admin_bar->remove_node( 'wp-logo' );

	$args = array(
		'id'    => 'sunflower',
		'title' => 'Sunflower',
		'href'  => home_url() . '/wp-admin/admin.php?page=sunflower_admin',
		'meta'  => array( 'class' => '' ),
	);
	$wp_admin_bar->add_node( $args );
}
add_action( 'admin_bar_menu', 'sunflower_admin_bar', 999 );

function sunflower_notice() {   ?>
	<div class="notice notice-info notice-large sunflower-plugins is-dismissible">
		<p><?php _e( "Thank you for using sunflower theme. <a href='admin.php?page=sunflower_admin'>More information ca be found on the theme's setting page</a>.", 'sunflower' ); ?></p>
	</div>
	<?php
}
if ( empty( get_option( 'sunflower-plugins-dismissed' ) ) ) {
	add_action( 'admin_notices', 'sunflower_notice' );
}

function sunflower_load_admin_scripts() {
	wp_enqueue_script(
		'sunflower-admin',
		get_template_directory_uri() . '/assets/js/admin.js',
		array( 'jquery' ),
		'1.0.0',
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


function sunflower_update_notice() {
	update_option( 'sunflower-plugins-dismissed', 1 );
}
add_action( 'wp_ajax_sunflower_plugins_dismiss', 'sunflower_update_notice' );

function sunflower_admin() {
	// delete_option( 'sunflower-plugins-dismissed' );
}
add_action( 'admin_init', 'sunflower_admin' );

function sunflower_admin_style() {
	wp_enqueue_style( 'sunflower-admin-fontawesome', get_template_directory_uri() . '/assets/css/admin-fontawesome.css' );
	wp_enqueue_style( 'sunflower-admin-styles', get_template_directory_uri() . '/assets/css/admin.css' );
}

 add_action( 'admin_enqueue_scripts', 'sunflower_admin_style' );

function sunflower_change_admin_footer() {
	echo '<span id="footer-note">Programmiert von <a href="https://sunflower-theme.de/" target="_blank">Tom Rose</a>.</span>';
}
add_filter( 'admin_footer_text', 'sunflower_change_admin_footer' );

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

function sunfloer_setup_help_tab() {
	$screen = get_current_screen();

	if ( 'sunflower_event' == $screen->post_type ) {

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
add_action( 'admin_head', 'sunfloer_setup_help_tab' );

function sunflower_login_logo() {
	?>
	<style type="text/css">
		#login h1 a, .login h1 a {
			background-image: url(<?php echo get_template_directory_uri(); ?>/assets/img/logo-theme.svg);
			padding-bottom: 30px;
		}
	</style>
	<?php
}
add_action( 'login_enqueue_scripts', 'sunflower_login_logo' );



function sunflower_fix_event_location() {
	$transient = sprintf( 'sunflower_geocache_%s', $_POST['transient'] );
	$lon       = $_POST['lon'];
	$lat       = $_POST['lat'];
	set_transient( $transient, array( $lon, $lat ) );
}
add_action( 'wp_ajax_sunflower_fix_event_location', 'sunflower_fix_event_location' );

