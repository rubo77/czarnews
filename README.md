# CzarNews v1.12 — PHP 8.x Port

Fork/Update of CzarNews v1.12 (released 2004-03-07, by Vance Lucas,
originally czaries.net — long offline) as used on eclabs.de.

Subtree of /var/www/czarnews (news/), extracted via `git subtree split`.
Contains all modernizations: mysql_* shim via mysqli, removed
each()/split()/register_globals/session_register, PHP-4 constructors,
6-arg session_set_save_handler, unquoted constants, request-var init.

Upstream: none (project is dead). License: original CzarNews terms
(free for non-commercial use per readme.txt).
