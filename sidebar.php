<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package sunflower
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>
<div class="container-fluid bg-darkgreen p-5">
	<aside id="secondary" class="widget-area container">
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	</aside>
</div>
