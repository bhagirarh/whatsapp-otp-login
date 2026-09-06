<?php
/**
 * Fires only on Delete (not Deactivate) from the Plugins screen — removes
 * this plugin's own options and any wa_otp_phone usermeta it created.
 * Deliberately does NOT delete the WordPress user accounts themselves (an
 * OTP-only "subscriber" account is still a real WP user with its own
 * content/orders potentially attached) or anything on the WALoops backend
 * (the API key and its usage history stay intact if the plugin is
 * reinstalled later).
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'wa_otp_login_settings' );

global $wpdb;
$wpdb->delete( $wpdb->usermeta, array( 'meta_key' => 'wa_otp_phone' ) ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
