# CzarNews v2.0 — PHP 8.x Port

A maintained fork of **CzarNews v1.12** (released 2004-03-07 by Vance Lucas,
originally czaries.net — long offline), modernized to run cleanly on
**PHP 8.x** as used on eclabs.de.

## What it does

CzarNews is a small, self-hosted **news/article publishing system** with a
web admin panel. No framework, no dependencies — plain PHP + MySQL.

### Features

- **News management** — add/edit/delete articles with subject, body,
  optional "entire story" (summary + extended text), author display and
  source citation (name + URL)
- **Multi-user admin** — per-user permission matrix: news, users,
  categories, configuration, word filter, category-scoped posting rights,
  cookie-based login lifetime
- **Categories** — organize news into categories; frontend category box
  lets visitors filter the listing
- **Comments** — visitors can comment on articles; username protection
  prevents posting under a registered user's name
- **Word filter** — configurable word replacement/censoring plus keyword
  highlighting in search results
- **Search** — full-text search over the news archive
- **Headlines embed** — `headlines.php` can be included in any other page
  to show the latest N headlines
- **Web configuration** — site name/URLs, items per page, date format,
  timezone offset, output/source/author templates, feature switches
  (search, comments, category box, pagination)
- **Forgot-password flow** — `fpass.php` mails credentials to the user's
  registered address
- **MySQL backend** — schema in `czarnews.sql` (originally `TYPE=MyISAM`;
  convert to `ENGINE=MyISAM`/`InnoDB` on modern MariaDB)

### PHP 8.x modernization (v2.0)

- `mysql_*` calls routed through a mysqli compatibility shim
  (`fix_mysql.inc.php`)
- removed APIs replaced: `each()`, `split()`, `ereg()`/`eregi()`,
  `$HTTP_*_VARS`, `session_register()`/`session_is_registered()`,
  `pg_errormessage()`, `(double)` casts
- unquoted constants/array keys quoted (PHP 8 fatals)
- PHP-4-style constructors → `__construct`
- optional request variables initialized at creation point
- login works without `register_globals`; cookie auth hardened
- footer shows the real release year instead of `date("Y")`

### Security fixes (v2.0)

All known CVEs are fixed:

- **CVE-2005-0859 / CVE-2006-3685** — remote file inclusion via the
  `tpath` parameter: request variables can no longer overwrite it
- **CVE-2006-1641** — SQL injection via `usern`/`passw` (cn_auth.php),
  `s` (news.php search) and `a` (dpost.php): escaped or cast to int
- **CVE-2006-1640** — stored XSS via the comment `email`/`name`/`comment`
  fields: all comment output is `htmlspecialchars`-escaped
- `dpost.php` (comment deletion for admins) re-implemented with
  authentication and integer-cast ids

### Changes since v1.12 (upstream 1.13/1.14 parity)

- comment deletion (`dpost.php`) for logged-in admins, with a delete
  link shown next to comments for authorized users

## Layout

- `index.php` — admin panel (news list, add/edit/delete)
- `login.php` / `fpass.php` — auth
- `news.php` — public news listing (include it from your site, set
  `$tpath` to the CzarNews dir first)
- `headlines.php` — embeddable latest-headlines snippet
- `cn_*.php` — admin sections (users, categories, words, config, code
  generator)
- `czarnews.sql` — database schema

## License

Original CzarNews terms (see `readme.txt`): free for personal and
commercial use as long as the credit link stays intact. Modifications are
allowed; the original author asked not to redistribute modified copies —
he has been unreachable for ~20 years; this repo exists to keep a working
PHP-8 version available. Credit to Vance Lucas for the original.

## Credits

- Original: Vance Lucas (czaries.net, 2003–2004)
- PHP 8.x port: eclabs.de, 2026
