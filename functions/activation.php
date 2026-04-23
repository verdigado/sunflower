<?php
/**
 * Jobs doing on activation of the sunflower theme.
 *
 * @package Sunflower 26
 */

/**
 * Whether the site title is empty or a generic WordPress placeholder.
 *
 * @param string $title Current site title.
 */
function sunflower_is_placeholder_site_title( $title ) {
	$normalized = strtolower( trim( (string) $title ) );

	return '' === $normalized || 'WordPress' === $normalized;
}

/**
 * Whether the tagline is empty or a default WordPress tagline (EN/de_DE).
 *
 * @param string $tagline Current tagline.
 */
function sunflower_is_placeholder_site_tagline( $tagline ) {
	$normalized = strtolower( trim( (string) $tagline ) );

	$placeholders = array(
		'',
		'just another wordpress site',
		'ein weiterer wordpress-blog',
		'ein weiterer wordpress blog',
	);

	return in_array( $normalized, $placeholders, true );
}

/**
 * Set default site title and tagline when still generic or empty (first setup).
 */
function sunflower_set_default_site_identity_if_placeholders() {
	if ( sunflower_is_placeholder_site_title( (string) get_option( 'blogname', '' ) ) ) {
		update_option( 'blogname', __( 'BÜNDNIS 90/DIE GRÜNEN', 'sunflower' ) );
	}
	if ( sunflower_is_placeholder_site_tagline( (string) get_option( 'blogdescription', '' ) ) ) {
		update_option( 'blogdescription', __( 'Stadt, Kreis oder Bundesland', 'sunflower' ) );
	}
}

/**
 * Set German as default site language on first activation.
 * Do not override deliberate non-default language choices.
 */
function sunflower_set_default_site_language_if_unset() {
	$current_language = get_option( 'WPLANG', '' );
	if ( '' === $current_language || 'en_US' === $current_language ) {
		update_option( 'WPLANG', 'de_DE' );
	}
}

/**
 * Set default sunflower options.
 */
function sunflower_set_default_options() {
	$options = get_option( 'sunflower_options' );

	if ( ! is_array( $options ) ) {
		$options = array();
	}

	$defaults = array(
		'sunflower_schema_org'         => 'checked',
		'sunflower_categories_archive' => 'main-categories',
		'sunflower_color_scheme'       => 'light',
		'sunflower_form_style'         => 'rounded',
		'sunflower_footer_layout'      => 'sand',
		'sunflower_post_image_format'  => 'modern',
		'excerpt_length'               => 15,
	);

	// Existierende Werte haben Vorrang, Defaults füllen nur Lücken.
	$options = wp_parse_args( $options, $defaults );

	update_option( 'sunflower_options', $options );

	// Ensure event options are persisted so the post type is registered
	// without requiring the user to save the settings form first.
	$events_options = get_option( 'sunflower_events_options' );
	if ( ! is_array( $events_options ) ) {
		$events_options = array();
	}
	$events_defaults = array(
		'sunflower_events_enabled'     => 1,
		'sunflower_show_event_archive' => 1,
		'sunflower_show_overall_map'   => 1,
	);
	$events_options  = wp_parse_args( $events_options, $events_defaults );
	update_option( 'sunflower_events_options', $events_options );

	$parent_theme_dir = get_template();
	$parent_theme     = wp_get_theme( $parent_theme_dir );
	$parent_version   = $parent_theme->get( 'Version' );
	update_option( 'sunflower_theme_version', $parent_version );
}

/**
 * Check if the theme is being activated for the first time.
 */
function sunflower_get_fresh_install() {

	$stored = get_option( 'sunflower_theme_version' );

	if ( false === $stored ) {
		return true;
	}
	return false;
}

/**
 * Decide what to do with demo content directly after theme activation.
 *
 * Always show the welcome page after activation, where users can either
 * install demo content or skip directly to first steps.
 */
function sunflower_schedule_welcome_or_skip(): void {

	if ( sunflower_get_fresh_install() ) {
		set_transient( 'sunflower_welcome_redirect', true, 5 * MINUTE_IN_SECONDS );
	} else {
		set_transient( 'sunflower_first_steps_redirect', true, 5 * MINUTE_IN_SECONDS );
	}
}


/**
 * Jobs run after activation of sunflower theme.
 */
function sunflower_activate_theme() {
	flush_rewrite_rules();
	sunflower_set_default_site_language_if_unset();
	sunflower_set_default_site_identity_if_placeholders();
	sunflower_schedule_welcome_or_skip();
	sunflower_set_default_options();
}

add_action( 'after_switch_theme', 'sunflower_activate_theme', 20, 2 );
