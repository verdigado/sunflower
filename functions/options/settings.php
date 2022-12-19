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
		add_action( 'admin_menu', array( $this, 'sunflower_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'sunflower_page_init' ) );
	}

	/**
	 * Add options page
	 */
	public function sunflower_add_plugin_page() {
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
			array( $this, 'create_sunflower_admin_page' )
		);

		add_submenu_page(
			'sunflower_admin',
			__( 'Settings', 'sunflower' ),
			__( 'Settings', 'sunflower' ),
			'edit_pages',
			'sunflower_settings',
			array( $this, 'create_sunflower_settings_page' )
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
		$this->options = get_option( 'sunflower_options' );
		?>
		<div class="wrap">
			<h1><?php _e( 'Sunflower Settings', 'sunflower' ); ?></h1>
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
						<?php } ?>

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
	public function sunflower_page_init() {
		register_setting(
			'sunflower_option_group', // Option group
			'sunflower_options', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		add_settings_section(
			'sunflower_layout', // ID
			__( 'Layout', 'sunflower' ), // Title
			array( $this, 'print_section_info' ), // Callback
			'sunflower-setting-admin' // Page
		);

		add_settings_field(
			'excerps_length', // ID
			__( 'Excerpt length (words)', 'sunflower' ), // Title
			array( $this, 'excerpt_length_callback' ), // Callback
			'sunflower-setting-admin', // Page
			'sunflower_layout' // Section
		);

		add_settings_field(
			'sunflower_show_related_posts', // ID
			__( 'show related posts', 'sunflower' ), // Title
			array( $this, 'sunflower_checkbox_callback' ), // Callback
			'sunflower-setting-admin', // Page
			'sunflower_layout', // Section
			array( 'sunflower_show_related_posts', __( 'show related posts', 'sunflower' ) )
		);

		add_settings_field(
			'sunflower_show_author', // ID
			__( 'show author of posts', 'sunflower' ), // Title
			array( $this, 'sunflower_checkbox_callback' ), // Callback
			'sunflower-setting-admin', // Page
			'sunflower_layout', // Section
			array( 'sunflower_show_author', __( 'show author of posts', 'sunflower' ) )
		);

		add_settings_field(
			'sunflower_hide_prev_next', // ID
			__( 'hide previous and next links', 'sunflower' ), // Title
			array( $this, 'sunflower_checkbox_callback' ), // Callback
			'sunflower-setting-admin', // Page
			'sunflower_layout', // Section
			array( 'sunflower_hide_prev_next', __( 'hide previous and next links', 'sunflower' ) )
		);

		add_settings_field(
			'sunflower_contact_form_to', // ID
			__( 'to-field for contact-forms', 'sunflower' ), // Title
			array( $this, 'sunflower_contact_form_to' ), // Callback
			'sunflower-setting-admin', // Page
			'sunflower_layout', // Section
			array( 'sunflower_contact_form_to', __( 'to-field for contact-forms', 'sunflower' ) )
		);

		add_settings_field(
			'sunflower_main_menu_item_is_placeholder', // ID
			__( 'items in menu', 'sunflower' ), // Title
			array( $this, 'sunflower_checkbox_callback' ), // Callback
			'sunflower-setting-admin', // Page
			'sunflower_layout', // Section
			array( 'sunflower_main_menu_item_is_placeholder', __( 'items with href=# in the main menu are placeholders for submenu', 'sunflower' ) )
		);

		add_settings_field(
			'sunflower_header_layout', // ID
			__( 'Use this header layout', 'sunflower' ), // Title
			array( $this, 'sunflower_header_layout' ), // Callback
			'sunflower-setting-admin', // Page
			'sunflower_layout', // Section
			array( 'sunflower_header_layout', __( 'Use this header layout', 'sunflower' ) )
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

	public function print_section_info_sharers() {
		_e( 'Show share buttons on single page', 'sunflower' );
	}

	public function sunflower_checkbox_callback( $args ) {
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

	public function excerpt_length_callback() {
		printf(
			'<input type="text" id="excerpt_length" name="sunflower_options[excerpt_length]" value="%s" />',
			isset( $this->options['excerpt_length'] ) ? esc_attr( $this->options['excerpt_length'] ) : ''
		);
	}

	public function sunflower_contact_form_to() {
		printf(
			'<input type="email" id="sunflower_contact_form_to" name="sunflower_options[sunflower_contact_form_to]" value="%s" />',
			isset( $this->options['sunflower_contact_form_to'] ) ? esc_attr( $this->options['sunflower_contact_form_to'] ) : ''
		);
	}

	public function sunflower_header_layout() {
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

