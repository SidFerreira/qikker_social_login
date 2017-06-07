<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           QikkerSocialLogin
 *
 * @wordpress-plugin
 * Plugin Name:       Qikker Social Login
 * Plugin URI:        http://qikkeronline.com/
 * Description:       Qikker's solution for lame social plugins
 * Version:           0.1
 * Author:            Qikker Online
 * Author URI:        http://qikkeronline.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       qikker-social-login
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_qikker_social_login() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-qikker-social-login-activator.php';
	QikkerSocialLoginActivator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_qikker_social_login() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-qikker-social-login-deactivator.php';
	QikkerSocialLoginDeactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_qikker_social_login' );
register_deactivation_hook( __FILE__, 'deactivate_qikker_social_login' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-qikker-social-login.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_qikker_social_login() {

	QikkerSocialLogin::getInstance()->run();

}
run_qikker_social_login();
