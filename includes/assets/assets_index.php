<?php

    $localurl = get_bloginfo('stylesheet_directory'); // set the URL of your installation here

    // Detect Gravity forms
    $gravityforms = !is_plugin_active('gravityforms/gravityforms.php');

    if(getenv('APP_ENV') == 'dev'){

        require_once('assets-js_vendor.php');
        require_once('assets-js.php');
        require_once('assets-css_development.php');

    } else {

        require_once('assets-js_both.php');
        require_once('assets-css_production.php');

    }