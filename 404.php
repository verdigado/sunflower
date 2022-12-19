<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package sunflower
 */

get_header();
?>

<div id="content" class="container">
		<div class="row">
			<div class="col-12">
				<main id="primary" class="site-main archive">
					<header class="page-header mb-5 text-center">
						<?php
						printf( '<h1 class="page-title">%s</h1>', __( 'Oops! That page can&rsquo;t be found.', 'sunflower' ) );
						?>
					</header><!-- .page-header -->

					<div class="col-12 text-center mb-5    ">
					<?php
					esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'sunflower' );
					get_search_form();
					?>
					</div>
				</main><!-- #main -->
			</div>
		</div>
</div>
<?php
get_footer();
