<div class="qsl__loginform">
    
    <a href="?action=<?php echo QikkerSocialLogin::ACTION_LOGIN;?>&provider=Facebook">Social Login</a>

    <?php wp_login_form(apply_filters('qikker_social_login_login_form_args', array())); ?>

</div>