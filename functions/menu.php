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
