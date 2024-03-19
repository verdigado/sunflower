<?php
function sunflower_admin_bar($wp_admin_bar)
{
    $wp_admin_bar->remove_node('wp-logo');

    $args = [
        'id' => 'sunflower',
        'title' => 'Sunflower',
        'href' => home_url() . '/wp-admin/admin.php?page=sunflower_admin',
        'meta' => [
            'class' => '',
        ],
    ];
    $wp_admin_bar->add_node($args);
}

add_action('admin_bar_menu', 'sunflower_admin_bar', 999);

function sunflower_notice()
{   ?>
	<div class="notice notice-info notice-large sunflower-plugins is-dismissible">
		<p><?php
            $linkgithub = "<a href='https://github.com/verdigado/sunflower' target='_blank'>open source</a>";
    $linkverdigado = "<a href='https://www.verdigado.com/' target='_blank' title='verdigado eG'>
                <img src='" . get_template_directory_uri() . "/assets/img/verdigado-logo.png' alt='Logo of verdigado eG' /></a>";
    /* translators: %1$s and %2$s are replaced with string */
    echo sprintf(
        __('Thank you for using sunflower theme. Sunflower is %1$s and maintained by %2$s.', 'sunflower'),
        $linkgithub,
        $linkverdigado
    );
    echo '<br />';
    _e("<a href='admin.php?page=sunflower_admin'>More information can be found on the theme's settings page</a>.", 'sunflower');
    ?>
        </p>
	</div>
	<?php
}

if (empty(get_option('sunflower-plugins-dismissed'))) {
    add_action('admin_notices', 'sunflower_notice');
}

function sunflower_notice_php()
{   ?>
	<div class="notice notice-error update-nag sunflower-plugins is-dismissible">
        <p class="h3"><?php __('PHP Version End of Life', 'sunflower') ?></p>
		<p><?php
            $phpversion = "Current PHP version: " . phpversion();
    $linkverdigado = "<a href='https://www.verdigado.com/' target='_blank' title='verdigado eG'>
                <img src='" . get_template_directory_uri() . "/assets/img/verdigado-logo.png' alt='Logo of verdigado eG' /></a>";
    /* translators: %1$s and %2$s are replaced with string */
    echo sprintf(
        __('<p>You are using PHP <strong>%1$s</strong> which <a href="https://www.php.net/supported-versions.php">is not supported</a> anymore!</p>
                <p>Please note, that Sunflower theme <strong>will require at least PHP 8.2+</strong> as of release 2.1.0. <br />
                If you want to continue receiving updates, you must update your server or contact your
                web hosting service.</p>
                <p>If you are looking for a web hosting service with attitute, have look at %2$s.</p>', 'sunflower'),
        phpversion(),
        $linkverdigado
    );
    ?>
        </p>
	</div>
	<?php
}

if (empty(get_option('sunflower-plugins-php-82-dismissed')) && version_compare(phpversion(), '8.2', '<')) {
    add_action('admin_notices', 'sunflower_notice_php');
}

function sunflower_load_admin_scripts()
{
    wp_enqueue_script(
        'sunflower-admin',
        get_template_directory_uri() . '/assets/js/admin.js',
        ['jquery'],
        '1.0.0',
        true
    );
    wp_localize_script(
        'sunflower-admin',
        'sunflower',
        [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'title' => get_the_title(),
        ]
    );
}

add_action('admin_enqueue_scripts', 'sunflower_load_admin_scripts');

function sunflower_update_notice()
{
    update_option('sunflower-plugins-dismissed', 1);
}

add_action('wp_ajax_sunflower_plugins_dismiss', 'sunflower_update_notice');

function sunflower_admin()
{
    // delete_option( 'sunflower-plugins-dismissed' );
}

add_action('admin_init', 'sunflower_admin');

function sunflower_admin_style()
{
    wp_enqueue_style('sunflower-admin-styles', get_template_directory_uri() . '/assets/css/admin.css');
}

add_action('admin_enqueue_scripts', 'sunflower_admin_style');

function sunflower_change_admin_footer()
{
    echo '<span id="footer-note"><a href="https://sunflower-theme.de/" target="_blank">Sunflower</a> wurde programmiert von <a href="https://github.com/codeispoetry" target="_blank">Tom Rose</a> für <a href="https://www.verdigado.com/" target="_blank">
    <img src="' . get_template_directory_uri() . '/assets/img/verdigado-logo.png" /></a></span>';
}

add_filter('admin_footer_text', 'sunflower_change_admin_footer');

function sunflower_add_custom_dashboard_widgets()
{
    wp_add_dashboard_widget(
        'sunflower_dashboard_widget', // Widget slug.
        __('Sunflower', 'sunflower'), // Title.
        'sunflower_dashboard_widget_content', // Display function.
        null,
        null,
        'column-4'
    );
}

add_action('wp_dashboard_setup', 'sunflower_add_custom_dashboard_widgets');

/**
 * Create the function to output the contents of your Dashboard Widget.
 */

function sunflower_dashboard_widget_content()
{
    echo 'Schön, dass Du Sunflower benutzt. Eine ausführliche Hilfe gibt es unter <a href="https://sunflower-theme.de/documentation" target="_blank">https://sunflower-theme.de/documentation</a>.';
}

function sunfloer_setup_help_tab()
{
    $screen = get_current_screen();

    if ('sunflower_event' == $screen->post_type) {
        get_current_screen()->add_help_tab(
            [
                'id' => 'sunflower-event',
                'title' => __('Help for sunflowers events', 'sunflower'),
                'content' => '<strong>Sunflower-Termine.</strong>
        <p>Du kannst Termine hier anlegen oder einen iCal-Kalender importieren. Mehr Infos dazu gibt es auf
        <a href="https://sunflower-theme.de/documentation/events/" target="_blank">https://sunflower-theme.de/documentation/events/</a></p>
    ',
            ]
        );
    }
}

add_action('admin_head', 'sunfloer_setup_help_tab');

function sunflower_login_logo()
{
    ?>
	<style type="text/css">
		#login h1 a, .login h1 a {
			background-image: url(<?php echo get_template_directory_uri(); ?>/assets/img/logo-theme.svg);
			padding-bottom: 30px;
		}
	</style>
	<?php
}

add_action('login_enqueue_scripts', 'sunflower_login_logo');

function sunflower_fix_event_location()
{
    $transient = sprintf('sunflower_geocache_%s', $_POST['transient']);
    $lon = $_POST['lon'];
    $lat = $_POST['lat'];
    set_transient($transient, [$lon, $lat]);
}

add_action('wp_ajax_sunflower_fix_event_location', 'sunflower_fix_event_location');
