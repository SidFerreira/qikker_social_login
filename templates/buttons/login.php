<?php

if (is_user_logged_in()) {


    $url = QikkerSocialLogin::getCurrentUrl();

    ?>

    <a href="<?php echo wp_login_url($url); ?>"
       class="qsl__button--logout qsl__provider--<?php echo $provider; ?> button button-primary button-large"><?php _e('Logout'); ?></a>

    <?php

} else {

    $url = QikkerSocialLogin::authHref($provider, 'refresh_parent')

    ?>

    <a xhref="javascript:void(0);" href="<?php echo $url; ?>"
       class="qsl__popupx qsl__provider--<?php echo $provider; ?> qsl__button--login button button-primary button-large">

        <?php _e('Login'); ?>

        <span class="qsl__providername"><?php echo $provider; ?></span>

    </a>

    <?php

}