<?php
/**
 * Custom Header Template
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php
$color_scheme = get_theme_mod( 'color-scheme', 'light' );
$header_classes = 'right-bar ' . ( $color_scheme === 'green' ? 'scheme-green' : 'scheme-light' );
?>

<?php /* Sticky Observer */ ?>
<div id="navbar-sticky-detector"></div>
<header class="top-bar">
  <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap">

    <!-- Left: Label Block -->
    <div class="brand-left d-flex flex-column align-items-center text-center">
      <div class="label-top fw-bold text-uppercase skew-box"><?php esc_attr( bloginfo( 'name' ) ); ?></div>
      <div class="label-bottom fw-bold skew-box"><?php esc_attr( bloginfo( 'description' ) ); ?></div>
    </div>

    <!-- Center: Logo + Menu -->

    <div class="nav-center d-flex align-items-center gap-4 flex-wrap justify-content-center <?php echo esc_attr( $header_classes ); ?>">
      <div class="logo-background">
        <?php
        $sunflower_options = get_option( 'sunflower_first_steps_options' );
        if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
          the_custom_logo();
        } elseif ( ( $sunflower_options['sunflower_terms_of_use'] ?? false ) === 'checked' ) {
          echo '<a href="' . esc_url( home_url() ) . '" rel="home">';
          printf( '<img src="%s" alt="Logo" class="logo-img">', esc_attr( sunflower_parent_or_child( 'assets/img/sunflower.svg' ) ) );
          echo '</a>';
        }
        ?>
      </div>

      <nav class="main-menu">
        <?php
        wp_nav_menu(
          array(
            'theme_location' => 'mainmenu',
            'menu_class'     => 'nav gap-3',
            'container'      => false,
            'fallback_cb'    => false,
          )
        );
        ?>
      </nav>
    </div>
  </div>
</header>



<?php wp_body_open(); ?>
