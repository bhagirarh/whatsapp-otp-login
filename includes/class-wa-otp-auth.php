<?php
/**
 * Front-end integration: renders the OTP form (via shortcode + auto-hooks
 * into wp-login.php and WooCommerce checkout) and handles the AJAX
 * send/verify round trip. This is an *added* login/register option — it
 * never replaces the normal username/password form.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WA_OTP_Auth {

	public static function init() {
		add_shortcode( 'wa_otp_login', array( __CLASS__, 'shortcode' ) );

		$settings = wa_otp_login_get_settings();
		if ( ! empty( $settings['show_on_login'] ) ) {
			add_action( 'login_form', array( __CLASS__, 'render_on_login_form' ) );
		}
		if ( ! empty( $settings['show_on_register'] ) ) {
			add_action( 'register_form', array( __CLASS__, 'render_on_login_form' ) );
		}
		if ( ! empty( $settings['show_on_checkout'] ) ) {
			add_action( 'woocommerce_before_checkout_form', array( __CLASS__, 'render' ) );
		}

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'login_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );

		add_action( 'wp_ajax_nopriv_wa_otp_send', array( __CLASS__, 'ajax_send' ) );
		add_action( 'wp_ajax_wa_otp_send', array( __CLASS__, 'ajax_send' ) );
		add_action( 'wp_ajax_nopriv_wa_otp_verify', array( __CLASS__, 'ajax_verify' ) );
		add_action( 'wp_ajax_wa_otp_verify', array( __CLASS__, 'ajax_verify' ) );
	}

	public static function enqueue_assets() {
		$settings = wa_otp_login_get_settings();
		$style = in_array( $settings['form_style'], array( 'modern', 'minimal', 'card' ), true ) ? $settings['form_style'] : 'modern';

		wp_enqueue_style( 'wa-otp-login-' . $style, WA_OTP_LOGIN_URL . "assets/css/otp-{$style}.css", array(), WA_OTP_LOGIN_VERSION );
		wp_enqueue_script( 'wa-otp-login-frontend', WA_OTP_LOGIN_URL . 'assets/js/otp-frontend.js', array( 'jquery' ), WA_OTP_LOGIN_VERSION, true );
		wp_localize_script( 'wa-otp-login-frontend', 'WA_OTP', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'wa_otp_nonce' ),
			'i18n'     => array(
				'sending'         => __( 'Sending code…', 'whatsapp-otp-login' ),
				'sent'            => __( 'Code sent! Check WhatsApp.', 'whatsapp-otp-login' ),
				'verifying'       => __( 'Verifying…', 'whatsapp-otp-login' ),
				'invalid_phone'   => __( 'Enter a valid WhatsApp number with country code.', 'whatsapp-otp-login' ),
				'generic_error'   => __( 'Something went wrong. Please try again.', 'whatsapp-otp-login' ),
				'resend_wait'     => __( 'You can resend in %d s', 'whatsapp-otp-login' ),
			),
		) );
	}

	public static function render_on_login_form() {
		// login_form and register_form are two distinct WP actions firing
		// this same callback — current_action() tells them apart so the
		// template knows whether a first-time phone may create an account.
		$context = 'register_form' === current_action() ? 'register' : 'login';
		echo self::render( false, $context ); // phpcs:ignore WordPress.Security.EscapeOutput -- render() output is already escaped at the point of interpolation.
	}

	public static function shortcode() {
		return self::render( false, 'login' );
	}

	/**
	 * @param bool   $echo    Whether to echo directly (used by the checkout
	 *                        hook, which is a plain action, not a filter).
	 * @param string $context 'login' or 'register' — controls whether a
	 *                        verified phone with no linked account may
	 *                        create one (see ajax_verify()).
	 */
	public static function render( $echo = true, $context = 'login' ) {
		$settings = wa_otp_login_get_settings();
		$style = in_array( $settings['form_style'], array( 'modern', 'minimal', 'card' ), true ) ? $settings['form_style'] : 'modern';
		$template_path = WA_OTP_LOGIN_DIR . "templates/otp-form-{$style}.php";

		if ( empty( $settings['api_key'] ) ) {
			return ''; // Nothing to render until the site owner has an API key.
		}

		ob_start();
		if ( file_exists( $template_path ) ) {
			include $template_path;
		}
		$html = ob_get_clean();

		if ( $echo ) {
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput -- template output is already escaped at the point of interpolation.
			return '';
		}
		return $html;
	}

	private static function check_ajax_nonce() {
		check_ajax_referer( 'wa_otp_nonce', 'nonce' );
	}

	public static function ajax_send() {
		self::check_ajax_nonce();

		$phone = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
		$phone = preg_replace( '/[^0-9]/', '', $phone );

		if ( strlen( $phone ) < 8 ) {
			wp_send_json_error( array( 'message' => __( 'Enter a valid WhatsApp number with country code.', 'whatsapp-otp-login' ) ) );
		}

		$result = WA_OTP_Api_Client::send_otp( $phone );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}
		if ( empty( $result['success'] ) ) {
			wp_send_json_error( array( 'message' => $result['error'] ?? __( 'Could not send the code. Please try again.', 'whatsapp-otp-login' ) ) );
		}

		wp_send_json_success( array( 'expires_in' => $result['expires_in'] ?? 300 ) );
	}

	public static function ajax_verify() {
		self::check_ajax_nonce();

		$phone = isset( $_POST['phone'] ) ? preg_replace( '/[^0-9]/', '', sanitize_text_field( wp_unslash( $_POST['phone'] ) ) ) : '';
		$code = isset( $_POST['code'] ) ? sanitize_text_field( wp_unslash( $_POST['code'] ) ) : '';

		if ( ! $phone || ! $code ) {
			wp_send_json_error( array( 'message' => __( 'Missing phone or code.', 'whatsapp-otp-login' ) ) );
		}

		$result = WA_OTP_Api_Client::verify_otp( $phone, $code );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}
		if ( empty( $result['verified'] ) ) {
			wp_send_json_error( array( 'message' => $result['error'] ?? __( 'Incorrect or expired code.', 'whatsapp-otp-login' ) ) );
		}

		// Verified with our backend — now resolve/create the local WP user
		// and complete the login. Registration screens allow account
		// creation; the plain login form does not (a phone with no linked
		// account there is told to register first).
		$is_registration_context = isset( $_POST['context'] ) && 'register' === $_POST['context'];
		$existing_user = WA_OTP_User::find_by_phone( $phone );
		if ( $existing_user ) {
			$user = $existing_user;
		} elseif ( $is_registration_context ) {
			$user = WA_OTP_User::find_or_create( $phone, true );
		} else {
			$user = new WP_Error( 'wa_otp_no_account', __( 'No account is linked to this WhatsApp number yet. Please register first.', 'whatsapp-otp-login' ) );
		}

		if ( is_wp_error( $user ) ) {
			wp_send_json_error( array( 'message' => $user->get_error_message() ) );
		}

		WA_OTP_User::log_in( $user );

		wp_send_json_success( array(
			'redirect' => apply_filters( 'wa_otp_login_redirect', admin_url(), $user ),
		) );
	}
}

WA_OTP_Auth::init();
