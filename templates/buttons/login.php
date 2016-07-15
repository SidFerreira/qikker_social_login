<?php

if (is_user_logged_in()) {

    ?>

    <a href="<?php echo wp_login_url($args['redirect']); ?>"
       class="qsl__button--logout qsl__provider--<?php echo $args['provider']; ?> button button-primary button-large"><?php _e('Logout'); ?></a>

    <?php

} else {

    $url = QikkerSocialLogin::authHref($args['provider'], $args['redirect'])

    ?>

    <a href="javascript:void(0);" data-href="<?php echo $url; ?>"
       class="qsl__popup qsl__provider--<?php echo $args['provider']; ?> qsl__button--login button button-primary button-large">

        <?php _e('Login'); ?>

        <span class="qsl__providername"><?php echo $args['provider']; ?></span>

    </a>

    <?php

}