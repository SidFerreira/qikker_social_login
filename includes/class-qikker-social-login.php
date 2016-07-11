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
 * @author     Sidney Ferreira <sid@qikkeronline.nl>
 */
class QikkerSocialLogin
{

    #region Constants and Singletons

    const ACTION_LOGIN = 'qsl-do-social-login';

    const ACTION_LOGOUT = 'qsl-do-social-logout';

    const PLUGIN_NAME   = 'qikker-social-login';

    const USER_AVATAR   = 'user_avatar';

    const NONCE_LOGIN   = '_qsl_login';

    /**
     * "Singleton"
     * @var $hybridAuthInstance Hybrid_Auth
     */
    private $hybridAuthInstance;

    /**
     * Singleton
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

    #endregion

    #region Usermeta Helpers

    public function usermetaIdentifierKey($provider) {

        return strtolower('qsl_' . $provider . '_identifier');

    }

    public function usermetaAuthDateKey($provider) {

        return strtolower('qsl_' . $provider . '_auth');

    }

    public function usermetaLoginDateKey($provider) {

        return strtolower('qsl_' . $provider . '_login');

    }

    public function usermetaProfileKey($provider) {

        return strtolower('qsl_' . $provider . '_profile');

    }

    #endregion

    #region Contructor and Setup

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

        $this->plugin_name = self::PLUGIN_NAME; // @TODO mode to constant
        $this->version = '0.2';

        $this->loadDependencies();
        $this->setLocale();
        $this->defineAdminHooks();
        $this->definePublicHooks();
        $this->defineShortcodes();
        $this->defineFilters();

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
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-qikker-social-login-public.php';

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

        $plugin_admin = new QikkerSocialLoginAdmin($this->getPluginName(), $this->getVersion());

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

        $plugin_public = new QikkerSocialLoginPublic($this->getPluginName(), $this->getVersion());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueueStyles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueueScripts');

        $this->loader->add_action('init', $this, 'onWpInit', 10, 3);
        $this->loader->add_action('show_user_profile', $this, 'templateProfileInfo');
        $this->loader->add_action('edit_user_profile', $this, 'templateProfileInfo');

        $this->loader->add_action('wp_logout', $this, 'logoutHook');
        $this->loader->add_action('wp_login', $this, 'loginHook');

    }

    /**
     * Register all of the shortcodes
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function defineShortcodes()
    {

        add_shortcode('qikker_social_login_form', array($this, 'templateLoginForm'));

    }

    /**
     * Register all of the filters
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function defineFilters()
    {

        $this->loader->add_filter('get_avatar_url', $this, 'getAvatarUrl', 10, 3);

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {


        $this->loader->run();

    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function getPluginName()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    QikkerSocialLoginLoader    Orchestrates the hooks of the plugin.
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function getVersion()
    {
        return $this->version;
    }

    #endregion

    #region Configuration

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

        $base_config = array();

        if (strpos(site_url(), 'sociallogin.qikkerinternal.nl')) {

            $base_config = array(
                "Facebook" => array(
                    "enabled" => true,
                    "keys" => array("id" => "289669238047330", "secret" => "0d8414a87a389b04231ff89f8cf81fec"),
                    "trustForwarded" => false,
                    "scope" => "email, user_about_me, user_birthday, user_hometown, user_website, user_friends, user_photos",
                    "display" => "popup"
                )
            );

        } else if (strpos(site_url(), 'sll.qikkeroffline.hl')) {

            $base_config = array(
                "Facebook" => array(
                    "enabled" => true,
                    "keys" => array("id" => "288424601505127", "secret" => "4a0bb2de87d206ac55d4cc84ada7f07b"),
                    "trustForwarded" => false,
                    "scope" => "email, user_about_me, user_birthday, user_hometown, user_website, user_friends, user_photos",
                    "display" => "popup"
                )
            );

        }

        return apply_filters('qikker_social_login_config_provider', $base_config);

    }

    #endregion

    #region Actions and Filters

    public function onWpInit() {

        $this->getHybridAuthInstance();

        if (!is_user_logged_in()) {

            if (isset($_GET['action']) && isset($_GET['provider'])) {

                $provider = $_GET['provider'];

                $redirect_to = isset($_GET['redirect_to']) ? $_GET['redirect_to'] : false;

                if ($_GET['action'] === self::ACTION_LOGIN) {

                    if (wp_verify_nonce( $_REQUEST[self::NONCE_LOGIN], self::NONCE_LOGIN )) {

                        $this->login($provider);

                    }

                }

                if ($redirect_to) {

                    wp_safe_redirect($redirect_to);
                    exit();

                }

            }

        }

        if (is_user_logged_in()) {

            $this->updateProfiles();

        }

    }

    public function loginHook() {

        $user_id = get_current_user_id();

        update_user_meta($user_id, $this->usermetaLoginDateKey('wordpress'), time());

        $providers = $this->getHybridAuthInstance()->getConnectedProviders();

        foreach($providers as $provider) {

            update_user_meta($user_id, $this->usermetaLoginDateKey($provider), time());

        }

    }

    public function logoutHook() {

        $hybridAuthInstance = $this->getHybridAuthInstance();

        $providers = $hybridAuthInstance->getConnectedProviders();

        foreach($providers as $provider) {

            $hybridUserProfile = $hybridAuthInstance->getAdapter($provider);
            $hybridUserProfile->logout();

            delete_user_meta(get_current_user_id(), $this->usermetaLoginDateKey($provider));

        }

        delete_user_meta(get_current_user_id(), $this->usermetaLoginDateKey('wordpress'));

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

    #endregion

    #region User Data Manipulation

    /**
     * @param $hybridUserProfile Hybrid_User_Profile
     * @param $provider String
     * @return WP_User|boolean
     */
    public function findUser($hybridUserProfile, $provider) {

        $wp_user = get_user_by('email', $hybridUserProfile->email);

        if (!$wp_user) {

            $results = get_users(array(
                'meta_key'   => $this->usermetaIdentifierKey($provider),
                'meta_value' => $hybridUserProfile->identifier,
                'compare'    => '='
            ));

            if ($results && count($results)) {

                $wp_user = $results[0];

            }

        }

        return $wp_user;

    }

