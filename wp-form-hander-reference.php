<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           wp-form-handler-reference
 *
 * @wordpress-plugin
 * Plugin Name:       WP Form Handler Reference
 * Plugin URI:        http://example.com/wp-plugin-name-uri/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Junaid Bin Jaman
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-form-handler-reference
 * Domain Path:       /languages
 */

add_action(
	'wp',
	function () {
		add_shortcode( 'example_form', 'wp_form_handler_shortcode' );
	}
);

/**
 * The shortcode displays the form.
 *
 * @return void
 */
function wp_form_handler_shortcode() {
	$action_url                      = esc_url( admin_url( 'admin-post.php' ) );
	$wp_form_handler_reference_nonce = wp_create_nonce( 'form-handler' );
	$cart_uri                        = get_permalink( wc_get_page_id( 'cart' ) );

	?>
	<form action="/cart" method="POST">
		<p>
			<label for="name">Name</label>
			<input type="text" id="name" name="name" />
		</p>
		<p>
			<label for="email">Email</label>
			<input type="email" id="email" name="email" />
		</p>
		<p>
			<label for="coupon">Coupon</label>
			<input type="text" id="coupon" name="coupon" />
		</p>
		<p>
			<label for="message">Message</label>
			<textarea rows="8" id="message" name="message"></textarea>
		</p>
		<input type="hidden" name="action" value="wp_form_handler_reference_submit_form" />
		<input type="hidden" name="form-handler-nonce" value="<?php echo esc_attr( $wp_form_handler_reference_nonce ); ?>" />
		<input type="submit" value="Submit" />
	</form>
	<?php
}

/**
 * Display a success message after form submission.
 */
function wp_form_handler_reference_display_message() {

	if (
		! isset( $_POST['action'] ) ||
		'wp_form_handler_reference_submit_form' !== $_POST['action']
	) {
		return;
	}

	if (
		! isset( $_POST['form-handler-nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['form-handler-nonce'] ) ), 'form-handler' )
	) {
		wp_die( 'Nonce verification failed' );
	}

	$coupon_code = isset( $_POST['coupon'] ) ? sanitize_text_field( wp_unslash( $_POST['coupon'] ) ) : '';

	WC()->cart->apply_coupon( $coupon_code );
}

add_action( 'init', 'wp_form_handler_reference_display_message' );

