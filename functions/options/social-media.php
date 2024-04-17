<?php
class SunflowerSocialMediaSettingsPage
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
        add_action('admin_init', $this->sunflower_social_media_page_init(...));
    }

    /**
     * Add options page
     */
    public function sunflower_add_plugin_page(): void
    {
        add_submenu_page(
            'sunflower_admin',
            __('Social Media', 'sunflower'),
            __('Social Media', 'sunflower'),
            'edit_pages',
            'sunflower_social_media',
            $this->create_sunflower_social_media_page(...)
        );
    }

    /**
     * Social Media Profiles page callback
     */
    public function create_sunflower_social_media_page(): void
    {
        // Set class property
        $this->options = get_option('sunflower_social_media_options');
        ?>
		<div class="wrap">
			<h1><?php _e('Sunflower Settings', 'sunflower'); ?></h1>
			<form method="post" action="options.php">
			<?php
                // This prints out all hidden setting fields
                settings_fields('sunflower_social_media_option_group');
                do_settings_sections('sunflower-setting-social-media-options');
                submit_button();
            ?>
			</form>
		</div>
		<?php
    }

    /**
     * Register and add social media settings
     */
    public function sunflower_social_media_page_init(): void
    {
        register_setting(
            'sunflower_social_media_option_group', // Option group
            'sunflower_social_media_options', // Option name
            $this->sanitize(...) // Sanitize
        );

        add_settings_section(
            'sunflower_social_media', // ID
            __('Social Media Profiles', 'sunflower'), // Title
            $this->print_section_info(...), // Callback
            'sunflower-setting-social-media-options' // Page
        );

        add_settings_field(
            'sunflower_social_media_profiles',
            __('Social Media Profiles', 'sunflower'), // Title
            $this->social_media_profiles_callback(...),
            'sunflower-setting-social-media-options', // Page
            'sunflower_social_media' // Section
        );

        add_settings_section(
            'sunflower_social_media_sharers', // ID
            __('Social Sharers', 'sunflower'), // Title
            $this->print_section_info_sharers(...), // Callback
            'sunflower-setting-social-media-options' // Page
        );

        add_settings_field(
            'sunflower_open_graph_fallback_image', // ID
            __('Open Graph Fallback Image', 'sunflower'), // Title
            $this->sunflower_open_graph_fallback_image(...), // Callback
            'sunflower-setting-social-media-options', // Page
            'sunflower_social_media_sharers' // Section
        );

        add_settings_field(
            'sunflower_sharer_x_twitter', // ID
            __('X (Twitter)', 'sunflower'), // Title
            $this->sunflower_checkbox_callback(...), // Callback
            'sunflower-setting-social-media-options', // Page
            'sunflower_social_media_sharers', // Section
            ['sunflower_sharer_x_twitter', __('X (Twitter)', 'sunflower')]
        );

        add_settings_field(
            'sunflower_sharer_facebook', // ID
            __('Facebook', 'sunflower'), // Title
            $this->sunflower_checkbox_callback(...), // Callback
            'sunflower-setting-social-media-options', // Page
            'sunflower_social_media_sharers', // Section
            ['sunflower_sharer_facebook', __('Facebook', 'sunflower')]
        );

        add_settings_field(
            'sunflower_sharer_mail', // ID
            __('mail', 'sunflower'), // Title
            $this->sunflower_checkbox_callback(...), // Callback
            'sunflower-setting-social-media-options', // Page
            'sunflower_social_media_sharers', // Section
            ['sunflower_sharer_mail', __('Mail', 'sunflower')]
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
        if (isset($input['sunflower_social_media_profiles'])) {
            $new_input['sunflower_social_media_profiles'] = sanitize_textarea_field($input['sunflower_social_media_profiles']);
        }

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
    }

    public function print_section_info_sharers(): void
    {
        _e('Show share buttons on single post page', 'sunflower');
    }

    public function sunflower_checkbox_callback($args): void
    {
        $field = $args[0];
        $label = $args[1];

        printf(
            '<label>
                    <input type="checkbox" id="%1$s" name="sunflower_social_media_options[%1$s]" value="checked" %2$s />
                    %3$s
                </label>',
            $field,
            isset($this->options[$field]) ? 'checked' : '',
            $label
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function social_media_profiles_callback($args): void
    {
        $default = [];
        $default[] = 'fab fa-x-twitter;X (Twitter);';
        $default[] = 'fab fa-twitter;Twitter;';
        $default[] = 'fab fa-facebook;Facebook;';
        $default[] = 'fab fa-linkedin;LinkedIn;';
        $default[] = 'fab fa-instagram;Instagram;';
        $default[] = 'fab fa-youtube;YouTube;';
        $default[] = 'fas fa-globe;Webseite;';
        $default[] = 'forkawesome fa-peertube;PeerTube;';
        $default[] = 'forkawesome fa-pixelfed;Pixelfed;';
        $default[] = 'fab fa-mastodon;Mastodon;';

        printf(
            '<textarea style="white-space: pre-wrap;width: 90%%;height:18em;" id="sunflower_social_media_profiles" name="sunflower_social_media_options[sunflower_social_media_profiles]">%s</textarea>',
            (isset($this->options['sunflower_social_media_profiles']) && $this->options['sunflower_social_media_profiles'] != '') ? $this->options['sunflower_social_media_profiles'] : implode("\n", $default)
        );
        echo '<div><a href="https://sunflower-theme.de/documentation/setup/#social-media-profile" target="_blank">Mehr zu den Einstellungen in der Dokumentation</a> und
        <a href="https://fontawesome.com/icons?d=gallery&p=2&m=free" target="_blank">alle möglichen Icons bei Fontawesome</a>.<br>
       Pro Zeile ein Social-Media-Profil<br>
       Format: Fontawesome-Klasse; Title-Attribut; URL<br>
       Wenn die URL fehlt, wird nichts verlinkt.

        </div>';
    }

    public function sunflower_open_graph_fallback_image(): void
    {
        // add media selector button
        wp_enqueue_media();
        wp_enqueue_script(
            'sunflower-admin-media',
            get_template_directory_uri() . '/assets/js/admin-media.js',
            ['jquery', 'jquery-ui-tabs', 'media-upload'],
            _S_VERSION
        );
        wp_localize_script('sunflower-admin-media', 'texts', [
            'select_image' => __('Select Open Graph Fallback Image', 'sunflower'),
        ]);

        printf(
            '<input type="text" id="sunflower_open_graph_fallback_image" name="sunflower_social_media_options[sunflower_open_graph_fallback_image]" size="%s" value="%s">',
            min(strlen((string) $this->options['sunflower_open_graph_fallback_image']), 120),
            $this->options['sunflower_open_graph_fallback_image'] ?? ''
        );

        printf(
            '<input type="button" id="sunflower_open_graph_fallback_image_button" class="button" value="%s"" />',
            __('Open Media Library', 'sunflower')
        );

        printf(
            '<p class="sunflower-help">%s</p>',
            __('This fallback image will be used when sharing pages and posts and no featured image has been selected.', 'sunflower')
        );
    }
}

if (is_admin()) {
    $my_settings_page = new SunflowerSocialMediaSettingsPage();
}

function get_sunflower_social_media_profiles()
{
    $profiles = block_core_social_link_services();

    $return = '';

    $lines = explode("\n", (string) get_sunflower_setting('sunflower_social_media_profiles'));
    foreach ($lines as $line) {
        $line = trim($line);
        @[$class, $title, $url] = explode(';', $line);
        if (! isset($url)) {
            continue;
        }

        if ($url === '') {
            continue;
        }

        $return .= sprintf(
            '<a href="%1$s" target="_blank" title="%3$s" class="social-media-profile" rel="me"><i class="%2$s"></i></a>',
            $url,
            $class,
            $title
        );
    }

    return $return;
}
