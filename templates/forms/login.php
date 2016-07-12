<?php wp_login_form($args); ?>

<?php /*

<div class="qsl__form qsl__form--login">

    <form name="<?= $args['form_id'] ?>" id="<?= $args['form_id'] ?>"
          action="<?= esc_url( QikkerSocialLogin::getCurrentUrl(array('action' => 'qsl_login')) ) ?>"
          method="post">

        <p class="login-username">
            <label for="<?= esc_attr( $args['id_username'] ) ?>"><?= esc_html( $args['label_username'] ) ?></label>
            <input type="text" name="log" id="<?= esc_attr( $args['id_username'] ) ?>" class="input"
                   value="<?= esc_attr( $args['value_username'] ) ?>" size="20" />
        </p>

        <p class="login-password">
            <label for="<?= esc_attr( $args['id_password'] ) ?>"><?= esc_html( $args['label_password'] ) ?></label>
            <input type="password" name="pwd" id="<?= esc_attr( $args['id_password'] ) ?>" class="input" value="" size="20" />
        </p>

        <?php if($args['remember']) { ?>

            <p class="login-remember">
                <label>
                    <input name="rememberme" type="checkbox" id="<?= esc_attr( $args['id_remember'] ) ?>"
                           value="forever"<?= ( $args['value_remember'] ? ' checked="checked"' : '' ) ?> />
                    <?= esc_html( $args['label_remember'] ) ?></label>
            </p>

        <?php } ?>

        <p class="login-submit">
            <input type="submit" name="wp-submit" id="<?= esc_attr( $args['id_submit'] ) ?>" class="button-primary" value="<?= esc_attr( $args['label_log_in'] ) ?>" />
            <input type="hidden" name="redirect_to" value="<?= esc_url( $args['redirect'] ) ?>" />
        </p>

    </form>

</div>

*/ ?>