<?php
/**
 * Plugin Name: Search Admin Menus
 * Description: The plugin reduces your 40% time wasted on accessing admin menus.
 * Version:     2.0
 * Author:      Vinod Dalvi
 * License:     GPL2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 * Text Domain: wp-easy-admin
 *
 *
 * Search Admin Menus. is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WP Easy Admin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Search Admin Menus. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 */


/**
 * Includes necessary dependencies and starts the plugin.
 *
 * @package WPEA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exits if accessed directly.
}

if ( ! class_exists( 'WP_Easy_Admin' ) ) {

	/**
	 * Main WP Easy Admin Class.
	 *
	 * @class WP_Easy_Admin
	 */
	final class WP_Easy_Admin {

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
		 * WP Easy Admin Constructor.
		 */
		public function __construct() {
			$this->opt = get_option( 'wp_easy_admin' );
			$this->define_constants();
			$this->includes();
			$this->init_hooks();
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
		 * Defines WP Easy Admin Constants.
		 */
		private function define_constants() {
			define( 'WPEA_VERSION', '2.0' );
			define( 'WPEA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			define( 'WPEA_PLUGIN_FILE', __FILE__ );
		}

		/**
		 * Includes required core files used in admin and on the frontend.
		 */
		public function includes() {
			require_once WPEA_PLUGIN_DIR . 'includes/class-wpea-activator.php';
			require_once WPEA_PLUGIN_DIR . 'includes/class-wpea-deactivator.php';
			require_once WPEA_PLUGIN_DIR . 'includes/class-wpea-i18n.php';
			require_once WPEA_PLUGIN_DIR . 'admin/class-wpea-admin.php';
			require_once WPEA_PLUGIN_DIR . 'includes/class-wpea.php';
		}

		/**
		 * Hooks into actions and filters.
		 */
		private function init_hooks() {
			// Executes necessary actions on plugin activation and deactivation.
			register_activation_hook( WPEA_PLUGIN_FILE, array( 'WPEA_Activator', 'activate' ) );
			register_deactivation_hook( WPEA_PLUGIN_FILE, array( 'WPEA_Deactivator', 'deactivate' ) );
		}
	}
}

/**
 * Starts plugin execution.
 */
function wpea_start_execution() {
	$wpea = WP_Easy_Admin::getInstance();
	new WPEA_Loader( $wpea );
}
add_action( 'plugins_loaded', 'wpea_start_execution' );
