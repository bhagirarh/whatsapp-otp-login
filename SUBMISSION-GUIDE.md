# Publishing WhatsApp OTP Login to WordPress.org

This is the standard process for getting a plugin onto the WordPress.org Plugin Directory, applied to this plugin specifically. Follow it top to bottom.

## 1. Before you submit — finish these first

- [ ] **LICENSE.txt** — currently a placeholder. Paste in the full GPLv2 text (copy `license.txt` from any WordPress core install, or download from https://www.gnu.org/licenses/gpl-2.0.txt).
- [x] **Meta WhatsApp Authentication template** — done: `otp` is already approved (Authentication, Copy Code delivery, security recommendation on, no expiry line). `config.php`'s `OTP_TEMPLATE_NAME_*` constants all point at it for now. **Verify `OTP_TEMPLATE_LANGUAGE` in `config.php`** matches the exact language code Meta recorded for this template (its API details, not just the "English" label in the editor) — currently set to `en`.
  - Optional, later: approve additional Authentication templates (e.g. one with "Add expiration time" on) and repoint the unused `OTP_TEMPLATE_NAME_PLAIN`/`OTP_TEMPLATE_NAME_EXPIRY` constants at them — the plugin's "Message style" picker already has those options ready, just disabled until a template exists for them.
- [ ] **Razorpay Plan objects** — create a Plan in the Razorpay Dashboard for Introduction ($5/mo) and Starter ($9.99/mo), then set `otp_plans.razorpay_plan_id` for each (via `api/admin/db_query.php` or a small script) — `api/otp/billing.php`'s checkout action refuses to start until this is set.
- [ ] **Run `migrate_all.php`** on the production DB to create the new `otp_*` tables (see the plan/PR notes — this is additive and idempotent).
- [ ] **Screenshots** — `readme.txt` references 4 screenshots (`screenshot-1.png` … `screenshot-4.png`). Take real ones once the plugin is installed on a test WordPress site: the settings page, the Modern form, the Card form, and the usage widget. Drop them in this folder (`wordpress-plugin/whatsapp-otp-login/`) — WordPress.org's assets are pulled from the `/assets` SVN folder, not the plugin zip; see step 5.
- [ ] **Plugin icon & banner** — WordPress.org strongly prefers a 256×256 (or 128×128) `icon.png` and a 772×250 `banner-772x250.png`. Not required to pass review, but your listing looks bare without them.
- [ ] **Update `Tested up to:`** in `readme.txt` to whatever WordPress version you actually test against at submission time.

## 2. Self-review with the Plugin Check tool

Install the official [Plugin Check (PCP)](https://wordpress.org/plugins/plugin-check/) plugin on a test WordPress site, then activate this plugin alongside it and run a check. It catches the most common rejection reasons automatically: missing escaping/sanitization, deprecated functions, missing text domain, direct file access, etc. Fix everything it flags before submitting.

## 3. Create your WordPress.org account

If you don't already have one: register at https://wordpress.org/support/register.php, then log in.

## 4. Submit for review

1. Go to https://wordpress.org/plugins/developers/add/.
2. Upload a zip of just this plugin folder (`whatsapp-otp-login/`) — see "Building the zip" below for the exact command.
3. Fill in the submission form (plugin name, description — this can mirror `readme.txt`'s `=== WhatsApp OTP Login ===` section).
4. Submit. You'll get an email confirmation and then wait — review typically takes anywhere from a few days to several weeks.
5. The review team may email back with required changes (very common on a first submission — security/escaping nitpicks, the "External services" disclosure wording, etc.). Address every point they raise and reply to the same email thread; don't resubmit a new zip from scratch.

## 5. Once approved — SVN, not the zip you uploaded

Approval gives you SVN commit access to `https://plugins.svn.wordpress.org/whatsapp-otp-login/` (or whatever slug they assign, if `whatsapp-otp-login` collides with something).

```bash
svn checkout https://plugins.svn.wordpress.org/whatsapp-otp-login/ svn-whatsapp-otp-login
cd svn-whatsapp-otp-login

# Plugin code goes in trunk/ — copy everything from this folder except
# this SUBMISSION-GUIDE.md (that's a dev-only doc, not part of the plugin).
rsync -av --exclude 'SUBMISSION-GUIDE.md' /path/to/wordpress-plugin/whatsapp-otp-login/ trunk/

# Directory listing assets (icon, banner, screenshots) go in assets/, a
# SIBLING of trunk/ — NOT inside trunk/ itself.
cp icon-256x256.png assets/icon-256x256.png
cp banner-772x250.png assets/banner-772x250.png
cp screenshot-1.png assets/screenshot-1.png
# ...screenshot-2/3/4.png

svn add trunk/* assets/* --force
svn commit -m "Initial release 1.0.0"

# Tag the release so 1.0.0 is a permanent, addressable version.
svn copy trunk tags/1.0.0
svn commit -m "Tag 1.0.0"
```

The plugin goes live at `https://wordpress.org/plugins/whatsapp-otp-login/` shortly after the `trunk` commit (the very first commit can take a bit longer to appear).

## 6. Every future release

1. Bump `Version:` in `whatsapp-otp-login.php`'s header, `Stable tag:` in `readme.txt`, and add a changelog entry.
2. `rsync` the updated code into your `trunk/` checkout, commit.
3. `svn copy trunk tags/<new-version>` and commit — this is what `Stable tag` in readme.txt should point to.

## Building the zip (for the initial developer-upload review)

Run from the repo root:

```bash
cd wordpress-plugin
zip -r whatsapp-otp-login.zip whatsapp-otp-login \
  -x "whatsapp-otp-login/SUBMISSION-GUIDE.md" \
  -x "*.DS_Store"
```

This is also the file to hand anyone who wants to install it manually (Plugins > Add New > Upload Plugin) before it's live on WordPress.org.
