<?php
/**
* HybridAuth
*
* A Social-Sign-On PHP Library for authentication through identity providers like Facebook,
* Twitter, Google, Yahoo, LinkedIn, MySpace, Windows Live, Tumblr, Friendster, OpenID, PayPal,
* Vimeo, Foursquare, AOL, Gowalla, and others.
*
* Copyright (c) 2009-2011 (http://hybridauth.sourceforge.net)
*/

// ------------------------------------------------------------------------
//	HybridAuth Config file
// ------------------------------------------------------------------------

/**
 * - "base_url" is the url to HybridAuth EndPoint 'index.php'
 * - "providers" is the list of providers supported by HybridAuth
 * - "enabled" can be true or false; if you dont want to use a specific provider then set it to 'false'
 * - "keys" are your application credentials for this provider
 * 		for example :
 *     		'id' is your facebook application id
 *     		'key' is your twitter application consumer key
 *     		'secret' is your twitter application consumer secret
 * - To enable Logging, set debug_mode to true, then provide a path of a writable file on debug_file
 *
 * Note: The HybridAuth Config file is not required, to know more please visit:
 *       http://hybridauth.sourceforge.net/userguide/Configuration.html
 */

global $AUTH_PATH;
return
	array(
		// set on "base_url" the url that point to HybridAuth Endpoint (where the index.php is found)
		"base_url"       => $AUTH_PATH,

		"providers"      => array (
			// openid
			"OpenID" => array ( // no keys required for OpenID based providers
					"enabled" => true
			),

			// google
			"Google" => array (
					"enabled" => true
			),

			// yahoo
			"Yahoo"  => array (
					"enabled" => true
			),

			// facebook
			"Facebook" => array (
					"enabled" => true,
					"keys"    => array ( "id" => "", "secret" => "" )
			),

			// twitter
			"Twitter" => array (
					"enabled" => true,
					"keys"    => array ( "key" => "", "secret" => "" )
			),

			// myspace
			"MySpace" => array (
					"enabled" => true,
					"keys"    => array ( "key" => "", "secret" => "" )
			),

			// windows live
			"Live"    => array (
					"enabled" => true,
					"keys"    => array ( "id" => "", "secret" => "" )
			),

			// linkedin
			"LinkedIn" => array (
					"enabled" => true,
					"keys"    => array ( "key" => "", "secret" => "" )
			),
		),

		// if you want to enable logging, set 'debug_mode' to true  then provide a writable file by the web server on "debug_file"
		"debug_mode"            => false,

		"debug_file"            => "",
	);
