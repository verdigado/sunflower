<?php
/**
 * Methods for Sunflower placeholder menu
 *
 * @package sunflower
 */

/**
 * Render placeholder menu if option is set.
 */
function sunflower_add_menu_item_is_placeholder_script() {
	if ( ! sunflower_get_setting( 'sunflower_main_menu_item_is_placeholder' ) ) {
		return;
	}
	?>
	<script>
		jQuery('.dropdown-toggle[href=#]').click(function(){
			jQuery(this).next().click();
			return false;
		})
	</script>
	<?php
}

add_action( 'wp_footer', 'sunflower_add_menu_item_is_placeholder_script' );

function sunflower_enqueue_scripts() {
	wp_enqueue_script(
		'nav-overflow',
		get_template_directory_uri() . '/assets/js/nav-overflow.js',
		array(),
		null,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'sunflower_enqueue_scripts' );
