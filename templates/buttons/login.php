<?php

if (is_user_logged_in()) {


    $url = QikkerSocialLogin::getCurrentUrl();

    ?>

    <a href="<?php echo wp_login_url($url); ?>"
       class="qsl__button--logout button button-primary button-large"><?php _e('Logout'); ?></a>

    <?php

} else {

    $url = QikkerSocialLogin::authHref($provider, 'refresh_parent')

    ?>

    <a href="javascript:void(0);" data-href="<?php echo $url; ?>"
       class="qsl__popup qsl__button--login button button-primary button-large"><?php _e('Login'); ?></a>

    <?php

}