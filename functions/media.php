<?php
/**
 * Add creator field to media upload.
 *
 * @package Sunflower 26
 */

/**
 * Add custom creator field to media form.
 *
 * @param array   $form_fields An array of attachment form fields.
 * @param WP_Post $post The WP_Post attachment object.
 * @return array
 */
function sunflower_add_creator_field_to_media( $form_fields, $post ) {

	$creator = get_post_meta( $post->ID, '_media_creator', true );

	$sunflower_media_creator_settings = sunflower_get_setting( 'sunflower_media_creator' ) ? sunflower_get_setting( 'sunflower_media_creator' ) : 'optional';

	if ( 'disabled' === $sunflower_media_creator_settings ) {
		return $form_fields;
	}

	$sunflower_media_creator_required = '';
	if ( 'required' === $sunflower_media_creator_settings || 'strict' === $sunflower_media_creator_settings ) {
		$sunflower_media_creator_required = true;
	}

	// Define new field.
	$form_fields['media_creator'] = array(
		'label'    => __( 'Creator', 'sunflower' ),
		'input'    => 'html',
		'required' => $sunflower_media_creator_required ? 'required' : '',
		'html'     => '<textarea class="widefat" cols="160" name="attachments[' . $post->ID . '][media_creator]"' . ( $sunflower_media_creator_required ? 'required' : '' ) . '>' . esc_textarea( $creator ) . '</textarea>',
		'value'    => $creator ? $creator : '',
		'helps'    => __( 'Creator / Source of this media (may contain links)', 'sunflower' ),
	);

	return $form_fields;
}
add_filter( 'attachment_fields_to_edit', 'sunflower_add_creator_field_to_media', 10, 2 );

/**
 * Add the media_creator field to t he REST api
 */
