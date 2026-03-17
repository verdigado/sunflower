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
			'name'          => __( 'Footer Mitte', 'sunflower' ),
			'id'            => 'footer-center',
			'description'   => __( 'Widget-Bereich in der Footer-Mitte (z.B. Kontaktdaten).', 'sunflower' ),
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
			'description'   => __( 'Widget-Bereich rechts im Footer (z.B. Suche).', 'sunflower' ),
			'before_widget' => '<div id="%1$s" class="widget footer-widget footer-widget--right %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Header (nach Logo)', 'sunflower' ),
			'id'            => 'header-after-brand',
			'description'   => __( 'Widget-Bereich im Header nach dem Brand-Link (z.B. Suche).', 'sunflower' ),
			'before_widget' => '<div id="%1$s" class="widget header-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '',
			'after_title'   => '',
		)
	);

	register_widget( 'Sunflower_Contact_Widget' );

	$remove = array(
		'WP_Widget_Pages',
		'WP_Widget_Calendar',
		'WP_Widget_Archives',
		'WP_Widget_Links',
		'WP_Widget_Meta',
		'WP_Widget_Text',
		'WP_Widget_Categories',
		'WP_Widget_Recent_Posts',
		'WP_Widget_Recent_Comments',
		'WP_Widget_RSS',
		'WP_Widget_Tag_Cloud',
		'WP_Nav_Menu_Widget',
		'WP_Widget_Custom_HTML',
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


/**
 * Contact details widget with Font Awesome icons.
 */
class Sunflower_Contact_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'sunflower_contact',
			__( 'Kontaktdaten', 'sunflower' ),
			array(
				'description'          => __( 'Zeigt Kontaktdaten mit Icons an (Adresse, Telefon, E-Mail, Öffnungszeiten).', 'sunflower' ),
				'show_instance_in_rest' => true,
			)
		);
	}

	/**
	 * Front-end output.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values.
	 */
	public function widget( $args, $instance ) {
		$args = wp_parse_args(
			$args,
			array(
				'before_widget' => '',
				'after_widget'  => '',
				'before_title'  => '<h4 class="widget-title">',
				'after_title'   => '</h4>',
			)
		);

		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . esc_html( $instance['title'] ) . $args['after_title'];
		}

		$fields = array(
			'address'       => array(
				'icon'    => 'fa-solid fa-location-dot',
				'class'   => 'contact-address',
				'multiline' => true,
			),
			'phone'         => array(
				'icon'  => 'fa-solid fa-phone',
				'class' => 'contact-phone',
				'link'  => 'tel',
			),
			'email'         => array(
				'icon'  => 'fa-solid fa-envelope',
				'class' => 'contact-email',
				'link'  => 'mailto',
			),
			'opening_hours' => array(
				'icon'    => 'fa-solid fa-clock',
				'class'   => 'contact-hours',
				'multiline' => true,
			),
		);

		echo '<ul class="sunflower-contact-list">';

		foreach ( $fields as $key => $field ) {
			$value = $instance[ $key ] ?? '';
			if ( empty( $value ) ) {
				continue;
			}

			echo '<li class="' . esc_attr( $field['class'] ) . '">';
			echo '<i class="' . esc_attr( $field['icon'] ) . '"></i>';
			echo '<span>';

			if ( ! empty( $field['link'] ) && 'tel' === $field['link'] ) {
				$tel = preg_replace( '/[^\d+]/', '', $value );
				printf( '<a href="tel:%s">%s</a>', esc_attr( $tel ), esc_html( $value ) );
			} elseif ( ! empty( $field['link'] ) && 'mailto' === $field['link'] ) {
				printf( '<a href="mailto:%s">%s</a>', esc_attr( $value ), esc_html( $value ) );
			} elseif ( ! empty( $field['multiline'] ) ) {
				echo nl2br( esc_html( $value ) );
			} else {
				echo esc_html( $value );
			}

			echo '</span>';
			echo '</li>';
		}

		echo '</ul>';

		echo $args['after_widget'];
	}

	/**
	 * Admin form.
	 *
	 * @param array $instance Previously saved values.
	 */
	public function form( $instance ) {
		$fields = array(
			'title'         => array(
				'label' => __( 'Titel', 'sunflower' ),
				'type'  => 'text',
			),
			'address'       => array(
				'label' => __( 'Adresse', 'sunflower' ),
				'type'  => 'textarea',
			),
			'phone'         => array(
				'label' => __( 'Telefon', 'sunflower' ),
				'type'  => 'text',
			),
			'email'         => array(
				'label' => __( 'E-Mail', 'sunflower' ),
				'type'  => 'text',
			),
			'opening_hours' => array(
				'label' => __( 'Öffnungszeiten', 'sunflower' ),
				'type'  => 'textarea',
			),
		);

		foreach ( $fields as $key => $field ) {
			$value = $instance[ $key ] ?? '';
			$id    = $this->get_field_id( $key );
			$name  = $this->get_field_name( $key );

			echo '<p>';
			printf( '<label for="%s">%s</label>', esc_attr( $id ), esc_html( $field['label'] ) );

			if ( 'textarea' === $field['type'] ) {
				printf(
					'<textarea class="widefat" id="%s" name="%s" rows="3">%s</textarea>',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_textarea( $value )
				);
			} else {
				printf(
					'<input class="widefat" id="%s" name="%s" type="text" value="%s">',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( $value )
				);
			}

			echo '</p>';
		}
	}

	/**
	 * Sanitize and save widget values.
	 *
	 * @param array $new_instance New values.
	 * @param array $old_instance Old values.
	 * @return array Sanitized values.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title']         = sanitize_text_field( $new_instance['title'] ?? '' );
		$instance['address']       = sanitize_textarea_field( $new_instance['address'] ?? '' );
		$instance['phone']         = sanitize_text_field( $new_instance['phone'] ?? '' );
		$instance['email']         = sanitize_email( $new_instance['email'] ?? '' );
		$instance['opening_hours'] = sanitize_textarea_field( $new_instance['opening_hours'] ?? '' );

		return $instance;
	}
}
