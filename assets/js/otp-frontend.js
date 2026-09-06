/* global WA_OTP */
/**
 * Shared front-end behavior for all three form styles (modern/minimal/card)
 * — they all use the same class names (wa-otp-phone, wa-otp-code,
 * wa-otp-send-btn, wa-otp-verify-btn, wa-otp-resend-btn, wa-otp-status,
 * [data-step]) plus optional [data-role="toggle"]/[data-role="body"] for
 * styles that start collapsed. Vanilla JS — no framework dependency needed
 * for this small amount of interaction, kept dependency-free on purpose.
 */
( function () {
	'use strict';

	function onReady( fn ) {
		if ( document.readyState !== 'loading' ) {
			fn();
		} else {
			document.addEventListener( 'DOMContentLoaded', fn );
		}
	}

	function setStatus( form, message, isError ) {
		var status = form.querySelector( '.wa-otp-status' );
		if ( ! status ) {
			return;
		}
		status.textContent = message || '';
		status.classList.toggle( 'wa-otp-status--error', !! isError );
	}

	function showStep( form, stepName ) {
		var steps = form.querySelectorAll( '[data-step]' );
		for ( var i = 0; i < steps.length; i++ ) {
			steps[ i ].hidden = steps[ i ].getAttribute( 'data-step' ) !== stepName;
		}
	}

	function postAjax( action, data, callback ) {
		var body = new URLSearchParams( Object.assign( { action: action, nonce: WA_OTP.nonce }, data ) );
		fetch( WA_OTP.ajax_url, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: body.toString(),
		} )
			.then( function ( r ) { return r.json(); } )
			.then( function ( json ) { callback( null, json ); } )
			.catch( function ( err ) { callback( err, null ); } );
	}

	function startResendCooldown( form, seconds ) {
		var resendBtn = form.querySelector( '.wa-otp-resend-btn' );
		if ( ! resendBtn ) {
			return;
		}
		var remaining = seconds;
		resendBtn.disabled = true;
		var original = resendBtn.textContent;
		var tick = function () {
			if ( remaining <= 0 ) {
				resendBtn.disabled = false;
				resendBtn.textContent = original;
				return;
			}
			resendBtn.textContent = WA_OTP.i18n.resend_wait.replace( '%d', remaining );
			remaining--;
			setTimeout( tick, 1000 );
		};
		tick();
	}

	function initForm( form ) {
		var toggle = form.querySelector( '[data-role="toggle"]' );
		var body = form.querySelector( '[data-role="body"]' );
		if ( toggle && body ) {
			toggle.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				body.hidden = ! body.hidden;
			} );
		}

		var phoneInput = form.querySelector( '.wa-otp-phone' );
		var codeInput = form.querySelector( '.wa-otp-code' );
		var sendBtn = form.querySelector( '.wa-otp-send-btn' );
		var verifyBtn = form.querySelector( '.wa-otp-verify-btn' );
		var resendBtn = form.querySelector( '.wa-otp-resend-btn' );
		var context = form.getAttribute( 'data-context' ) || 'login';

		function doSend() {
			var phone = ( phoneInput.value || '' ).replace( /[^0-9+]/g, '' );
			if ( phone.replace( '+', '' ).length < 8 ) {
				setStatus( form, WA_OTP.i18n.invalid_phone, true );
				return;
			}
			setStatus( form, WA_OTP.i18n.sending, false );
			sendBtn.disabled = true;

			postAjax( 'wa_otp_send', { phone: phone }, function ( err, json ) {
				sendBtn.disabled = false;
				if ( err || ! json || ! json.success ) {
					setStatus( form, ( json && json.data && json.data.message ) || WA_OTP.i18n.generic_error, true );
					return;
				}
				setStatus( form, WA_OTP.i18n.sent, false );
				showStep( form, 'code' );
				startResendCooldown( form, 30 );
				if ( codeInput ) {
					codeInput.focus();
				}
			} );
		}

		function doVerify() {
			var phone = ( phoneInput.value || '' ).replace( /[^0-9+]/g, '' );
			var code = ( codeInput.value || '' ).trim();
			if ( ! code ) {
				return;
			}
			setStatus( form, WA_OTP.i18n.verifying, false );
			verifyBtn.disabled = true;

			postAjax( 'wa_otp_verify', { phone: phone, code: code, context: context }, function ( err, json ) {
				verifyBtn.disabled = false;
				if ( err || ! json || ! json.success ) {
					setStatus( form, ( json && json.data && json.data.message ) || WA_OTP.i18n.generic_error, true );
					return;
				}
				setStatus( form, '', false );
				window.location.href = json.data.redirect || window.location.href;
			} );
		}

		if ( sendBtn ) {
			sendBtn.addEventListener( 'click', doSend );
		}
		if ( verifyBtn ) {
			verifyBtn.addEventListener( 'click', doVerify );
		}
		if ( resendBtn ) {
			resendBtn.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				if ( ! resendBtn.disabled ) {
					doSend();
				}
			} );
		}
		if ( codeInput ) {
			codeInput.addEventListener( 'keydown', function ( e ) {
				if ( 'Enter' === e.key ) {
					e.preventDefault();
					doVerify();
				}
			} );
		}
	}

	onReady( function () {
		var forms = document.querySelectorAll( '.wa-otp-form' );
		for ( var i = 0; i < forms.length; i++ ) {
			initForm( forms[ i ] );
		}
	} );
} )();
