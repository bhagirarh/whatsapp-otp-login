<?php
/**
 * Settings page view. Rendered by WA_OTP_Settings::render_page() — expects
 * $settings, $usage, $plan_key, $sender_mode, $can_edit_number in scope.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap wa-otp-settings">
	<h1><?php esc_html_e( 'WhatsApp OTP Login', 'otp-login-by-waloops' ); ?></h1>

	<?php if ( isset( $_GET['updated'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only display flag from handle_save()'s own wp_safe_redirect(), not a state-changing action. ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'otp-login-by-waloops' ); ?></p></div>
	<?php endif; ?>

	<?php if ( empty( $settings['api_key'] ) ) : ?>
		<div class="wa-otp-card wa-otp-signup-card">
			<h2><?php esc_html_e( 'Get your free API key', 'otp-login-by-waloops' ); ?></h2>
			<p><?php esc_html_e( '100 WhatsApp OTP messages every month, free — sent from our shared WhatsApp number. No credit card required.', 'otp-login-by-waloops' ); ?></p>
			<p>
				<a href="https://app.waloops.com/#/register?plan=wp-otp-free" target="_blank" rel="noopener noreferrer" class="button button-primary"><?php esc_html_e( 'Create a free WALoops account', 'otp-login-by-waloops' ); ?></a>
				<a href="https://app.waloops.com/#/login" target="_blank" rel="noopener noreferrer" class="button"><?php esc_html_e( 'Already have one? Log in', 'otp-login-by-waloops' ); ?></a>
			</p>
			<p class="description">
				<?php esc_html_e( 'You\'ll land straight on your API key — copy it and paste it in the field below.', 'otp-login-by-waloops' ); ?>
			</p>
		</div>
	<?php else : ?>
		<div class="wa-otp-card wa-otp-usage-card">
			<h2><?php esc_html_e( 'Usage', 'otp-login-by-waloops' ); ?></h2>
			<?php if ( is_wp_error( $usage ) ) : ?>
				<p class="wa-otp-error"><?php echo esc_html( $usage->get_error_message() ); ?></p>
			<?php elseif ( is_array( $usage ) && empty( $usage['error'] ) ) : ?>
				<table class="wa-otp-usage-table">
					<tr>
						<td><?php esc_html_e( 'Plan', 'otp-login-by-waloops' ); ?></td>
						<td><strong><?php echo esc_html( $usage['plan']['name'] ?? '—' ); ?></strong></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Sent this month', 'otp-login-by-waloops' ); ?></td>
						<td>
							<?php
							$wa_otp_limit = $usage['plan']['monthly_limit'] ?? null;
							$wa_otp_sent  = $usage['usage']['sent_this_month'] ?? 0;
							echo esc_html( $wa_otp_limit === null ? sprintf( /* translators: %d: OTPs sent */ __( '%d (unlimited plan)', 'otp-login-by-waloops' ), $wa_otp_sent ) : "{$wa_otp_sent} / {$wa_otp_limit}" );
							?>
						</td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Sender number', 'otp-login-by-waloops' ); ?></td>
						<td><?php echo esc_html( ( $usage['sender']['preferred'] ?? 'platform' ) === 'own' ? __( 'Your connected number', 'otp-login-by-waloops' ) : __( 'Shared WALoops number', 'otp-login-by-waloops' ) ); ?></td>
					</tr>
				</table>
				<p><a href="https://waloops.com/wordpress-otp-login#pricing" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Need more OTPs or your own number? View plans →', 'otp-login-by-waloops' ); ?></a></p>
			<?php else : ?>
				<p class="wa-otp-error"><?php echo esc_html( $usage['error'] ?? __( 'Could not load usage.', 'otp-login-by-waloops' ) ); ?></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="wa-otp-card">
		<?php wp_nonce_field( 'wa_otp_save_settings' ); ?>
		<input type="hidden" name="action" value="wa_otp_save_settings" />

		<h2><?php esc_html_e( 'Configuration', 'otp-login-by-waloops' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="api_key"><?php esc_html_e( 'API Key', 'otp-login-by-waloops' ); ?></label></th>
				<td><input type="text" id="api_key" name="api_key" class="regular-text" value="<?php echo esc_attr( $settings['api_key'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Message style', 'otp-login-by-waloops' ); ?></th>
				<td>
					<select name="template_variant">
						<option value="expiry_security" <?php selected( $settings['template_variant'], 'expiry_security' ); ?>><?php esc_html_e( 'Code + "do not share" security notice', 'otp-login-by-waloops' ); ?></option>
						<option value="plain" disabled><?php esc_html_e( 'Code only (coming soon)', 'otp-login-by-waloops' ); ?></option>
						<option value="expiry" disabled><?php esc_html_e( 'Code + expiry notice (coming soon)', 'otp-login-by-waloops' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'WhatsApp only allows pre-approved wording for one-time-passcode messages. Only one is approved right now; more styles appear here automatically once additional ones are approved.', 'otp-login-by-waloops' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Form style', 'otp-login-by-waloops' ); ?></th>
				<td>
					<label><input type="radio" name="form_style" value="modern" <?php checked( $settings['form_style'], 'modern' ); ?> /> <?php esc_html_e( 'Modern', 'otp-login-by-waloops' ); ?></label><br />
					<label><input type="radio" name="form_style" value="minimal" <?php checked( $settings['form_style'], 'minimal' ); ?> /> <?php esc_html_e( 'Minimal', 'otp-login-by-waloops' ); ?></label><br />
					<label><input type="radio" name="form_style" value="card" <?php checked( $settings['form_style'], 'card' ); ?> /> <?php esc_html_e( 'Card', 'otp-login-by-waloops' ); ?></label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Show on', 'otp-login-by-waloops' ); ?></th>
				<td>
					<label><input type="checkbox" name="show_on_login" <?php checked( ! empty( $settings['show_on_login'] ) ); ?> /> <?php esc_html_e( 'Login page', 'otp-login-by-waloops' ); ?></label><br />
					<label><input type="checkbox" name="show_on_register" <?php checked( ! empty( $settings['show_on_register'] ) ); ?> /> <?php esc_html_e( 'Registration page', 'otp-login-by-waloops' ); ?></label><br />
					<?php if ( class_exists( 'WooCommerce' ) ) : ?>
						<label><input type="checkbox" name="show_on_checkout" <?php checked( ! empty( $settings['show_on_checkout'] ) ); ?> /> <?php esc_html_e( 'WooCommerce checkout', 'otp-login-by-waloops' ); ?></label><br />
					<?php endif; ?>
					<p class="description"><?php esc_html_e( 'Or place it anywhere with the [wa_otp_login] shortcode.', 'otp-login-by-waloops' ); ?></p>
				</td>
			</tr>
		</table>
		<?php submit_button( __( 'Save Settings', 'otp-login-by-waloops' ) ); ?>
	</form>

	<div class="wa-otp-card">
		<h2><?php esc_html_e( 'Your own WhatsApp number', 'otp-login-by-waloops' ); ?></h2>
		<?php if ( ! $settings['api_key'] ) : ?>
			<p class="description"><?php esc_html_e( 'Get an API key first.', 'otp-login-by-waloops' ); ?></p>
		<?php elseif ( ! $can_edit_number ) : ?>
			<p><?php esc_html_e( 'The Free plan always sends from our shared WhatsApp number.', 'otp-login-by-waloops' ); ?> <a href="https://waloops.com/wordpress-otp-login#pricing" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Upgrade to connect your own number →', 'otp-login-by-waloops' ); ?></a></p>
		<?php else : ?>
			<p class="description">
				<?php echo 'own_number_required' === $sender_mode
					? esc_html__( 'Your plan requires your own WhatsApp Business number — enter your Meta Cloud API credentials below.', 'otp-login-by-waloops' )
					: esc_html__( 'Optional on your plan: connect your own WhatsApp Business number, or keep using our shared number.', 'otp-login-by-waloops' ); ?>
			</p>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="wa-otp-access-token"><?php esc_html_e( 'Access Token', 'otp-login-by-waloops' ); ?></label></th>
					<td><input type="password" id="wa-otp-access-token" class="regular-text" autocomplete="off" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="wa-otp-phone-id"><?php esc_html_e( 'Phone Number ID', 'otp-login-by-waloops' ); ?></label></th>
					<td><input type="text" id="wa-otp-phone-id" class="regular-text" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="wa-otp-waba-id"><?php esc_html_e( 'WABA ID (optional)', 'otp-login-by-waloops' ); ?></label></th>
					<td><input type="text" id="wa-otp-waba-id" class="regular-text" /></td>
				</tr>
			</table>
			<p>
				<button type="button" class="button button-primary" id="wa-otp-connect-number-btn"><?php esc_html_e( 'Save & Verify', 'otp-login-by-waloops' ); ?></button>
				<?php if ( 'choice' === $sender_mode ) : ?>
					<button type="button" class="button" id="wa-otp-use-platform-btn"><?php esc_html_e( 'Use shared number instead', 'otp-login-by-waloops' ); ?></button>
				<?php endif; ?>
			</p>
			<p id="wa-otp-connect-status" class="description"></p>
		<?php endif; ?>
	</div>
</div>
