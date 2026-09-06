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
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- uninstall.php has no caching layer to use, and $wpdb->delete() is the documented way to bulk-remove a plugin's own usermeta on Delete.
$wpdb->delete( $wpdb->usermeta, array( 'meta_key' => 'wa_otp_phone' ) );
