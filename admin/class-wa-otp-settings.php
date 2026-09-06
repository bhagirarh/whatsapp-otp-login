<?php
/**
 * Settings > WhatsApp OTP Login admin page: API key (+ free-key signup),
 * message template picker, front-end form style picker with live preview,
 * where the form appears, own-number connection (locked on Free, optional
 * on Introduction, required on Starter), and a usage widget.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WA_OTP_Settings {

	const OPTION_KEY = 'wa_otp_login_settings';
	const PAGE_SLUG  = 'wa-otp-login';

	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu' ) );
		add_action( 'admin_post_wa_otp_save_settings', array( __CLASS__, 'handle_save' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );

		add_action( 'wp_ajax_wa_otp_admin_signup', array( __CLASS__, 'ajax_signup' ) );
		add_action( 'wp_ajax_wa_otp_admin_connect_number', array( __CLASS__, 'ajax_connect_number' ) );
	}

	public static function add_menu() {
		add_options_page(
			__( 'WhatsApp OTP Login', 'whatsapp-otp-login' ),
			__( 'WhatsApp OTP Login', 'whatsapp-otp-login' ),
			'manage_options',
			self::PAGE_SLUG,
			array( __CLASS__, 'render_page' )
		);
	}

	public static function enqueue_admin_assets( $hook ) {
		if ( 'settings_page_' . self::PAGE_SLUG !== $hook ) {
			return;
		}
		wp_enqueue_style( 'wa-otp-admin', WA_OTP_LOGIN_URL . 'assets/css/admin.css', array(), WA_OTP_LOGIN_VERSION );
		wp_enqueue_script( 'wa-otp-admin', WA_OTP_LOGIN_URL . 'assets/js/admin.js', array( 'jquery' ), WA_OTP_LOGIN_VERSION, true );
		wp_localize_script( 'wa-otp-admin', 'WA_OTP_ADMIN', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'wa_otp_admin_nonce' ),
			'site_url' => home_url(),
		) );
	}

	public static function handle_save() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to do this.', 'whatsapp-otp-login' ) );
		}
		check_admin_referer( 'wa_otp_save_settings' );

		$settings = wa_otp_login_get_settings();

		$settings['api_key']          = isset( $_POST['api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['api_key'] ) ) : '';
		$settings['template_variant'] = isset( $_POST['template_variant'] ) && in_array( $_POST['template_variant'], array( 'plain', 'expiry', 'expiry_security' ), true )
			? sanitize_text_field( wp_unslash( $_POST['template_variant'] ) ) : 'expiry_security';
		$settings['form_style']       = isset( $_POST['form_style'] ) && in_array( $_POST['form_style'], array( 'modern', 'minimal', 'card' ), true )
			? sanitize_text_field( wp_unslash( $_POST['form_style'] ) ) : 'modern';
		$settings['show_on_login']    = isset( $_POST['show_on_login'] ) ? 1 : 0;
		$settings['show_on_register'] = isset( $_POST['show_on_register'] ) ? 1 : 0;
		$settings['show_on_checkout'] = isset( $_POST['show_on_checkout'] ) ? 1 : 0;

		update_option( self::OPTION_KEY, $settings );

		wp_safe_redirect( add_query_arg( array( 'page' => self::PAGE_SLUG, 'updated' => '1' ), admin_url( 'options-general.php' ) ) );
		exit;
	}

	public static function ajax_signup() {
		check_ajax_referer( 'wa_otp_admin_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'whatsapp-otp-login' ) ) );
		}

		$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		if ( ! is_email( $email ) ) {
			wp_send_json_error( array( 'message' => __( 'Enter a valid email address.', 'whatsapp-otp-login' ) ) );
		}

		$result = WA_OTP_Api_Client::signup( $email, home_url() );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}
		if ( empty( $result['success'] ) ) {
			wp_send_json_error( array( 'message' => $result['error'] ?? __( 'Signup failed.', 'whatsapp-otp-login' ) ) );
		}

		if ( ! empty( $result['api_key'] ) ) {
			$settings = wa_otp_login_get_settings();
			$settings['api_key'] = sanitize_text_field( $result['api_key'] );
			update_option( self::OPTION_KEY, $settings );
		}

		wp_send_json_success( array( 'api_key' => $result['api_key'] ?? '' ) );
	}

	public static function ajax_connect_number() {
		check_ajax_referer( 'wa_otp_admin_nonce', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'whatsapp-otp-login' ) ) );
		}

		$args = array(
			'access_token'       => isset( $_POST['access_token'] ) ? sanitize_text_field( wp_unslash( $_POST['access_token'] ) ) : '',
			'whatsapp_number_id' => isset( $_POST['whatsapp_number_id'] ) ? sanitize_text_field( wp_unslash( $_POST['whatsapp_number_id'] ) ) : '',
			'waba_id'            => isset( $_POST['waba_id'] ) ? sanitize_text_field( wp_unslash( $_POST['waba_id'] ) ) : '',
			'preferred_sender'   => isset( $_POST['preferred_sender'] ) ? sanitize_text_field( wp_unslash( $_POST['preferred_sender'] ) ) : '',
		);

		$result = WA_OTP_Api_Client::connect_number( $args );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}
		if ( empty( $result['success'] ) ) {
			wp_send_json_error( array( 'message' => $result['error'] ?? __( 'Could not save this number.', 'whatsapp-otp-login' ) ) );
		}

		wp_send_json_success( $result );
	}

	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = wa_otp_login_get_settings();
		$usage    = $settings['api_key'] ? WA_OTP_Api_Client::usage() : null;
		$plan_key = ( is_array( $usage ) && ! empty( $usage['plan']['key'] ) ) ? $usage['plan']['key'] : null;
		$sender_mode = ( is_array( $usage ) && ! empty( $usage['plan']['sender_mode'] ) ) ? $usage['plan']['sender_mode'] : 'platform_default';
		$can_edit_number = 'platform_default' !== $sender_mode;

		include WA_OTP_LOGIN_DIR . 'admin/views/settings-page.php';
	}
}

WA_OTP_Settings::init();
