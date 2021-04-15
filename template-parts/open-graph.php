<?php

?>
<!-- Facebook Meta Tags -->
<meta property="og:url" content="<?php the_permalink(); ?>">
<meta property="og:type" content="website">
<meta property="og:title" content="<?php echo esc_attr(get_the_title()); ?>">
<meta property="og:description" content="<?php echo esc_attr(strip_tags(get_the_excerpt())); ?>">
<meta property="og:image" content="<?php the_post_thumbnail_url( null, 'medium' ); ?>">

<!-- Twitter Meta Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta property="twitter:domain" content="<?php echo get_site_url(); ?>">
<meta property="twitter:url" content="<?php the_permalink(); ?>">
<meta name="twitter:title" content="<?php echo esc_attr(get_the_title()); ?>">
<meta name="twitter:description" content="<?php echo esc_attr(strip_tags(get_the_excerpt())); ?>">
<meta name="twitter:image" content="<?php the_post_thumbnail_url( null, 'medium' ); ?>">
