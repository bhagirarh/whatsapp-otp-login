<?php
/**
 * Maps a verified phone number to a WordPress user account and completes
 * the login — this is the only place the plugin touches wp_users/wp_usermeta.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WA_OTP_User {

	const META_KEY = 'wa_otp_phone';

	/**
	 * Finds the WP_User already linked to $phone, or null.
	 */
	public static function find_by_phone( $phone ) {
		// get_users() by a single-value meta lookup is the documented WP API
		// for this; no indexed alternative exists without introducing a
		// custom table for what's normally a small user set (OTP-plugin
		// subscribers, not the whole site).
		$users = get_users( array(
			'meta_key'   => self::META_KEY, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value' => $phone, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'number'     => 1,
		) );
		return $users ? $users[0] : null;
	}

	/**
	 * Finds the user for $phone, creating one on first-ever OTP login
	 * (registration path) if $create_if_missing is true. Returns WP_User or
	 * WP_Error.
	 */
	public static function find_or_create( $phone, $create_if_missing = true ) {
		$user = self::find_by_phone( $phone );
		if ( $user ) {
			return $user;
		}
		if ( ! $create_if_missing ) {
			return new WP_Error( 'wa_otp_no_account', __( 'No account is linked to this WhatsApp number yet. Please register first.', 'otp-login-by-waloops' ) );
		}

		$username = 'wa_' . preg_replace( '/[^0-9]/', '', $phone );
		// Extremely unlikely collision (two different phones normalizing to
		// the same digits never happens, but a stray manual account with the
		// same login name could) — fall back to a suffix rather than fail.
		$base = $username;
		$i = 1;
		while ( username_exists( $username ) ) {
			$username = $base . '_' . ( ++$i );
		}

		$user_id = wp_insert_user( array(
			'user_login' => $username,
			// WordPress requires *some* password; the account is only ever
			// accessed via OTP, so this is intentionally unusable directly.
			'user_pass'  => wp_generate_password( 32, true, true ),
			'user_email' => $username . '@wa-otp.invalid',
			'role'       => 'subscriber',
		) );

		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		update_user_meta( $user_id, self::META_KEY, $phone );

		return get_user_by( 'id', $user_id );
	}

	/**
	 * Logs $user in exactly like wp_signon() would — sets the auth cookie
	 * and fires wp_login, so anything else hooked into normal login still
	 * runs (e.g. WooCommerce's post-login redirect).
	 */
	public static function log_in( WP_User $user ) {
		wp_set_current_user( $user->ID );
		wp_set_auth_cookie( $user->ID, true );
		/** This action is documented in wp-includes/user.php */
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- 'wp_login' is WordPress core's own hook, fired deliberately here (not one this plugin defines) so anything else hooked into a normal login still runs.
		do_action( 'wp_login', $user->user_login, $user );
	}
}
