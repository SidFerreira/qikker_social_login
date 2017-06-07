<div class="qsl__login">

    <?php

    if(!is_user_logged_in()) {
        
        echo do_shortcode('[qikker_social_login_button]');

    } else {

        ?>

        <a href="<?=QikkerSocialLogin::logoutHref();?>">Social Logout</a>

        <?php

    }
    ?>
</div>