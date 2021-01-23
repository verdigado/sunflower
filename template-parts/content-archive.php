<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package sunflower
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('bg-white'); ?>>
    <div class="">
        <div class="">
            <a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">
                <?php sunflower_post_thumbnail(); ?>
            </a>
        </div>
        <div class="">
            <div class="p-4">
                <header class="entry-header mb-2">
                    <div class="theme arvogruen mb-1">
                        Ernährung
                    </div> 
                    <?php
                    
                    the_title( '<h2 class="card-title h4"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
           

                    if ( 'post' === get_post_type() ) :
                        ?>
                        <div class="entry-meta">
                            <?php
                            sunflower_posted_on();
                            sunflower_posted_by();
                            ?>
                        </div><!-- .entry-meta -->
                    <?php endif; ?>
                </header><!-- .entry-header -->

            

                <div class="entry-content">
                    <a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">
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

                    wp_link_pages(
                        array(
                            'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'sunflower' ),
                            'after'  => '</div>',
                        )
                    );
                    ?>
                </a>
                </div><!-- .entry-content -->

                <footer class="entry-footer">
                    <?php sunflower_entry_footer(); ?>

                    
                    <a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" class="continue-reading">
                    <?php
                        _e('Continue reading', 'sunflower');
                    ?>
                    <i class="fas fa-long-arrow-alt-right"></i>
                    </a>
                </footer><!-- .entry-footer -->
            </div>
        </div>
    </div>
</article><!-- #post-<?php the_ID(); ?> -->
