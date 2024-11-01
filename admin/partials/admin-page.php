<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package WPEA
 */
?>
<div class="wrap">
	<form id="wp_easy_admin_options" action="options.php" method="post">
		<?php
			settings_fields( 'wp_easy_admin' );
			do_settings_sections( 'wp_easy_admin' );
			submit_button( 'Save Options', 'primary', 'wp_easy_admin_options_submit' );
		?>
		<div id="after-submit">
			<p>
				<?php esc_html_e( 'Like WP Easy Admin?', 'wp-easy-admin' ); ?> <a href="https://wordpress.org/support/plugin/wp-easy-admin/reviews/?filter=5#new-post" target="_blank"><?php esc_html_e( 'Give us a rating', 'wp-easy-admin' ); ?></a>
			</p>
			<p>
				<?php esc_html_e( 'Need Help or Have Suggestions?', 'wp-easy-admin' ); ?> <?php esc_html_e( 'contact us on', 'wp-easy-admin' ); ?> <a href="https://wordpress.org/support/plugin/wp-easy-admin/" target="_blank"><?php esc_html_e( 'Plugin support forum', 'wp-easy-admin' ); ?></a>
			</p>
		</div>
	 </form>
</div>
