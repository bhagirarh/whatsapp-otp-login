<?php
/**
 * Plugin Name:       WALoops – WhatsApp OTP Login
 * Plugin URI:        https://waloops.com/wordpress-otp-login
 * Description:       Add "Login / Register with WhatsApp OTP" to your WordPress login, registration and WooCommerce checkout screens. 100 OTP messages free every month.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            WALoops
 * Author URI:        https://waloops.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       whatsapp-otp-login
 * Domain Path:       /languages
 *
 * This plugin sends the phone number entered at login/registration to the
 * WALoops WhatsApp OTP API (https://app.waloops.com/api/otp/) solely to
 * deliver the one-time password over WhatsApp. See readme.txt "External
 * services" section for full disclosure.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

define( 'WA_OTP_LOGIN_VERSION', '1.0.0' );
define( 'WA_OTP_LOGIN_FILE', __FILE__ );
define( 'WA_OTP_LOGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WA_OTP_LOGIN_URL', plugin_dir_url( __FILE__ ) );
// Base URL of the WALoops backend this plugin talks to. Not user-configurable
// on purpose — it always points at the account's own API key's home, never a
// third-party server.
define( 'WA_OTP_API_BASE', 'https://app.waloops.com/api/otp' );

require_once WA_OTP_LOGIN_DIR . 'includes/class-wa-otp-api-client.php';
require_once WA_OTP_LOGIN_DIR . 'includes/class-wa-otp-user.php';
require_once WA_OTP_LOGIN_DIR . 'includes/class-wa-otp-auth.php';

if ( is_admin() ) {
	require_once WA_OTP_LOGIN_DIR . 'admin/class-wa-otp-settings.php';
}

/**
 * Default options written on activation — never overwrites an existing
 * install's settings (e.g. a deactivate/reactivate cycle keeps the API key).
 */
function wa_otp_login_activate() {
	$defaults = array(
		'api_key'          => '',
		'template_variant' => 'expiry_security', // plain | expiry | expiry_security — see readme: only expiry_security has an approved Meta template right now
		'code_ttl_minutes' => 5,
		'form_style'       => 'modern', // modern | minimal | card
		'show_on_login'    => 1,
		'show_on_register' => 1,
		'show_on_checkout' => 0,
	);
	if ( false === get_option( 'wa_otp_login_settings' ) ) {
		add_option( 'wa_otp_login_settings', $defaults );
	}
}
register_activation_hook( __FILE__, 'wa_otp_login_activate' );

function wa_otp_login_load_textdomain() {
	load_plugin_textdomain( 'whatsapp-otp-login', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'wa_otp_login_load_textdomain' );

/**
 * Reads the merged settings array (defaults + saved options) — the single
 * source every other file in this plugin pulls its config from.
 */
function wa_otp_login_get_settings() {
	$defaults = array(
		'api_key'          => '',
		'template_variant' => 'expiry_security',
		'code_ttl_minutes' => 5,
		'form_style'       => 'modern',
		'show_on_login'    => 1,
		'show_on_register' => 1,
		'show_on_checkout' => 0,
	);
	return wp_parse_args( get_option( 'wa_otp_login_settings', array() ), $defaults );
}
