<?php

class SunflowerEventSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action('admin_menu', $this->sunflower_add_plugin_page(...));
        add_action('admin_init', $this->sunflower_event_page_init(...));
    }

    /**
     * Add options page
     */
    public function sunflower_add_plugin_page(): void
    {
        add_submenu_page(
            'sunflower_admin',
            __('Events', 'sunflower'),
            __('Events', 'sunflower'),
            'edit_pages',
            'sunflower_events_options',
            $this->create_sunflower_events_options_page(...)
        );
    }

    /**
     * Events Options page callback
     */
    public function create_sunflower_events_options_page(): void
    {
        // Set class property
        $this->options = get_option('sunflower_events_options');
        ?>
		<div class="wrap">
			<h1><?php esc_attr_e('Sunflower Settings', 'sunflower'); ?></h1>
			<form method="post" action="options.php">
			<?php
                // This prints out all hidden setting fields
                settings_fields('sunflower_events_option_group');
        do_settings_sections('sunflower-setting-events');
        submit_button();
        ?>
			</form>

			<h2>Kalenderimport</h2>
			<?php

        if (isset($_GET['icalimport'])) {
            sunflower_import_icals(true);
            echo '<div>Die Termine wurden aktualisiert.</div>';
            printf('<div><a href="../?post_type=sunflower_event">Termine ansehen</a></div>');
        } elseif (get_sunflower_setting('sunflower_ical_urls')) {
            if (ini_get('allow_url_fopen')) {
                echo '<a href="admin.php?page=sunflower_events_options&icalimport=1" class="button button-primary">Kalender jetzt importieren</a>';
            } else {
                echo 'Der externe Kalender kann noch nicht importiert werden. Bitte erlaube in den php-Einstellungen <em>allow_url_fopen</em>.';
            }
        } else {
            echo 'Um einen Kalender importieren zu können, trage die URL bitte unter Sunflower-Einstellungen ein.';
        }
        ?>

			<h2>Korrektur der Marker auf Landkarten von importierten Terminen</h2>
			<input type="hidden" name="_sunflower_event_lat" id="_sunflower_event_lat">
			<input type="hidden" name="_sunflower_event_lon" id="_sunflower_event_lon">
			<?php wp_nonce_field( 'sunflower_location' ); ?>
			<div id="sunflower-location-row" style="display:none">
				Bearbeite die Geo-Markierung für:
				<select name="sunflower_location" id="sunflower-location">
					<option value="">bitte wählen</option>
				<?php
                global $wpdb;
        $transients = $wpdb->get_results(sprintf("SELECT * FROM %s WHERE option_name LIKE '_transient_sunflower_geocache_%%'", $wpdb->options));

        foreach ($transients as $transient) {
            $location = preg_replace("/_transient_sunflower_geocache_/", '', (string) $transient->option_name);

            [$lon, $lat] = unserialize($transient->option_value);
            printf('<option value="%s;%s">%s</option>', $lat, $lon, $location);
        }

        ?>
				</select>
				<button id="sunflower-fix-location-delete">Geodaten für diesen Ort löschen</button>
				<br>
				Die Änderung wird automatisch nach Setzen der Markierung gespeichert. Wirksam wird sie beim nächsten Import. Den Import kannst Du per Hand auslösen.
			</div>
			<?php
        $lat = 49.5;
        $lon = 12;
        $zoom = 5;
        printf(
            '
                <div>
                    <button id="sunflowerShowMap" onClick="sunflowerShowLeaflet( %3$s, %4$s, %5$s, false );">%2$s</button>
                </div>
                <div id="leaflet" style="height:400px"></div>',
            __('Map', 'sunflower'),
            __('load map', 'sunflower'),
            $lat,
            $lon,
            $zoom
        );
        ?>
		</div>
		<?php
    }

    /**
     * Register and add event settings
     */
    public function sunflower_event_page_init(): void
    {
        register_setting(
            'sunflower_events_option_group', // Option group
            'sunflower_events_options', // Option name
            $this->sanitize(...) // Sanitize
        );

        add_settings_section(
            'sunflower-setting-events', // ID
            __('Events', 'sunflower'), // Title
            $this->print_section_info(...), // Callback
            'sunflower-setting-events' // Page
        );

        add_settings_field(
            'sunflower_events_description', // ID
            __('Description shown on the events page', 'sunflower'), // Title
            $this->sunflower_events_description_callback(...), // Callback
            'sunflower-setting-events', // Page
            'sunflower-setting-events' // Section
        );

        add_settings_field(
            'sunflower_ical_urls', // ID
            __('URLs of iCal calendars, one per row', 'sunflower'), // Title
            $this->sunflower_ical_urls_callback(...), // Callback
            'sunflower-setting-events', // Page
            'sunflower-setting-events' // Section
        );

        add_settings_field(
            'sunflower_fix_time_zone_error', // ID
            __('Rectify time zone', 'sunflower'), // Title
            $this->sunflower_checkbox_callback(...), // Callback
            'sunflower-setting-events', // Page
            'sunflower-setting-events', // Section
            ['sunflower_fix_time_zone_error', __('Rectify time zone', 'sunflower')]       // args
        );

        add_settings_field(
            'sunflower_show_overall_map', // ID
            __('Show overall map', 'sunflower'), // Title
            $this->sunflower_checkbox_callback(...), // Callback
            'sunflower-setting-events', // Page
            'sunflower-setting-events', // Section
            ['sunflower_show_overall_map', __('Show overall map', 'sunflower')]       // args
        );

        add_settings_field(
            'sunflower_show_event_archive', // ID
            __('Show events archive', 'sunflower'), // Title
            $this->sunflower_checkbox_callback(...), // Callback
            'sunflower-setting-events', // Page
            'sunflower-setting-events', // Section
            ['sunflower_show_event_archive', __('Show events archive', 'sunflower')]       // args
        );

        add_settings_field(
            'sunflower_zoom', // ID
            __('Zoom-level of overall map', 'sunflower'), // Title
            $this->sunflower_zoom_callback(...), // Callback
            'sunflower-setting-events', // Page
            'sunflower-setting-events' // Section
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize($input)
    {
        $new_input = [];

        // Sanitize everything
        foreach ($input as $key => $value) {
            if (isset($input[$key])) {
                $new_input[$key] = sanitize_text_field($value);
            }
        }

        // Sanitize special values
        if (isset($input['sunflower_ical_urls'])) {
            $new_input['sunflower_ical_urls'] = $input['sunflower_ical_urls'] ?: '';
        }

        if (isset($input['sunflower_events_description'])) {
            $new_input['sunflower_events_description'] = $input['sunflower_events_description'] ?: '';
        }

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
    }

    public function sunflower_checkbox_callback($args): void
    {
        $field = $args[0];
        $label = $args[1];

        printf(
            '<label>
                    <input type="checkbox" id="%1$s" name="sunflower_events_options[%1$s]" value="checked" %2$s />
                    %3$s
                </label>',
            $field,
            isset($this->options[$field]) ? 'checked' : '',
            $label
        );
    }

    public function sunflower_events_description_callback(): void
    {
        printf(
            '<textarea style="white-space: pre-wrap;width: 90%%;height:7em;" id="sunflower_events_description" name="sunflower_events_options[sunflower_events_description]">%s</textarea>',
            $this->options['sunflower_events_description'] ?? ''
        );
        echo '<div>Dieser Text wird auf der Übersichtsseite für Termine angezeigt. HTML-Tags sind erlaubt.</div>';
    }

    public function sunflower_ical_urls_callback(): void
    {
        printf(
            '<textarea style="white-space: pre-wrap;width: 90%%;height:7em;" id="sunflower_ical_urls" name="sunflower_events_options[sunflower_ical_urls]">%s</textarea>',
            $this->options['sunflower_ical_urls'] ?? ''
        );
        echo '<div><a href="https://sunflower-theme.de/documentation/events/" target="_blank">Mehr zu den Einstellungen in der Dokumentation</a><br>
        Importierte Termine dürfen nicht im WordPress-Backend bearbeitet werden, weil Änderungen beim nächsten
        Import überschrieben werden.<br>
        Jede URL muss mit http:// oder https:// beginnen. Automatische Kategorien pro Kalender bitte mit ; anfügen.

        </div>';
    }

    public function sunflower_zoom_callback(): void
    {
        printf(
            '<input type="number" min="1" max="19" id="sunflower_zoom" name="sunflower_events_options[sunflower_zoom]" value="%s">',
            $this->options['sunflower_zoom'] ?? '11'
        );
        echo '<div>1 (ganze Welt) bis 19 (einzelne Straße), Zoomlevel für die Übersichtskarte für Termine</div>';
    }
}

if (is_admin()) {
    $my_settings_page = new SunflowerEventSettingsPage();
}
