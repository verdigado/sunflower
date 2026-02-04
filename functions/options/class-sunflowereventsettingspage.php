<?php
/**
 * Class for the Sunflower events settings page.
 *
 * @package Sunflower 26
 */

/**
 * The class itself.
 */
class SunflowerEventSettingsPage {

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
		add_action( 'admin_init', $this->sunflower_event_page_init( ... ) );
	}

	/**
	 * Add options page
	 */
	public function sunflower_add_plugin_page(): void {
		add_submenu_page(
			'sunflower_admin',
			__( 'Events', 'sunflower' ),
			__( 'Events', 'sunflower' ),
			'edit_pages',
			'sunflower_events_options',
			$this->create_sunflower_events_options_page( ... )
		);
	}

	/**
	 * Events Options page callback
	 */
	public function create_sunflower_events_options_page(): void {
		// Set class properties from options.
		$this->options = get_option( 'sunflower_events_options' );
		?>
		<div class="wrap">
			<h1><?php esc_attr_e( 'Sunflower Settings', 'sunflower' ); ?></h1>
			<form method="post" action="options.php">
			<?php
				// This prints out all hidden setting fields.
				settings_fields( 'sunflower_events_option_group' );
				do_settings_sections( 'sunflower-setting-events' );
				submit_button();
			?>
			</form>

			<div class="sunflower-ical-import sunflower-events-activated">
			<h2><?php esc_attr_e( 'ICS Calendar Import', 'sunflower' ); ?></h2>
			<?php

			if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'sunflower_events_options-icalimport' ) && isset( $_GET['icalimport'] ) ) {
				$sunflower_icalimport_report = sunflower_import_icals( true );
				foreach ( $sunflower_icalimport_report as $index => $report ) :
					$has_error = ! empty( $report['error'] );
					?>
					<div class="sunflower-import-card <?php echo $has_error ? 'has-error' : ''; ?>">
						<div class="sunflower-import-header">
							<strong>
								<?php echo esc_html__( 'Import Line', 'sunflower' ); ?>
								#<?php echo (int) $index; ?>
							</strong>
						</div>

						<div class="sunflower-import-body">
							<p class="sunflower-import-source">
								<?php echo esc_html__( 'ICS calendar imported from:', 'sunflower' ); ?>
								<a href="<?php echo esc_url( $report['url'] ); ?>" target="_blank" rel="noopener noreferrer">
									<?php echo esc_html( $report['url'] ); ?>
								</a>
							</p>

							<?php if ( $has_error ) : ?>

								<div class="sunflower-import-error">
									<strong><?php echo esc_html__( 'Error:', 'sunflower' ); ?></strong><br>
									<?php echo esc_html( $report['error'] ); ?>
								</div>

							<?php else : ?>

								<ul class="sunflower-import-stats">
									<li class="new">
										<?php echo esc_html__( 'New', 'sunflower' ); ?>
										<span><?php echo (int) $report['new_events']; ?></span>
									</li>
									<li class="updated">
										<?php echo esc_html__( 'Updated', 'sunflower' ); ?>
										<span><?php echo (int) $report['updated_events']; ?></span>
									</li>
									<li class="deleted">
										<?php echo esc_html__( 'Deleted', 'sunflower' ); ?>
										<span><?php echo (int) $report['deleted_events']; ?></span>
									</li>
								</ul>

							<?php endif; ?>
						</div>
					</div>
					<?php
				endforeach;
				printf(
					'<div>
					<a href="../?post_type=sunflower_event" class="button button-secondary">%s</a>
					<a href="edit.php?post_type=sunflower_event" class="button button-secondary">%s</a>
					</div>',
					esc_attr__( 'See all events (Frontend)', 'sunflower' ),
					esc_attr__( 'Edit events (Backend)', 'sunflower' )
				);
				if ( ini_get( 'allow_url_fopen' ) ) {
					$sunflower_icalimport_url = wp_nonce_url( 'admin.php?page=sunflower_events_options&icalimport=1', 'sunflower_events_options-icalimport' );
					printf( '<a href="%s" class="button button-primary">%s</a>', esc_html( $sunflower_icalimport_url ), esc_attr__( 'Import calendars now', 'sunflower' ) );
				}
			} elseif ( sunflower_get_setting( 'sunflower_ical_urls' ) ) {
				if ( ini_get( 'allow_url_fopen' ) ) {
					$sunflower_icalimport_url = wp_nonce_url( 'admin.php?page=sunflower_events_options&icalimport=1', 'sunflower_events_options-icalimport' );
					printf( '<a href="%s" class="button button-primary">%s</a>', esc_html( $sunflower_icalimport_url ), esc_attr__( 'Import calendars now', 'sunflower' ) );
				} else {
					echo 'Der externe Kalender kann noch nicht importiert werden. Bitte erlaube in den php-Einstellungen <em>allow_url_fopen</em>.';
				}
			} else {
				echo 'Um einen Kalender importieren zu können, trage die URL bitte unter Sunflower-Einstellungen ein.';
			}
			?>
			</div>

			<div class="sunflower-event-location-fix sunflower-events-activated">
				<h2>Korrektur der Marker auf Landkarten von importierten Terminen</h2>
				<input type="hidden" name="_sunflower_event_lat" id="_sunflower_event_lat">
				<input type="hidden" name="_sunflower_event_lon" id="_sunflower_event_lon">
				<?php wp_nonce_field( 'sunflower_location', '_wpnonce-locationfix' ); ?>
				<div id="sunflower-location-row" style="display:none">
					<?php
					printf(
						'%1$s
						<select name="sunflower_location" id="sunflower-location">
							<option value="">%2$s</option>',
						esc_attr__( 'Adjust geo marker for:', 'sunflower' ),
						esc_attr__( 'please choose', 'sunflower' )
					);
					global $wpdb;
					$transients = $wpdb->get_results( sprintf( "SELECT * FROM %s WHERE option_name LIKE '_transient_sunflower_geocache_%%'", $wpdb->options ) );

					foreach ( $transients as $transient ) {
						[$lon, $lat] = get_option( (string) $transient->option_name );
						$location    = preg_replace( '/_transient_sunflower_geocache_/', '', (string) $transient->option_name );
						printf( '<option value="%s;%s">%s</option>', esc_attr( $lat ), esc_attr( $lon ), esc_attr( $location ) );
					}

					?>
					</select>
					<?php
					printf(
						'<button class="button-secondary" id="sunflower-fix-location-delete">%s</button>',
						esc_attr__( 'Delete geo data for this location', 'sunflower' )
					);
					?>
					<br>

					Die Änderung wird automatisch nach Setzen der Markierung gespeichert. Wirksam wird sie beim nächsten Import. Den Import kannst Du per Hand auslösen.
				</div>
				<?php
				$lat  = 49.5;
				$lon  = 12;
				$zoom = 5;
				printf(
					'
					<div>
						<button id="sunflowerShowMap" class="button-primary" onClick="sunflowerShowLeaflet( %3$s, %4$s, %5$s, false );">%2$s</button>
					</div>
					<div id="leaflet" style="height:400px"></div>',
					esc_attr__( 'Map', 'sunflower' ),
					esc_attr__( 'load map', 'sunflower' ),
					esc_attr( $lat ),
					esc_attr( $lon ),
					esc_attr( $zoom )
				);
				?>
			</div>
		<?php
	}

	/**
	 * Register and add event settings
	 */
	public function sunflower_event_page_init(): void {

		register_setting(
			'sunflower_events_option_group',
			'sunflower_events_options',
			array(
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => array(
					'sunflower_events_enabled'     => 1,
					'sunflower_show_event_archive' => 1,
				),
			)
		);

		add_settings_section(
			'sunflower-setting-events-enabled',
			__( 'Events Feature', 'sunflower' ),
			$this->print_section_info_events_enabled( ... ),
			'sunflower-setting-events',
			array(
				'before_section' => '<div class="%s">',
				'after_section'  => '</div><br><hr>',
				'section_class'  => 'sunflower-events-enable',
			)
		);

		add_settings_section(
			'sunflower-setting-events',
			__( 'Events', 'sunflower' ),
			$this->print_section_info( ... ),
			'sunflower-setting-events',
			array(
				'before_section' => '<div class="%s">',
				'after_section'  => '</div>',
				'section_class'  => 'sunflower-events-activated',
			)
		);

		add_settings_field(
			'sunflower_events_enabled',
			__( 'Enable events feature', 'sunflower' ),
			$this->sunflower_checkbox_callback( ... ),
			'sunflower-setting-events',
			'sunflower-setting-events-enabled',
			array( 'sunflower_events_enabled', __( 'Enable events feature', 'sunflower' ) )
		);

		add_settings_field(
			'sunflower_events_slug',
			__( 'Events page slug', 'sunflower' ),
			$this->sunflower_events_slug_callback( ... ),
			'sunflower-setting-events',
			'sunflower-setting-events'
		);

		add_settings_field(
			'sunflower_events_description',
			__( 'Description shown on the events page', 'sunflower' ),
			$this->sunflower_events_description_callback( ... ),
			'sunflower-setting-events',
			'sunflower-setting-events'
		);

		add_settings_field(
			'sunflower_ical_urls',
			__( 'URLs of iCal calendars, one per row', 'sunflower' ),
			$this->sunflower_ical_urls_callback( ... ),
			'sunflower-setting-events',
			'sunflower-setting-events'
		);

		add_settings_field(
			'sunflower_fix_time_zone_error',
			__( 'Rectify time zone', 'sunflower' ),
			$this->sunflower_checkbox_callback( ... ),
			'sunflower-setting-events',
			'sunflower-setting-events',
			array( 'sunflower_fix_time_zone_error', __( 'Rectify time zone', 'sunflower' ) )
		);

		add_settings_field(
			'sunflower_show_overall_map',
			__( 'Show overall map', 'sunflower' ),
			$this->sunflower_checkbox_callback( ... ),
			'sunflower-setting-events',
			'sunflower-setting-events',
			array( 'sunflower_show_overall_map', __( 'Show overall map', 'sunflower' ) )
		);

		add_settings_field(
			'sunflower_show_event_archive',
			__( 'Show events archive', 'sunflower' ),
			$this->sunflower_checkbox_callback( ... ),
			'sunflower-setting-events',
			'sunflower-setting-events',
			array( 'sunflower_show_event_archive', __( 'Show events archive', 'sunflower' ) )
		);

		add_settings_field(
			'sunflower_zoom',
			__( 'Zoom-level of overall map', 'sunflower' ),
			$this->sunflower_zoom_callback( ... ),
			'sunflower-setting-events',
			'sunflower-setting-events'
		);
		add_settings_field(
			'sunflower_show_event_archive',
			__( 'Show events archive', 'sunflower' ),
			$this->sunflower_checkbox_callback( ... ),
			'sunflower-setting-events',
			'sunflower-setting-events',
			array( 'sunflower_show_event_archive', __( 'Show events archive', 'sunflower' ) )
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

			switch ( $key ) {

				case 'sunflower_events_enabled':
					$new_input[ $key ] = $value ? 1 : 0;
					break;

				case 'sunflower_events_slug':
					$new_input[ $key ] = sanitize_title( $value );
					break;

				case 'sunflower_ical_urls':
				case 'sunflower_events_description':
					$new_input[ $key ] = $value;
					break;

				default:
					$new_input[ $key ] = sanitize_text_field( $value );
			}
		}

		return $new_input;
	}


	/**
	 * Print the Section text
	 */
	public function print_section_info_events_enabled() {
		printf(
			'<p class="description">%s</p>',
			esc_attr__( 'Enable or disable the events feature of the Sunflower theme.', 'sunflower' )
		);
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info() {
		printf(
			'<p class="description">%s</p>',
			esc_attr__( 'Configure how events behave on your website.', 'sunflower' )
		);
		?>
		<div class="sunflower-events-activated">
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row">Permalinks</th>
						<td>

						<?php
						if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'sunflower_options-flushpermalinks' ) && isset( $_GET['flush_permalinks'] ) ) {
							flush_rewrite_rules();
							printf( '<p>%s</p>', esc_attr__( 'The permalink structure has been reimported.', 'sunflower' ) );
						} else {
								printf( '<p>%s</p>', esc_attr__( 'After changing the event page slug, you need to reimport the permalink structure.', 'sunflower' ) );
								printf( '<p>%s</p>', esc_attr__( 'This can be safely done any time. E.g. if the event page does not show up correctly.', 'sunflower' ) );
								$sunflower_flushpermalinks_url = wp_nonce_url( 'admin.php?page=sunflower_events_options&flush_permalinks=1', 'sunflower_options-flushpermalinks' );
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
	 * Checkbox callback
	 *
	 * @param array $args The field arguments.
	 */
	public function sunflower_checkbox_callback( $args ) {

		$options = get_option( 'sunflower_events_options' );
		$key     = $args[0];
		$label   = $args[1];
		$value   = $options[ $key ] ?? 0;
		?>

		<input type="hidden"
				name="sunflower_events_options[<?php echo esc_attr( $key ); ?>]"
				value="0">

		<label>
			<input type="checkbox"
				name="sunflower_events_options[<?php echo esc_attr( $key ); ?>]"
				value="1"
				<?php checked( $value, 1 ); ?>>
			<?php echo esc_html( $label ); ?>
		</label>

		<?php
	}

	/**
	 * Excerpt length field
	 */
	public function sunflower_events_slug_callback(): void {
		printf(
			'
			<input type="text" id="sunflower_events_slug" name="sunflower_events_options[sunflower_events_slug]" value="%s" />
			</div>',
			( isset( $this->options['sunflower_events_slug'] ) && ! empty( $this->options['sunflower_events_slug'] ) ) ? esc_attr( $this->options['sunflower_events_slug'] ) : 'termine'
		);
	}

	/**
	 * Event description callback
	 */
	public function sunflower_events_description_callback(): void {
		printf(
			'<textarea style="white-space: pre-wrap;width: 90%%;height:7em;" id="sunflower_events_description" name="sunflower_events_options[sunflower_events_description]">%s</textarea>',
			wp_kses_post( $this->options['sunflower_events_description'] ?? '' )
		);
		echo '<div>Dieser Text wird auf der Übersichtsseite für Termine angezeigt. HTML-Tags sind erlaubt.</div>';
	}

	/**
	 * Event ical url description
	 */
	public function sunflower_ical_urls_callback(): void {
		printf(
			'<textarea style="white-space: pre-wrap;width: 90%%;height:7em;" id="sunflower_ical_urls" name="sunflower_events_options[sunflower_ical_urls]">%s</textarea>',
			esc_attr( $this->options['sunflower_ical_urls'] ?? '' )
		);
		echo '<div><a href="https://sunflower-theme.de/documentation/events/" target="_blank">Mehr zu den Einstellungen in der Dokumentation</a><br>
        Importierte Termine dürfen nicht im WordPress-Backend bearbeitet werden, weil Änderungen beim nächsten
        Import überschrieben werden.<br>
        Jede URL muss mit http:// oder https:// beginnen. Automatische Kategorien pro Kalender bitte mit ; anfügen.
        </div>';
	}

	/**
	 * Event zoom input field
	 */
	public function sunflower_zoom_callback(): void {
		printf(
			'<input type="number" min="1" max="19" id="sunflower_zoom" name="sunflower_events_options[sunflower_zoom]" value="%s">',
			esc_attr( $this->options['sunflower_zoom'] ?? '11' )
		);
		echo '<div>1 (ganze Welt) bis 19 (einzelne Straße), Zoomlevel für die Übersichtskarte für Termine</div>';
	}
}

