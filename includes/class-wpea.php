<?php
/**
 * The class is the core plugin responsible for including and
 * instantiating all of the code that composes the plugin.
 *
 * The class includes an instance to the plugin
 * Loader which is responsible for coordinating the hooks that exist within the
 * plugin.
 *
 * @since    1.0.0
 * @package WPEA
 */

if ( ! class_exists( 'WPEA_Loader' ) ) {

	class WPEA_Loader {

		/**
		 * Stores plugin options.
		 */
		public $opt;

		/**
		 * Core singleton class
		 * @var self
		 */
		private static $_instance;

		/**
		 * Instantiates the plugin by setting up the core properties and loading
		 * all necessary dependencies and defining the hooks.
		 *
		 * The constructor uses internal functions to import all the
		 * plugin dependencies, and will leverage the WP_Easy_Admin for
		 * registering the hooks and the callback functions used throughout the plugin.
		 */
		public function __construct( $wpea = null ) {
			$this->opt = ( null !== $wpea ) ? $wpea->opt : get_option( 'wp_easy_admin' );
			$this->set_locale();

			if ( ( is_admin() || current_user_can( 'administrator' ) ) && ! wp_is_mobile() ) {
				$this->admin_hooks();
			}
		}

		/**
		 * Gets the instance of this class.
		 *
		 * @return self
		 */
		public static function getInstance() {
			if ( ! ( self::$_instance instanceof self ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Defines the locale for this plugin for internationalization.
		 *
		 * Uses the WPEA_i18n class in order to set the domain and to register the hook
		 * with WordPress.
		 *
		 * @since    1.0.0
		 * @access   private
		 */
		private function set_locale() {
			$wpea_i18n = WPEA_i18n::getInstance();
			add_action( 'plugins_loaded', array( $wpea_i18n, 'load_plugin_textdomain' ) );
		}

		/**
		 * Defines the hooks and callback functions that are used for setting up the plugin's admin options.
		 *
		 * @access    private
		 */
		private function admin_hooks() {
			$admin = WPEA_Admin::getInstance();

			if ( ! isset( $this->opt['dismiss_admin_notices'] ) || ! $this->opt['dismiss_admin_notices'] ) {
				add_action( 'all_admin_notices', array( $admin, 'setup_notice' ) );
			}

			add_action( 'plugin_action_links', array( $admin, 'plugin_settings_link' ), 10, 2 );
			add_action( 'admin_menu', array( $admin, 'admin_menu_setup' ) );
			add_action( 'wp_ajax_nopriv_dismiss_notice', array( $admin, 'dismiss_notice' ) );
			add_action( 'wp_ajax_dismiss_notice', array( $admin, 'dismiss_notice' ) );
			add_action( 'admin_enqueue_scripts', array( $admin, 'admin_script_style' ) );
			add_action( 'admin_init', array( $admin, 'settings_init' ) );
			add_action( 'admin_footer', array( $admin, 'wpea_display_popup' ) );

			if ( isset( $this->opt['wpea_front_end'] ) && $this->opt['wpea_front_end'] ) {
				add_action( 'wp_enqueue_scripts', array( $admin, 'admin_script_style' ) );
				add_action( 'wp_footer', array( $admin, 'wpea_display_popup' ) );
			}
		}
	}
}
