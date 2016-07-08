<div class="qsl__login">
    
    <a href="<?=QikkerSocialLogin::getLoginHref('facebook');?>">Social Login</a>

    <?php wp_login_form(apply_filters('qikker_social_login_login_form_args', array())); ?>

</div>