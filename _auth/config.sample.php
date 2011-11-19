<?php

# The public URL of the _auth/ folder
# This should be {the FULL .htaccess RewriteBase URL}/_auth/
$AUTH_PATH = "http://example.com/path/to/directory/_auth/";

# allow($email, $path) should return True if $email is allowed to view $path.
function allow($email, $path) {
	return in_array($email, array(
		'user1@gmail.com',
		'user2@gmail.com',
	));
}

# If someone accesses /path/, what file should we serve?
$DIRECTORY_INDEX = "index.html";

?>