    public function setupUserData($user_id, $hybridUserProfile) {

        $userdata = get_userdata($user_id);

        $userdata->user_nicename = $hybridUserProfile->displayName;
        $userdata->display_name  = $hybridUserProfile->displayName;
        $userdata->first_name    = $hybridUserProfile->firstName;
        $userdata->last_name     = $hybridUserProfile->lastName;
        $userdata->description   = $hybridUserProfile->description;

        wp_update_user($userdata);

    }

    public function setupUserAvatar($user_id, $hybridUserProfile) {

        $current_social_photo_url = get_user_meta($user_id, 'social_photo_url', true);

        $social_photo_url = $hybridUserProfile->photoURL;

        if ($hybridUserProfile->photoURL && $social_photo_url != $current_social_photo_url) {

            update_user_meta($user_id, 'social_photo_url', $social_photo_url);

            if (strpos($social_photo_url, 'facebook')) {

                $parts = explode('?', $social_photo_url);
                $social_photo_url = $parts[0] . '?width=1500&height=1500'; //Probably 232 x 232

            }


            if (!function_exists('download_url')) {

                require_once ABSPATH . '/wp-admin/includes/file.php';
                require_once ABSPATH . '/wp-admin/includes/media.php';
                require_once ABSPATH . '/wp-admin/includes/image.php';

            }

            $downloaded_file = download_url($social_photo_url, 10);

            $downloaded_data = array(
                'name'     => $user_id . '.jpg',
                'tmp_name' => $downloaded_file,
                'ext'      => 'jpg',
                'type'     => 'image',
            );

            $attachment_id = media_handle_sideload($downloaded_data, 0);

            if (!is_wp_error($attachment_id)) {

                update_user_meta($user_id, self::USER_AVATAR, $attachment_id);

            }

            if (file_exists($downloaded_file)) {

                unlink($downloaded_file);

            }

        }

    }


    /**
     * Tries to update the current connected profiles.
     * Each provider will be updated or logged off if it fails to update.
     * If after all providers are processed we logged off from all providers
     * We'll logoff from WP it self.
     */
    public function updateProfiles() {

        if (is_user_logged_in()) {

            $providers = $this->getHybridAuthInstance()->getConnectedProviders();

            if (count($providers)) {

                foreach ($providers as $provider) {

                    $this->updateProfile($provider);

                }

                $providers = $this->getHybridAuthInstance()->getConnectedProviders();

                if (!count($providers)) {

                    wp_logout();
                    wp_safe_redirect($this->getCurrentUrl(array('logout-reason' => 'social')));

                    exit();

                }

            }

        }

    }

    public function saveUserProfile($provider, $hybridUserProfile) {

        $profile = (array) $hybridUserProfile;
        $profile['updated'] = time();

        update_user_meta( get_current_user_id() , $this->usermetaProfileKey($provider), $profile);

    }

