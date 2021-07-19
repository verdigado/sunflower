<?php

require_once('inc/class-tgm-plugin-activation.php');

require_once('functions/_s.php');
require_once('functions/options/settings.php');
require_once('functions/options/social-media.php');
require_once('functions/options/events.php');
require_once('functions/events.php');
require_once('functions/excerpts.php');
require_once('functions/admin.php');
require_once('functions/plugin-activation.php');
require_once('functions/metaboxes.php');
require_once('functions/blocks.php');
require_once('functions/blocks/latest-posts.php');
require_once('functions/blocks/next-events.php');
require_once('functions/colors.php');
require_once('functions/update.php');
require_once('functions/widgets.php');
require_once('functions/block-patterns.php');
require_once('functions/related-posts.php');
require_once('functions/activation.php');
require_once('functions/comments.php');
require_once('functions/icalimport.php');
require_once('functions/pictureimport.php');
require_once('functions/emailscrambler.php');
require_once('functions/contact-form.php');
require_once('functions/childtheme.php');


// Remove generator meta-tag
remove_action('wp_head', 'wp_generator');









