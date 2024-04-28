<?php
class SunflowerSettingsPage {

	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct() {
		add_action( 'admin_menu', $this->sunflower_add_plugin_page( ... ) );
		add_action( 'admin_init', $this->sunflower_settings_page_init( ... ) );
	}

	/**
	 * Add options page
	 */
	public function sunflower_add_plugin_page(): void {
		add_submenu_page(
			'sunflower_admin',
			__( 'Settings', 'sunflower' ),
			__( 'Settings', 'sunflower' ),
			'edit_pages',
			'sunflower_settings',
			$this->create_sunflower_settings_page( ... )
		);
	}

	/**
	 * Options page callback
	 */
	public function create_sunflower_settings_page(): void {
		// Set class property
		$this->options = get_option( 'sunflower_options' );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Sunflower Settings', 'sunflower' ); ?></h1>
			<form method="post" action="options.php">
			<?php
				// This prints out all hidden setting fields
				settings_fields( 'sunflower_option_group' );
			do_settings_sections( 'sunflower-setting-admin' );
			submit_button();
			?>
			</form>

			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">Permalinks</th>
						<td>

						<?php
						if ( isset( $_GET['flush_permalinks'] ) ) {
							flush_rewrite_rules();
							?>
							Die Permalinkstruktur wurde neu eingelesen.
								<?php } else { ?>
							<p>Falls der Link für die Termineseite fehlerhaft ist, kannst Du die Permalinkstruktur neu einlesen. </p><br>
							<a href="admin.php?page=sunflower_settings&flush_permalinks=1" class="button button-primary">Permalinkstruktur neu einlesen </a>
							<?php
								}
								?>

						</td>
					</tr>
				</tbody>
			</table>

		</div>
		<?php
	}

