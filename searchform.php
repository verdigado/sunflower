<?php
/**
 * Custom search form template.
 *
 * Uses <button> for submit so FontAwesome icon can be rendered inside.
 * Supports dynamic placeholder via $sunflower_search_placeholder global
 * (set by widget_title filter when a search widget provides a title).
 *
 * @package Sunflower 26
 */

global $sunflower_search_placeholder;

$sunflower_placeholder = ! empty( $sunflower_search_placeholder )
	? $sunflower_search_placeholder
	: __( 'Suche', 'sunflower' );

$sunflower_search_placeholder = '';
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<span class="screen-reader-text"><?php esc_html_e( 'Suche', 'sunflower' ); ?></span>
		<input type="search" class="search-field" placeholder="<?php echo esc_attr( $sunflower_placeholder ); ?>" value="<?php echo get_search_query(); ?>" name="s">
	</label>
	<button type="submit" class="search-submit"><i class="fa-solid fa-magnifying-glass"></i></button>
</form>
