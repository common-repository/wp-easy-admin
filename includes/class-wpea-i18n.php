<?php
/**
 * Defines the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    WPEA
 * @subpackage WPEA/includes
 * @author     Vinod Dalvi <mozillavvd@gmail.com>
 */

if ( ! class_exists( 'WPEA_i18n' ) ) {

	class WPEA_i18n {

		/**
		 * Core singleton class
		 * @var self
		 */
		private static $_instance;

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
		 * Loads the plugin text domain for translation.
		 *
		 * @since    1.0.0
		 */
		public function load_plugin_textdomain() {
			load_plugin_textdomain( 'wp-easy-admin', false, dirname( dirname( plugin_basename( WPEA_PLUGIN_FILE ) ) ) . '/languages/' );
		}
	}
}
