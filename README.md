Protect Static Files
====================
Restricts static files to a specific people using Apache/PHP.

I built this because I needed to protect some static files on a shared web host
that offers Apache without mod-auth-openid.

Installation
------------
To restrict access a folder located at http://example.com/path:

- Copy `.htaccess` and `_auth/` into the folder
- In .htaccess, change `RewriteBase` to `/path`
- In `_auth/authorize.php`, change `$AUTH_PATH` to `http://example.com/path/`
- In `_auth/authorize.php`, add allowed email IDs to `$USERS`

How it works
------------
1. `.htaccess` redirects *all* requests to `_auth/authorize.php`.
2. `_auth/authorize.php` uses [HybridAuth](http://hybridauth.sourceforge.net/)
   to log the user in with their Google ID. (You can change this behaviour.)
3. If the user is in the `$USERS` array you set up, it reads & shows the file.

This is tolerably efficient. On my laptop, raw files are served at 2,500/s.
Reading via PHP brought it down to 1,700/s. When that's a worry, I'll be rich.

Alternatives
------------
1. [mod-auth-openid](http://findingscience.com/mod_auth_openid/)
2. [AppEngine](http://code.google.com/appengine/docs/python/config/appconfig.html#Requiring_Login_or_Administrator_Status)

Licenses
--------
ProtectStatic is released under dual licence MIT and GPL.
Same as [HybridAuth licenes](http://hybridauth.sourceforge.net/licenses.html).
