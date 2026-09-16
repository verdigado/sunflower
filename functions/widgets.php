<?php
/**
 * Widget areas and custom widgets for Sunflower 26.
 *
 * @package Sunflower 26
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register widget areas and custom widgets.
 */
function sunflower_widgets_init() {

	register_sidebar(
		array(
			'name'          => __( 'Header (Nur Suche!)', 'sunflower' ),
			'id'            => 'header-after-brand',
			'description'   => __( 'Achtung! Ausschließlich für das Such-Widget!', 'sunflower' ),
			'before_widget' => '<div id="%1$s" class="widget header-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '',
			'after_title'   => '',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Footer Links', 'sunflower' ),
			'id'            => 'footer-left',
			'description'   => __( 'Bereich links im Footer für Widgets', 'sunflower' ),
			'before_widget' => '<div id="%1$s" class="widget footer-widget footer-widget--left %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Footer Mitte', 'sunflower' ),
			'id'            => 'footer-center',
			'description'   => __( 'Bereich in der Footer-Mitte für Widgets', 'sunflower' ),
			'before_widget' => '<div id="%1$s" class="widget footer-widget footer-widget--center %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Footer Rechts', 'sunflower' ),
			'id'            => 'footer-right',
			'description'   => __( 'Bereich rechts im Footer für Widgets', 'sunflower' ),
			'before_widget' => '<div id="%1$s" class="widget footer-widget footer-widget--right %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		)
	);

	register_widget( 'Sunflower_Contact_Widget' );

	$remove = array(
		'WP_Widget_Pages',
		'WP_Widget_Calendar',
		'WP_Widget_Archives',
		'WP_Widget_Links',
		'WP_Widget_Meta',
		'WP_Widget_Categories',
		'WP_Widget_Recent_Comments',
		'WP_Widget_RSS',
		'WP_Widget_Tag_Cloud',
		'WP_Nav_Menu_Widget',
		'WP_Widget_Media_Audio',
		'WP_Widget_Media_Image',
		'WP_Widget_Media_Gallery',
		'WP_Widget_Media_Video',
		'WP_Widget_Block',
	);

	foreach ( $remove as $widget_class ) {
		unregister_widget( $widget_class );
	}
}
add_action( 'widgets_init', 'sunflower_widgets_init' );

add_filter( 'use_widgets_block_editor', '__return_false' );

// Text-Widgets führen Shortcodes von sich aus nicht aus.
add_filter( 'widget_text_content', 'do_shortcode', 11 );
add_filter( 'widget_text', 'do_shortcode', 11 );

/**
 * Use the search widget title as the search field placeholder instead of
 * displaying it as a heading. The title is stored in a global that
 * searchform.php picks up.
 *
 * @param string $title    The widget title.
 * @param array  $instance The widget instance settings.
 * @param string $id_base  The widget ID base.
 * @return string Empty string to suppress the heading output.
 */
function sunflower_search_title_to_placeholder( $title, $instance = array(), $id_base = '' ) {
	if ( 'search' === $id_base && ! empty( $title ) ) {
		global $sunflower_search_placeholder;
		$sunflower_search_placeholder = $title;
		return '';
	}
	return $title;
}
add_filter( 'widget_title', 'sunflower_search_title_to_placeholder', 10, 3 );
