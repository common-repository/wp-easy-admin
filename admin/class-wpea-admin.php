<?php
/**
 * The class defines all functionality for the dashboard of the plugin.
 *
 * @package WPEA
 * @since    1.0.0
 */

if ( ! class_exists( 'WPEA_Admin' ) ) {

	class WPEA_Admin {

		/**
		 * Stores plugin options.
		 */
		public $opt;

		/**
		 * Stores network activation status.
		 */
		private $networkactive;

		/**
		 * Core singleton class
		 * @var self
		 */
		private static $_instance;

		/**
		 * Initializes this class.
		 *
		 */
		public function __construct() {
			$wpea = WP_Easy_Admin::getInstance();
			$this->opt = ( null !== $wpea ) ? $wpea->opt : get_option( 'wp_easy_admin' );
			$this->networkactive = ( is_multisite() && array_key_exists( plugin_basename( WPEA_PLUGIN_FILE ), (array) get_site_option( 'active_sitewide_plugins' ) ) );
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
		 * Strips tags and its content.
		 */
		function strip_tags_content( $text, $tags = '', $invert = FALSE ) {

			preg_match_all( '/<(.+?)[\s]*\/?[\s]*>/si', trim( $tags ), $tags );
			$tags = array_unique( $tags[1] );

			if ( is_array( $tags ) AND count( $tags ) > 0 ) {

				if ( $invert == FALSE ) {
					return preg_replace( '@<(?!(?:'. implode( '|', $tags ) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text );
				} else {
					return preg_replace( '@<('. implode( '|', $tags ) .')\b.*?>.*?</\1>@si', '', $text );
				}
			} else if ( $invert == FALSE ) {
				return preg_replace( '@<(\w+)\b.*?>.*?</\1>@si', '', $text );
			}

			return $text;
		}

		/**
		 * Loads plugin javascript and stylesheet files in the admin area.
		 */
		function admin_script_style(){
			wp_enqueue_style( 'wp-easy-admin-styles', plugins_url( '/admin/css/wp-easy-admin.css', WPEA_PLUGIN_FILE ), array(), WPEA_VERSION );
			wp_register_script( 'wp-easy-admin-scripts', plugins_url( '/admin/js/wp-easy-admin.js', WPEA_PLUGIN_FILE ), array( 'jquery' ), WPEA_VERSION, true  );
			$this->opt['wpea_key'] = isset( $this->opt['wpea_key'] ) ? $this->opt['wpea_key'] : 'x';

			global $submenu, $menu;

			if ( ! empty( $menu ) ) {

				$wpea_menu = array();

				foreach ( $menu as $value ) {
					$menu_url = $this->strip_tags_content( $value[2] );

					if ( false === strpos( $menu_url, '.php' ) ) {
						$menu_url = 'admin.php?page=' . $menu_url;
					}

					array_push( $wpea_menu, array( $this->strip_tags_content( $value[0] ), $menu_url ) );
				}

				foreach ( $submenu as $key => $submenu1 ) {

					foreach ( $submenu1 as $submenu2 ) {

						$menu_url = $this->strip_tags_content( $submenu2[2] );

						if ( false === strpos( $menu_url, '.php' ) ) {
							$key_temp = strpos( $key, '.php' ) ? $key : 'admin.php';
							$menu_url = $key_temp . '?page=' . $menu_url;
						}

						$submenu3 = array( $this->strip_tags_content( $submenu2[0] ), $menu_url );
						$found = array_search( $key, array_column( $wpea_menu, 1 ) );

						if ( $found && isset( $wpea_menu[ $found ][0] ) ) {
							array_push( $submenu3, $this->strip_tags_content( $wpea_menu[ $found ][0] ) );
						}

						array_push( $wpea_menu, $submenu3 );
					}
				}

				$this->opt['wpea_menus'] = $wpea_menu;

				if ( isset( $this->opt['wpea_front_end'] ) && $this->opt['wpea_front_end'] ) {
					update_option( 'wp_easy_admin', $this->opt );
				}
			}

			wp_localize_script( 'wp-easy-admin-scripts', 'wp_easy_admin', array(
				'ajax_url'  => admin_url( 'admin-ajax.php' ),
				'admin_url' => get_admin_url(),
				'wpea_key'  => $this->opt['wpea_key'],
				'wpea_menu' => $this->opt['wpea_menus']
			) );
			wp_enqueue_script( 'wp-easy-admin-scripts' );
		}

		/**
		 * Adds a link to the settings page in the plugins list.
		 *
		 * @param array  $links array of links for the plugins, adapted when the current plugin is found.
		 * @param string $file  the filename for the current plugin, which the filter loops through.
		 *
		 * @return array $links
		 */
		function plugin_settings_link( $links, $file ) {
			if ( false !== strpos( $file, 'wp-easy-admin' ) ) {
				$mylinks = array(
					'<a href="options-general.php?page=wp_easy_admin">' . esc_html__( 'Settings', 'wp-easy-admin' ) . '</a>'
				);
				$links = array_merge( $mylinks, $links );
			}
			return $links;
		}

		/**
		 * Displays plugin configuration notice in admin area.
		 */
		function setup_notice(){
			if (  0 === strpos( get_current_screen()->id, 'settings_page_wp_easy_admin' ) ) {
				return;
			}

			$hascaps = $this->networkactive ? is_network_admin() && current_user_can( 'manage_network_plugins' ) : current_user_can( 'manage_options' );

			if ( $hascaps ) {
				$url = is_network_admin() ? network_site_url() : site_url( '/' );
				echo '<div class="notice notice-info is-dismissible wp-easy-admin"><p>' . sprintf( __( 'To configure <em>Search Admin Menus plugin</em> please visit its <a href="%1$s">configuration page</a>.', 'wp-easy-admin'), $url . 'wp-admin/options-general.php?page=wp_easy_admin' ) . '</p></div>';
			}
		}

		/**
		 * Handles plugin notice dismiss functionality using AJAX.
		 */
		function dismiss_notice() {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				$this->opt['dismiss_admin_notices'] = 1;
				update_option( 'wp_easy_admin', $this->opt );
			}
			die();
		}

		/**
		 * Registers plugin admin menu item.
		 */
		function admin_menu_setup(){
			add_submenu_page( 'options-general.php', __( 'Search Admin Menus Settings', 'wp-easy-admin' ), __( 'Search Admin Menus', 'wp-easy-admin' ), 'manage_options', 'wp_easy_admin', array( $this, 'admin_page_screen' ) );
		}

		/**
		 * Renders the settings page for this plugin.
		 */
		function admin_page_screen() {
			include_once( 'partials/admin-page.php' );
		}

		/**
		 * Registers plugin settings.
		 */
		function settings_init(){
			add_settings_section( 'wp_easy_admin_section', __( 'WP Easy Admin Settings', 'wp-easy-admin' ),  array( $this, 'settings_section_desc'), 'wp_easy_admin' );

			add_settings_field( 'wpea_key', __( 'Easy Admin Access Key : ', 'wp-easy-admin' ),  array( $this, 'wpea_key' ), 'wp_easy_admin', 'wp_easy_admin_section' );
			add_settings_field( 'wpea_front_end', __( 'Front-end Easy Admin : ', 'wp-easy-admin' ),  array( $this, 'wpea_front_end' ), 'wp_easy_admin', 'wp_easy_admin_section' );
			add_settings_field( 'wp_easy_admin_css', __( 'Custom CSS : ', 'wp-easy-admin' ),  array( $this, 'custom_css' ), 'wp_easy_admin', 'wp_easy_admin_section' );

			register_setting( 'wp_easy_admin', 'wp_easy_admin' );
		}

		/**
		 * Displays plugin description text.
		 */
		function settings_section_desc(){
			echo '<p>' . esc_html__( 'Configure the WP Easy Admin plugin settings here.', 'wp-easy-admin' ) . '</p>';
		}

		/**
		 * Displays search menu title field.
		 */
		function wpea_key() {
			$this->opt['wpea_key'] = isset( $this->opt['wpea_key'] ) ? $this->opt['wpea_key'] : 'x';
			$html = 'ALT + <input type="text" id="wpea_key" name="wp_easy_admin[wpea_key]" value="' . esc_attr( $this->opt['wpea_key'] ) . '" size="20" />';
			$html .= '<br /><label for="wpea_key" style="font-size: 10px;">' . esc_html__( "Enter the key here that you want to use to access easy admin.", 'wp-easy-admin' ) . '</label>';
			echo $html;
		}

		/**
		 * Displays search form close icon field.
		 */
		function wpea_front_end() {
			$check_value = isset( $this->opt['wpea_front_end'] ) ? $this->opt['wpea_front_end'] : 0;
			$html = '<input type="checkbox" id="wpea_front_end" name="wp_easy_admin[wpea_front_end]" value="wpea_front_end" ' . checked( 'wpea_front_end', $check_value, false ) . ' />';
			$html .= '<label for="wpea_front_end"> ' . esc_html__( 'I want to use Easy Admin in the site front end.', 'wp-easy-admin' ) . '</label>';
			$html .= '<br /><label for="wpea_front_end" style="font-size: 10px;">' . esc_html__( "Only admin users can access easy admin in the front end of the site..", 'wp-easy-admin' ) . '</label>';
			echo $html;
		}

		/**
		 * Displays custom css field.
		 */
		function custom_css() {
			$this->opt['wp_easy_admin_css'] = isset( $this->opt['wp_easy_admin_css'] ) ? $this->opt['wp_easy_admin_css'] : '';
			$html = '<textarea rows="4" cols="53" id="wp_easy_admin_css" name="wp_easy_admin[wp_easy_admin_css]" >' . esc_attr( $this->opt['wp_easy_admin_css'] ) . '</textarea>';
			$html .= '<br /><label for="wp_easy_admin_css" style="font-size: 10px;">' . esc_html__( "Add custom css code here if any to style easy admin screen.", 'wp-easy-admin' ) . '</label>';
			echo $html;
		}

		/**
		 * Displays Easy Admin popup.
		 */
		function wpea_display_popup() { ?>
			<div id="wpea-popup">
				<div class="wpea-popup-content">
					<button type="button" class="wpea-modal-close">
						<span class="wpea-modal-icon">
							<span class="screen-reader-text"><?php _e( 'Close menu search panel', 'wp-easy-admin' ); ?></span>
						</span>
					</button>
					<div class="wpea-search-form">
						<label for="wpea-popup-input" class="screen-reader-text"><?php _e( 'Search Menu', 'wp-easy-admin' ); ?></label>
						<input type="search" placeholder="" id="wpea-popup-input" class="search">
						<div class="wpea-search-result"></div>
					</div>
				</div>
			</div>
			<?php
			if ( isset( $this->opt['wp_easy_admin_css'] ) && '' != $this->opt['wp_easy_admin_css'] ) {
				echo '<style type="text/css" media="screen">';
				echo '/* WP Easy Admin custom CSS code */';
				echo wp_specialchars_decode( esc_html( $this->opt['wp_easy_admin_css'] ), ENT_QUOTES );
				echo '</style>';
			}
		}
	}
}
