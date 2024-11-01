<?php
/**
 * Fires during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    WPEA
 * @subpackage WPEA/includes
 * @author     Vinod Dalvi <mozillavvd@gmail.com>
 */

if ( ! class_exists( 'WPEA_Deactivator' ) ) {

	class WPEA_Deactivator {

		/**
		 * The code that runs during plugin deactivation.
		 *
		 * @since    1.0.0
		 */
		public static function deactivate() {
			$opt = get_option( 'wp_easy_admin' );

			if ( isset( $opt['dismiss_admin_notices'] ) ) {
				unset( $opt['dismiss_admin_notices'] );
				update_option( 'wp_easy_admin', $opt );
			}
		}
	}
}
