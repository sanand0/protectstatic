<?php

# The public URL of the _auth/ folder
# This should be {the FULL .htaccess RewriteBase URL}/_auth/
$AUTH_PATH = "http://example.com/path/to/directory/_auth/";

# These are the list of authorised email IDs
$USERS = array(
    'user1@gmail.com',
    'user2@gmail.com',
);

# Get the file path
$path = "../" . $_REQUEST['file'];

# If the file is not found, print an error message.
# You may want to redirect to a standard 404 page.
if (!is_file($path)) {
    header("HTTP/1.1 404 Not Found");
    die("<h1>File not found</h1>");
}

# Get its MIME type
$mime = apache_lookup_uri($path)->content_type;

# By default, everything expires 1 week later
$expires = 60*60*24*7;
# ... except for specific MIME types
# See https://github.com/h5bp/html5-boilerplate/blob/master/.htaccess
if (strstr($mime, 'text/') ||
    strstr($mime, 'application/xml') ||
    strstr($mime, 'application/json')) {
        $expires = 0;
}

# We use http://hybridauth.sourceforge.net/ for authentication
# You can choose any other library
require_once("Hybrid/Auth.php");

# Hybrid_Auth uses session_id() which disables caching of content.
# Let's allow at least private caching
# http://www.php.net/manual/en/function.session-cache-limiter.php
session_cache_expire($expires/60);
session_cache_limiter('private_no_expire');

# Users will log in via Google.
$hybridauth = new Hybrid_Auth("config.php");
$google = $hybridauth->authenticate("Google");
$profile = $google->getUserProfile();
$email = $profile->email;

# Check if the user is in the list below. Add your list of users here.
if (!in_array($email, $USERS)) {
    # Print an eror message for unauthorised users
    header("HTTP/1.1 401 Not Authorised");
    die("<h1>$email is not allowed to see this page</h1>");
}

# Serve the file with the right Content-type and Expires
header("Content-type: $mime");
header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + $expires));
readfile($path);

?>
