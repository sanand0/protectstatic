<?php

include "config.php";

# Get the file path
$path = $_REQUEST['file'];
$filepath = "../$path";

if (is_dir($filepath)) {
	$filepath = "$filepath$DIRECTORY_INDEX";
}

# If the file is not found, print an error message.
# You may want to redirect to a standard 404 page.
if (!is_file($filepath)) {
	header("HTTP/1.1 404 Not Found");
	include '404.php';
	die;
}

# Get its MIME type. Wrote it by hand because:
# - mime_content_type is crappy (css files fail!) and is deprecated.
# - FileInfo requires PECL (not univerally available)...
# - apache_lookup_uri works only if PHP is an Apache module.
$mime_types = array(
	'ai'      => 'application/postscript',
	'aif'     => 'audio/x-aiff',
	'aifc'    => 'audio/x-aiff',
	'aiff'    => 'audio/x-aiff',
	'asc'     => 'text/plain',
	'asf'     => 'video/x-ms-asf',
	'asx'     => 'video/x-ms-asf',
	'au'      => 'audio/basic',
	'avi'     => 'video/x-msvideo',
	'bcpio'   => 'application/x-bcpio',
	'bin'     => 'application/octet-stream',
	'bmp'     => 'image/bmp',
	'bz2'     => 'application/x-bzip2',
	'cdf'     => 'application/x-netcdf',
	'chrt'    => 'application/x-kchart',
	'class'   => 'application/octet-stream',
	'cpio'    => 'application/x-cpio',
	'cpt'     => 'application/mac-compactpro',
	'csh'     => 'application/x-csh',
	'css'     => 'text/css',
	'dcr'     => 'application/x-director',
	'dir'     => 'application/x-director',
	'djv'     => 'image/vnd.djvu',
	'djvu'    => 'image/vnd.djvu',
	'dll'     => 'application/octet-stream',
	'dms'     => 'application/octet-stream',
	'doc'     => 'application/msword',
	'dvi'     => 'application/x-dvi',
	'dxr'     => 'application/x-director',
	'eps'     => 'application/postscript',
	'etx'     => 'text/x-setext',
	'exe'     => 'application/octet-stream',
	'ez'      => 'application/andrew-inset',
	'flv'     => 'video/x-flv',
	'gif'     => 'image/gif',
	'gtar'    => 'application/x-gtar',
	'gz'      => 'application/x-gzip',
	'hdf'     => 'application/x-hdf',
	'hqx'     => 'application/mac-binhex40',
	'htm'     => 'text/html',
	'html'    => 'text/html',
	'ice'     => 'x-conference/x-cooltalk',
	'ief'     => 'image/ief',
	'iges'    => 'model/iges',
	'igs'     => 'model/iges',
	'img'     => 'application/octet-stream',
	'iso'     => 'application/octet-stream',
	'jad'     => 'text/vnd.sun.j2me.app-descriptor',
	'jar'     => 'application/x-java-archive',
	'jnlp'    => 'application/x-java-jnlp-file',
	'jpe'     => 'image/jpeg',
	'jpeg'    => 'image/jpeg',
	'jpg'     => 'image/jpeg',
	'js'      => 'application/x-javascript',
	'kar'     => 'audio/midi',
	'kil'     => 'application/x-killustrator',
	'kpr'     => 'application/x-kpresenter',
	'kpt'     => 'application/x-kpresenter',
	'ksp'     => 'application/x-kspread',
	'kwd'     => 'application/x-kword',
	'kwt'     => 'application/x-kword',
	'latex'   => 'application/x-latex',
	'lha'     => 'application/octet-stream',
	'lzh'     => 'application/octet-stream',
	'm3u'     => 'audio/x-mpegurl',
	'man'     => 'application/x-troff-man',
	'me'      => 'application/x-troff-me',
	'mesh'    => 'model/mesh',
	'mid'     => 'audio/midi',
	'midi'    => 'audio/midi',
	'mif'     => 'application/vnd.mif',
	'mov'     => 'video/quicktime',
	'movie'   => 'video/x-sgi-movie',
	'mp2'     => 'audio/mpeg',
	'mp3'     => 'audio/mpeg',
	'mpe'     => 'video/mpeg',
	'mpeg'    => 'video/mpeg',
	'mpg'     => 'video/mpeg',
	'mpga'    => 'audio/mpeg',
	'ms'      => 'application/x-troff-ms',
	'msh'     => 'model/mesh',
	'mxu'     => 'video/vnd.mpegurl',
	'nc'      => 'application/x-netcdf',
	'odb'     => 'application/vnd.oasis.opendocument.database',
	'odc'     => 'application/vnd.oasis.opendocument.chart',
	'odf'     => 'application/vnd.oasis.opendocument.formula',
	'odg'     => 'application/vnd.oasis.opendocument.graphics',
	'odi'     => 'application/vnd.oasis.opendocument.image',
	'odm'     => 'application/vnd.oasis.opendocument.text-master',
	'odp'     => 'application/vnd.oasis.opendocument.presentation',
	'ods'     => 'application/vnd.oasis.opendocument.spreadsheet',
	'odt'     => 'application/vnd.oasis.opendocument.text',
	'ogg'     => 'application/ogg',
	'otg'     => 'application/vnd.oasis.opendocument.graphics-template',
	'oth'     => 'application/vnd.oasis.opendocument.text-web',
	'otp'     => 'application/vnd.oasis.opendocument.presentation-template',
	'ots'     => 'application/vnd.oasis.opendocument.spreadsheet-template',
	'ott'     => 'application/vnd.oasis.opendocument.text-template',
	'pbm'     => 'image/x-portable-bitmap',
	'pdb'     => 'chemical/x-pdb',
	'pdf'     => 'application/pdf',
	'pgm'     => 'image/x-portable-graymap',
	'pgn'     => 'application/x-chess-pgn',
	'png'     => 'image/png',
	'pnm'     => 'image/x-portable-anymap',
	'ppm'     => 'image/x-portable-pixmap',
	'ppt'     => 'application/vnd.ms-powerpoint',
	'ps'      => 'application/postscript',
	'qt'      => 'video/quicktime',
	'ra'      => 'audio/x-realaudio',
	'ram'     => 'audio/x-pn-realaudio',
	'ras'     => 'image/x-cmu-raster',
	'rgb'     => 'image/x-rgb',
	'rm'      => 'audio/x-pn-realaudio',
	'roff'    => 'application/x-troff',
	'rpm'     => 'application/x-rpm',
	'rtf'     => 'text/rtf',
	'rtx'     => 'text/richtext',
	'sgm'     => 'text/sgml',
	'sgml'    => 'text/sgml',
	'sh'      => 'application/x-sh',
	'shar'    => 'application/x-shar',
	'silo'    => 'model/mesh',
	'sis'     => 'application/vnd.symbian.install',
	'sit'     => 'application/x-stuffit',
	'skd'     => 'application/x-koan',
	'skm'     => 'application/x-koan',
	'skp'     => 'application/x-koan',
	'skt'     => 'application/x-koan',
	'smi'     => 'application/smil',
	'smil'    => 'application/smil',
	'snd'     => 'audio/basic',
	'so'      => 'application/octet-stream',
	'spl'     => 'application/x-futuresplash',
	'src'     => 'application/x-wais-source',
	'stc'     => 'application/vnd.sun.xml.calc.template',
	'std'     => 'application/vnd.sun.xml.draw.template',
	'sti'     => 'application/vnd.sun.xml.impress.template',
	'stw'     => 'application/vnd.sun.xml.writer.template',
	'sv4cpio' => 'application/x-sv4cpio',
	'sv4crc'  => 'application/x-sv4crc',
	'swf'     => 'application/x-shockwave-flash',
	'sxc'     => 'application/vnd.sun.xml.calc',
	'sxd'     => 'application/vnd.sun.xml.draw',
	'sxg'     => 'application/vnd.sun.xml.writer.global',
	'sxi'     => 'application/vnd.sun.xml.impress',
	'sxm'     => 'application/vnd.sun.xml.math',
	'sxw'     => 'application/vnd.sun.xml.writer',
	't'       => 'application/x-troff',
	'tar'     => 'application/x-tar',
	'tcl'     => 'application/x-tcl',
	'tex'     => 'application/x-tex',
	'texi'    => 'application/x-texinfo',
	'texinfo' => 'application/x-texinfo',
	'tgz'     => 'application/x-gzip',
	'tif'     => 'image/tiff',
	'tiff'    => 'image/tiff',
	'torrent' => 'application/x-bittorrent',
	'tr'      => 'application/x-troff',
	'tsv'     => 'text/tab-separated-values',
	'txt'     => 'text/plain',
	'ustar'   => 'application/x-ustar',
	'vcd'     => 'application/x-cdlink',
	'vrml'    => 'model/vrml',
	'wav'     => 'audio/x-wav',
	'wax'     => 'audio/x-ms-wax',
	'wbmp'    => 'image/vnd.wap.wbmp',
	'wbxml'   => 'application/vnd.wap.wbxml',
	'wm'      => 'video/x-ms-wm',
	'wma'     => 'audio/x-ms-wma',
	'wml'     => 'text/vnd.wap.wml',
	'wmlc'    => 'application/vnd.wap.wmlc',
	'wmls'    => 'text/vnd.wap.wmlscript',
	'wmlsc'   => 'application/vnd.wap.wmlscriptc',
	'wmv'     => 'video/x-ms-wmv',
	'wmx'     => 'video/x-ms-wmx',
	'wrl'     => 'model/vrml',
	'wvx'     => 'video/x-ms-wvx',
	'xbm'     => 'image/x-xbitmap',
	'xht'     => 'application/xhtml+xml',
	'xhtml'   => 'application/xhtml+xml',
	'xls'     => 'application/vnd.ms-excel',
	'xml'     => 'text/xml',
	'xpm'     => 'image/x-xpixmap',
	'xsl'     => 'text/xml',
	'zip'     => 'application/zip'
);
$parts = explode('.', $filepath);
$ext = strtolower(array_pop($parts));
$mime = isset($mime_types[$ext]) ? $mime_types[$ext] : 'text/plain';

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
$hybridauth = new Hybrid_Auth("hybrid_auth_config.php");
$google = $hybridauth->authenticate("Google");
$profile = $google->getUserProfile();
$email = $profile->email;

# Check if the user is in the list below. Add your list of users here.
if (!allow($email, $path)) {
	# Print an eror message for unauthorised users
	header("HTTP/1.1 401 Not Authorised");
	include '401.php';
	die;
}

# Serve the file with the right Content-type and Expires
header("Content-type: $mime");
$now = time();
$last_modified_time = filemtime($filepath);
header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified_time)." GMT");
header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', $now + $expires));
readfile($filepath);

# Log the request
if (isset($LOGGING)) {
	$datetime = gmdate('Y-M-d H:i:s', $now);
	$yyyymmdd = gmdate('Y-M-d', $now);
	$log_file = "log/auth-$yyyymmdd.csv";
	$log_message = "$datetime,$email,$path\n";

	# append($log_file, $log_message);
	$handle = fopen($log_file, 'a');
	if ($handle) {
		fwrite($handle, $log_message);
	}
}

?>
