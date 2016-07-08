<div class="qsl__login">

    <?php

    if(!is_user_logged_in()) {

        ?>

        <a href="<?=QikkerSocialLogin::loginHref('facebook');?>">Social Login</a>

        <?php

        wp_login_form(apply_filters('qikker_social_login_login_form_args', array()));

    } else {

        ?>

        <a href="<?=QikkerSocialLogin::logoutHref();?>">Social Logout</a>

        <?php

    }
    ?>
</div>