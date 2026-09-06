<?php
/**
 * "Card" front-end OTP form — boxed panel with a WhatsApp-green header and
 * icon. Included by WA_OTP_Auth::render(); $context is in scope.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wa-otp-form wa-otp-card-style" data-context="<?php echo esc_attr( $context ); ?>">
	<div class="wa-otp-card-style__header">
		<span class="wa-otp-card-style__icon" aria-hidden="true">
			<svg width="20" height="20" viewBox="0 0 24 24" fill="#fff"><path d="M12 2C6.48 2 2 6.48 2 12c0 1.85.5 3.58 1.38 5.07L2 22l4.93-1.38C8.42 21.5 10.15 22 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2z"/></svg>
		</span>
		<span><?php echo esc_html( 'register' === $context ? __( 'Register with WhatsApp', 'otp-login-by-waloops' ) : __( 'Login with WhatsApp', 'otp-login-by-waloops' ) ); ?></span>
	</div>

	<div class="wa-otp-card-style__body">
		<div class="wa-otp-step" data-step="phone">
			<label class="wa-otp-card-style__label" for="wa-otp-card-phone-<?php echo esc_attr( $context ); ?>"><?php esc_html_e( 'WhatsApp Number', 'otp-login-by-waloops' ); ?></label>
			<input type="tel" class="wa-otp-card-style__input wa-otp-phone" id="wa-otp-card-phone-<?php echo esc_attr( $context ); ?>" placeholder="+91 9XXXXXXXXX" />
			<button type="button" class="wa-otp-card-style__btn wa-otp-send-btn"><?php esc_html_e( 'Send OTP', 'otp-login-by-waloops' ); ?></button>
		</div>
		<div class="wa-otp-step" data-step="code" hidden>
			<label class="wa-otp-card-style__label"><?php esc_html_e( 'Verification Code', 'otp-login-by-waloops' ); ?></label>
			<input type="text" inputmode="numeric" maxlength="6" class="wa-otp-card-style__input wa-otp-code" placeholder="123456" />
			<button type="button" class="wa-otp-card-style__btn wa-otp-verify-btn"><?php esc_html_e( 'Verify OTP', 'otp-login-by-waloops' ); ?></button>
			<button type="button" class="wa-otp-card-style__resend wa-otp-resend-btn"><?php esc_html_e( 'Didn\'t get it? Resend', 'otp-login-by-waloops' ); ?></button>
		</div>
		<p class="wa-otp-status" role="status" aria-live="polite"></p>
	</div>
</div>
