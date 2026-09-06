# WALoops – WhatsApp OTP Login for WordPress

**Add WhatsApp OTP (one-time password) login and registration to any WordPress site — free for 100 messages a month, no Meta Developer account required.**

Password-free login, two-factor verification, and WooCommerce checkout confirmation, delivered over WhatsApp instead of SMS or email. Works alongside your existing username/password login — it's an added option, never a replacement.

[![WordPress Plugin](https://img.shields.io/badge/WordPress-Plugin-blue.svg)](https://wordpress.org/plugins/otp-login-by-waloops/) [![License: GPLv2+](https://img.shields.io/badge/license-GPLv2%2B-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html) [![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4.svg)](https://www.php.net) [![WordPress](https://img.shields.io/badge/WordPress-5.8%2B-21759b.svg)](https://wordpress.org)

## Why WhatsApp OTP instead of SMS or email OTP?

- **Higher delivery rates** than SMS in most regions — no carrier filtering, no spam folder.
- **Free for your visitors** — WhatsApp messages don't cost them anything, unlike some SMS routes.
- **Familiar** — over 2 billion people already have WhatsApp open on their phone.
- **No Meta Developer account needed** on the Free or Introduction shared-number plans — the plugin's backend handles the WhatsApp Business API integration for you.

## Features

- 🔐 **WhatsApp OTP login & registration** — added to `wp-login.php`, the registration form, and (optionally) WooCommerce checkout
- 🧩 **`[wa_otp_login]` shortcode** — drop the form anywhere: a custom login page, a membership site, a landing page
- 🎨 **3 built-in form designs** — Modern, Minimal, Card — pick one in Settings, no CSS required
- 🆓 **100 free WhatsApp OTPs every month** — no credit card, real messages from day one
- 📈 **Scales with you** — 1,000/month (Introduction, $5/mo) or unlimited (Starter, $9.99/mo, your own connected WhatsApp number)
- 🌍 **Translation-ready** — text domain `otp-login-by-waloops`
- 🔒 **Secure by design** — hashed, single-use, expiring codes; nonce-verified AJAX; capability-checked admin actions

## Screenshots

| Settings | Modern Form | Card Form |
|---|---|---|
| API key, message & form style, usage widget | Login page with WhatsApp OTP option | Boxed WhatsApp-branded form |

*(See the `/assets` folder on the WordPress.org listing for full-size screenshots.)*

## Installation

1. **From WordPress**: Plugins → Add New → search "WALoops – WhatsApp OTP Login" → Install → Activate. *(Once published to wordpress.org/plugins/.)*
2. **Manual**: download the zip from [waloops.com/wordpress-otp-login](https://waloops.com/wordpress-otp-login), then Plugins → Add New → Upload Plugin.
3. Go to **Settings → WhatsApp OTP Login** and click **Get Free API Key**.
4. Choose where the OTP option appears (login page, registration, WooCommerce checkout) — or use the shortcode.

## How it works

```
Visitor enters WhatsApp number
        │
        ▼
Plugin calls the WALoops OTP API (send.php) ── WhatsApp delivers a 6-digit code
        │
        ▼
Visitor enters the code
        │
        ▼
Plugin calls the WALoops OTP API (verify.php) ── correct? → WordPress session created
```

No WhatsApp Business API setup, no webhook configuration, no server-side WhatsApp integration to maintain — the plugin talks to WALoops' hosted API, which handles WhatsApp delivery for you.

## Pricing

| Plan | Price | OTPs/month | Sender number |
|---|---|---|---|
| **Free** | $0 | 100 | WALoops' shared WhatsApp number |
| **Introduction** | $5/mo | 1,000 | Your choice — shared number or your own |
| **Starter** | $9.99/mo | Unlimited | Your own connected WhatsApp Business number |

Full details: https://waloops.com/wordpress-otp-login

## Frequently asked questions

**Do I need a Meta/Facebook Developer account?**
No, not on the Free or Introduction plans. Connecting your own WhatsApp Business number (optional on Introduction, required on Starter) does require one.

**Does this replace my normal WordPress login?**
No — it's always an added option next to your existing username/password login and registration forms.

**Can I customize the WhatsApp message text?**
WhatsApp restricts one-time-passcode messages to pre-approved wording, so you pick from a small set of approved variants rather than writing free-form copy.

More in [`readme.txt`](readme.txt) (the canonical WordPress.org-format FAQ/changelog) and the **External Services** disclosure there.

## Development

```
otp-login-by-waloops/
├── otp-login-by-waloops.php   # Main plugin file
├── includes/                # API client, auth flow, user mapping
├── admin/                   # Settings page
├── templates/               # 3 front-end form designs
└── assets/                  # CSS/JS
```

Contributions and issues welcome. See [`SUBMISSION-GUIDE.md`](../SUBMISSION-GUIDE.md) for the WordPress.org publishing process this plugin follows.

## License

GPLv2 or later — see [`LICENSE.txt`](LICENSE.txt).

## Links

- 🌐 Product page & pricing: https://waloops.com/wordpress-otp-login
- 📖 WordPress.org listing: *coming soon*
- 💬 Support: support@waloops.com
