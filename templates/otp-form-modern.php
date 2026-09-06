<?php
/**
 * "Modern" front-end OTP form — rounded card, brand-green accent, animated
 * code boxes. Included by WA_OTP_Auth::render(); $context ('login' or
 * 'register') is in scope.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wa-otp-form wa-otp-modern" data-context="<?php echo esc_attr( $context ); ?>">
	<div class="wa-otp-modern__toggle" data-role="toggle">
		<span class="wa-otp-modern__icon" aria-hidden="true">
			<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12c0 1.85.5 3.58 1.38 5.07L2 22l4.93-1.38C8.42 21.5 10.15 22 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2z"/></svg>
		</span>
		<?php echo esc_html( 'register' === $context ? __( 'Or register with WhatsApp OTP', 'whatsapp-otp-login' ) : __( 'Or log in with WhatsApp OTP', 'whatsapp-otp-login' ) ); ?>
	</div>

	<div class="wa-otp-modern__body" data-role="body" hidden>
		<div class="wa-otp-step" data-step="phone">
			<label class="wa-otp-modern__label" for="wa-otp-phone-<?php echo esc_attr( $context ); ?>"><?php esc_html_e( 'WhatsApp number (with country code)', 'whatsapp-otp-login' ); ?></label>
			<input type="tel" class="wa-otp-modern__input wa-otp-phone" id="wa-otp-phone-<?php echo esc_attr( $context ); ?>" placeholder="+91 9XXXXXXXXX" />
			<button type="button" class="wa-otp-modern__btn wa-otp-send-btn"><?php esc_html_e( 'Send Code', 'whatsapp-otp-login' ); ?></button>
		</div>

		<div class="wa-otp-step" data-step="code" hidden>
			<label class="wa-otp-modern__label"><?php esc_html_e( 'Enter the 6-digit code', 'whatsapp-otp-login' ); ?></label>
			<div class="wa-otp-modern__code-boxes">
				<input type="text" inputmode="numeric" maxlength="6" class="wa-otp-modern__input wa-otp-code" placeholder="••••••" />
			</div>
			<button type="button" class="wa-otp-modern__btn wa-otp-verify-btn"><?php esc_html_e( 'Verify & Continue', 'whatsapp-otp-login' ); ?></button>
			<button type="button" class="wa-otp-modern__resend wa-otp-resend-btn"><?php esc_html_e( 'Resend code', 'whatsapp-otp-login' ); ?></button>
		</div>

		<p class="wa-otp-status" role="status" aria-live="polite"></p>
	</div>
</div>
