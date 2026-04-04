<?php
/**
 * Welcome page shown once after a fresh theme activation.
 *
 * On a genuine first install the activation hook sets a short-lived transient
 * that causes the next admin page-load to redirect here.  The page offers a
 * single "Set up example pages" button which installs demo content via AJAX
 * and then continues to the First Steps settings page.
 *
 * @package Sunflower 26
 */

// ---------------------------------------------------------------------------
// Activation helper – called from activation.php
// ---------------------------------------------------------------------------

/**
 * Decide what to do with demo content directly after theme activation.
 *
 * Always show the welcome page after activation, where users can either
 * install demo content or skip directly to first steps.
 */
function sunflower_schedule_welcome_or_skip(): void {
	set_transient( 'sunflower_welcome_redirect', true, 5 * MINUTE_IN_SECONDS );
}

// ---------------------------------------------------------------------------
// Redirect
// ---------------------------------------------------------------------------

/**
 * Once per fresh activation: redirect the admin to the welcome page.
 * Fires on admin_init so headers are not yet sent.
 */
function sunflower_maybe_redirect_to_welcome(): void {
	if ( ! get_transient( 'sunflower_welcome_redirect' ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	// Avoid redirect loop when already on the welcome page.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['page'] ) && 'sunflower_welcome' === sanitize_key( $_GET['page'] ) ) {
		return;
	}

	delete_transient( 'sunflower_welcome_redirect' );
	wp_safe_redirect( admin_url( 'admin.php?page=sunflower_welcome' ) );
	exit;
}
add_action( 'admin_init', 'sunflower_maybe_redirect_to_welcome' );

/**
 * For non-fresh installs: redirect to first steps page.
 */
function sunflower_maybe_redirect_to_first_steps(): void {
	if ( ! get_transient( 'sunflower_first_steps_redirect' ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Avoid redirect loop when already on the first steps page.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['page'] ) && 'sunflower_admin' === sanitize_key( $_GET['page'] ) ) {
		delete_transient( 'sunflower_first_steps_redirect' );
		return;
	}

	delete_transient( 'sunflower_first_steps_redirect' );
	wp_safe_redirect( admin_url( 'admin.php?page=sunflower_admin' ) );
	exit;
}
add_action( 'admin_init', 'sunflower_maybe_redirect_to_first_steps', 11 );

// ---------------------------------------------------------------------------
// Page registration
// ---------------------------------------------------------------------------

/**
 * Register the welcome page as a hidden admin page (accessible by URL only,
 * not listed in any menu).
 */
function sunflower_register_welcome_page(): void {
	add_submenu_page(
		'',
		__( 'Willkommen', 'sunflower' ),
		__( 'Willkommen', 'sunflower' ),
		'manage_options',
		'sunflower_welcome',
		'sunflower_render_welcome_page'
	);
}
add_action( 'admin_menu', 'sunflower_register_welcome_page' );

// ---------------------------------------------------------------------------
// Page rendering
// ---------------------------------------------------------------------------

/**
 * Output the welcome page HTML.
 */
function sunflower_render_welcome_page(): void {
	$first_steps_url = admin_url( 'admin.php?page=sunflower_admin' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Willkommen', 'sunflower' ); ?></h1>

		<div class="card" style="max-width:680px;padding:28px 28px 24px;margin-top:24px;">

			<p style="font-size:1.05em;margin-top:0">
				<?php esc_html_e( 'Damit du schnell starten kannst, empfehlen wir, die Beispielseiten einzurichten.', 'sunflower' ); ?>
			</p>

			<p style="margin-bottom:0">
				<button id="sunflower-install-demo"
						class="button button-primary button-hero"
						style="display:inline-flex;align-items:center;gap:8px">
					<span id="sunflower-demo-spinner"
							class="spinner"
							style="float:none;margin:0;display:none;visibility:visible"></span>
					<span id="sunflower-demo-label">
						<?php esc_html_e( 'Beispielseiten einrichten', 'sunflower' ); ?>
					</span>
				</button>
				&nbsp;
				<a href="<?php echo esc_url( $first_steps_url ); ?>"
					class="button button-secondary button-hero">
					<?php esc_html_e( 'Überspringen', 'sunflower' ); ?>
				</a>
			</p>

			<p id="sunflower-demo-msg"
				style="display:none;margin-top:12px;color:#d63638;"></p>
		</div>
	</div>

	<script>
	(function () {
		var btn     = document.getElementById( 'sunflower-install-demo' );
		var spinner = document.getElementById( 'sunflower-demo-spinner' );
		var label   = document.getElementById( 'sunflower-demo-label' );
		var msg     = document.getElementById( 'sunflower-demo-msg' );

		var nonce   = <?php echo wp_json_encode( wp_create_nonce( 'sunflower_install_demo' ) ); ?>;
		var ajaxUrl = <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>;
		var nextUrl = <?php echo wp_json_encode( $first_steps_url ); ?>;

		var txtInstall  = <?php echo wp_json_encode( __( 'Beispielseiten einrichten', 'sunflower' ) ); ?>;
		var txtWorking  = <?php echo wp_json_encode( __( 'Wird eingerichtet ...', 'sunflower' ) ); ?>;
		var txtNetError = <?php echo wp_json_encode( __( 'Netzwerkfehler - bitte erneut versuchen.', 'sunflower' ) ); ?>;
		var txtError    = <?php echo wp_json_encode( __( 'Es ist ein Fehler aufgetreten. Bitte erneut versuchen.', 'sunflower' ) ); ?>;

		btn.addEventListener( 'click', function () {
			// Keep button height stable while spinner/text switch to avoid visual jump.
			if ( ! btn.dataset.lockedHeight ) {
				btn.style.height = btn.offsetHeight + 'px';
				btn.dataset.lockedHeight = '1';
			}

			btn.disabled         = true;
			spinner.style.display = 'inline-block';
			label.textContent    = txtWorking;
			msg.style.display    = 'none';

			var fd = new FormData();
			fd.append( 'action', 'sunflower_install_demo_content' );
			fd.append( '_nonce', nonce );

			fetch( ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' } )
				.then( function ( r ) { return r.json(); } )
				.then( function ( data ) {
					if ( data.success ) {
						window.location.href = nextUrl;
					} else {
						btn.disabled          = false;
						spinner.style.display = 'none';
						label.textContent     = txtInstall;
						msg.textContent       = data.data || txtError;
						msg.style.display     = 'block';
					}
				} )
				.catch( function () {
					btn.disabled          = false;
					spinner.style.display = 'none';
					label.textContent     = txtInstall;
					msg.textContent       = txtNetError;
					msg.style.display     = 'block';
				} );
		} );
	}() );
	</script>
	<?php
}

// ---------------------------------------------------------------------------
// AJAX handler
// ---------------------------------------------------------------------------

/**
 * Install demo content via AJAX.
 * Called when the user clicks "Set up example pages" on the welcome page.
 */
function sunflower_ajax_install_demo_content(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( __( 'Unzureichende Berechtigungen.', 'sunflower' ) );
	}
	if ( ! check_ajax_referer( 'sunflower_install_demo', '_nonce', false ) ) {
		wp_send_json_error( __( 'Sicherheitspruefung fehlgeschlagen.', 'sunflower' ) );
	}

	// Ensure admin includes are available (needed by image processing functions).
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$image_ids = sunflower_import_demo_images();
	// Explicit user action via welcome screen should always run demo setup.
	sunflower_create_demo_content( $image_ids, true );

	wp_send_json_success();
}
add_action( 'wp_ajax_sunflower_install_demo_content', 'sunflower_ajax_install_demo_content' );