if ( is_admin() ) {
	$sunflower_settings_page = new SunflowerEventSettingsPage();
}

/**
 * On theme update, ensure that the 'sunflower_events_enabled' option is set.
*/
add_action(
	'admin_init',
	function () {

		$options = get_option( 'sunflower_events_options' );

		if ( ! is_array( $options ) ) {
			return;
		}

		// Set only if the option does not exist yet.
		if ( ! array_key_exists( 'sunflower_events_enabled', $options ) ) {

			$options['sunflower_events_enabled'] = 1;
			update_option( 'sunflower_events_options', $options );
		}
	}
);


/**
 * Add inline script to handle dependent fields.
*/
add_action(
	'admin_enqueue_scripts',
	function ( $hook ) {

		if ( 'sunflower_page_sunflower_events_options' !== $hook ) {
			return;
		}

		wp_add_inline_script(
			'jquery-core',
			'
		jQuery(function ($) {

			const checkbox = $("input[name=\'sunflower_events_options[sunflower_events_enabled]\']");
			const dependentRow = $(".sunflower-events-activated");

			function toggleFields() {
				dependentRow.toggle( checkbox.is(":checked") );
			}

			toggleFields();
			checkbox.on("change", toggleFields);
		});
		'
		);
	}
);

/**
 * Flush rewrite rules if the event slug has changed.
 */
add_action(
	'update_option_sunflower_events_options',
	function ( $old_value, $new_value ) {
		$old_slug = $old_value['sunflower_events_slug'] ?? '';
		$new_slug = $new_value['sunflower_events_slug'] ?? '';

		if ( $old_slug !== $new_slug ) {
			flush_rewrite_rules();
		}
	},
	10,
	3
);
