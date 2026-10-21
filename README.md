# CzarNews v2.1 — PHP 8.x Port

A maintained fork of **CzarNews v1.12** (released 2004-03-07 by Vance Lucas,
originally czaries.net — long offline), modernized to run cleanly on
**PHP 8.x**, maintained by eclabs.de.

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
- **Language selection** — admin panel + public news page in English or
  German, switchable in the web configuration (lang/en.php, lang/de.php;
  untranslated strings fall back to English). Easy to extend with more
  languages
- **Configurable admin logo** — logo URL in the web configuration
  replaces the bundled header logo
- **Forgot-password flow** — `fpass.php` mails credentials to the user's
  registered address
- **MySQL backend** — schema in `czarnews.sql` (originally `TYPE=MyISAM`;
  convert to `ENGINE=MyISAM`/`InnoDB` on modern MariaDB)

### Changes in v2.1

- language selection (en/de) for the admin panel and public news page,
  configured in the site settings; new `lang/` directory with string
  maps and an `L()` helper
- configurable header logo via site settings (`logo` column)
- `cn_config` schema gains `lang` + `logo` columns — added
  automatically on first run for existing installs, no manual migration

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

## Installation

The original web installer (`install.php`) is gone — installs are done
manually:

1. Import the schema: `mysql -u USER -p DBNAME < czarnews.sql`
   (on modern MariaDB/MySQL replace `TYPE=MyISAM` with `ENGINE=MyISAM`
   if your server rejects it)
2. Copy `cn_dbdefs.example.php` to `cn_dbdefs.php` and fill in your
   database credentials.
3. Create the single required config row — either open
   `cn_webconfig.php` once (it seeds defaults when the table is empty)
   or insert a row into `cn_config` with `version` matching `$cnver`
   in `cn_config.php`.
4. Create a user in `cn_users` (or open `index.php` — with zero users
   the user-admin page is reachable without login).

## Embedding the news page

`news.php` is included into your own PHP page. Declare `$tpath` as the
absolute filesystem path to the CzarNews directory first:

```php
<?php
$tpath = "/home/user/public_html/czarnews/";
include($tpath . "news.php");
?>
```

For just the latest headlines, use `headlines.php` the same way. The
admin "Code Generator" page produces ready-made snippets.

## Troubleshooting

- **Blank/failed page right after install**: `cn_config` has no row —
  `cn_config.php` then tries to include the (removed) installer. Seed
  one row via `cn_webconfig.php`.
- **Upgrade from an older release**: no manual migration needed. Missing
  `lang`/`logo` columns on `cn_config` are added automatically on the
  first request; just set `version` in `cn_config` to the new `$cnver`.

## Layout

- `index.php` — admin panel (news list, add/edit/delete)
- `login.php` / `fpass.php` — auth
- `news.php` — public news listing (include it from your site, set
  `$tpath` to the CzarNews dir first)
- `headlines.php` — embeddable latest-headlines snippet
- `cn_*.php` — admin sections (users, categories, words, config, code
  generator)
- `lang/` — language files (`en.php`, `de.php`); add a `<code>.php` map
  there and it shows up as a language option
- `czarnews.sql` — database schema

## Looking for CzarNews v1.12 / v1.13 / v1.14?

If you are running **CzarNews v1.12**, **CzarNews v1.13**, **CzarNews v1.13b**, or **CzarNews v1.14** and need a secure update — this is it.
This fork is a drop-in replacement for the original CzarNews by Vance
Lucas (czaries.net, offline since ~2006) and fixes every publicly known
exploit while running on modern PHP 8.x:

- **Fix for CzarNews `tpath` remote file inclusion** (CVE-2005-0859,
  CVE-2006-3685) — the `tpath` parameter could be pointed at a remote
  URL to execute arbitrary code. Vulnerable entry points:
  `news.php`, `headlines.php`, `dpost.php`, `cn_config.php`,
  `cn_head.php`.
- **Fix for CzarNews SQL injection** (CVE-2006-1641) — exploitable via
  the `usern`/`passw` login fields in `cn_auth.php` / `login.php`, the
  `s` search parameter in `news.php`, and the `a` parameter in
  `dpost.php`.
- **Fix for CzarNews stored XSS** (CVE-2006-1640) — the comment fields
  `name`, `email`, and `comment` in `news.php` allowed script injection.

Error messages and strings from the vulnerable original that people
typically search for: `"tpath"`, `"remote file inclusion"`,
`"CzarNews"`, `"Powered by CzarNews"`, `"CzarNews v1.12"`,
`"news.php?a="`, `"headlines.php?tpath="`.

## Migrating from v1.12–v1.14

1. Keep your `cn_*` database tables — they are compatible.
2. Replace all files with this release, copy `cn_dbdefs.example.php`
   to `cn_dbdefs.php` with your credentials.
3. Set `cn_config.version` in the database to the `$cnver` value in
   `cn_config.php`. Schema upgrades run automatically on first load.

## Upgrading from 1.x

Import-keep your `cn_*` tables, drop in the new files, and set
`cn_config.version` to the `$cnver` value in `cn_config.php` so the
version check passes. The schema upgrade runs automatically.

## License

Original CzarNews terms, as shipped in the 2004 `readme.txt`:

> This script is distributed for free by www.czaries.net, or my personal
> collection of websites only. It is intended for use on websites of all
> types, personal and commercial, as long as the credit link remains
> intact and visible wherever the news is displayed. This script was made
> in 2003 by Vance Lucas. If you choose to modify your copy of the
> script, you have the right to do so freely. You may submit your
> modifications to me, but please do not re-distribute the script with
> your modifications, as it is not the original work.

The original author has been unreachable for ~20 years and his site is
offline; this fork exists to keep a working, secure PHP-8 version
available. Credit to Vance Lucas for the original.

## Credits

- Original: Vance Lucas (czaries.net, 2003–2004)
- PHP 8.x port: eclabs.de, 2026
