<?php
/**
 * Contact details widget with Font Awesome icons.
 *
 * @package Sunflower 26
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
				'description'           => __( 'Zeigt Kontaktdaten mit Icons an (Adresse, Telefon, E-Mail, Öffnungszeiten).', 'sunflower' ),
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

		echo wp_kses_post( $args['before_widget'] );

		if ( ! empty( $instance['title'] ) ) {
			echo wp_kses_post( $args['before_title'] ) . esc_html( $instance['title'] ) . wp_kses_post( $args['after_title'] );
		}

		$fields = array(
			'address'       => array(
				'icon'      => 'fa-solid fa-location-dot',
				'class'     => 'contact-address',
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
				'icon'      => 'fa-solid fa-clock',
				'class'     => 'contact-hours',
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

		echo wp_kses_post( $args['after_widget'] );
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
