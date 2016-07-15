<?php

    if (is_user_logged_in()) {

        return;

    }

    add_filter('login_form_bottom', array('QikkerSocialLogin', 'getSocialLoginBottom'));
    $login_form_bottom = apply_filters( 'login_form_bottom', '', $args );

    wp_login_form($args);
