<?php
/**
 * Class for the Sunflower settings page.
 *
 * @package sunflower
 */

/**
 * The class itself.
 */
class SunflowerSettingsPage {

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
		// Set class properties from options.
		$this->options = get_option( 'sunflower_options' );

		if ( ! is_array( $this->options ) ) {
			$this->options = array();
		}

		// Set default values.
		$this->options['sunflower_schema_org']         = $this->options['sunflower_schema_org'] ?? 'checked';
		$this->options['sunflower_categories_archive'] = $this->options['sunflower_categories_archive'] ?? 'main-categories';

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Sunflower Settings', 'sunflower' ); ?></h1>
			<form method="post" action="options.php">
			<?php
				// This prints out all hidden setting fields.
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
						if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'sunflower_options-flushpermalinks' ) && isset( $_GET['flush_permalinks'] ) ) {
							flush_rewrite_rules();
							?>
							Die Permalinkstruktur wurde neu eingelesen.
								<?php } else { ?>
							<p>Falls der Link für die Termineseite fehlerhaft ist, kannst Du die Permalinkstruktur neu einlesen. </p><br>
							<?php
								$sunflower_flushpermalinks_url = wp_nonce_url( 'admin.php?page=sunflower_settings&flush_permalinks=1', 'sunflower_options-flushpermalinks' );
								printf( '<a href="%s" class="button button-primary">%s</a>', esc_html( $sunflower_flushpermalinks_url ), esc_attr__( 'Reimport permalink structure', 'sunflower' ) );
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
			'sunflower_option_group',
			'sunflower_options',
			$this->sanitize( ... )
		);

		add_settings_section(
			'sunflower_layout',
			__( 'Layout', 'sunflower' ),
			$this->print_section_info( ... ),
			'sunflower-setting-admin'
		);

		add_settings_field(
			'excerps_length',
			__( 'Excerpt length (words)', 'sunflower' ),
			$this->excerpt_length_callback( ... ),
			'sunflower-setting-admin',
			'sunflower_layout'
		);

		add_settings_field(
			'sunflower_show_related_posts',
			__( 'show related posts', 'sunflower' ),
			$this->sunflower_checkbox_callback( ... ),
			'sunflower-setting-admin',
			'sunflower_layout',
			array(
				'field' => 'sunflower_show_related_posts',
				'label' => __( 'show related posts', 'sunflower' ),
			)
		);

		add_settings_field(
			'sunflower_show_author',
			__( 'show author of posts', 'sunflower' ),
			$this->sunflower_checkbox_callback( ... ),
			'sunflower-setting-admin',
			'sunflower_layout',
			array(
				'field' => 'sunflower_show_author',
				'label' => __( 'Show post author on post details and via REST api.', 'sunflower' ),
			)
		);

		add_settings_field(
			'sunflower_hide_prev_next',
			__( 'hide previous and next links', 'sunflower' ),
			$this->sunflower_checkbox_callback( ... ),
			'sunflower-setting-admin',
			'sunflower_layout',
			array(
				'field' => 'sunflower_hide_prev_next',
				'label' => __( 'hide previous and next links', 'sunflower' ),
			)
		);

		add_settings_field(
			'sunflower_contact_form_to',
			__( 'to-field for contact-forms', 'sunflower' ),
			$this->sunflower_contact_form_to( ... ),
			'sunflower-setting-admin',
			'sunflower_layout',
			array( 'sunflower_contact_form_to', __( 'to-field for contact-forms', 'sunflower' ) )
		);

		add_settings_field(
			'sunflower_main_menu_item_is_placeholder',
			__( 'items in menu', 'sunflower' ),
			$this->sunflower_checkbox_callback( ... ),
			'sunflower-setting-admin',
			'sunflower_layout',
			array(
				'field' => 'sunflower_main_menu_item_is_placeholder',
				'label' => __( 'items with href=# in the main menu are placeholders for submenu', 'sunflower' ),
			)
		);

		add_settings_field(
			'sunflower_header_layout',
			__( 'Use this header layout', 'sunflower' ),
			$this->sunflower_header_layout( ... ),
			'sunflower-setting-admin',
			'sunflower_layout',
			array( 'sunflower_header_layout', __( 'Use this header layout', 'sunflower' ) )
		);

		add_settings_field(
			'sunflower_header_social_media',
			__( 'Show social media icons in header', 'sunflower' ),
			$this->sunflower_header_social_media( ... ),
			'sunflower-setting-admin',
			'sunflower_layout',
			array( 'sunflower_header_social_media', __( 'Show social media icons in header', 'sunflower' ) )
		);

		add_settings_field(
			'sunflower_categories_archive',
			__( 'Show list of categories on category archive', 'sunflower' ),
			$this->sunflower_categories_archive( ... ),
			'sunflower-setting-admin',
			'sunflower_layout'
		);

		add_settings_field(
			'sunflower_schema_org',
			__( 'Enhance SEO', 'sunflower' ),
			$this->sunflower_checkbox_callback( ... ),
			'sunflower-setting-admin',
			'sunflower_layout',
			array(
				'field' => 'sunflower_schema_org',
				'label' => __( 'Set website name in page metadata', 'sunflower' ),
			)
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
		if ( isset( $input['excerpt_length'] ) ) {
			$new_input['excerpt_length'] = absint( $input['excerpt_length'] ) ?? '';
		}

		return $new_input;
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info() {
	}

	/**
	 * Checkbox callback
	 *
	 * @param array $args The field arguments.
	 */
	public function sunflower_checkbox_callback( $args ): void {
		$field = $args['field'];
		$label = $args['label'];

		printf(
			'<label>
                    <input type="checkbox" id="%1$s" name="sunflower_options[%1$s]" value="checked" %2$s />
                    %3$s
                </label>',
			esc_attr( $field ),
			isset( $this->options[ $field ] ) ? 'checked' : '',
			esc_attr( $label )
		);
	}

	/**
	 * Excerpt length field
	 */
	public function excerpt_length_callback(): void {
		printf(
			'<input type="text" id="excerpt_length" name="sunflower_options[excerpt_length]" value="%s" />',
			isset( $this->options['excerpt_length'] ) ? esc_attr( $this->options['excerpt_length'] ) : ''
		);
	}

	/**
	 * Contact form field "to"
	 */
	public function sunflower_contact_form_to(): void {
		printf(
			'<input type="email" id="sunflower_contact_form_to" name="sunflower_options[sunflower_contact_form_to]" value="%s" />',
			isset( $this->options['sunflower_contact_form_to'] ) ? esc_attr( $this->options['sunflower_contact_form_to'] ) : ''
		);
	}

	/**
	 * Header layout variant field
	 */
	public function sunflower_header_layout(): void {
		echo '<select id="sunflower_header_layout" name="sunflower_options[sunflower_header_layout]">';

		$options = array(
			array( 'standard', __( 'Standard', 'sunflower' ) ),
			array( 'personal', __( 'Personal', 'sunflower' ) ),
		);
		foreach ( $options as $option ) {
			$selected = ( isset( $this->options['sunflower_header_layout'] ) && $this->options['sunflower_header_layout'] === $option[0] ) ? 'selected' : '';
			printf(
				'<option value="%1$s" %2$s>%3$s</option>',
				esc_attr( $option[0] ),
				esc_attr( $selected ),
				esc_attr( $option[1] )
			);
		}

		echo '</select>';
	}

	/**
	 * Header layout variant field
	 */
	public function sunflower_categories_archive(): void {
		echo '<select id="sunflower_categories_archive" name="sunflower_options[sunflower_categories_archive]">';

		$options = array(
			array( 'no', __( 'do not show', 'sunflower' ) ),
			array( 'main-categories', __( 'main categories', 'sunflower' ) ),
			array( 'only-subcategories', __( 'only sub-categories', 'sunflower' ) ),
		);
		foreach ( $options as $option ) {
			$selected = ( isset( $this->options['sunflower_categories_archive'] ) && $this->options['sunflower_categories_archive'] === $option[0] ) ? 'selected' : '';
			printf(
				'<option value="%1$s" %2$s>%3$s</option>',
				esc_attr( $option[0] ),
				esc_attr( $selected ),
				esc_attr( $option[1] )
			);
		}

		echo '</select>';
	}

	/**
	 * Show social media icons in header, too.
	 *
	 * @param Array $args The forms argument.
	 */
	public function sunflower_header_social_media( $args ): void {
		$field = $args[0];
		$label = $args[1];

		printf(
			'<label>
                    <input type="checkbox" id="%1$s" name="sunflower_options[%1$s]" value="checked" %2$s />
                    %3$s
                </label>',
			esc_attr( $field ),
			isset( $this->options[ $field ] ) ? 'checked' : '',
			esc_attr( $label )
		);
	}
}

if ( is_admin() ) {
	$sunflower_settings_page = new SunflowerSettingsPage();
}
