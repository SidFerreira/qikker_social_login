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
class QikkerSocialLogin {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      QikkerSocialLoginLoader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
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
	public function __construct() {

		$this->plugin_name = 'qikker-social-login';
		$this->version = '0.1';

//		self::$instance = $this;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

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
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-qikker-social-login-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-qikker-social-login-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-qikker-social-login-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-qikker-social-login-public.php';

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
	private function set_locale() {

		$plugin_i18n = new QikkerSocialLogini18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new QikkerSocialLoginAdmin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new QikkerSocialLoginPublic( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {

		$this->loader->run();

	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    QikkerSocialLoginLoader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	private function getConfig() {

		return apply_filters('qikker_social_login_config', array(
			"base_url" => $this->getHybridAuthEntrypointUrl(),
			"debug_mode" => true,
			"debug_file" => plugin_dir_path(__file__) . '../qsl.log',
			"providers" => $this->getProviderConfig()
		));

	}

	private function getHybridAuthEntrypointUrl() {

		return apply_filters('qikker_social_login_config_entrypoint', plugins_url('vendor/hybridauth/', dirname(__FILE__)) . "/");

	}

	private function getProviderConfig() {

		$base_config = array(
			"Facebook" => array (
				"enabled" => true,
				"keys"    => array ( "id" => "288424601505127", "secret" => "4a0bb2de87d206ac55d4cc84ada7f07b" ),
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

	public static function getInstance() {

		if (!self::$instance) {

			self::$instance = new QikkerSocialLogin();

		}

		return self::$instance;

	}

	/**
	 * @var $hybridAuthInstance Hybrid_Auth
	 */
	private $hybridAuthInstance;

	public function getHybridAuthInstance() {

		if (!$this->hybridAuthInstance) {

			if (!class_exists('Hybrid_Auth')) {

				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/hybridauth/Hybrid/Auth.php';

			}

			$this->hybridAuthInstance = new Hybrid_Auth($this->getConfig());

		}

		return $this->hybridAuthInstance;

	}

	public static function loginFacebook() {

		try {

            /** @var $facebook Hybrid_Provider_Adapter */
			$authenticated = self::getInstance()->getHybridAuthInstance()->authenticate("Facebook");
			// Get the user profile
			$social_profile = $authenticated->getUserProfile();
			var_dump($facebook);
			var_dump($facebook_user_profile);
//			$profile = new QikkerSocialLoginProfile()

            $wp_user = get_user_by('email', $social_profile['email']);

			if (apply_filters('qikker_social_login_create_users', true) && !$wp_user) {
                
                wp_create_user()

			}
/*
			// debug the user profile
			echo '<h2>Your Facebook profile</h2>';
			echo '<pre>';
			print_r( $facebook_user_profile );
			echo '</pre>';

			// The user's Facebook profile ID
			$profile_id = $facebook_user_profile->identifier;

			// Example of using the facebook social api: Returns settings for the authenticating user
			// $facebook->api()->api == facebook's GET method
			// use https://developers.facebook.com/tools/explorer/286161255064795 to play around (forget the docs, they are a fucking pile of shit)
			$last_uploaded_picture = $facebook->api()->api( '/' . $profile_id . '/photos/uploaded?limit=1' );
			$individual_photo_id = $last_uploaded_picture['data'][0]['id'];
			$individual_photo_object = $facebook->api()->api( '/' . $individual_photo_id . '?fields=source');
			$individual_photo_url = $individual_photo_object['source'];

			echo '<h2>Array with data of your last uploaded photo</h2>';
			echo '<pre>';
			print_r($individual_photo_object);
			echo '</pre>';

			echo '<h2>Your last uploaded picture</h2>';
			echo '<img src="' . $individual_photo_url . '"/>';

			// disconnect the user ONLY form facebook
			// this will not disconnect the user from others providers if any used nor from your application
*/
		}
		catch( Exception $e ) {

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

}
