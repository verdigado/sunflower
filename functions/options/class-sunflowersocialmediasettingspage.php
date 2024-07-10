<?php
/**
 * Class for the Sunflower social media settings page.
 *
 * @package sunflower
 */

/**
 * The class itself.
 */
class SunflowerSocialMediaSettingsPage {

	/**
	 * Holds the values to be used in the fields callbacks
	 *
	 * @var $options
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct() {
		add_action( 'admin_menu', $this->sunflower_add_plugin_page( ... ) );
		add_action( 'admin_init', $this->sunflower_social_media_page_init( ... ) );
	}

	/**
	 * Add options page
	 */
	public function sunflower_add_plugin_page(): void {
		add_submenu_page(
			'sunflower_admin',
			__( 'Social Media', 'sunflower' ),
			__( 'Social Media', 'sunflower' ),
			'edit_pages',
			'sunflower_social_media',
			$this->create_sunflower_social_media_page( ... )
		);
	}

	/**
	 * Social Media Profiles page callback
	 */
	public function create_sunflower_social_media_page(): void {
		// Set class properties from options.
		$this->options = get_option( 'sunflower_social_media_options' );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Sunflower Settings', 'sunflower' ); ?></h1>
			<form method="post" action="options.php">
			<?php
				// This prints out all hidden setting fields.
				settings_fields( 'sunflower_social_media_option_group' );
			do_settings_sections( 'sunflower-setting-social-media-options' );
			submit_button();
			?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register and add social media settings
	 */
	public function sunflower_social_media_page_init(): void {
		register_setting(
			'sunflower_social_media_option_group',
			'sunflower_social_media_options',
			$this->sanitize( ... )
		);

		add_settings_section(
			'sunflower_social_media',
			__( 'Social Media Profiles', 'sunflower' ),
			$this->print_section_info( ... ),
			'sunflower-setting-social-media-options'
		);

		add_settings_field(
			'sunflower_social_media_profiles',
			__( 'Social Media Profiles', 'sunflower' ),
			$this->social_media_profiles_callback( ... ),
			'sunflower-setting-social-media-options',
			'sunflower_social_media'
		);

		add_settings_section(
			'sunflower_social_media_sharers',
			__( 'Social Sharers', 'sunflower' ),
			$this->print_section_info_sharers( ... ),
			'sunflower-setting-social-media-options'
		);

		add_settings_field(
			'sunflower_open_graph_fallback_image',
			__( 'Open Graph Fallback Image', 'sunflower' ),
			$this->sunflower_open_graph_fallback_image( ... ),
			'sunflower-setting-social-media-options',
			'sunflower_social_media_sharers'
		);

		add_settings_field(
			'sunflower_sharer_x_twitter',
			__( 'X (Twitter)', 'sunflower' ),
			$this->sunflower_checkbox_callback( ... ),
			'sunflower-setting-social-media-options',
			'sunflower_social_media_sharers',
			array( 'sunflower_sharer_x_twitter', __( 'X (Twitter)', 'sunflower' ) )
		);

		add_settings_field(
			'sunflower_sharer_facebook',
			__( 'Facebook', 'sunflower' ),
			$this->sunflower_checkbox_callback( ... ),
			'sunflower-setting-social-media-options',
			'sunflower_social_media_sharers',
			array( 'sunflower_sharer_facebook', __( 'Facebook', 'sunflower' ) )
		);

		add_settings_field(
			'sunflower_sharer_whatsapp',
			__( 'WhatsApp', 'sunflower' ),
			$this->sunflower_checkbox_callback( ... ),
			'sunflower-setting-social-media-options',
			'sunflower_social_media_sharers',
			array( 'sunflower_sharer_whatsapp', __( 'WhatsApp', 'sunflower' ) )
		);

		add_settings_field(
			'sunflower_sharer_mail',
			__( 'mail', 'sunflower' ),
			$this->sunflower_checkbox_callback( ... ),
			'sunflower-setting-social-media-options',
			'sunflower_social_media_sharers',
			array( 'sunflower_sharer_mail', __( 'Mail', 'sunflower' ) )
		);
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys.
	 */
	public function sanitize( $input ) {
		$new_input = array();

		// Sanitize everything element of the input array.
		foreach ( $input as $key => $value ) {
			if ( isset( $input[ $key ] ) ) {
				$new_input[ $key ] = sanitize_text_field( $value );
			}
		}

		// Sanitize special values.
		if ( isset( $input['sunflower_social_media_profiles'] ) ) {
			$new_input['sunflower_social_media_profiles'] = sanitize_textarea_field( $input['sunflower_social_media_profiles'] );
		}

		return $new_input;
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info() {
	}

	/**
	 * Show info text about share buttons
	 */
	public function print_section_info_sharers(): void {
		esc_html_e( 'Show share buttons on single post page', 'sunflower' );
	}

	/**
	 * Checkbox callback
	 *
	 * @param array $args The field arguments.
	 */
	public function sunflower_checkbox_callback( $args ): void {
		$field = $args[0];
		$label = $args[1];

		printf(
			'<label>
                    <input type="checkbox" id="%1$s" name="sunflower_social_media_options[%1$s]" value="checked" %2$s />
                    %3$s
                </label>',
			esc_attr( $field ),
			isset( $this->options[ $field ] ) ? 'checked' : '',
			esc_attr( $label )
		);
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function social_media_profiles_callback(): void {
		$default   = array();
		$default[] = 'fab fa-x-twitter;X (Twitter);';
		$default[] = 'fab fa-twitter;Twitter;';
		$default[] = 'fab fa-bluesky;Bluesky;';
		$default[] = 'fab fa-facebook-f;Facebook;';
		$default[] = 'fab fa-whatsapp;WhatsApp;';
		$default[] = 'fab fa-threads;Threads;';
		$default[] = 'fab fa-tiktok;TikTok;';
		$default[] = 'fab fa-linkedin;LinkedIn;';
		$default[] = 'fab fa-instagram;Instagram;';
		$default[] = 'fab fa-youtube;YouTube;';
		$default[] = 'fas fa-globe;Webseite;';
		$default[] = 'forkawesome fa-peertube;PeerTube;';
		$default[] = 'forkawesome fa-pixelfed;Pixelfed;';
		$default[] = 'fab fa-mastodon;Mastodon;';

		printf(
			'<textarea style="white-space: pre-wrap;width: 90%%;height:18em;" id="sunflower_social_media_profiles" name="sunflower_social_media_options[sunflower_social_media_profiles]">%s</textarea>',
			( isset( $this->options['sunflower_social_media_profiles'] ) && ! empty( $this->options['sunflower_social_media_profiles'] ) ) ? esc_attr( $this->options['sunflower_social_media_profiles'] ) : esc_attr( implode( "\n", $default ) )
		);
		echo '<div><a href="https://sunflower-theme.de/documentation/setup/#social-media-profile" target="_blank">Mehr zu den Einstellungen in der Dokumentation</a> und
        <a href="https://fontawesome.com/icons?d=gallery&p=2&m=free" target="_blank">alle möglichen Icons bei Fontawesome</a>.<br>
       Pro Zeile ein Social-Media-Profil<br>
       Format: Fontawesome-Klasse; Title-Attribut; URL<br>
       Wenn die URL fehlt, wird nichts verlinkt.

        </div>';
	}

	/**
	 * Field for open graph fallback image with media selection.
	 */
	public function sunflower_open_graph_fallback_image(): void {
		// Add media selector button.
		wp_enqueue_media();
		wp_enqueue_script(
			'sunflower-admin-media',
			get_template_directory_uri() . '/assets/js/admin-media.js',
			array( 'jquery', 'jquery-ui-tabs', 'media-upload' ),
			SUNFLOWER_VERSION,
			array( 'in_footer' => true )
		);
		wp_localize_script(
			'sunflower-admin-media',
			'texts',
			array(
				'select_image' => __( 'Select Open Graph Fallback Image', 'sunflower' ),
			)
		);

		printf(
			'<input type="text" id="sunflower_open_graph_fallback_image" name="sunflower_social_media_options[sunflower_open_graph_fallback_image]" size="%s" value="%s">',
			esc_attr( min( strlen( (string) $this->options['sunflower_open_graph_fallback_image'] ), 120 ) ),
			esc_attr( $this->options['sunflower_open_graph_fallback_image'] ?? '' )
		);

		printf(
			'<input type="button" id="sunflower_open_graph_fallback_image_button" class="button" value="%s"" />',
			esc_attr__( 'Open Media Library', 'sunflower' )
		);

		printf(
			'<p class="sunflower-help">%s</p>',
			esc_attr__( 'This fallback image will be used when sharing pages and posts and no featured image has been selected.', 'sunflower' )
		);
	}
}

if ( is_admin() ) {
	$sunflower_settings_page = new SunflowerSocialMediaSettingsPage();
}
