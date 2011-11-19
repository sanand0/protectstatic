Protect Static Files
====================
Restricts static files to a specific people using Apache/PHP.

I built this because I needed to protect some static files on a shared web host
that offers Apache (without mod-auth-openid) and PHP.

Installation
------------
To restrict access a folder located at http://example.com/path/:

- Copy `.htaccess` and `_auth/` under `/path`
- In `.htaccess`, change `RewriteBase` to `/path`
- In `_auth/config.php`, change `$AUTH_PATH` to `http://example.com/path/`
- In `_auth/config.php`, add allowed email IDs to `functio allow()`

How it works
------------
1. `.htaccess` redirects *all* requests to `_auth/authorize.php`.
2. `_auth/authorize.php` logs the user in via their Google ID.
    (This uses uses [HybridAuth](http://hybridauth.sourceforge.net/).
    You can use Twitter, Facebook, OpenID, etc instead of Google.)
3. If the user is allowed by your `function allow()`, it reads & shows the file.

This is tolerably efficient. On my laptop, static files are served at 2,500/s.
With this module, it still managed 1,700/s.

Alternatives
------------
1. [mod-auth-openid](http://findingscience.com/mod_auth_openid/)
2. [AppEngine](http://code.google.com/appengine/docs/python/config/appconfig.html#Requiring_Login_or_Administrator_Status)

Licenses
--------
ProtectStatic is released under dual licence MIT and GPL.
Same as [HybridAuth licenes](http://hybridauth.sourceforge.net/licenses.html).
