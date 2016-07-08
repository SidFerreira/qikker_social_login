<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    QikkerSocialLogin
 * @subpackage QikkerSocialLogin/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    QikkerSocialLogin
 * @subpackage QikkerSocialLogin/public
 * @author     Sid Ferreira <sid@qikkeronline.nl>
 */
class QikkerSocialLoginPublic
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of the plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueueStyles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in QikkerSocialLoginLoader as all of the hooks are defined
         * in that particular class.
         *
         * The QikkerSocialLoginLoader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        if (!add_filter($this->plugin_name . '_should_enqueue_styles', true)) {

            return;

        }

        if (getenv('APP_ENV') == 'dev') {

            require_once(QikkerSocialLogin::pluginDirPath() . '/includes/assets/assets-css_development.php' );

        } else {

            require_once(QikkerSocialLogin::pluginDirPath() . '/includes/assets/assets-css_production.php') ;

        }

        wp_enqueue_style($this->plugin_name, QikkerSocialLogin::pluginDirUrl() . QikkerSocialLoginStyles::getPath(),
            array(), $this->version, 'all');


    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueueScripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in QikkerSocialLoginLoader as all of the hooks are defined
         * in that particular class.
         *
         * The QikkerSocialLoginLoader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        if (!add_filter($this->plugin_name . '_should_enqueue_scripts', true)) {

            return;

        }

        if (getenv('APP_ENV') == 'dev') {

            require_once(QikkerSocialLogin::pluginDirPath() . '/includes/assets/assets-js_vendor.php' );
            require_once(QikkerSocialLogin::pluginDirPath() . '/includes/assets/assets-js.php' );
            /*
                        wp_enqueue_style($this->plugin_name . '_vendors',
                            QikkerSocialLogin::pluginDirUrl() . QikkerSocialLoginScriptsVendor::getPath(),
                            array(), $this->version, 'all');*/

        } else {

            require_once(QikkerSocialLogin::pluginDirPath() . '/includes/assets/assets-js_both.php') ;


        }

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/plugin-name-public.js', array('jquery'),
            $this->version, false);

    }

}
