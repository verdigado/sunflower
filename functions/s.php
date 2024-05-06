<?php
/**
 * Sunflower functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package sunflower
 */

if ( ! defined( 'SUNFLOWER_VERSION' ) ) {
	$sunflower_theme_data    = wp_get_theme( get_option( 'template' ) );
	$sunflower_theme_version = $sunflower_theme_data->version;
	define( 'SUNFLOWER_VERSION', $sunflower_theme_version );
}

if ( ! function_exists( 'sunflower_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function sunflower_setup() {
		/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on sunflower, use a find and replace
		* to change 'sunflower' to the name of your theme in all the template files.
		*/
		load_theme_textdomain( 'sunflower', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
		add_theme_support( 'title-tag' );

		/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'mainmenu' => esc_html__( 'Mainmenu', 'sunflower' ),
				'topmenu'  => esc_html__( 'Topmenu', 'sunflower' ),
				'footer1'  => esc_html__( 'Footer Menu 1', 'sunflower' ),
				'footer2'  => esc_html__( 'Footer Menu 2', 'sunflower' ),
			)
		);

		/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
		add_theme_support(
			'html5',
			array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		add_theme_support( 'align-wide' );
		add_theme_support( 'editor-styles' );
		add_editor_style( '/assets/css/editor-style.css' );
		add_editor_style( '/assets/css/admin-fontawesome.css' );
	}
endif;

add_action( 'after_setup_theme', 'sunflower_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function sunflower_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'sunflower_content_width', 640 );
}

add_action( 'after_setup_theme', 'sunflower_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function sunflower_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'sunflower' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'sunflower' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}

add_action( 'widgets_init', 'sunflower_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function sunflower_scripts() {
	wp_enqueue_style( 'sunflower-style', get_template_directory_uri() . '/style.css', array(), SUNFLOWER_VERSION );
	wp_style_add_data( 'sunflower-style', 'rtl', 'replace' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	wp_enqueue_script(
		'popper',
		get_template_directory_uri() . '/assets/vndr/@popperjs/core/dist/umd/popper.min.js',
		array(),
		SUNFLOWER_VERSION,
		true
	);

	wp_enqueue_script(
		'bootstrap',
		get_template_directory_uri() . '/assets/vndr/bootstrap/dist/js/bootstrap.min.js',
		array( 'jquery' ),
		SUNFLOWER_VERSION,
		true
	);

	wp_enqueue_script(
		'frontend',
		get_template_directory_uri() . '/assets/js/frontend.js',
		null,
		SUNFLOWER_VERSION,
		true
	);
	wp_localize_script(
		'sunflower-admin-media',
		'texts',
		array(
			'select_image' => __( 'Select Open Graph Fallback Image', 'sunflower' ),
		)
	);

	wp_localize_script(
		'frontend',
		'sunflower',
		array(
			'ajaxurl'            => admin_url( 'admin-ajax.php' ),
			'privacy_policy_url' => get_privacy_policy_url(),
			'theme_url'          => get_template_directory_uri(),
			'maps_marker'        => sunflower_parent_or_child( 'assets/img/marker.png' ),
			'texts'              => array(
				'readmore' => __( 'Continue reading', 'sunflower' ),
			),
		)
	);

	if ( 'sunflower_event' === get_post_type() ) {
		wp_enqueue_script(
			'sunflower-leaflet',
			get_template_directory_uri() . '/assets/vndr/leaflet/dist/leaflet.js',
			null,
			SUNFLOWER_VERSION,
			true
		);

		wp_enqueue_style(
			'sunflower-leaflet',
			get_template_directory_uri() . '/assets/vndr/leaflet/dist/leaflet.css',
			array(),
			SUNFLOWER_VERSION
		);
	}

	wp_enqueue_style( 'lightbox', get_template_directory_uri() . '/assets/vndr/lightbox2/dist/css/lightbox.min.css', array(), '4.3.0' );
	wp_enqueue_script( 'lightbox', get_template_directory_uri() . '/assets/vndr/lightbox2/dist/js/lightbox.min.js', array( 'jquery' ), '3.3.0', true );
	wp_enqueue_script( 'masonry', get_template_directory_uri() . '/assets/vndr/masonry-layout/dist/masonry.pkgd.min.js', array( 'masorny' ), '4.2.2', true );
}

add_action( 'wp_enqueue_scripts', 'sunflower_scripts' );

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	include get_template_directory() . '/inc/jetpack.php';
}

/**
 * Register Custom Navigation Walker
 */
function sunflower_register_navwalker() {
	include_once get_template_directory() . '/inc/class-wp-bootstrap-navwalker.php';
}

add_action( 'after_setup_theme', 'sunflower_register_navwalker' );

/**
 * Return constant if defined
 *
 * @param string $constant The constant to check.
 */
function sunflower_get_constant( $constant ) {
	if ( ! defined( $constant ) ) {
		return false;
	}

	return constant( $constant );
}

add_filter(
	'wp_kses_allowed_html',
	static function ( $allowed_tags, $context ) { // phpcs:ignore
		$allowed_tags['form']     = array(
			'action' => 1,
			'method' => 1,
			'id'     => 1,
			'class'  => 1,
		);
		$allowed_tags['input']    = array(
			'id'        => 1,
			'class'     => 1,
			'name'      => 1,
			'type'      => 1,
			'value'     => 1,
			'size'      => 1,
			'maxlength' => 1,
			'required'  => 1,
		);
		$allowed_tags['textarea'] = array(
			'action'    => 1,
			'method'    => 1,
			'id'        => 1,
			'class'     => 1,
			'name'      => 1,
			'cols'      => 1,
			'rows'      => 1,
			'maxlength' => 1,
			'required'  => 1,
		);
		return $allowed_tags;
	},
	10,
	2
);
