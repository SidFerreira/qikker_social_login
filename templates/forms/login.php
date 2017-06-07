<?php

    if (is_user_logged_in()) {

        return;

    }

    $login_form_top = apply_filters( 'login_form_top', '', $args );

    $login_form_middle = apply_filters( 'login_form_middle', '', $args );

    $login_form_bottom = apply_filters( 'login_form_bottom', '', $args );

    $login_fields = array(
        'id_username' => array(
            'label'    =>   $args['label_username'],
            'name'     =>   'log',
            'value'    =>   $args['value_username'],
            'type'     =>   'text'
        ),
        'id_password' => array(
            'label'    =>   $args['label_password'],
            'name'     =>   'pwd',
            'value'    =>   '',
            'type'     =>   'password'
        ),
    )

    ?>

        <form name="<?php echo $args['form_id']; ?>" id="<?php echo $args['form_id']; ?>"
              action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" method="post">

            <?php echo $login_form_top; ?>

            <?php foreach($login_fields as $field => $config) { ?>

                <?php do_action(QikkerSocialLogin::ACTION_BEFORE_LOGIN_FIELD, true, $field, $config); ?>

                <?php if (apply_filters(QikkerSocialLogin::FILTER_SHOW_LOGIN_FIELD, true, $field, $config)) { ?>

                    <p>

                        <label for="<?=$field;?>">

                            <?php echo $config['label'] ?>

                            <?php if(isset($config['required']) && $config['required']) { ?>

                                <span class="qsl__required">*</span>

                            <?php } ?>

                            <br />
                            <input type="<?php echo $config['type'] ?>" name="<?=$config['name'];?>" id="<?=$field;?>"
                                   class="input qsl__input" value="<?php echo $config['value']; ?>" size="40" />

                        </label>

                    </p>

                <?php } ?>

            <?php } ?>

            <?php echo $login_form_middle; ?>

            <?php if ($args['remember']) { ?>

                <p class="login-remember">

                    <label>

                        <input name="rememberme" type="checkbox" id="<?php echo esc_attr( $args['id_remember'] ); ?>"
                            value="forever"<?php echo ( $args['value_remember'] ? ' checked="checked"' : '' ); ?> />
                        <?php echo $args['label_remember']; ?>

                    </label>

                </p>

            <?php } ?>

            <p class="login-submit">

                <input type="submit" name="wp-submit" id="<?php echo esc_attr( $args['id_submit'] ); ?>"
                       class="button-primary" value="<?php echo esc_attr( $args['label_log_in'] ); ?>" />

                <input type="hidden" name="redirect_to" value="<?php echo esc_url( $args['redirect'] ); ?>" />

            </p>

            <?php echo $login_form_bottom; ?>

        </form>

    <?php
