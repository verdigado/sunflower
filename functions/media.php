<?php
/**
 * Medien-Uploader um ein „Creator“-Feld erweitern
 */

// Feld im Medien-Uploader hinzufügen
function sunflower_add_creator_field_to_media( $form_fields, $post ) {
    // Aktuellen Wert abrufen
    $creator = get_post_meta( $post->ID, '_media_creator', true );

    // Neues Feld definieren
    $form_fields['media_creator'] = array(
        'label' => __( 'Creator', 'mytheme' ),
        'input' => 'html',
		'html'  => '<textarea class="widefat" name="attachments[' . $post->ID . '][media_creator]">' . esc_textarea( $creator ) . '</textarea>',
        'value' => $creator ? $creator : '',
        'helps' => __( 'Name des Erstellers dieses Mediums', 'mytheme' ),
    );

	$sunflower_media_creator      = sunflower_get_setting( 'sunflower_media_creator' );
	if ($sunflower_media_creator) {
		$form_fields['media_creator']['required'] = 'required';
	}

    return $form_fields;
}
add_filter( 'attachment_fields_to_edit', 'sunflower_add_creator_field_to_media', 10, 2 );

// Feld speichern und Pflicht prüfen
function sunflower_save_creator_field_to_media( $post, $attachment ) {
    if ( isset( $attachment['media_creator'] ) ) {
        $creator = ( $attachment['media_creator'] );

        // Wenn leer -> Fehler erzeugen
        if ( empty( $creator ) ) {
            add_filter( 'redirect_post_location', function( $location ) {
                return add_query_arg( 'media_creator_error', 1, $location );
            });
        } else {
            update_post_meta( $post['ID'], '_media_creator', $creator );
        }
    }

    return $post;
}
add_filter( 'attachment_fields_to_save', 'sunflower_save_creator_field_to_media', 10, 2 );

// Admin-Fehlermeldung anzeigen
function sunflower_creator_field_admin_notice() {
    if ( isset( $_GET['media_creator_error'] ) ) {
        echo '<div class="error"><p>' . esc_html__( 'Das Creator-Feld ist ein Pflichtfeld und darf nicht leer sein.', 'mytheme' ) . '</p></div>';
    }
}
add_action( 'admin_notices', 'sunflower_creator_field_admin_notice' );


// JavaScript im Medien-Uploader laden
function mytheme_enqueue_media_creator_script( $hook ) {
    if ( 'upload.php' === $hook || 'post.php' === $hook || 'post-new.php' === $hook ) {
        wp_enqueue_script( 'mytheme-media-creator-js', get_stylesheet_directory_uri() . '/assets/js/media.js', array( 'jquery' ), null, true );
    }
}
add_action( 'admin_enqueue_scripts', 'mytheme_enqueue_media_creator_script' );
