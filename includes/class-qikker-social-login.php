<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    QikkerSocialLogin
 * @subpackage QikkerSocialLogin/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    QikkerSocialLogin
 * @subpackage QikkerSocialLogin/includes
 * @author     Your Name <email@example.com>
 */
class QikkerSocialLogin
{

    const ACTION_LOGIN = 'qsl-do-social-login';

    const ACTION_LOGOUT = 'qsl-do-social-logout';

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      QikkerSocialLoginLoader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {

        $this->plugin_name = 'qikker-social-login';
        $this->version = '0.1';

        $this->loadDependencies();
        $this->setLocale();
        $this->defineAdminHooks();
        $this->definePublicHooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - QikkerSocialLoginLoader. Orchestrates the hooks of the plugin.
     * - QikkerSocialLogini18n. Defines internationalization functionality.
     * - QikkerSocialLoginAdmin. Defines all hooks for the admin area.
     * - QikkerSocialLoginPublic. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function loadDependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-qikker-social-login-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-qikker-social-login-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-qikker-social-login-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-qikker-social-login-public.php';

        $this->loader = new QikkerSocialLoginLoader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the QikkerSocialLogini18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function setLocale()
    {

        $plugin_i18n = new QikkerSocialLogini18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function defineAdminHooks()
    {

        $plugin_admin = new QikkerSocialLoginAdmin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function definePublicHooks()
    {
/*
        $plugin_public = new QikkerSocialLoginPublic($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    */
        $this->loader->add_action('init', $this, 'onWpInit', 10, 3);
        $this->loader->add_action('show_user_profile', $this, 'userProfileInfo');
        $this->loader->add_action('edit_user_profile', $this, 'userProfileInfo');

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {

        $this->loader->add_filter('get_avatar_url', $this, 'getAvatarUrl', 10, 3);
        $this->loader->add_filter('do_parse_request', $this, 'doParseRequest', 10, 3);

        $this->loader->run();

    }

    public function onWpInit() {

        add_shortcode('qikker_social_login_form', array($this, 'shortcodeLogin'));

    }

    public function doParseRequest($valid, $wp, $extra_query_vars) {

        if (isset($_GET['action']) && isset($_GET['provider'])) {

            $provider = $_GET['provider'];

            $redirect_to = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : site_url();

            if ($_GET['action'] === self::ACTION_LOGIN) {

                $this->socialLogin($provider, $redirect_to);
                exit();

            }

            if ($_GET['action'] === self::ACTION_LOGOUT) {

                $this->socialLogout($provider, $redirect_to);
                exit();

            }

        }

        return $valid;

    }

    public function shortcodeLogin() {

        $output = '';

        if(!is_user_logged_in()) {

            ob_start();

            include_once dirname(__DIR__) . '/templates/login.php';

            $output = ob_get_contents();

            ob_end_clean();

        } else {

            $output = 'Already Logged In';

        }

        return $output;
        
    }

    public function getAvatarUrl($url, $id_or_email, $args) {

        if (is_object($id_or_email)) {

            if ('WP_Comment' === get_class($id_or_email)) {

                $id_or_email = $id_or_email->user_id;

            }

        }

        if (!$id_or_email) {

            return $url;

        }

        $user = get_user_by('id', $id_or_email);

        if (!$user) {

            $user = get_user_by('email', $id_or_email);

        }

        if ($user) {

            $attachment_id = get_user_meta($user->ID, 'avatar_attachment_id', true);

            if ($attachment_id && $attacument_url = wp_get_attachment_thumb_url($attachment_id)) {

                $url = $attacument_url;

            }

        }

        return $url;

    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    QikkerSocialLoginLoader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

    private function getConfig()
    {

        return apply_filters('qikker_social_login_config', array(
            "base_url" => $this->getHybridAuthEntrypointUrl(),
            "debug_mode" => true,
            "debug_file" => plugin_dir_path(__file__) . '../qsl.log',
            "providers" => $this->getProviderConfig()
        ));

    }

    private function getHybridAuthEntrypointUrl()
    {

        return apply_filters('qikker_social_login_config_entrypoint',
            plugins_url('vendor/hybridauth/', dirname(__FILE__)) . "/");

    }

    private function getProviderConfig()
    {

        $base_config = array(
            "Facebook" => array(
                "enabled" => true,
                "keys" => array("id" => "288424601505127", "secret" => "4a0bb2de87d206ac55d4cc84ada7f07b"),
                "trustForwarded" => false,
                "scope" => "email, user_about_me, user_birthday, user_hometown, user_website, user_friends, user_photos",
//				"display" => "popup"
            )
        );

        return apply_filters('qikker_social_login_config_provider', $base_config);

    }

    /**
     * @var $instance QikkerSocialLogin
     */
    private static $instance;

    public static function getInstance()
    {

        if (!self::$instance) {

            self::$instance = new QikkerSocialLogin();

        }

        return self::$instance;

    }

    /**
     * @var $hybridAuthInstance Hybrid_Auth
     */
    private $hybridAuthInstance;

    public function getHybridAuthInstance()
    {

        if (!$this->hybridAuthInstance) {

            if (!class_exists('Hybrid_Auth')) {

                require_once plugin_dir_path(dirname(__FILE__)) . 'vendor/hybridauth/Hybrid/Auth.php';

            }

            $this->hybridAuthInstance = new Hybrid_Auth($this->getConfig());

        }

        return $this->hybridAuthInstance;

    }
    public static function login($provider) {

        self::getInstance()->socialLogin($provider);

    }

    public function usermetaIdentifierKey($provider) {

        return strtolower('qsl_' . $provider . '_identifier');

    }

    public function usermetaDateKey($provider) {

        return strtolower('qsl_' . $provider . '_date');

    }

    /**
     * @param $hybridUserProfile Hybrid_User_Profile
     * @param $provider String
     * @return WP_User|boolean
     */
    public function findUser($hybridUserProfile, $provider) {
        $wp_user = get_user_by('email', $hybridUserProfile->email);

        if (!$wp_user) {

            global $wpdb;

            $key = $this->usermetaIdentifierKey($provider);

            $results = $wpdb->get_row("SELECT * FROM `{$wpdb->usermeta}` WHERE `{$wpdb->usermeta}`.`meta_key` = '$key' AND `{$wpdb->usermeta}`.`meta_value` = '{$hybridUserProfile->identifier}'", ARRAY_A);

            if ($results && isset($results['user_id'])) {

                $wp_user = get_user_by('id', $results['user_id']);

            }

        }

        return $wp_user;

    }


    public function socialLogout($provider, $redirect_to) {

        if (!$redirect_to) {

            $redirect_to = site_url();

        }

        $hybridAuthInstance = $this->getHybridAuthInstance();

        if (is_user_logged_in() && $hybridAuthInstance->isConnectedWith($provider)) {

            $hybridUserProfile = $hybridAuthInstance->authenticate($provider);

            $hybridUserProfile->logout();

        }

        wp_safe_redirect($redirect_to);
        exit();

    }

    public function socialLogin($provider, $redirect_to = false)
    {

        if (is_user_logged_in()) {

            return;

        }

        if (!$redirect_to) {

            $redirect_to = site_url();

        }

        $hybridAuthInstance = $this->getHybridAuthInstance();

        try {

            /** @var $facebook Hybrid_Provider_Adapter */
            $providerAdapter = $hybridAuthInstance->authenticate($provider);

            $hybridUserProfile = $providerAdapter->getUserProfile();

            $email = $hybridUserProfile->email;

            $wp_user = $this->findUser($hybridUserProfile, $provider);

            if (apply_filters('qikker_social_login_create_users', true) && !$wp_user) {

                $email_prefix = substr($email, 0, strpos($email, '@') );
                $user_id_or_error = register_new_user($email_prefix, $email); //new WP_Error('algum erro');//

                if (is_wp_error($user_id_or_error)) {

                    //Send an e-mail to the blog admin / add filter
                    do_action('qikker_social_login_authentication_error', $user_id_or_error);

                } else {

                    wp_set_auth_cookie( $user_id_or_error );

                    $userdata = get_userdata($user_id_or_error);
                    $userdata->user_nicename = $hybridUserProfile->displayName;
                    $userdata->display_name  = $hybridUserProfile->displayName;
                    $userdata->first_name    = $hybridUserProfile->firstName;
                    $userdata->last_name     = $hybridUserProfile->lastName;
                    $userdata->description   = $hybridUserProfile->description;

                    wp_update_user($userdata);

                    update_user_meta($user_id_or_error, $this->usermetaIdentifierKey($provider), $hybridUserProfile->identifier);
                    update_user_meta($user_id_or_error, $this->usermetaDateKey($provider), time());

                    if ($hybridUserProfile->photoURL) {

                        update_user_meta($user_id_or_error, 'social_photo_url', $hybridUserProfile->photoURL);

                        if (!function_exists('download_url')) {

                            require_once ABSPATH . '/wp-admin/includes/file.php';
                            require_once ABSPATH . '/wp-admin/includes/media.php';
                            require_once ABSPATH . '/wp-admin/includes/image.php';

                        }

                        $downloaded_file = download_url($hybridUserProfile->photoURL, 10);

                        $downloaded_data = array(
                            'name'     => $user_id_or_error . '.jpg',
                            'tmp_name' => $downloaded_file,
                            'ext'      => 'jpg',
                            'type'     => 'image',
                        );

                        $attachment_id = media_handle_sideload($downloaded_data, 0);

                        if (!is_wp_error($attachment_id)) {

                            update_user_meta($user_id_or_error, 'avatar_attachment_id', $attachment_id);

                        }

                        if (file_exists($downloaded_file)) {

                            unlink($downloaded_file);

                        }

                    }

                }

            } else {

                wp_set_auth_cookie( $wp_user->ID );

            }

            wp_safe_redirect($redirect_to);

        } catch (Exception $e) {

            // Display the recived error,
            // to know more please refer to Exceptions handling section on the userguide
            switch ($e->getCode()) {

                case 0 :
                    echo "Unspecified error.";
                    break;
                case 1 :
                    echo "Hybriauth configuration error.";
                    break;
                case 2 :
                    echo "Provider not properly configured.";
                    break;
                case 3 :
                    echo "Unknown or disabled provider.";
                    break;
                case 4 :
                    echo "Missing provider application credentials.";
                    break;
                case 5 :
                    echo "Authentification failed. "
                         . "The user has canceled the authentication or the provider refused the connection.";
                    break;
                case 6 :
                    echo "User profile request failed. Most likely the user is not connected "
                         . "to the provider and he should authenticate again.";
                    $facebook->logout();
                    break;
                case 7 :
                    echo "User not connected to the provider.";
                    $facebook->logout();
                    break;
                case 8 :
                    echo "Provider does not support this feature.";
                    break;

            }

            // well, basically your should not display this to the end user, just give him a hint and move on..
            echo "<br /><br /><b>Original error message:</b> " . $e->getMessage();

        }

    }

    public function userProfileInfo($profileuser) {
        ?>
        <h2>Social Sessions</h2>
        <table class="form-table">
            <tbody>
                <?php

                    $qikkerSocialLogin = QikkerSocialLogin::getInstance();

                    $providers = array_keys($qikkerSocialLogin->getProviderConfig());

                    foreach($providers as $provider) {
                        ?>

                        <tr class="user-sessions-wrap">
                            <th><?=$provider;?></th>
                            <td aria-live="assertive">
                                <?php

                                    $social_login_date = get_user_meta($profileuser->ID, $qikkerSocialLogin->usermetaDateKey($provider), true);

                                    if ($social_login_date) {

                                        if ($profileuser->ID === get_current_user_id()){

                                            ?>
                                                <div class="destroy-sessions">
                                                    <a href="?action=<?=QikkerSocialLogin::ACTION_LOGOUT;?>&provider=<?=$provider;?>"
                                                       type="button" class="button button-secondary">Disconnect</a>
                                                </div>
                                            <?php

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
<?php

    }

}
