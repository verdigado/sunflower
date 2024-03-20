<?php

function sunflower_email_scrambler($content)
{
    return preg_replace_callback(
        '/MAILTO:(.*?)([\'\"])/i',
        'sunflower_text_scramble',
        (string) $content
    );
}

function sunflower_text_scramble($input)
{
    $mail = strrev((string) $input[1]);
    return sprintf('#%2$s data-unscramble="%1$s"', $mail, $input[2]);
}

if (! sunflower_get_constant('SUNFLOWER_EMAIL_SCRAMBLE_NO')) {
    add_filter('the_content', 'sunflower_email_scrambler');
}
