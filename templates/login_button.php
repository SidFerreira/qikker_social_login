<?php

$url = QikkerSocialLogin::loginHref('Facebook', 'refresh_parent')

?>

<a href="javascript:void(0);" data-href="<?php echo $url; ?>" class="qsl_popup button"><?php _e('Login'); ?></a>