    public function updateProfile($provider) {

        $valid = true;

        $hybridAdapter = $this->getHybridAuthInstance()->getAdapter($provider);

        try {

            $this->saveUserProfile($provider, $hybridAdapter->getUserProfile());

        } catch ( Exception $e ) {

            $previous = $e->getPrevious();

            if ($previous) {

                // Facebook
                if ( method_exists($previous, 'getType') && $previous->getType() === 'OAuthException' ) {

                    $hybridAdapter->logout();

                }

            }

        }

        return $valid;

    }

    #endregion

    #region Login / Register Core

    private function assignAuthInformation($user_id, $provider, $hybridUserProfile) {

        update_user_meta($user_id, $this->usermetaIdentifierKey($provider), $hybridUserProfile->identifier);
        update_user_meta($user_id, $this->usermetaAuthDateKey($provider), time());

    }

    private function loginUser($user_id) {

        wp_set_auth_cookie( $user_id );
        wp_set_current_user( $user_id );

    }

    public function login($provider) {

        if (is_user_logged_in()) {

            return;

        }

        try {

            $hybridUserProfile = $this->getHybridAuthInstance()->authenticate($provider)->getUserProfile();

            $wp_user = $this->findUser($hybridUserProfile, $provider);

            if (!$wp_user && apply_filters('qikker_social_login_should_create_users', true, $hybridUserProfile)) {

                $wp_user = $this->userCreate($hybridUserProfile, $provider);

            }

            if($wp_user) {

                $this->loginUser($wp_user->ID);

                if (!get_user_meta($wp_user->ID, $this->usermetaIdentifierKey($provider))) {

                    $this->assignAuthInformation($wp_user->ID, $provider, $hybridUserProfile);

                }

                $this->saveUserProfile($provider, $hybridUserProfile);

                do_action( 'wp_login', $wp_user->user_login, $wp_user );

            }


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

    /**
     * @param $hybridUserProfile Hybrid_User_Profile
     * @param $provider String
     * @return WP_User | boolean
     */
    public function userCreate($hybridUserProfile, $provider) {

        $email = $hybridUserProfile->email;

        $username = substr($email, 0, strpos($email, '@') );

        if( username_exists( $username ) ) {

            $try = 1;
            $tmp_username = $username;

            do {

                $tmp_username = $username . "_" . ($try++);

            } while( username_exists ($tmp_username));

            $username = $tmp_username;

        }

        $user_id_or_error = register_new_user($username, $email); //new WP_Error('algum erro');//

        if (is_wp_error($user_id_or_error)) {

            //Send an e-mail to the blog admin / add filter
            do_action('qikker_social_login_user_register_error', $user_id_or_error);

            $user_id_or_error = false;

        } else {

            $this->loginUser($user_id_or_error);

            $this->assignAuthInformation($user_id_or_error, $provider, $hybridUserProfile);

            $this->setupUserData($user_id_or_error, $hybridUserProfile);
            $this->setupUserAvatar($user_id_or_error, $hybridUserProfile);

            return new WP_User($user_id_or_error);

        }

    }

    //endregion

    #region Links and Buttons

    public static function loginHref($provider, $redirect_to = true) {

        if ($redirect_to === true) {

            $redirect_to = self::getCurrentUrl();

        }

        return wp_nonce_url( site_url() . '?action=' . self::ACTION_LOGIN .
                             '&provider=' . $provider . '&redirect_to=' . urlencode($redirect_to), self::NONCE_LOGIN, self::NONCE_LOGIN);

    }

    public static function logoutHref($redirect_to = true) {

        if ($redirect_to === true) {

            $redirect_to = self::getCurrentUrl();

        }

        return wp_logout_url($redirect_to);

    }

    #endregion


    #region Templates

    public function templateLoginForm() {

        ob_start();

        include $this->locateTemplate('login.php');

        $output = ob_get_contents();

        ob_end_clean();

        return $output;

    }


    public function templateProfileInfo($profileuser) {

        $qikkerSocialLogin = QikkerSocialLogin::getInstance();

        $providers = array_keys($qikkerSocialLogin->getProviderConfig());

        ob_start();

        include $this->locateTemplate('admin/profile.php');

        $output = ob_get_contents();

        ob_end_clean();

        return $output;

    }

    #endregion

    #region Templating Methods

    public static function getCurrentUrl($args = array()) {

        global $wp;

        return home_url(add_query_arg($args,$wp->request));

    }

    static function pluginDirUrl() {

        return plugin_dir_url(__DIR__);

    }

    static function pluginDirPath() {

        return trailingslashit( dirname(__DIR__) );

    }

    public function locateTemplate($template_name) {

        $template = locate_template( trailingslashit( $this->plugin_name ) . $template_name );

        if (!$template) {

            $template = $this->pluginDirPath() . 'templates/' . $template_name;

        }

        return $template;

    }

    #endregion
}
