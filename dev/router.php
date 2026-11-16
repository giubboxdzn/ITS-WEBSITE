<?php
/**
 * Router for PHP's built-in server: static files as-is, everything else to WordPress.
 *
 * @package its-rossellini-dev
 */

// Links follow the address the page was opened with: localhost on this PC, its LAN IP from a phone.
$host = (string) ( $_SERVER['HTTP_HOST'] ?? '' );
if ( preg_match( '/^[a-z0-9.-]+(:\d{1,5})?$/i', $host ) ) {
	putenv( 'WP_HOME=http://' . $host );
}

$root = $_SERVER['DOCUMENT_ROOT'];
$path = rawurldecode( (string) parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) );
$file = realpath( $root . $path );

if ( false !== $file && is_file( $file ) ) {
	if ( str_ends_with( $file, '.php' ) ) {
		chdir( dirname( $file ) );
		require $file;
		return true;
	}
	return false;
}
if ( false !== $file && is_dir( $file ) && is_file( $file . '/index.php' ) ) {
	chdir( $file );
	require $file . '/index.php';
	return true;
}
chdir( $root );
require $root . '/index.php';
return true;
