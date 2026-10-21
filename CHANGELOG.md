# Changelog

## v2.1 (2026-10-22)

- Language selection in the site settings (English/German). UI strings
  go through `L()` with English keys; translations live in
  `lang/<code>.php`. Missing keys fall back to the English source text.
- Configurable admin logo: "Logo URL" in the site settings replaces the
  bundled `cn_logo_l.gif` when set; empty keeps the default.
- Schema: `cn_config` gains `lang` and `logo` columns; `cn_config.php`
  adds them automatically on first run for older installs.
- Credentials moved out of the repository: `cn_dbdefs.php` is gitignored,
  `cn_dbdefs.example.php` serves as the template.

## v2.0 (2026-10-22) — PHP 8.x port by eclabs.de

- Ported to PHP 8.x/9-ready: removed `each()`, `split()`, `ereg()`,
  `$HTTP_*_VARS`, `session_register()`, `mysql_*` via compatibility
  shims, PHP4 constructors, unquoted constants, `create_function()`.
- Security fixes for the known public exploits:
  - RFI via user-controlled `$tpath` include path (CVE-2005-0859,
    CVE-2006-3685): `tpath` is no longer accepted from the request.
  - SQL injection in login (`usern`/`passw`), search (`s`) and
    article/comment ids (CVE-2006-1641): escaped input + int casts.
  - Stored XSS in comment `name`/`email`/`comment` fields
    (CVE-2006-1640): `htmlspecialchars()` on output and on the
    reserved-name repost fields.
- Re-implemented the v1.14 comment-deletion feature as `dpost.php` with
  proper admin/news authorization and integer ids.
- Modernized admin add/edit form (clean submit button).

## v1.14 (upstream, ~2006)

- Added `dpost.php` comment deletion (re-implemented in v2.0; the
  upstream source is no longer archived anywhere).

## v1.13 / v1.13b (upstream)

- Interim release; still carried the same RFI flaws as v1.12
  (`headlines.php` among them).

## v1.12 (2004-03-07, upstream)

New features:

- 'All Categories' news display selection for category box
- News search feature with highlighting of the searched word
- Comments with username protection (a comment cannot be posted under a
  CzarNews user's name without their password)
- `cn_info.php` file explaining the script
- `cn_generate.php` to generate the PHP include code for `news.php`
- News summaries and full stories
- Move and delete multiple news items at once
- Editable HTML for 'source' and 'author' links
- Warning for leaving `install.php` on the server after install

Bug fixes/improvements:

- Optimized several lines of code for faster execution
- Fixed category display box (would not display when not logged in)
- Fixed security bug that allowed users to delete news items outside
  their category permissions
- 'Allow user to edit/delete only own posts' permission

## v1.01 (2003-12-16, upstream)

- 'Forgot Password' mailer
- Single article link (view a single news item by id)
- News numbering fix in the admin page
- Easier defaults when adding a new user
- User listing fixes when only one user exists
- Category selection box for viewing/displaying news
- Source URL display fix
- `cn_config.php` function speedups
- `czarnews.sql` shipped as fallback for the install script

## v1.00 (2003-07-30, upstream)

- First public release (beta).
