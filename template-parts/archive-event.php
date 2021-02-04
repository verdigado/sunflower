<?php
/**
 * Template part for displaying events
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package sunflower
 */

?>

<a href="<?php echo esc_url( get_permalink() ); ?>" class="event-card" rel="bookmark">
<article id="post-<?php the_ID(); ?>" <?php post_class('mb-2 event'); ?>>
    <div class="p-4">
        <?php
            $from = strToTime(get_post_meta( $post->ID, '_sunflower_event_from')[0]);
            $attribute = date('Y-m-d', $from );
            $weekday = date_i18n( 'l ', $from);
            $date = date_i18n( 'j. F Y', $from);
        ?>
        <div>
            <div><?php echo $weekday; ?></div>
            <div class="date">
                <time datetime="<?php echo $attribute; ?>">
                    <?php echo $date;?>
                </time>
            </div>
        </div>

        <div class="">
            <header class="entry-header pt-2">
                <?php
                    the_title( '<strong>', '</strong>' );
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
</article><!-- #post-<?php the_ID(); ?> -->
</a>
