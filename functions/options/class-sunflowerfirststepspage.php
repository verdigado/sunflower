<?php
/**
 * Class for the "First Steps" settings page.
 *
 * @package sunflower
 */

/**
 * The class itself.
 */
class SunflowerFirstStepsPage {

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
		add_action( 'admin_init', $this->sunflower_first_steps_page_init( ... ) );
	}

	/**
	 * Add options page
	 */
	public function sunflower_add_plugin_page(): void {
		add_menu_page(
			__( 'Sunflower', 'sunflower' ),
			__( 'Sunflower', 'sunflower' ),
			'edit_pages',
			'sunflower_admin',
			null,
			'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjxzdmcKICAgeG1sbnM6ZGM9Imh0dHA6Ly9wdXJsLm9yZy9kYy9lbGVtZW50cy8xLjEvIgogICB4bWxuczpjYz0iaHR0cDovL2NyZWF0aXZlY29tbW9ucy5vcmcvbnMjIgogICB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiCiAgIHhtbG5zOnN2Zz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiAgIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIKICAgeG1sbnM6c29kaXBvZGk9Imh0dHA6Ly9zb2RpcG9kaS5zb3VyY2Vmb3JnZS5uZXQvRFREL3NvZGlwb2RpLTAuZHRkIgogICB4bWxuczppbmtzY2FwZT0iaHR0cDovL3d3dy5pbmtzY2FwZS5vcmcvbmFtZXNwYWNlcy9pbmtzY2FwZSIKICAgdmVyc2lvbj0iMS4xIgogICBpZD0ic3ZnMiIKICAgdmlld0JveD0iMCAwIDMwMDAuMDAwMSAzMDIxLjUzMDQiCiAgIGhlaWdodD0iMzAyMS41MzAzIgogICB3aWR0aD0iMzAwMCIKICAgc29kaXBvZGk6ZG9jbmFtZT0ic29ubmVuYmx1bWUuc3ZnIgogICBpbmtzY2FwZTp2ZXJzaW9uPSIwLjkyLjUgKDIwNjBlYzFmOWYsIDIwMjAtMDQtMDgpIj4KICA8c29kaXBvZGk6bmFtZWR2aWV3CiAgICAgcGFnZWNvbG9yPSIjZmZmZmZmIgogICAgIGJvcmRlcmNvbG9yPSIjNjY2NjY2IgogICAgIGJvcmRlcm9wYWNpdHk9IjEiCiAgICAgb2JqZWN0dG9sZXJhbmNlPSIxMCIKICAgICBncmlkdG9sZXJhbmNlPSIxMCIKICAgICBndWlkZXRvbGVyYW5jZT0iMTAiCiAgICAgaW5rc2NhcGU6cGFnZW9wYWNpdHk9IjAiCiAgICAgaW5rc2NhcGU6cGFnZXNoYWRvdz0iMiIKICAgICBpbmtzY2FwZTp3aW5kb3ctd2lkdGg9IjE4NDgiCiAgICAgaW5rc2NhcGU6d2luZG93LWhlaWdodD0iMTAxNiIKICAgICBpZD0ibmFtZWR2aWV3MTIiCiAgICAgc2hvd2dyaWQ9ImZhbHNlIgogICAgIGlua3NjYXBlOnpvb209IjAuMDY5MDM2NzEiCiAgICAgaW5rc2NhcGU6Y3g9Ii0yOTIzLjEwNDciCiAgICAgaW5rc2NhcGU6Y3k9Ijg1NS4yMjk2OCIKICAgICBpbmtzY2FwZTp3aW5kb3cteD0iMTc1MiIKICAgICBpbmtzY2FwZTp3aW5kb3cteT0iMjciCiAgICAgaW5rc2NhcGU6d2luZG93LW1heGltaXplZD0iMSIKICAgICBpbmtzY2FwZTpjdXJyZW50LWxheWVyPSJzdmcyIiAvPgogIDxkZWZzCiAgICAgaWQ9ImRlZnM0Ij4KICAgIDxzdHlsZQogICAgICAgdHlwZT0idGV4dC9jc3MiCiAgICAgICBpZD0ic3R5bGU0MTQwIj48IVtDREFUQVsKICAgIC5maWwwIHtmaWxsOiM0N0I1RTc7ZmlsbC1ydWxlOm5vbnplcm99CiAgICAuZmlsMiB7ZmlsbDojRkVGRUZFO2ZpbGwtcnVsZTpub256ZXJvfQogICAgLmZpbDEge2ZpbGw6I0ZGRjIyNTtmaWxsLXJ1bGU6bm9uemVyb30KICAgXV0+PC9zdHlsZT4KICAgIDxzdHlsZQogICAgICAgdHlwZT0idGV4dC9jc3MiCiAgICAgICBpZD0ic3R5bGU0MjI5Ij48IVtDREFUQVsKICAgIC5maWwwIHtmaWxsOiNGRkYyMjU7ZmlsbC1ydWxlOm5vbnplcm99CiAgIF1dPjwvc3R5bGU+CiAgPC9kZWZzPgogIDxtZXRhZGF0YQogICAgIGlkPSJtZXRhZGF0YTciPgogICAgPHJkZjpSREY+CiAgICAgIDxjYzpXb3JrCiAgICAgICAgIHJkZjphYm91dD0iIj4KICAgICAgICA8ZGM6Zm9ybWF0PmltYWdlL3N2Zyt4bWw8L2RjOmZvcm1hdD4KICAgICAgICA8ZGM6dHlwZQogICAgICAgICAgIHJkZjpyZXNvdXJjZT0iaHR0cDovL3B1cmwub3JnL2RjL2RjbWl0eXBlL1N0aWxsSW1hZ2UiIC8+CiAgICAgICAgPGRjOnRpdGxlPjwvZGM6dGl0bGU+CiAgICAgIDwvY2M6V29yaz4KICAgIDwvcmRmOlJERj4KICA8L21ldGFkYXRhPgogIDxnCiAgICAgdHJhbnNmb3JtPSJtYXRyaXgoMTAuMDAwMDAxLDAsMCwxMC4wMDAwMDEsMCwtNzUwMi4wOTI5KSIKICAgICBpZD0ibGF5ZXIxIj4KICAgIDxnCiAgICAgICB0cmFuc2Zvcm09Im1hdHJpeCgwLjA0MjIxNzg0LDAsMCwwLjA0MjIxNzg1LDAuMDQyMjE3ODQsNzUwLjI1MTI4KSIKICAgICAgIGlkPSJnNDI0MCIKICAgICAgIHN0eWxlPSJjbGlwLXJ1bGU6ZXZlbm9kZDtmaWxsLXJ1bGU6ZXZlbm9kZDtpbWFnZS1yZW5kZXJpbmc6b3B0aW1pemVRdWFsaXR5O3NoYXBlLXJlbmRlcmluZzpnZW9tZXRyaWNQcmVjaXNpb247dGV4dC1yZW5kZXJpbmc6Z2VvbWV0cmljUHJlY2lzaW9uIj4KICAgICAgPGcKICAgICAgICAgaWQ9IkxheWVyX3gwMDIwXzEtNCI+CiAgICAgICAgPG1ldGFkYXRhCiAgICAgICAgICAgaWQ9IkNvcmVsQ29ycElEXzBDb3JlbC1MYXllci0wIiAvPgogICAgICAgIDxwYXRoCiAgICAgICAgICAgc3R5bGU9ImZpbGw6I2ZmZjIyNTtmaWxsLXJ1bGU6bm9uemVybyIKICAgICAgICAgICBjbGFzcz0iZmlsMCIKICAgICAgICAgICBkPSJtIDcxMDUsMzEyNiBjIC0zLC0xOCAtNDksLTM1IC04MSwtNjEgLTEwNiwtODggLTQ4NSwtNDYxIC0xMjMwLC00MzcgLTk0LDMgLTE4MCwxMiAtMjU5LDI1IDE1OSwtNTEgMjQxLC04MiAyODksLTEwMSA1OCwtMjMgNDM4LC0xNTMgNjQzLC04MjIgMTcsLTU3IDY3LC0xOTUgNTUsLTIwNSAtMTUsLTEzIC0xNDMsMTAgLTI3Miw4OSAtMTMwLDc5IC01NjUsMTQ4IC02MTcsMTU2IC0zMCw1IC0xNTgsLTE0IC0zNTAsODYgMjE1LC0yMjEgNDQ0LC01MzYgNTA1LC05NzcgMjksLTIxMiAxNywtMjk4IDgsLTI5NyAtMTMsMSAtMjAsMSAtMzYsNiAtNDgsMTQgLTk3LDU1IC0yNTUsMTYxIC0xNTksMTA2IC0zMjcsMjIzIC0zMjcsMjIzIDAsMCA1OCwtNTc2IDQwLC01OTAgLTE4LC0xMyAtMzQ1LC0xMCAtODQ3LDU1MCAwLDAgNTcsLTQ3OSAtMzcsLTY2MCAwLDAgLTI2LC0xMzEgLTQ3LC0xOTkgLTEwLC0zNCAtMzcsLTcwIC00MSwtNjcgLTUsNCAtNTAsMTEgLTEyMSw5MyAtMTExLDEyNyAtNjI2LDI4MiAtNzExLDk0OSBDIDMzNDksNzc5IDMyMTIsNDE5IDI4NzQsMjM3IDI2OTYsNjMgMjYxMCwyIDI1OTEsLTEgaCAtMyBjIDAsMCAwLDAgMCwwIC05LDQgLTUsODcgLTM0LDI3NSAtMzMsMjEwIC02MiwyNzYgLTYyLDI3NiAwLDAgLTM1NCwtMzEyIC0zNjMsLTMyMSAtMTcsLTE2IC0yMzMsNjY4IC0xNjksMTAwOSAwLDAgLTExMiwtODcgLTMxNiwtMTkyIC0yMzIsLTExOSAtNjIxLC0xNDAgLTYxOSwtMTIxIDUsMzEgMzI5LDY2NCA0NjUsODg3IDAsMCAtODczLC0xNTAgLTg1OCwtOTUgNSwxOCAyNiw1MiA2MCwxOTggMCwwIC0zNDMsLTcgLTMzOCwyMSA1LDMyIDI2MCw0NTMgMjc5LDQ3NiAwLDAgLTI2NSw1MSAtMzIxLDU0IC0zOCwyIDE3Nyw1OTYgNzY3LDc0NCA4OSwyMiAxNzUsMzkgMjU3LDUxIC0xOTMsLTQgLTQwNywzNyAtNjUzLDEyOCAwLDAgLTE0Miw1NSAtMjE3LDExNSAtNzQsNTkgLTQ0MSwxMjEgLTQ2NywxNzUgdiA0IGMgMjMsNDggNDU2LDM0NSA0NTYsMzQ1IDAsMCAtMjYzLDEyOCAtMjgxLDE1MiAtMTIsMTcgLTIyLDMyIC0xOSwzNiAzLDUgMjEsMjYgNjYsNTQgMTA1LDY1IDEwNDAsMTYzIDEwOTgsMTQwIDU3LC0yMyAtNTA4LDYxMyAtNTE3LDkzMyAwLDAgOTMsNyAxNDUsMTEgNDAsMyAtMTcwLDQwMSAtMTMyLDM5OSAyNTQsLTExIDY3NSwtMjU2IDc1NCwtMzEzIDc5LC01NiAxODcsLTE0MCAyMTYsLTE2OCAyOSwtMjcgLTE4NCwzNTQgLTk3LDY5MCAwLDAgMjgsMjYwIDI5LDMxMyAxLDUyIDc1LDUzIDc1LDg4IC0xLDQzIDIwNiwtMTYzIDMwNCwtMjQyIDk4LC03OSAyMzEsLTIzMiAyOTUsLTMzMyA2NCwtMTAxIDcsNzg5IC0xOSw5NjAgLTgsNTUgNCw2MSAxMyw4MCAzLDYgOTQsLTcwIDEyMywtODkgNDMsLTI5IDQ4OSwtNjI4IDUxNywtNzc4IDAsMCAyMzgsMTA0NSAzMTgsMTE1NyAxMiwxNiAyMiwzNCAzMiwzOCBoIDcgYyAyLC0xIDQsLTMgNiwtNiAyNCwtMzcgNTksLTEzNSA4NiwtMTQzIDM0LC0xMCAxMzUsLTIzIDE4OCwtMTcyIDUzLC0xNDkgMTQ4LC0yOTQgMTkzLC02ODEgMCwwIDUwNiw2NDUgNTcxLDcwNSAyNiwyNCAxNjMsLTIwNSAyMjQsLTU3MiA2MSwtMzY3IC03MCwtODYwIC0xNTIsLTg3NyAwLDAgLTM0LC01NCA2MSwtMjAgNTUsMjAgMjY1LDM4NSA3NTAsNjYyIDgyLDQ3IDI1NSw0NyAyNTYsNDQgMzksLTEwOSAtMzQyLC0xMTA1IC0zNDIsLTExMDUgMCwwIDksLTExIDg2LDIwIDc2LDMxIDgwNCw1MDIgMTI0NSwyODEgODksLTQ1IDk1LC01NCA5NSwtNTQgNTEsLTIxNyAtMzM4LC02MjggLTU3NywtODE0IDAsMCA2MjcsLTI1IDcyMywtMTkyIDAsMCAxMTYsLTQyIDE3NSwtMTI0IDEwLC0xNCAtNjcsLTE1NyAtMTU4LC0yMjAgLTIzNywtMTY0IC0yODgsLTIyNiAtMjg4LC0yMjYgMCwwIDY2LC04MCAxNjIsLTExMCA5NiwtMzAgNDA0LC0yMjAgMzIyLC0zMTQgMCwwIDkzLC04MSA5NiwtMTA3IHYgLTIgMCB6IG0gLTIwMjcsLTEyIGMgLTk3LDE2IC0zNDYsMjIgLTExNiwyMTggMCwwIDQyLDMgLTcxLDcwIC0xMTMsNjcgMjUyLDEwNiAyOTUsMTU1IDQzLDQ5IC0zNjIsLTQgLTM1NCwxMTcgOCwxMjIgLTcwLDQ1IDE5OSwzMzIgMCwwIC0xNjIsLTM4IC0xODAsLTE1IC0xOSwyMyAzNTQsMzMyIDM1Miw0MjAgMCwwIC03Niw1MyAtMTA4LDcgLTMyLC00NiAtMjU4LC0yOTYgLTI5NCwtMzAzIC0yOCwtNSAtODAsNyAtMTA1LDU2IDAsMCA5OSwyMzAgNjgsMjM2IC0zMSw2IC0xODUsLTEwMiAtMTg1LC0xMDIgMCwwIC0xMDEsNDQgLTY4LDEzNiAzMyw5MiAzMDgsNDAzIDI5Miw0MjIgMCwwIC0yMyw1MSAtNTksNDggLTM1LC00IC0yMjUsLTM0OCAtMjcyLC0zNjEgLTQ3LC0xMyA1NSwzMjEgNTUsMzIxIDAsMCAtMTcxLC0xODEgLTIxNywtMjE4IC00NiwtMzggLTUyLDQwIC01Miw0MCBsIDUwLDM4MiBjIDAsMCAtNTM0LC03OTYgLTQwMywxNTMgMCwwIC0xMiw1NCAtMzAsNTIgLTE4LC0yIC0xMzEsLTMxNCAtMTE4LC00MDAgMTMsLTg2IC05NCwyMzcgLTk0LDIzNyAwLDAgLTE2NywtNjY1IC01MTQsMTcgMCwwIC0yNCwtMjU4IC03OCwtMzAyIC01MywtNDQgLTk3LC02OCAtMTExLC02MCAtMTMsOCAtMTM2LDEyNSAtMTg0LDEzNyAtNDgsMTEgMzgsLTE1NCAtMzMsLTIwMSAwLDAgNiwtNDYgLTM1LC0xMTIgLTQxLC02NiAtODEsLTQyIC0zMjAsMjE2IDAsMCAxMDAsLTI0NCA5OSwtMzI5IC0xLC04NCAtOTQsLTEwNCAtOTQsLTEwNCAwLDAgLTE3NCwxMzIgLTIyNCwxMTYgLTUxLC0xNiA4MywtMTc4IDg2LC0yMjMgMSwtMTQgLTE5LC05IC0xOSwtOSAwLDAgLTIxLC0xNSAtMTk2LDg3IGwgLTI4MywxODAgYyAtMjEsLTEzIC00OSwtMTAgLTQwLC03OCAyLC0xOSA1MjksLTI2NyA1MDYsLTI5MyAtMjMsLTI2IC0xOTksLTY5IC0xOTksLTY5IDAsMCAxMTAsLTEzMyA3NywtMTQ4IC0zMywtMTQgLTI3MywtMzQgLTI3MywtMzQgMCwwIC00MiwtNjYgLTMzLC01MyA4LDEzIDIzNSwtMzYgMjM1LC0zNiBsIC0xNTIsLTExOSBjIDAsMCAzODMsLTY3IDQ1LC0yNzEgLTEyNSwtNzUgLTI1NiwtMTI3IC0zOTcsLTE1MiAzNjEsMTggNjAwLC03MyA1NTksLTIwNiAwLDAgLTExLC00OCAtMzcxLC0xNzkgMCwwIDEwLC00MyAzNCwtNDkgMjQsLTYgMzk4LDEzMSAzNzIsMTA5IC0yNywtMjIgLTE2NCwtMTc0IC0xMzksLTE3MyAyNSwxIDIwMCw3NSAyNTksMjEgNTYsLTUyIDkxLC0xNDggMTI4LC0xNjkgMzcsLTIxIC05OCwtMTQ1IDgwLC04OCAwLDAgMTIwLC02NyAyNSwtMTc4IC05NSwtMTExIC0zOTUsLTQ2NiA5NSwtMzcgMTE3LDEwMyA0MTAsLTIxMCAzODYsLTM4MCAwLDAgMTE3LDExOCAxNDIsMTMxIDI1LDEzIDE4OSwzMiAyMjYsLTI1IDE3LC0yNiAtNjAsLTI2OCAtMTAsLTM3NSAwLDAgMjEsLTIgNDEsMjEgMjAsMjMgMjIsMjc1IDExMSwzMjMgMCwwIDEwMCwtNTkgMTE3LC0xMDIgMTcsLTQ0IC0yNywyNzMgMjcyLDEzNiAwLDAgMTQ2LC0xMzAgMTg4LC0xMzMgNDIsLTQgLTMxLDE1NCA1LDIxMCAzNyw1NiAxNjAsOTQgMTk5LDI0IDM5LC03MCAyMjYsLTIxMSAyNDEsLTIwNSAxNSw2IC05NSwyOTggLTEwOCwzMjcgLTE4LDQyIC0zMSwxMDQgLTMxLDEwNCAwLDAgMTQsODIgMTE2LDEwNCAwLDAgMTkzLC0xMTMgMzkzLC0yNTkgLTIxLDI1IC00Miw1MiAtNjQsODAgMCwwIC0yNTksMjY3IC0yNjAsMzA1IDAsMzkgODksMTI1IDE2Miw3NiA3MywtNDkgMjQ4LC0xMjEgMjY0LC0xMDkgMTUsMTMgNDMsNDkgNyw3MCAtMzcsMjEgLTI0MSwxMjggLTI0MSwxMjggMCwwIDIzMywxNiA1MTksLTQyIC0zMDksMTI1IC00MTYsMzE2IC00MTMsMzM0IDksNjEgNDg5LC02MyA1MDIsLTMzIDY5LDE1NiAtMTUyLDYwIC0yNDksNzYgeiIKICAgICAgICAgICBpZD0icGF0aDQyMzMiCiAgICAgICAgICAgaW5rc2NhcGU6Y29ubmVjdG9yLWN1cnZhdHVyZT0iMCIgLz4KICAgICAgPC9nPgogICAgPC9nPgogIDwvZz4KPC9zdmc+Cg==',
			null
		);

		add_submenu_page(
			'sunflower_admin',
			__( 'First steps', 'sunflower' ),
			__( 'First steps', 'sunflower' ),
			'edit_pages',
			'sunflower_admin',
			$this->create_sunflower_admin_page( ... )
		);
	}

	/**
	 * Admin page callback
	 */
	public function create_sunflower_admin_page(): void {
		$this->options = get_option( 'sunflower_first_steps_options' );

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'About Sunflower', 'sunflower' ); ?></h1>

			<h2>Erste Schritte</h2>
			<div class="notice-info sunflower-settings">
				Eine ausführliche Dokumentation gibt es unter
				<a href="https://sunflower-theme.de/documentation/" target="_blank">https://sunflower-theme.de/documentation/</a>.
			</div>

			<form method="post" action="options.php">
			<?php
				// This prints out all hidden setting fields.
				settings_fields( 'sunflower_first_steps_option_group' );
			do_settings_sections( 'sunflower_admin' );
			submit_button();
			?>
			</form>

			<h2>Umzug von Urwahl3000</h2>

			Widgets, Menüs und Termine werden automatisch importiert.<br>
			<a href="https://sunflower-theme.de/documentation/urwahl3000" target="_blank">
			Hier gibt es mehr Info über den Umzug von Urwahl3000</a>.

			<h2><?php esc_attr_e( 'Settings', 'sunflower' ); ?></h2>
			Bitte siehe links im Menü, welche Unterpunkte es gibt.

		</div>
		<?php
	}

	/**
	 * Register and add first_steps settings
	 */
	public function sunflower_first_steps_page_init(): void {
		register_setting(
			'sunflower_first_steps_option_group', // Option group.
			'sunflower_first_steps_options', // Option name.
			$this->sanitize( ... ) // Sanitized argmuments.
		);

		add_settings_section(
			'sunflower-first-steps', // The section ID.
			__( 'Activate Theme', 'sunflower' ),
			$this->print_section_info( ... ), // The callback function.
			'sunflower_admin' // The page ID.
		);

		add_settings_field(
			'sunflower_terms_of_use', // The field ID.
			__( 'Sunflower Terms of Use', 'sunflower' ),
			$this->sunflower_checkbox_terms_conditions_callback( ... ), // The callback function.
			'sunflower_admin', // The page ID.
			'sunflower-first-steps', // The section ID.
			array( 'sunflower_terms_of_use', __( 'Accept Terms of Use', 'sunflower' ) )
		);
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys.
	 */
	public function sanitize( $input ) {
		$new_input = array();

		// Sanitize every element in the input array.
		foreach ( $input as $key => $value ) {
			if ( isset( $input[ $key ] ) ) {
				$new_input[ $key ] = sanitize_text_field( $value );
			}
		}

		return $new_input;
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info() {
	}

	/**
	 * Print checkbox for terms of use with notice
	 *
	 * @param array $args The field arguments.
	 */
	public function sunflower_checkbox_terms_conditions_callback( $args ): void {
		$field = $args[0];
		$label = $args[1];

		printf(
			'<label>
                    <input type="checkbox" id="%1$s" name="sunflower_first_steps_options[%1$s]" value="checked" %2$s />
                    %3$s
                </label>',
			esc_attr( $field ),
			isset( $this->options[ $field ] ) ? 'checked' : '',
			esc_attr( $label )
		);

		printf(
			'<p id="help-sunflower-terms-condition" class="notice-info sunflower-settings sunflower-help">%s</p>',
			'Die in der Designvorlage verwendeten Grafiken sind als beim DPMA eingetragene Marken (u.a. Registernummer <a href="https://register.dpma.de/DPMAregister/marke/register/3020162092606/DE" target="_blank">302016209260</a> und <a href="https://register.dpma.de/DPMAregister/marke/register/3020162095842/DE" target="_blank">302016209584</a>)
            durch das Markenrecht sowie durch Namens- und Kennzeichnungsrechte geschützt. <br />
            Grundsätzlich bedarf es einer Erlaubnis, die eingetragenen
            Schutzrechte zu verwenden (Lizenz). Die Lizenz für Gliederungen ist beschränkt auf ihre Gebiete – digital unbeschränkt – und unbefristet
            bis zu einem Widerruf. <br />
            Gebietsverbänden ist es erlaubt, Lizenzen zum Zweck der eigenen Präsentation auch Kandidat*innen mit der Einschränkung
            einzuräumen, dass der Anschein eigene Meinungen und Äußerungen seien Positionen des Bundesverbandes oder anderer Gebietsverbände vermieden
            werden muss. Auch hier besteht ein Widerrufsvorbehalt zugunsten des Bundesverbandes.'
		);
	}
}

if ( is_admin() ) {
	$sunflower_settings_page = new SunflowerFirstStepsPage();
}
