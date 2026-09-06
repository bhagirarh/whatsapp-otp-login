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
	<h1><?php esc_html_e( 'WhatsApp OTP Login', 'whatsapp-otp-login' ); ?></h1>

	<?php if ( isset( $_GET['updated'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'whatsapp-otp-login' ); ?></p></div>
	<?php endif; ?>

	<?php if ( empty( $settings['api_key'] ) ) : ?>
		<div class="wa-otp-card wa-otp-signup-card">
			<h2><?php esc_html_e( 'Get your free API key', 'whatsapp-otp-login' ); ?></h2>
			<p><?php esc_html_e( '100 WhatsApp OTP messages every month, free — sent from our shared WhatsApp number. No credit card required.', 'whatsapp-otp-login' ); ?></p>
			<p>
				<input type="email" id="wa-otp-signup-email" class="regular-text" placeholder="<?php esc_attr_e( 'you@example.com', 'whatsapp-otp-login' ); ?>" />
				<button type="button" class="button button-primary" id="wa-otp-signup-btn"><?php esc_html_e( 'Get Free API Key', 'whatsapp-otp-login' ); ?></button>
			</p>
			<p id="wa-otp-signup-status" class="description"></p>
			<p class="description">
				<?php esc_html_e( 'Already have an API key? Paste it in the field below instead.', 'whatsapp-otp-login' ); ?>
			</p>
		</div>
	<?php else : ?>
		<div class="wa-otp-card wa-otp-usage-card">
			<h2><?php esc_html_e( 'Usage', 'whatsapp-otp-login' ); ?></h2>
			<?php if ( is_wp_error( $usage ) ) : ?>
				<p class="wa-otp-error"><?php echo esc_html( $usage->get_error_message() ); ?></p>
			<?php elseif ( is_array( $usage ) && empty( $usage['error'] ) ) : ?>
				<table class="wa-otp-usage-table">
					<tr>
						<td><?php esc_html_e( 'Plan', 'whatsapp-otp-login' ); ?></td>
						<td><strong><?php echo esc_html( $usage['plan']['name'] ?? '—' ); ?></strong></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Sent this month', 'whatsapp-otp-login' ); ?></td>
						<td>
							<?php
							$limit = $usage['plan']['monthly_limit'] ?? null;
							$sent  = $usage['usage']['sent_this_month'] ?? 0;
							echo esc_html( $limit === null ? sprintf( /* translators: %d: OTPs sent */ __( '%d (unlimited plan)', 'whatsapp-otp-login' ), $sent ) : "{$sent} / {$limit}" );
							?>
						</td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Sender number', 'whatsapp-otp-login' ); ?></td>
						<td><?php echo esc_html( ( $usage['sender']['preferred'] ?? 'platform' ) === 'own' ? __( 'Your connected number', 'whatsapp-otp-login' ) : __( 'Shared WALoops number', 'whatsapp-otp-login' ) ); ?></td>
					</tr>
				</table>
				<p><a href="https://waloops.com/wordpress-otp-login#pricing" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Need more OTPs or your own number? View plans →', 'whatsapp-otp-login' ); ?></a></p>
			<?php else : ?>
				<p class="wa-otp-error"><?php echo esc_html( $usage['error'] ?? __( 'Could not load usage.', 'whatsapp-otp-login' ) ); ?></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="wa-otp-card">
		<?php wp_nonce_field( 'wa_otp_save_settings' ); ?>
		<input type="hidden" name="action" value="wa_otp_save_settings" />

		<h2><?php esc_html_e( 'Configuration', 'whatsapp-otp-login' ); ?></h2>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="api_key"><?php esc_html_e( 'API Key', 'whatsapp-otp-login' ); ?></label></th>
				<td><input type="text" id="api_key" name="api_key" class="regular-text" value="<?php echo esc_attr( $settings['api_key'] ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Message style', 'whatsapp-otp-login' ); ?></th>
				<td>
					<select name="template_variant">
						<option value="expiry_security" <?php selected( $settings['template_variant'], 'expiry_security' ); ?>><?php esc_html_e( 'Code + "do not share" security notice', 'whatsapp-otp-login' ); ?></option>
						<option value="plain" disabled><?php esc_html_e( 'Code only (coming soon)', 'whatsapp-otp-login' ); ?></option>
						<option value="expiry" disabled><?php esc_html_e( 'Code + expiry notice (coming soon)', 'whatsapp-otp-login' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'WhatsApp only allows pre-approved wording for one-time-passcode messages. Only one is approved right now; more styles appear here automatically once additional ones are approved.', 'whatsapp-otp-login' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Form style', 'whatsapp-otp-login' ); ?></th>
				<td>
					<label><input type="radio" name="form_style" value="modern" <?php checked( $settings['form_style'], 'modern' ); ?> /> <?php esc_html_e( 'Modern', 'whatsapp-otp-login' ); ?></label><br />
					<label><input type="radio" name="form_style" value="minimal" <?php checked( $settings['form_style'], 'minimal' ); ?> /> <?php esc_html_e( 'Minimal', 'whatsapp-otp-login' ); ?></label><br />
					<label><input type="radio" name="form_style" value="card" <?php checked( $settings['form_style'], 'card' ); ?> /> <?php esc_html_e( 'Card', 'whatsapp-otp-login' ); ?></label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Show on', 'whatsapp-otp-login' ); ?></th>
				<td>
					<label><input type="checkbox" name="show_on_login" <?php checked( ! empty( $settings['show_on_login'] ) ); ?> /> <?php esc_html_e( 'Login page', 'whatsapp-otp-login' ); ?></label><br />
					<label><input type="checkbox" name="show_on_register" <?php checked( ! empty( $settings['show_on_register'] ) ); ?> /> <?php esc_html_e( 'Registration page', 'whatsapp-otp-login' ); ?></label><br />
					<?php if ( class_exists( 'WooCommerce' ) ) : ?>
						<label><input type="checkbox" name="show_on_checkout" <?php checked( ! empty( $settings['show_on_checkout'] ) ); ?> /> <?php esc_html_e( 'WooCommerce checkout', 'whatsapp-otp-login' ); ?></label><br />
					<?php endif; ?>
					<p class="description"><?php esc_html_e( 'Or place it anywhere with the [wa_otp_login] shortcode.', 'whatsapp-otp-login' ); ?></p>
				</td>
			</tr>
		</table>
		<?php submit_button( __( 'Save Settings', 'whatsapp-otp-login' ) ); ?>
	</form>

	<div class="wa-otp-card">
		<h2><?php esc_html_e( 'Your own WhatsApp number', 'whatsapp-otp-login' ); ?></h2>
		<?php if ( ! $settings['api_key'] ) : ?>
			<p class="description"><?php esc_html_e( 'Get an API key first.', 'whatsapp-otp-login' ); ?></p>
		<?php elseif ( ! $can_edit_number ) : ?>
			<p><?php esc_html_e( 'The Free plan always sends from our shared WhatsApp number.', 'whatsapp-otp-login' ); ?> <a href="https://waloops.com/wordpress-otp-login#pricing" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Upgrade to connect your own number →', 'whatsapp-otp-login' ); ?></a></p>
		<?php else : ?>
			<p class="description">
				<?php echo 'own_number_required' === $sender_mode
					? esc_html__( 'Your plan requires your own WhatsApp Business number — enter your Meta Cloud API credentials below.', 'whatsapp-otp-login' )
					: esc_html__( 'Optional on your plan: connect your own WhatsApp Business number, or keep using our shared number.', 'whatsapp-otp-login' ); ?>
			</p>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="wa-otp-access-token"><?php esc_html_e( 'Access Token', 'whatsapp-otp-login' ); ?></label></th>
					<td><input type="password" id="wa-otp-access-token" class="regular-text" autocomplete="off" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="wa-otp-phone-id"><?php esc_html_e( 'Phone Number ID', 'whatsapp-otp-login' ); ?></label></th>
					<td><input type="text" id="wa-otp-phone-id" class="regular-text" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="wa-otp-waba-id"><?php esc_html_e( 'WABA ID (optional)', 'whatsapp-otp-login' ); ?></label></th>
					<td><input type="text" id="wa-otp-waba-id" class="regular-text" /></td>
				</tr>
			</table>
			<p>
				<button type="button" class="button button-primary" id="wa-otp-connect-number-btn"><?php esc_html_e( 'Save & Verify', 'whatsapp-otp-login' ); ?></button>
				<?php if ( 'choice' === $sender_mode ) : ?>
					<button type="button" class="button" id="wa-otp-use-platform-btn"><?php esc_html_e( 'Use shared number instead', 'whatsapp-otp-login' ); ?></button>
				<?php endif; ?>
			</p>
			<p id="wa-otp-connect-status" class="description"></p>
		<?php endif; ?>
	</div>
</div>