function sunflower_rest_api_media_creator() {
	register_post_meta(
		'attachment',
		'_media_creator',
		array(
			'show_in_rest'  => true,
			'single'        => true,
			'type'          => 'string',
			'auth_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'sunflower_rest_api_media_creator' );

/**
 * Check field for requirements and save.
 *
 * @param array $post       An array of post data.
 * @param array $attachment An array of attachment form fields.
 */
function sunflower_save_creator_field_to_media( $post, $attachment ) {

	if ( isset( $attachment['media_creator'] ) ) {
		$sunflower_media_creator_settings = sunflower_get_setting( 'sunflower_media_creator' ) ? sunflower_get_setting( 'sunflower_media_creator' ) : 'optional';

		$sunflower_media_creator_required = '';
		if ( 'required' === $sunflower_media_creator_settings || 'strict' === $sunflower_media_creator_settings ) {
			$sunflower_media_creator_required = true;
		}
		$creator = ( $attachment['media_creator'] );

		// If creator field is empty and required --> do not save and show Error.
		if ( $sunflower_media_creator_required && empty( $creator ) ) {
			add_filter(
				'redirect_post_location',
				function ( $location ) {
					return add_query_arg(
						array(
							'media_creator_error' => 1,
							'_sunflower_nonce'    => wp_create_nonce( 'sunflower_media_notice' ),
						),
						$location
					);
				}
			);
		} else {
			update_post_meta( $post['ID'], '_media_creator', $creator );
		}
	}

	// If alt text is empty, show warning.
	if ( empty( $post['_wp_attachment_image_alt'] ) ) {

		add_filter(
			'redirect_post_location',
			function ( $location ) {
				return add_query_arg(
					array(
						'media_alt_warning' => 1,
						'_sunflower_nonce'  => wp_create_nonce( 'sunflower_media_notice' ),
					),
					$location
				);
			}
		);
	}

	return $post;
}
add_filter( 'attachment_fields_to_save', 'sunflower_save_creator_field_to_media', 10, 2 );

/**
 * Show some notice on issues with media for admins.
 */
function sunflower_media_field_admin_notice() {
	if (
		isset( $_GET['_sunflower_nonce'] ) &&
		wp_verify_nonce( $_GET['_sunflower_nonce'], 'sunflower_media_notice' )
	) {
		if ( isset( $_GET['media_creator_error'] ) ) {
			echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'The creator field is mandatory and must not be left empty.', 'sunflower' ) . '</p></div>';
		}
		if ( isset( $_GET['media_alt_warning'] ) ) {
			echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__( 'No alternative text provided. Please consider adding a description for better accessibility.', 'sunflower' ) . '</p></div>';
		}
	}
}
add_action( 'admin_notices', 'sunflower_media_field_admin_notice' );


/**
 * Add upload media related JavaScript.
 *
 * @param string $hook Hook suffix for the current admin page.
 */
function sunflower_enqueue_media_script( $hook ) {
	if ( 'upload.php' === $hook || 'post.php' === $hook || 'post-new.php' === $hook ) {
		wp_enqueue_script(
			'sunflower-media-js',
			get_template_directory_uri() . '/assets/js/media.js',
			array( 'jquery', 'wp-blocks', 'wp-element', 'wp-components' ),
			SUNFLOWER_VERSION,
			true
		);

		$sunflower_media_creator_settings = sunflower_get_setting( 'sunflower_media_creator' ) ? sunflower_get_setting( 'sunflower_media_creator' ) : 'optional';

		wp_localize_script(
			'sunflower-media-js',
			'sunflower',
			array(
				'options' => array(
					'mediaCreator' => $sunflower_media_creator_settings,
				),
				'texts'   => array(
					'creatorFieldEmpty' => esc_html__( 'The creator field is mandatory and must not be left empty.', 'sunflower' ),
					'emptyAltText'      => esc_html__( 'No alternative text provided. Please consider adding a description for better accessibility.', 'sunflower' ),
				),
			)
		);

	}
}
add_action( 'admin_enqueue_scripts', 'sunflower_enqueue_media_script' );

/**
 * Output creator field to image block.
 *
 * @param string $block_content The block content.
 * @param array  $block         The full block, including name and attributes.
 */
function sunflower_add_creator_to_image_block( $block_content, $block ) {

	if ( isset( $block['blockName'] ) && ( 'core/image' === $block['blockName'] || 'core/media-text' === $block['blockName'] ) ) {

		$sunflower_media_creator_settings = sunflower_get_setting( 'sunflower_media_creator' ) ? sunflower_get_setting( 'sunflower_media_creator' ) : 'optional';

		if ( 'disabled' === $sunflower_media_creator_settings ) {
			return $block_content;
		}

		$attachment_id = 0;
		if ( isset( $block['attrs']['id'] ) ) {
			$attachment_id = $block['attrs']['id'];
		} elseif ( isset( $block['attrs']['mediaId'] ) ) {
			$attachment_id = $block['attrs']['mediaId'];
		}

		// Get the attachement ID.
		if ( $attachment_id ) {
			$creator = get_post_meta( $attachment_id, '_media_creator', true );

			if ( ! empty( $creator ) ) {
				// Add the creator field as additional figcaption part.
				if ( strpos( $block_content, '<figcaption' ) !== false ) {
					// Extend figcaption.
					$block_content = preg_replace(
						'/(<figcaption[^>]*>)/',
						'$1' . wp_kses_post( $creator ) . ' | ',
						$block_content
					);
				} else {
					// No figcaption present yet: add it.
					$block_content = preg_replace(
						'/(<\/figure>)/',
						'<figcaption>' . wp_kses_post( $creator ) . '</figcaption>$1',
						$block_content
					);
				}
			} elseif ( 'strict' === $sunflower_media_creator_settings ) {
				// Find img tag and extend it.
				$block_content = preg_replace(
					'/(<img[^>]*class=")([^"]*)"/',
					'$1$2 no-creator"',
					$block_content
				);

				// Add class attribute if none is available.
				$block_content = preg_replace(
					'/(<img(?![^>]*class=))/',
					'<img class="no-creator" ',
					$block_content
				);

			}
		}
	}

	return $block_content;
}
add_filter( 'render_block', 'sunflower_add_creator_to_image_block', 10, 2 );