	/**
	 * Register and add settings
	 */
	public function sunflower_settings_page_init(): void {
		register_setting(
			'sunflower_option_group', // Option group
			'sunflower_options', // Option name
			$this->sanitize( ... ) // Sanitize
		);

		add_settings_section(
			'sunflower_layout', // ID
			__( 'Layout', 'sunflower' ), // Title
			$this->print_section_info( ... ), // Callback
			'sunflower-setting-admin' // Page
		);

		add_settings_field(
			'excerps_length', // ID
			__( 'Excerpt length (words)', 'sunflower' ), // Title
			$this->excerpt_length_callback( ... ), // Callback
			'sunflower-setting-admin', // Page
			'sunflower_layout' // Section
		);

		add_settings_field(
			'sunflower_show_related_posts', // ID
			__( 'show related posts', 'sunflower' ), // Title
			$this->sunflower_checkbox_callback( ... ), // Callback
			'sunflower-setting-admin', // Page
			'sunflower_layout', // Section
			array( 'sunflower_show_related_posts', __( 'show related posts', 'sunflower' ) )
		);

		add_settings_field(
			'sunflower_show_author', // ID
			__( 'show author of posts', 'sunflower' ), // Title
			$this->sunflower_checkbox_callback( ... ), // Callback
			'sunflower-setting-admin', // Page
			'sunflower_layout', // Section
			array( 'sunflower_show_author', __( 'show author of posts', 'sunflower' ) )
		);

		add_settings_field(
			'sunflower_hide_prev_next', // ID
			__( 'hide previous and next links', 'sunflower' ), // Title
			$this->sunflower_checkbox_callback( ... ), // Callback
			'sunflower-setting-admin', // Page
			'sunflower_layout', // Section
			array( 'sunflower_hide_prev_next', __( 'hide previous and next links', 'sunflower' ) )
		);

		add_settings_field(
			'sunflower_contact_form_to', // ID
			__( 'to-field for contact-forms', 'sunflower' ), // Title
			$this->sunflower_contact_form_to( ... ), // Callback
			'sunflower-setting-admin', // Page
			'sunflower_layout', // Section
			array( 'sunflower_contact_form_to', __( 'to-field for contact-forms', 'sunflower' ) )
		);

		add_settings_field(
			'sunflower_main_menu_item_is_placeholder', // ID
			__( 'items in menu', 'sunflower' ), // Title
			$this->sunflower_checkbox_callback( ... ), // Callback
			'sunflower-setting-admin', // Page
			'sunflower_layout', // Section
			array( 'sunflower_main_menu_item_is_placeholder', __( 'items with href=# in the main menu are placeholders for submenu', 'sunflower' ) )
		);

		add_settings_field(
			'sunflower_header_layout', // ID
			__( 'Use this header layout', 'sunflower' ), // Title
			$this->sunflower_header_layout( ... ), // Callback
			'sunflower-setting-admin', // Page
			'sunflower_layout', // Section
			array( 'sunflower_header_layout', __( 'Use this header layout', 'sunflower' ) )
		);

		add_settings_field(
			'sunflower_header_social_media', // ID
			__( 'Show social media icons in header', 'sunflower' ), // Title
			$this->sunflower_header_social_media( ... ), // Callback
			'sunflower-setting-admin', // Page
			'sunflower_layout', // Section
			array( 'sunflower_header_social_media', __( 'Show social media icons in header', 'sunflower' ) )
		);
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function sanitize( $input ) {
		$new_input = array();

		// Sanitize everything
		foreach ( $input as $key => $value ) {
			if ( isset( $input[ $key ] ) ) {
				$new_input[ $key ] = sanitize_text_field( $value );
			}
		}

		// Sanitize special values
		if ( isset( $input['excerpt_length'] ) ) {
			$new_input['excerpt_length'] = absint( $input['excerpt_length'] ) ?: '';
		}

		return $new_input;
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info() {
	}

	public function sunflower_checkbox_callback( $args ): void {
		$field = $args[0];
		$label = $args[1];

		printf(
			'<label>
                    <input type="checkbox" id="%1$s" name="sunflower_options[%1$s]" value="checked" %2$s />
                    %3$s
                </label>',
			$field,
			isset( $this->options[ $field ] ) ? 'checked' : '',
			$label
		);
	}

	public function excerpt_length_callback(): void {
		printf(
			'<input type="text" id="excerpt_length" name="sunflower_options[excerpt_length]" value="%s" />',
			isset( $this->options['excerpt_length'] ) ? esc_attr( $this->options['excerpt_length'] ) : ''
		);
	}

	public function sunflower_contact_form_to(): void {
		printf(
			'<input type="email" id="sunflower_contact_form_to" name="sunflower_options[sunflower_contact_form_to]" value="%s" />',
			isset( $this->options['sunflower_contact_form_to'] ) ? esc_attr( $this->options['sunflower_contact_form_to'] ) : ''
		);
	}

	public function sunflower_header_layout(): void {
		echo '<select id="sunflower_header_layout" name="sunflower_options[sunflower_header_layout]">';

		$options = array( 'standard', 'personal' );
		foreach ( $options as $option ) {
			$selected = ( isset( $this->options['sunflower_header_layout'] ) && $this->options['sunflower_header_layout'] == $option ) ? 'selected' : '';
			printf(
				'<option value="%1$s" %2$s>%1$s</option>',
				$option,
				$selected
			);
		}

		echo '</select>';
	}

	/**
	 * Show social media icons in header, too.
	 *
	 * @params Array $args The forms argument.
	 */
	public function sunflower_header_social_media( $args ): void {
		$field = $args[0];
		$label = $args[1];

		printf(
			'<label>
                    <input type="checkbox" id="%1$s" name="sunflower_options[%1$s]" value="checked" %2$s />
                    %3$s
                </label>',
			$field,
			isset( $this->options[ $field ] ) ? 'checked' : '',
			$label
		);
	}
}

if ( is_admin() ) {
	$my_settings_page = new SunflowerSettingsPage();
}

function get_sunflower_setting( $option ) {
	$options = get_option( 'sunflower_options' );

	if ( ! is_array( $options ) ) {
		$options = array();
	}

	$sunflower_social_media_options = get_option( 'sunflower_social_media_options' );
	if ( is_array( $sunflower_social_media_options ) ) {
		$options = array_merge( $options, $sunflower_social_media_options );
	}

	$sunflower_events_options = get_option( 'sunflower_events_options' );
	if ( is_array( $sunflower_events_options ) ) {
		$options = array_merge( $options, $sunflower_events_options );
	}

	if ( ! isset( $options[ $option ] ) ) {
		return false;
	}

	if ( empty( $options[ $option ] ) ) {
		return false;
	}

	return $options[ $option ];
}
