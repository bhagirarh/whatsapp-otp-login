<?php
/**
 * Thin wrapper around the WALoops OTP backend (app.waloops.com/api/otp/*).
 * Every request goes through wp_remote_post()/wp_remote_get() — no raw
 * cURL — per WordPress.org plugin guidelines.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WA_OTP_Api_Client {

	/**
	 * Calls /api/otp/signup.php — no API key needed yet, this is how one is
	 * obtained. Returns the decoded response array, or a WP_Error.
	 */
	public static function signup( $email, $site_url ) {
		return self::request( 'POST', 'signup.php', array(
			'email'    => $email,
			'site_url' => $site_url,
		), '' );
	}

	public static function send_otp( $phone, $purpose = 'login' ) {
		$settings = wa_otp_login_get_settings();
		return self::request( 'POST', 'send.php', array(
			'phone'             => $phone,
			'purpose'           => $purpose,
			'template_variant'  => $settings['template_variant'],
		), $settings['api_key'] );
	}

	public static function verify_otp( $phone, $code ) {
		$settings = wa_otp_login_get_settings();
		return self::request( 'POST', 'verify.php', array(
			'phone' => $phone,
			'code'  => $code,
		), $settings['api_key'] );
	}

	public static function usage() {
		$settings = wa_otp_login_get_settings();
		return self::request( 'GET', 'usage.php', array(), $settings['api_key'] );
	}

	public static function connect_number( $args ) {
		$settings = wa_otp_login_get_settings();
		return self::request( 'POST', 'connect_number.php', $args, $settings['api_key'] );
	}

	/**
	 * @param string $method  'GET' or 'POST'.
	 * @param string $path    Endpoint filename, e.g. 'send.php'.
	 * @param array  $body    Request body (POST) or query args (GET).
	 * @param string $api_key Empty string for the unauthenticated signup call.
	 * @return array|WP_Error Decoded JSON body on success (HTTP errors from
	 *                        the API itself are also returned as an array
	 *                        with an 'error' key — only transport failures
	 *                        become a WP_Error), or WP_Error on transport failure.
	 */
	private static function request( $method, $path, array $body, $api_key ) {
		$url = trailingslashit( WA_OTP_API_BASE ) . ltrim( $path, '/' );

		$args = array(
			'timeout' => 15,
			'headers' => array(),
		);
		if ( $api_key ) {
			$args['headers']['X-API-Key'] = $api_key;
		}

		if ( 'GET' === $method ) {
			if ( ! empty( $body ) ) {
				$url = add_query_arg( $body, $url );
			}
			$response = wp_remote_get( $url, $args );
		} else {
			$args['headers']['Content-Type'] = 'application/json';
			$args['body'] = wp_json_encode( $body );
			$response = wp_remote_post( $url, $args );
		}

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$decoded = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! is_array( $decoded ) ) {
			return new WP_Error( 'wa_otp_bad_response', __( 'The WhatsApp OTP service returned an unexpected response.', 'whatsapp-otp-login' ) );
		}

		return $decoded;
	}
}
