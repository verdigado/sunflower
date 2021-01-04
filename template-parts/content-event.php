<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package sunflower
 */

?>

<a href="<?php echo esc_url( get_permalink() ); ?>" class="event-card" rel="bookmark">
<article id="post-<?php the_ID(); ?>" <?php post_class('mb-2 event'); ?>>
    <div class="row">
        <div class="col-3 d-flex align-items-center justify-content-around arvogruen">
            <?php
                $from = strToTime( get_post_meta( $post->ID, '_sunflower_event_from')[0] );
                $attribute = date('Y-m-d', $from );
                $weekday = date_i18n( 'l ', $from);
                $date = date_i18n( 'j. F Y', $from);
            ?>
            <div class="text-center">
                <div><?php echo $weekday; ?></div>
                <div class="date">
                    <time datetime="<?php echo $attribute; ?>">
                        <?php echo $date; ?>
                    </time>
                </div>
            </div>


        </div>
        <div class="col-6">
            <div class="">
                <header class="entry-header pt-2">
                    <?php
                        the_title( '<h2 class="h4">', '</h2>' );
                    ?>

                </header><!-- .entry-header -->

            
                <div class="entry-content">
                    <?php
                    the_excerpt(
                        sprintf(
                            wp_kses(
                                /* translators: %s: Name of current post. Only visible to screen readers */
                                __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'sunflower' ),
                                array(
                                    'span' => array(
                                        'class' => array(),
                                    ),
                                )
                            ),
                            wp_kses_post( get_the_title() )
                        )
                    );
                    ?>
                </div><!-- .entry-content -->

            </div>
        </div>
        <div class="col-3">
            <div class="post-thumbnail">
				<?php the_post_thumbnail(); ?>
			</div><!-- .post-thumbnail -->
        </div>
    </div>
</article><!-- #post-<?php the_ID(); ?> -->
</a>
