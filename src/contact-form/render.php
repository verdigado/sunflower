<?php
if (isset($attributes['title']) && !empty($attributes['title'])) {
    $title = $attributes['title'];
} else {
    $title = __('Contact Form', 'sunflower-contact-form');
}
?>


<div class="comment-respond mb-5">
    <?php echo sprintf('<h2 id="contact-form-title" class="text-center h1">%s</h2>', $title); ?>
    <form id="sunflower-contact-form" method="post" class="row">

    <div class="col-12 col-md-6">
        <p class="comment-form-comment">
            <label for="message">Nachricht <span class="required">*</span></label>
            <textarea id="message" name="message" cols="45" rows="8" maxlength="2000" required="required"></textarea>
        </p>
        <p class="small"><?php echo __('Please fill in all required (<span class="required">*</span>) fields.', 'sunflower-contact-form'); ?></p>
         <p class="small"><?php echo __('By using this form, you consent to the storage and processing of your data through our website.
            Additional information can be found in our privacy policy on <a href="#" id="privacy_policy_url">Datenschutzerklärung</a>', 'sunflower-contact-form') ?>.
        </p>
    </div>
    <div class="col-12 col-md-6"><p class="comment-form-author">
        <label for="name"><?php echo __('Name', 'sunflower-contact-form') ?></label>
        <input id="name" name="name" type="text" value="" size="30" maxlength="245"/>

        </p>
        <p class="comment-form-email">
            <label for="mail"><?php echo __('E-Mail', 'sunflower-contact-form') ?></label>
            <input id="mail" name="mail" type="email" value="" size="30" maxlength="100"/>
        </p>
        <p class="comment-form-email">
            <label for="captcha"><?php echo __('How much is 1 + 1 ?', 'sunflower-contact-form') ?> <span class="required">*</span></label>
            <input id="captcha" name="captcha" type="text" value="" size="30" maxlength="100" required="required"/>
        </p>

    </div>
        <p class="form-submit">
            <input name="submit" type="submit" id="submit" class="submit" value="<?php echo __('submit', 'sunflower-contact-form') ?>"/>
        </p>
    </form>
</div>
