<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package sunflower
 */

?>

<section class="no-results not-found">
	<header class="page-header">
		<h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'sunflower' ); ?></h1>
	</header><!-- .page-header -->

	<div class="page-content">
		<?php
			_e( 'No upcoming events.', 'sunflower' );
		?>
	</div><!-- .page-content -->
</section><!-- .no-results -->
