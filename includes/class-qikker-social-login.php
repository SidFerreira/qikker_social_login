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

    const PLUGIN_NAME       = 'qikker-social-login';

    const PLUGIN_INITIALS   = 'qsl';

    const ACTION_AUTH       = 'qsl-do-social-auth';

    const ACTION_LOGIN      = 'qsl-do-social-login';

    const ACTION_LOGOUT     = 'qsl-do-social-logout';

    const NONCE_AUTH        = 'qsl_auth';

    const NONCE_LOGIN       = 'qsl_login';

    /* Same pattern as ACF */
    const USER_AVATAR       = 'user_avatar';

    const USER_PROVIDED_EMAIL   = 'qsl_user_provided_email';


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

                require_once dirname(__DIR__) . '/vendor/hybridauth/Hybrid/Auth.php';

            }

            $this->hybridAuthInstance = new Hybrid_Auth($this->getConfig());

        }

        return $this->hybridAuthInstance;

    }

    #endregion

    #region Usermeta Helpers

    public function usermetaIdentifierKey($provider) {

        return strtolower( self::PLUGIN_INITIALS . '_' . $provider . '_identifier');

    }

    public function usermetaAuthDateKey($provider) {

        return strtolower( self::PLUGIN_INITIALS . '_' . $provider . '_auth');

    }

    public function usermetaLoginDateKey($provider) {

        return strtolower( self::PLUGIN_INITIALS . '_' . $provider . '_login');

    }

    public function usermetaProfileKey($provider) {

        return strtolower( self::PLUGIN_INITIALS . '_' . $provider . '_profile');

    }

    public function usermetaPermissionsKey($provider) {

        return strtolower( self::PLUGIN_INITIALS . '_' . $provider . '_permissions');

    }

    #endregion

    #region Protected

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

    #endregion

    #region Setup - Constructor
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

        $this->plugin_name = self::PLUGIN_NAME;
        $this->version = '0.2';

        $this->loadDependencies();
        $this->setLocale();
        $this->defineAdminHooks();
        $this->definePublicHooks();
        $this->defineShortcodes();
        $this->defineFilters();

    }

    #endregion

    #region Setup - Loaders

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
        require_once dirname(__DIR__) . '/includes/class-qikker-social-login-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once dirname(__DIR__) . '/includes/class-qikker-social-login-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once dirname(__DIR__) . '/admin/class-qikker-social-login-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once dirname(__DIR__) . '/includes/class-qikker-social-login-public.php';

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

    #endregion

    #region Setup - Hooks

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
    private function defineShortcodes() {

        add_shortcode('qikker_social_login_form', array($this, 'templateLoginForm'));

        add_shortcode('qikker_social_register_form', array($this, 'templateRegisterForm'));

        add_shortcode('qikker_social_login_button', array($this, 'templateLoginButton'));

        add_shortcode('qikker_social_login_buttons', array($this, 'templateLoginButtons'));

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
        $this->loader->add_filter('login_form_top', $this, 'loginFormErrors', 10, 2);
        $this->loader->add_filter('wp_login_errors', $this, 'filterLoginErrors', 10, 2);

        //$errors = apply_filters( 'wp_login_errors', $errors, $redirect_to );filterLoginErrors

    }

    #endregion

    #region Basic Methods

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
            "debug_mode" => false,
            "debug_file" => plugin_dir_path(__DIR__) . '/qsl.log',
            "providers" => $this->getProviderConfig()
        ));

    }

    private function getHybridAuthEntrypointUrl()
    {

        return plugins_url('vendor/hybridauth/', dirname(__FILE__)) . "/";

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
                    "scope" => "email, user_about_me, user_birthday, user_hometown, user_website, user_friends, user_photos, user_likes",
                    "display" => "popup"
                )
            );

        } else if (strpos(site_url(), 'sll.qikkeroffline.hl')) {

            $base_config = array(
                "Facebook" => array(
                    "enabled" => true,
                    "keys" => array("id" => "288424601505127", "secret" => "4a0bb2de87d206ac55d4cc84ada7f07b"),
                    "trustForwarded" => false,
                    "scope" => "email, user_about_me, user_birthday, user_hometown, user_website, user_friends, user_photos, user_likes",
                    "display" => "popup"
                ),

                "Twitter" => array(
                    "enabled" => true,
                    "keys" => array("key" => "jwfrRd760m6WVvxsDrGz63MY9", "secret" => "cSjyqs3qM2KriaC1Ma1moqTmVKFQfTFv2ODbiWTp1kY9fob4jx"),
                    "includeEmail" => true
                )

            );

        }

        return $base_config;

    }

    #endregion

    #region Actions and Filters Methods

    public function onWpInit() {

        $this->getHybridAuthInstance();

        if (!is_user_logged_in()) {

            if (isset($_GET['action']) && isset($_GET['provider'])) {

                $provider = $_GET['provider'];

                $redirect_to = isset($_GET['redirect_to']) ? $_GET['redirect_to'] : false;

                if ($_GET['action'] === self::ACTION_AUTH) {

                    if (wp_verify_nonce($_REQUEST[self::NONCE_AUTH], self::NONCE_AUTH)) {

                        $this->authenticate($provider);

                    }

                }

                if ($redirect_to) {

                    if ($redirect_to === 'refresh_parent') {

                        ?>
                        <script>

                            window.opener.location.reload();
                            window.close();

                        </script>
                        <?php

                    } else {

                        wp_safe_redirect($redirect_to);

                    }

                    exit();

                }

            } else if (isset($_GET['action'])) {

                if ($_GET['action'] === self::ACTION_LOGIN) {

                    if (wp_verify_nonce($_REQUEST[self::NONCE_LOGIN], self::NONCE_LOGIN)) {

                        $this->registerate();

                    }

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

    public function loginFormErrors($output, $args) {

        if (isset($args['errors'])) {

            $output .= $args['errors'];

        }

        if (isset($args['messages'])) {

            $output .= $args['messages'];

        }

        return $output;

    }


    function filterLoginErrors( $errors ) {

        if ( is_wp_error($errors) && !isset($_GET['reauth'])) {

            $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : site_url();  // where did the post submission come from?

            $error_array = (array) $errors;

            foreach ($error_array['errors'] as $error_code => $messages) {

                foreach ($messages as $k => $message) {

                    $error_array['errors'][$error_code][$k] = urlencode($message);

                }

            }

            $login_url = wp_login_url();

            if ( !empty($referrer) && (strpos($referrer, $login_url) !== 0) && !strstr($referrer,'wp-admin') ) {

                $referrer = add_query_arg(array(self::PLUGIN_INITIALS . '_login_error' => $error_array), $referrer);

                wp_redirect( $referrer );  // let's append some information (login=failed) to the URL for the theme to use
                exit;

            }

        }

    }

    #endregion

    #region User Data Manipulation

    static function getInvalidDomains() {

        return apply_filters(self::PLUGIN_INITIALS . '_invalid_email_domains', array('@facebook.com', '@icq.com'));

    }

    public function isValidSocialEmail($email) {

        $email = sanitize_email($email);

        $name = $domain = '';

        if ($email) {

            list($name, $domain) = explode('@', $email, 2);

        }
        
        $invalid_domains = self::getInvalidDomains();
        
        if (!$email || in_array('@' . $domain, $invalid_domains)) {


            return false;

        }

        return true;

    }

    /**
     * @param $hybridUserProfile Hybrid_User_Profile
     * @param $provider String
     * @return WP_User|boolean
     */
    public function findUser($hybridUserProfile, $provider) {

        $wp_user = false;

        $results = get_users(array(
            'meta_key'   => $this->usermetaIdentifierKey($provider),
            'meta_value' => $hybridUserProfile->identifier,
            'compare'    => '='
        ));

        if ($results && count($results)) {

            $wp_user = $results[0];

        }

        if (!$wp_user) {

            $wp_user = get_user_by('email', $hybridUserProfile->email);

        }

        return $wp_user;

    }

    public function setupUserData($user_id, $user_fields) {

        $default_fields = _get_additional_user_keys(new WP_User());

        $userdata = get_userdata($user_id);

        $userdata->to_array();

        foreach($user_fields as $field => $value) {

            if (in_array($field, $default_fields)) {

                $userdata->{$field} = $value;

            } else {

                update_user_meta($user_id, $field, $value);

            }

        }

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

        /**
         * @var $hybridAdapter Hybrid_Provider_Adapter
         */
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

    #region Login / Register Core

    private function assignAuthInformation($user_id, $provider, $hybridUserProfile) {

        update_user_meta($user_id, $this->usermetaIdentifierKey($provider), $hybridUserProfile->identifier);
        update_user_meta($user_id, $this->usermetaAuthDateKey($provider), time());

    }

    private function loginUser($user_id) {

        wp_set_auth_cookie( $user_id );
        wp_set_current_user( $user_id );

    }

    public function authenticate($provider) {

        if (is_user_logged_in()) {

            return;

        }

        $hybridAdapter = false;
        $hybridUserProfile = false;

        try {

            /**
             * @var $hybridAdapter Hybrid_Provider_Adapter
             * @var $hybridUserProfile Hybrid_User_Profile
             */

            $hybridAdapter = $this->getHybridAuthInstance()->authenticate($provider);

            $hybridUserProfile = $hybridAdapter->getUserProfile();

            if (isset($_POST[self::USER_PROVIDED_EMAIL])) {

                $hybridUserProfile->email = sanitize_email($_POST[self::USER_PROVIDED_EMAIL]);

            }

            if (!$this->isValidSocialEmail($hybridUserProfile->email)) {

                echo $this->templateProvideEmailForm();
                exit();

            }

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

            if ($hybridAdapter) {

                $hybridAdapter->logout();

            }

            $this->unlink($provider);

            do_action('qsl_authentication_failed', $e);

        }

    }

    public function registerate() {

        $errors = new WP_Error();

        $fields = self::getRegisterFields();

        $values = array();

        //Required Fields Validation
        foreach($fields as $field => $config) {

            $values[$field] = isset($_POST[$field]) ? $_POST[$field] : '';

            if (isset($config['required']) && $config['required'] && empty($values[$field])) {

                $errors->add( 'empty_' . $field, $config['required_error']);

            }

        }

        $error = false;

        if (!count($errors->get_error_codes())) {

            $user_id_or_error = register_new_user($values['user_login'], $values['user_email']); //new WP_Error('algum erro');//

            if (is_wp_error($user_id_or_error)) {

                //Send an e-mail to the blog admin / add filter
                do_action('qikker_social_login_register_error', $user_id_or_error);

                $error = $user_id_or_error;

            } else {

                $this->loginUser($user_id_or_error);

                $this->setupUserData($user_id_or_error, $values);

                if (isset($_POST['redirect_to'])) {

                    wp_safe_redirect($_POST['redirect_to']);
                    exit();

                }

                return $user_id_or_error;

            }

        } else {

            $error = $errors;

        }

        if ($error) {

            $_GET[self::PLUGIN_INITIALS . '_register_error'] = (array) $error;

        }

    }

    public function unlink($provider) {

        if ($provider === 'Facebook') {

            if ($this->getHybridAuthInstance()->isConnectedWith($provider)) {

                $this->getHybridAuthInstance()->getAdapter('Facebook')->adapter->api->api('/me/permissions', 'DELETE');
                delete_user_meta(get_current_user_id(), $this->usermetaIdentifierKey($provider));
                delete_user_meta(get_current_user_id(), $this->usermetaAuthDateKey($provider));
                delete_user_meta(get_current_user_id(), $this->usermetaProfileKey($provider));
                delete_user_meta(get_current_user_id(), $this->usermetaLoginDateKey($provider));
                delete_user_meta(get_current_user_id(), $this->usermetaPermissionsKey($provider));

            }

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

            $try = 0;

            $tmp_username = $username . "_" . ($try++);

            while( username_exists ($tmp_username) ) {

                $tmp_username = $username . "_" . ($try++);

            }

            $username = $tmp_username;

        }

        $user_id_or_error = register_new_user($username, $email); //new WP_Error('algum erro');//

        if (is_wp_error($user_id_or_error)) {

            //Send an e-mail to the blog admin / add filter
            do_action('qikker_social_login_auth_error', $user_id_or_error);

            return $user_id_or_error;

        } else {

            $this->loginUser($user_id_or_error);

            $this->assignAuthInformation($user_id_or_error, $provider, $hybridUserProfile);

            $this->setupUserData($user_id_or_error,  (array) $hybridUserProfile);
            $this->setupUserAvatar($user_id_or_error, $hybridUserProfile);

            return new WP_User($user_id_or_error);

        }

    }

    public static function getRegisterFields() {

        $error_label = '<strong>' . __( 'ERROR' ) . '</strong>: ';
        $please_enter_a = __( 'Please enter a ' );

        $fields = apply_filters(self::PLUGIN_INITIALS . '_register_fields', array(

            'user_login' => array(
                'label'    => __('Username'),
                'required' => true,
                'required_error' => $error_label . $please_enter_a . strtolower(__('Username')) . '.'
            ),
            'user_nicename' => array(
                'label'    => __('Nickname'),
                'required' => true,
                'required_error' => $error_label . $please_enter_a . strtolower(__('Username')) . '.'
            ),
            'first_name' => array(
                'label'    => __('First name'),
                'required' => true,
                'required_error' => $error_label . $please_enter_a .  strtolower(__('First name')) . '.'
            ),
            'last_name' => array(
                'label'    => __('Last name'),
                'required' => true,
                'required_error' => $error_label . $please_enter_a . strtolower(__('Last name')) . '.'
            ),
            'user_email' => array(
                'label'    => __('Email'),
                'required' => true,
                'required_error' => $error_label . $please_enter_a . strtolower(__('Email')) . '.'
            ),
            'pets_name' => array(
                'label'    => __('Pet\'s name'),
                'required' => true,
                'required_error' => $error_label . $please_enter_a . strtolower(__('Pet\'s name')) . '.'
            )

        ));

        return $fields;

    }

    //endregion

    #region Links and Buttons

    public static function authHref($provider, $redirect_to = true, $extra_query = '') {

        if ($redirect_to === true) {

            $redirect_to = self::getCurrentUrl();

        }

        $url = site_url() . '?action=' . self::ACTION_AUTH .
               '&provider=' . $provider . '&redirect_to=' . urlencode($redirect_to) .
               '&' . $extra_query;

        return wp_nonce_url($url , self::NONCE_AUTH, self::NONCE_AUTH);

    }

    public static function getLoginUrl($extra_query = array()) {

        $extra_query['action'] = self::ACTION_LOGIN;

        $url = add_query_arg($extra_query, self::getCurrentUrl());

        return wp_nonce_url($url , self::NONCE_LOGIN, self::NONCE_LOGIN);

    }

    public static function logoutHref($redirect_to = true) {

        if ($redirect_to === true) {

            $redirect_to = self::getCurrentUrl();

        }

        return wp_logout_url($redirect_to);

    }

    #endregion

    #region Templates

    public function getFormErrors($key) {

        $args = array();

        if ( isset($_GET[ self::PLUGIN_INITIALS . '_' . $key . '_error' ]) && $errors_data = $_GET[ self::PLUGIN_INITIALS . '_' . $key . '_error' ]) {

            $error_data = isset($errors_data['error_data']) ? $errors_data['error_data'] : array();
            $error_info = $errors_data['errors'];

            $messages = '';
            $errors   = '';

            foreach ( $error_info as $code => $error_messages ) {

                $severity = isset($error_data[$code]) ? $error_data[$code] : '';

                foreach($error_messages as $error_message) {

                    if ( 'message' == $severity )
                        $messages .= '	' . $error_message . "<br />\n";
                    else
                        $errors .= '	' . $error_message . "<br />\n";

                }

            }
            if ( ! empty( $errors ) ) {

                $args['errors'] =  '<div id="' . $key . '_error" class="qsl__errors qsl__errors--' . $key . '">' .
                                   apply_filters( 'login_errors', $errors ) . "</div>\n";

            }
            if ( ! empty( $messages ) ) {

                $args['messages'] =  '<p class="message qsl__message qsl__message--' . $key . '">' .
                                     apply_filters( $key . '_messages', $messages ) . "</p>\n";

            }

        }

        return $args;

    }

    /**
     * Based on wp_login_form
     * @param array $args
     * @return string
     */
    public function templateLoginForm($args = array()) {

        $defaults = array(
            'echo' => true,
            // Default 'redirect' value takes the user back to the request URI.
            'post_url'          => $this->getCurrentUrl(array( 'action' => self::ACTION_LOGIN )),
            'redirect'          => $this->getCurrentUrl(),
            'form_id'           => 'loginform',
            'label_username'    => __( 'Username or Email' ),
            'label_password'    => __( 'Password' ),
            'label_remember'    => __( 'Remember Me' ),
            'label_log_in'      => __( 'Log In' ),
            'id_username'       => 'user_login',
            'id_password'       => 'user_pass',
            'id_remember'       => 'rememberme',
            'id_submit'         => 'wp-submit',
            'remember'          => false,
            'value_username'    => 'developer5',
            // Set 'value_remember' to true to default the "Remember me" checkbox to checked.
            'value_remember'        => false,
        );

        $args = apply_filters(self::PLUGIN_INITIALS . '_login_form_args', shortcode_atts($defaults, $args), $args);

        $message_args = $this->getFormErrors('login');

        if (isset($message_args['errors'])) {

            $args['errors'] = $message_args['errors'];

        }

        if (isset($message_args['messages'])) {

            $args['messages'] = $message_args['messages'];

        }

        ob_start();

        include $this->locateTemplate('forms/login.php');

        $output = ob_get_contents();

        ob_end_clean();

        return $output;

    }

    public static function getSocialLoginBottom($output = '') {

        $output .= '<input type="hidden" name="' . self::PLUGIN_INITIALS . '_login" value="1">';

        return $output;

    }

    public function templateRegisterForm($args = array()) {

        $defaults = array(
            'redirect'          => $this->getCurrentUrl(),
            'fields'            => QikkerSocialLogin::getRegisterFields(),
            'form_id'           => 'registerform',
            'label_register'    => __( 'Register' )

        );

        $args = apply_filters(self::PLUGIN_INITIALS . '_register_form_args', shortcode_atts($defaults, $args), $args);

        $message_args = $this->getFormErrors('register');

        if (isset($message_args['errors'])) { $args['errors'] = $message_args['errors']; }

        if (isset($message_args['messages'])) { $args['messages'] = $message_args['messages']; }

        ob_start();

        include $this->locateTemplate('forms/register.php');

        $output = ob_get_contents();

        ob_end_clean();

        return $output;

    }

    public function templateLoginButtons($attributes = array()) {

        $providers = self::getHybridAuthInstance()->getProviders();

        $output = '';

        foreach($providers as $provider => $config) {

            $attributes['provider'] = $provider;

            $output .= $this->templateLoginButton($attributes);

        }

        return $output;

    }

    public function templateLoginButton($args = array()) {

        $args = shortcode_atts(array(
            'provider' => 'Facebook',
            'redirect' => 'refresh_parent' // QikkerSocialLogin::getCurrentUrl()
        ), $args);

        ob_start();

        include $this->locateTemplate('buttons/login.php');

        $output = ob_get_contents();

        ob_end_clean();

        return $output;

    }
    
    public function templateProvideEmailForm($attributes = array()) {
        
        $attributes = shortcode_atts(array('provider' => 'Facebook'), $attributes);
        
        $provider = $attributes['provider'];
        
        ob_start();
        
        include $this->locateTemplate('forms/provide-email.php');
        
        $output = ob_get_contents();
        
        ob_end_clean();
        
        return $output;
        
    }

    public function templateProfileInfo($profileuser) {

        $qikkerSocialLogin = $this;

        $providers = array_keys($qikkerSocialLogin->getProviderConfig());

        include $this->locateTemplate('admin/profile.php');

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
