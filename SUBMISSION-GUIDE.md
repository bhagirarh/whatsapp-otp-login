# Publishing WhatsApp OTP Login to WordPress.org

This is the standard process for getting a plugin onto the WordPress.org Plugin Directory, applied to this plugin specifically. Follow it top to bottom.

## 1. Before you submit — finish these first

- [x] **LICENSE.txt** — done: real canonical GPLv2 text (copied verbatim from a WordPress core install's own `license.txt`), with a short plugin-specific header on top.
- [x] **Meta WhatsApp Authentication template** — done: `otp` is already approved (Authentication, Copy Code delivery, security recommendation on, no expiry line). `config.php`'s `OTP_TEMPLATE_NAME_*` constants all point at it for now. **Verify `OTP_TEMPLATE_LANGUAGE` in `config.php`** matches the exact language code Meta recorded for this template (its API details, not just the "English" label in the editor) — currently set to `en`.
  - Optional, later: approve additional Authentication templates (e.g. one with "Add expiration time" on) and repoint the unused `OTP_TEMPLATE_NAME_PLAIN`/`OTP_TEMPLATE_NAME_EXPIRY` constants at them — the plugin's "Message style" picker already has those options ready, just disabled until a template exists for them.
- [x] **Razorpay Plan objects** — automated: `migrate_all.php` now creates the Razorpay Plan for Introduction ($5/mo → ₹415 at the fixed ₹83/$1 rate) and Starter ($9.99/mo → ₹829) via the live Razorpay API and stores the returned `id` into `otp_plans.razorpay_plan_id`, the same way the main `plans` table's Plan objects are bootstrapped. Runs once `RAZORPAY_KEY_ID`/`RAZORPAY_KEY_SECRET` resolve (they already do — `RAZORPAY_MODE` is `live`). No manual Dashboard step needed; just run the migration.
- [x] **Run `migrate_all.php`** on the production DB — done: `otp_accounts.client_id`, `clients.otp_only` columns added, Razorpay Plan objects created for Introduction/Starter.
- [x] **Screenshots** — done: `screenshot-1.png` (settings/config page, API key redacted to a placeholder), `screenshot-2.png` (Modern form on the login page), `screenshot-3.png` (Card form), `screenshot-4.png` (usage widget). Captured from the local demo site and live in `wordpress-plugin/wporg-assets/` — a sibling of this plugin folder, since WordPress.org pulls listing assets from the `/assets` SVN directory, not the plugin zip (see step 5).
- [x] **Plugin icon & banner** — done: `icon-256x256.png` and `banner-772x250.png`, brand gradient (emerald → teal) matching the marketing site, also in `wordpress-plugin/wporg-assets/`.
- [x] **Update `Tested up to:`** in `readme.txt` — set to `7.1`, the version actually running on the local test/demo site.

## 2. Self-review with the Plugin Check tool

Install the official [Plugin Check (PCP)](https://wordpress.org/plugins/plugin-check/) plugin on a test WordPress site, then activate this plugin alongside it and run a check. It catches the most common rejection reasons automatically: missing escaping/sanitization, deprecated functions, missing text domain, direct file access, etc. Fix everything it flags before submitting.

## 3. Create your WordPress.org account

If you don't already have one: register at https://wordpress.org/support/register.php, then log in.

## 4. Submit for review

1. Go to https://wordpress.org/plugins/developers/add/.
2. Upload a zip of just this plugin folder (`otp-login-by-waloops/`) — see "Building the zip" below for the exact command.
3. Fill in the submission form (plugin name, description — this can mirror `readme.txt`'s `=== WhatsApp OTP Login ===` section).
4. Submit. You'll get an email confirmation and then wait — review typically takes anywhere from a few days to several weeks.
5. The review team may email back with required changes (very common on a first submission — security/escaping nitpicks, the "External services" disclosure wording, etc.). Address every point they raise and reply to the same email thread; don't resubmit a new zip from scratch.

## 5. Once approved — SVN, not the zip you uploaded

Approval gives you SVN commit access to `https://plugins.svn.wordpress.org/otp-login-by-waloops/` (or whatever slug they assign, if `otp-login-by-waloops` collides with something).

```bash
svn checkout https://plugins.svn.wordpress.org/otp-login-by-waloops/ svn-otp-login-by-waloops
cd svn-otp-login-by-waloops

# Plugin code goes in trunk/ — copy everything from this folder except
# this SUBMISSION-GUIDE.md (that's a dev-only doc, not part of the plugin).
rsync -av --exclude 'SUBMISSION-GUIDE.md' /path/to/wordpress-plugin/otp-login-by-waloops/ trunk/

# Directory listing assets (icon, banner, screenshots) go in assets/, a
# SIBLING of trunk/ — NOT inside trunk/ itself. Already built and staged at
# wordpress-plugin/wporg-assets/ in this repo.
cp /path/to/wordpress-plugin/wporg-assets/*.png assets/

svn add trunk/* assets/* --force
svn commit -m "Initial release 1.0.0"

# Tag the release so 1.0.0 is a permanent, addressable version.
svn copy trunk tags/1.0.0
svn commit -m "Tag 1.0.0"
```

The plugin goes live at `https://wordpress.org/plugins/otp-login-by-waloops/` shortly after the `trunk` commit (the very first commit can take a bit longer to appear).

## 6. Every future release

1. Bump `Version:` in `otp-login-by-waloops.php`'s header, `Stable tag:` in `readme.txt`, and add a changelog entry.
2. `rsync` the updated code into your `trunk/` checkout, commit.
3. `svn copy trunk tags/<new-version>` and commit — this is what `Stable tag` in readme.txt should point to.

## Building the zip (for the initial developer-upload review)

Run from the repo root:

```bash
cd wordpress-plugin
zip -r otp-login-by-waloops.zip otp-login-by-waloops \
  -x "otp-login-by-waloops/SUBMISSION-GUIDE.md" \
  -x "*.DS_Store"
```

This is also the file to hand anyone who wants to install it manually (Plugins > Add New > Upload Plugin) before it's live on WordPress.org.
