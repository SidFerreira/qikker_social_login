<?php

    if (!get_option('users_can_register')) {

        return;

    }

    if (is_user_logged_in()) {
        ?>

        <a href="<?php echo QikkerSocialLogin::logoutHref($args['redirect']); ?>"
           class="qsl__button--logout button button-primary button-large"><?php _e('Logout'); ?></a>

        <?php

    } else {

        if (isset($args['errors'])) {

            echo $args['errors'];

        }

        if (isset($args['messages'])) {

            echo $args['messages'];

        }

?>
    <form name="registerform" id="registerform"
          action="<?php echo QikkerSocialLogin::getLoginUrl() ?>"
          class="registerform qsl__form qsl__form--register"
          method="post" novalidate="novalidate">

        <?php foreach($args['fields'] as $field => $config) { $value = isset($_POST[$field]) ? $_POST[$field] : ''; ?>

            <?php do_action(QikkerSocialLogin::ACTION_BEFORE_REGISTER_FIELD, true, $field, $config); ?>
            <?php if (apply_filters(QikkerSocialLogin::FILTER_SHOW_REGISTER_FIELD, true, $field, $config)) { ?>

                <p>

                    <label for="<?=$field;?>">

                        <?php echo $config['label'] ?>

                        <?php if(isset($config['required']) && $config['required']) { ?>

                            <span class="qsl__required">*</span>

                        <?php } ?>

                        <br />
                        <input type="<?php echo $config['type'] ?>" name="<?=$field;?>" id="<?=$field;?>"
                               class="input qsl__input" value="<?php echo esc_attr(wp_unslash($value)); ?>" size="40" />

                    </label>

                </p>

            <?php } ?>

        <?php } ?>

        <?php
        /**
         * Fires following the 'Email' field in the user registration form.
         *
         * @since 2.1.0
         */
        do_action( 'register_form' );
        ?>
        <p id="reg_passmail"><?php _e( 'Registration confirmation will be emailed to you.' ); ?></p>

        <br class="clear" />

        <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $args['redirect'] ); ?>" />

        <p class="submit">

            <input type="submit" name="wp-submit" id="wp-submit"
                   class="button button-primary button-large qsl__register"
                   value="<?=$args['label_register']; ?>" />
            
        </p>

    </form>

<?php

    }