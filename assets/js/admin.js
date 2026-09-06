/* global jQuery, WA_OTP_ADMIN */
( function ( $ ) {
	'use strict';

	$( function () {
		$( '#wa-otp-connect-number-btn' ).on( 'click', function () {
			var $btn = $( this );
			var $status = $( '#wa-otp-connect-status' );
			var accessToken = $( '#wa-otp-access-token' ).val();
			var phoneId = $( '#wa-otp-phone-id' ).val();
			var wabaId = $( '#wa-otp-waba-id' ).val();

			if ( ! accessToken || ! phoneId ) {
				$status.text( 'Access Token and Phone Number ID are both required.' );
				return;
			}

			$btn.prop( 'disabled', true );
			$status.text( 'Verifying with Meta…' );

			$.post( WA_OTP_ADMIN.ajax_url, {
				action: 'wa_otp_admin_connect_number',
				nonce: WA_OTP_ADMIN.nonce,
				access_token: accessToken,
				whatsapp_number_id: phoneId,
				waba_id: wabaId,
				preferred_sender: 'own',
			} ).done( function ( json ) {
				$btn.prop( 'disabled', false );
				if ( json && json.success ) {
					$status.text( 'Connected: ' + ( json.data.display_phone_number || '' ) );
				} else {
					$status.text( ( json && json.data && json.data.message ) || 'Could not verify this number.' );
				}
			} ).fail( function () {
				$btn.prop( 'disabled', false );
				$status.text( 'Could not reach the server. Try again.' );
			} );
		} );

		$( '#wa-otp-use-platform-btn' ).on( 'click', function () {
			var $btn = $( this );
			var $status = $( '#wa-otp-connect-status' );
			$btn.prop( 'disabled', true );

			$.post( WA_OTP_ADMIN.ajax_url, {
				action: 'wa_otp_admin_connect_number',
				nonce: WA_OTP_ADMIN.nonce,
				preferred_sender: 'platform',
			} ).done( function ( json ) {
				$btn.prop( 'disabled', false );
				$status.text( json && json.success ? 'Switched to shared number.' : ( ( json && json.data && json.data.message ) || 'Could not switch.' ) );
			} ).fail( function () {
				$btn.prop( 'disabled', false );
				$status.text( 'Could not reach the server. Try again.' );
			} );
		} );
	} );
} )( jQuery );
