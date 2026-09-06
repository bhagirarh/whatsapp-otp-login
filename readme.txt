=== WALoops – WhatsApp OTP Login ===
Contributors: waloops
Tags: whatsapp, otp, login, two factor, woocommerce
Requires at least: 5.8
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add "Login / Register with WhatsApp OTP" to your login, registration, and WooCommerce checkout screens. 100 OTPs free every month.

== Description ==

WhatsApp OTP Login adds a one-time-password login and registration option — delivered over WhatsApp — to any WordPress site. It sits alongside your existing username/password login; it never replaces it.

**Free forever, 100 OTPs/month**

Sign up for a free API key straight from the plugin's settings page (no credit card) and start sending real WhatsApp OTPs from WALoops' shared WhatsApp number immediately.

= Features =

* Adds a WhatsApp OTP option to the login form, registration form, and (optionally) the WooCommerce checkout page
* `[wa_otp_login]` shortcode to place the form anywhere
* Three built-in front-end designs: Modern, Minimal, Card
* Free plan: 100 OTPs/month from our shared WhatsApp number
* Introduction plan ($5/mo): 1,000 OTPs/month, use our number or connect your own
* Starter plan ($9.99/mo): unlimited OTPs, using your own connected WhatsApp Business number
* Works out of the box — no Meta Developer account needed on the Free or Introduction (shared-number) plans
* Fully translatable (text domain: `whatsapp-otp-login`)

= How it works =

1. Install and activate the plugin.
2. Go to **Settings > WhatsApp OTP Login** and click **Get Free API Key**.
3. Pick where the OTP option should appear (login page, registration page, WooCommerce checkout) — or drop the `[wa_otp_login]` shortcode anywhere.
4. Done. Visitors can now log in or register with just their WhatsApp number.

== External services ==

This plugin connects to the WALoops WhatsApp OTP API (**https://app.waloops.com/api/otp/**), operated by WALoops, to deliver one-time-password messages over WhatsApp. This is required for the plugin's core functionality — there is no local/offline way to send a WhatsApp message.

What is sent, and when:

* **When you click "Get Free API Key"**: the email address you enter and your site's URL are sent to `https://app.waloops.com/api/otp/signup.php`, to create your account and email you an API key.
* **When a visitor requests a login code**: the phone number they entered is sent to `https://app.waloops.com/api/otp/send.php`, which triggers a WhatsApp message to that number.
* **When a visitor submits a code**: the phone number and the 6-digit code they entered are sent to `https://app.waloops.com/api/otp/verify.php` to check whether it's correct.
* **On the settings page**: your account's monthly usage is read from `https://app.waloops.com/api/otp/usage.php`, and — only if you choose to connect your own WhatsApp Business number — your Meta access token and phone number ID are sent to `https://app.waloops.com/api/otp/connect_number.php` to verify and store them for your account.

No data is sent to any other third party. See WALoops' [Terms of Service](https://waloops.com/legal/terms) and [Privacy Policy](https://waloops.com/legal/privacy).

== Frequently Asked Questions ==

= Do I need a Meta/Facebook Developer account? =

No — not on the Free or Introduction plans, where you can send from WALoops' shared WhatsApp number. Connecting your own number (optional on Introduction, required on Starter) does require your own WhatsApp Business Cloud API app.

= What happens when I run out of free OTPs for the month? =

Sends are blocked with a clear "monthly limit reached" message until the next calendar month starts, or until you upgrade.

= Does this replace my normal login form? =

No. It's added as an extra option alongside your existing username/password login and registration forms.

= Can I customize the WhatsApp message text? =

WhatsApp only allows pre-approved wording for one-time-passcode (Authentication-category) messages — you can't write fully custom copy. Right now the message is "{code} is your verification code. For your security, do not share this code." More variants (with an expiry line, code-only) will appear as an option once they're approved with Meta.

= Is my visitors' data safe? =

Phone numbers and OTP codes are transmitted over HTTPS and are used solely to deliver and verify the one-time password. See the "External services" section above for exactly what's sent and when.

== Screenshots ==

1. Settings page — get your free API key and configure your OTP options.
2. The "Modern" front-end OTP form on a login page.
3. The "Card" front-end OTP form style.
4. Usage dashboard showing your monthly OTP quota.

== Changelog ==

= 1.0.0 =
* Initial release: Free/Introduction/Starter plans, login/register/WooCommerce-checkout integration, three form styles, own-number connection.

== Upgrade Notice ==

= 1.0.0 =
Initial release.
