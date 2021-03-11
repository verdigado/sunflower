<?php
function sunflower_contact_form() {
    $subject = sanitize_text_field($_POST['subject']);
    $message = sanitize_textarea_field($_POST['message']);
    $name = sanitize_text_field($_POST['name']);
    $mail = sanitize_email($_POST['mail']);

    $response = __('sent', 'sunflower');

    $to = get_option('admin_email');
    $subject = __('New contact form', 'sunflower');
    $message = sprintf("Name: %s\nE-Mail:%s\n\n%s", $name, $mail, $message);
    wp_mail($to, $subject, $message );

    echo json_encode(['code' => 200, 'text' => $response]);
    die();
}

add_action( 'wp_ajax_sunflower_contact_form', 'sunflower_contact_form' );
add_action( 'wp_ajax_nopiv_sunflower_contact_form', 'sunflower_contact_form' );

