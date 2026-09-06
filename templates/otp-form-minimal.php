<?php
/**
 * "Minimal" front-end OTP form — bare inputs, no chrome, blends into any
 * theme's own form styling. Included by WA_OTP_Auth::render(); $context is
 * in scope.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wa-otp-form wa-otp-minimal" data-context="<?php echo esc_attr( $context ); ?>">
	<p class="wa-otp-minimal__toggle-wrap">
		<a href="#" class="wa-otp-minimal__toggle" data-role="toggle"><?php echo esc_html( 'register' === $context ? __( 'Register with WhatsApp OTP instead', 'whatsapp-otp-login' ) : __( 'Log in with WhatsApp OTP instead', 'whatsapp-otp-login' ) ); ?></a>
	</p>

	<div class="wa-otp-minimal__body" data-role="body" hidden>
		<div class="wa-otp-step" data-step="phone">
			<input type="tel" class="wa-otp-minimal__input wa-otp-phone" placeholder="<?php esc_attr_e( 'WhatsApp number, e.g. +91 9XXXXXXXXX', 'whatsapp-otp-login' ); ?>" />
			<button type="button" class="wa-otp-minimal__btn wa-otp-send-btn"><?php esc_html_e( 'Send code', 'whatsapp-otp-login' ); ?></button>
		</div>
		<div class="wa-otp-step" data-step="code" hidden>
			<input type="text" inputmode="numeric" maxlength="6" class="wa-otp-minimal__input wa-otp-code" placeholder="<?php esc_attr_e( '6-digit code', 'whatsapp-otp-login' ); ?>" />
			<button type="button" class="wa-otp-minimal__btn wa-otp-verify-btn"><?php esc_html_e( 'Verify', 'whatsapp-otp-login' ); ?></button>
			<a href="#" class="wa-otp-minimal__resend wa-otp-resend-btn"><?php esc_html_e( 'Resend', 'whatsapp-otp-login' ); ?></a>
		</div>
		<p class="wa-otp-status" role="status" aria-live="polite"></p>
	</div>
</div>
