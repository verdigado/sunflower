<?php

if ( is_admin() ) {
	$my_settings_page = new SunflowerEventSettingsPage();
}

class SunflowerEventSettingsPage {

	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'sunflower_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'sunflower_page_init' ) );
	}

	/**
	 * Add options page
	 */
	public function sunflower_add_plugin_page() {
		add_submenu_page(
			'sunflower_admin',
			__( 'Events', 'sunflower' ),
			__( 'Events', 'sunflower' ),
			'edit_pages',
			'sunflower_events_options',
			array( $this, 'create_sunflower_events_options_page' )
		);
	}

	/**
	 * Admin page callback
	 */
	public function create_sunflower_admin_page() {
		?>
		<div class="wrap">
			<h1><?php _e( 'About Sunflower', 'sunflower' ); ?></h1>
			<h2>Erste Schritte</h2>
			<a href="https://sunflower-theme.de/documentation/" target="_blank">Eine ausführliche Dokumentation gibt es unter https://sunflower-theme.de/documentation/</a>
		

			<h2>Umzug von Urwahl3000</h2>
	   
			Widgets, Menüs und Termine werden automatisch importiert.<br>
			<a href="https://sunflower-theme.de/documentation/urwahl3000" target="_blank">
			Hier gibt es mehr Info über den Umzug von Urwahl3000</a>. 

			<h2>Einstellungen</h2>
			Bitte siehe links im Menü, welche Unterpunkte es gibt.

			<h2>Import von Muster-Bildern</h2>
			<?php

			if ( isset( $_GET['pictureimport'] ) ) {
				$count = sunflower_import_all_pictures();
				printf( '<a href="upload.php">Es wurden %d Bilder importiert. Sieh sie Dir in der Mediathek an</a>', $count );
			} else {
				?>
				Wir haben eine Auswahl an Muster-Bildern zusammengestellt, die Du Dir in Deine Mediathek 
				herunterladen kannst. Du darfst diese Bilder ohne Quellenangabe nutzen. 
				Hier siehst Du die Bilder, die du importieren kannst:
				<div style="margin-bottom:1em">
					<img src="https://sunflower-theme.de/updateserver/images/thumbnails.jpg" alt="Thumbnails">
				</div>
				<div>
					<a href="admin.php?page=sunflower_admin&pictureimport=1" class="button button-primary">
						Bilder in Mediathek importieren</a>
				</div>
				<div>
				Der Import kann einige Minuten dauern. Bitte warte so lange, und klicke nirgendwo hin.
			</div>
				<?php
			}
			?>

		</div>
		<?php
	}

	/**
	 * Options page callback
	 */
	public function create_sunflower_settings_page() {
		// Set class property
		$this->options = get_option( 'sunflower_events_options' );
		?>
		<div class="wrap">
			<h1><?php _e( 'Sunflower Settings', 'sunflower' ); ?></h1>
			<form method="post" action="options.php">
			<?php
				// This prints out all hidden setting fields
				settings_fields( 'sunflower_events_option_group' );
				do_settings_sections( 'sunflower-setting-admin' );
				submit_button();
			?>
			</form>
		</div>
		<?php
	}

	 /**
	  * Events Options page callback
	  */
	public function create_sunflower_events_options_page() {
		// Set class property
		$this->options = get_option( 'sunflower_events_options' );
		?>
		<div class="wrap">
			<h1><?php _e( 'Sunflower Settings', 'sunflower' ); ?></h1>
			<form method="post" action="options.php">
			<?php
				// This prints out all hidden setting fields
				settings_fields( 'sunflower_events_option_group' );
				do_settings_sections( 'sunflower-setting-events' );
				submit_button();
			?>
			</form>

			<h2>Kalenderimport</h2>
			<?php

			if ( isset( $_GET['icalimport'] ) ) {
				sunflower_import_icals( true );
				echo '<div>Die Termine wurden aktualisiert.</div>';
				printf( '<div><a href="../?post_type=sunflower_event">Termine ansehen</a></div>' );
			} else {
				if ( get_sunflower_setting( 'sunflower_ical_urls' ) ) {
					if ( ini_get( 'allow_url_fopen' ) ) {
						echo '<a href="admin.php?page=sunflower_events_options&icalimport=1" class="button button-primary">Kalender jetzt importieren</a>';
					} else {
						echo 'Der externe Kalender kann noch nicht importiert werden. Bitte erlaube in den php-Einstellungen <em>allow_url_fopen</em>.';
					}
				} else {
					echo 'Um einen Kalender importieren zu können, trage die URL bitte unter Sunflower-Einstellungen ein.';
				}
			}
			?>

			<h2>Korrektur der Marker auf Landkarten von importierten Terminen</h2>
			<input type="hidden" name="_sunflower_event_lat" id="_sunflower_event_lat">
			<input type="hidden" name="_sunflower_event_lon" id="_sunflower_event_lon">

			<div id="sunflower-location-row" style="display:none">
				Bearbeite die Geo-Markierung für: 
				<select name="sunflower_location" id="sunflower-location">
					<option value="">bitte wählen</option>
				<?php
					global $wpdb;
					$prefix = 'sunflower_geocache_';

					$transients = $wpdb->get_results( "SELECT * FROM $wpdb->options WHERE option_name LIKE '_transient_${prefix}%'" );

				foreach ( $transients as $transient ) {
					$location = preg_replace( "/_transient_${prefix}/", '', $transient->option_name );

					list($lon, $lat) = unserialize( $transient->option_value );
					printf( '<option value="%s;%s">%s</option>', $lat, $lon, $location );

				}

				?>
				</select>
				<button id="sunflower-fix-location-delete">Geodaten für diesen Ort löschen</button>
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
                    <button id="sunflowerShowMap" onClick="sunflowerShowLeaflet( %3$s, %4$s, %5$s, false );">%2$s</button>
                </div>
                <div id="leaflet" style="height:400px"></div>',
					__( 'Map', 'sunflower' ),
					__( 'load map', 'sunflower' ),
					$lat,
					$lon,
					$zoom
				);
			?>
		</div>
		<?php
	}


	/**
	 * Register and add settings
	 */
	public function sunflower_page_init() {
		register_setting(
			'sunflower_events_option_group', // Option group
			'sunflower_events_options', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		add_settings_section(
			'sunflower-setting-events', // ID
			__( 'Events', 'sunflower' ), // Title
			array( $this, 'print_section_info' ), // Callback
			'sunflower-setting-events' // Page
		);

		add_settings_field(
			'sunflower_events_description', // ID
			__( 'Description shown on the events page', 'sunflower' ), // Title
			array( $this, 'sunflower_events_description_callback' ), // Callback
			'sunflower-setting-events', // Page
			'sunflower-setting-events' // Section
		);

		add_settings_field(
			'sunflower_ical_urls', // ID
			__( 'URLs of iCal calendars, one per row', 'sunflower' ), // Title
			array( $this, 'sunflower_ical_urls_callback' ), // Callback
			'sunflower-setting-events', // Page
			'sunflower-setting-events' // Section
		);

		add_settings_field(
			'sunflower_fix_time_zone_error', // ID
			__( 'Rectify time zone', 'sunflower' ), // Title
			array( $this, 'sunflower_checkbox_callback' ), // Callback
			'sunflower-setting-events', // Page
			'sunflower-setting-events', // Section
			array( 'sunflower_fix_time_zone_error', __( 'Rectify time zone', 'sunflower' ) )       // args
		);

		add_settings_field(
			'sunflower_show_overall_map', // ID
			__( 'Show overall map', 'sunflower' ), // Title
			array( $this, 'sunflower_checkbox_callback' ), // Callback
			'sunflower-setting-events', // Page
			'sunflower-setting-events', // Section
			array( 'sunflower_show_overall_map', __( 'Show overall map', 'sunflower' ) )       // args
		);

		add_settings_field(
			'sunflower_show_event_archive', // ID
			__( 'Show events archive', 'sunflower' ), // Title
			array( $this, 'sunflower_checkbox_callback' ), // Callback
			'sunflower-setting-events', // Page
			'sunflower-setting-events', // Section
			array( 'sunflower_show_event_archive', __( 'Show events archive', 'sunflower' ) )       // args
		);

		add_settings_field(
			'sunflower_zoom', // ID
			__( 'Zoom-level of overall map', 'sunflower' ), // Title
			array( $this, 'sunflower_zoom_callback' ), // Callback
			'sunflower-setting-events', // Page
			'sunflower-setting-events' // Section
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
		if ( isset( $input['sunflower_ical_urls'] ) ) {
			$new_input['sunflower_ical_urls'] = $input['sunflower_ical_urls'] ?: '';
		}

		if ( isset( $input['sunflower_events_description'] ) ) {
			$new_input['sunflower_events_description'] = $input['sunflower_events_description'] ?: '';
		}

		return $new_input;
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info() {
	}


	public function sunflower_checkbox_callback( $args ) {
		$field = $args[0];
		$label = $args[1];

		printf(
			'<label>
                    <input type="checkbox" id="%1$s" name="sunflower_events_options[%1$s]" value="checked" %2$s />
                    %3$s
                </label>',
			$field,
			isset( $this->options[ $field ] ) ? 'checked' : '',
			$label
		);
	}

	public function sunflower_events_description_callback() {
		printf(
			'<textarea style="white-space: pre-wrap;width: 90%%;height:7em;" id="sunflower_events_description" name="sunflower_events_options[sunflower_events_description]">%s</textarea>',
			isset( $this->options['sunflower_events_description'] ) ? $this->options['sunflower_events_description'] : ''
		);
		echo '<div>Dieser Text wird auf der Übersichtsseite für Termine angezeigt. HTML-Tags sind erlaubt.</div>';
	}

	public function sunflower_ical_urls_callback() {
		printf(
			'<textarea style="white-space: pre-wrap;width: 90%%;height:7em;" id="sunflower_ical_urls" name="sunflower_events_options[sunflower_ical_urls]">%s</textarea>',
			isset( $this->options['sunflower_ical_urls'] ) ? $this->options['sunflower_ical_urls'] : ''
		);
		echo '<div><a href="https://sunflower-theme.de/documentation/events/" target="_blank">Mehr zu den Einstellungen in der Dokumenation</a><br>
        Importierte Termine dürfen nicht im WordPress-Backend bearbeitet werden, weil Änderungen beim nächsten 
        Import überschrieben werden.<br>
        Jede URL muss mit http:// oder https:// beginnen. Automatische Kategorien pro Kalender bitte mit ; anfügen.

        </div>';
	}

	public function sunflower_zoom_callback() {
		printf(
			'<input type="number" min="1" max="19" id="sunflower_zoom" name="sunflower_events_options[sunflower_zoom]" value="%s">',
			isset( $this->options['sunflower_zoom'] ) ? $this->options['sunflower_zoom'] : '11'
		);
		echo '<div>1 (ganze Welt) bis 19 (einzelne Straße), Zoomlevel für die Übersichtskarte für Termine</div>';
	}



}

