<h2>Social Sessions</h2>
<table class="form-table">
    <tbody>
    <?php

    foreach($providers as $provider) {
        ?>

        <tr class="user-sessions-wrap">
            <th><?=$provider;?></th>
            <td aria-live="assertive">
                <?php

                $social_login_date = get_user_meta($profileuser->ID, $qikkerSocialLogin->usermetaAuthDateKey($provider), true);

                if ($social_login_date) {

                    if ($profileuser->ID === get_current_user_id()){
/*
                        ?>
                        <div class="destroy-sessions">
                            <a href="?action=<?=QikkerSocialLogin::ACTION_LOGOUT;?>&provider=<?=$provider;?>"
                               type="button" class="button button-secondary">Logout</a>
                        </div>
                        <?php
*/
                    }

                    ?>
                    <p class="description">Connected since: <?= date('r', $social_login_date); ?></p>
                    <?php
                } else {

                    ?>
                    <p class="description">Not connected.</p>
                    <?php

                }
                ?>
            </td>
        </tr>

        <?php

    }

    ?>

    </tbody>
</table>