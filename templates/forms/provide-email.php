<!doctype html>

<!--[if lt IE 7]><html class="ie6"><![endif]-->
<!--[if IE 7]><html class="ie7"><![endif]-->
<!--[if IE 8]><html class="ie8"><![endif]-->
<!--[if IE 9]><html class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->

<html>

<!--<![endif]-->

<head>

    <!-- General Meta START -->

    <meta charset="UTF-8">

    <!-- General Meta END -->

    <!-- SEO Meta START -->

    <title><?php wp_title(''); ?></title>
    <meta name="viewport" content="width=device-width" />

    <!-- SEO Meta END -->

    <!-- Wordpress Head START -->

    <?php wp_head(); ?>

    <!-- Wordpress Head END -->

</head>

<body>

    <form name="emailform" id="emailform"
          class="emailform qsl__form qsl__form--email qsl__form--fullpage"
          method="post" novalidate="novalidate">

        <p class="">
        <?php _e('Please, provide a valid e-mail for your profile:'); ?>
        </p>

        <p class="qsl__domains">

            <?php _e('Invalid domains') ?>: <?php echo implode(', ', QikkerSocialLogin::getInvalidDomains()); ?>

        </p>

        <p>

            <label for="qsl_user_provided_email">

                <?php _e('Email') ?> <span class="qsl__required">*</span>
                <br />
                <input type="text" name="qsl_user_provided_email" id="qsl_user_provided_email"
                       class="input qsl__input" value="" size="40" />

            </label>

        </p>
    
        <br class="clear" />
    
        <p class="submit">
        
            <input type="submit" name="wp-submit" id="wp-submit"
                   class="button button-primary button-large qsl__register"
                   value="<?php _e('Save'); ?>" />
    
        </p>

    </form>

    <?php wp_footer(); ?>

</body>

</html>