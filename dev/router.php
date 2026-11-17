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

// Production's security block (server/htaccess-sicurezza.conf), applied as Apache would, so pages are
// tested under the same headers: "Header [always] set|unset", the <If> that keeps admin and login out,
// and <FilesMatch> blocks that deny files.
$admin  = (bool) preg_match( '#^/wp-(admin|login)#', $path );
$scope  = '';
$denied = '';
foreach ( (array) file( dirname( __DIR__ ) . '/server/htaccess-sicurezza.conf', FILE_IGNORE_NEW_LINES ) as $line ) {
	$line = trim( (string) $line );
	if ( preg_match( '/^<(If|FilesMatch)\b\s*"?(.*?)"?>$/', $line, $open ) ) {
		$scope = 'If' === $open[1] ? 'if' : 'files:' . $open[2];
	} elseif ( preg_match( '#^</(If|FilesMatch)>$#', $line ) ) {
		$scope = '';
	} elseif ( 'if' === $scope && $admin ) {
		continue;
	} elseif ( preg_match( '/^Header (?:always )?set (\S+) "(.*)"$/', $line, $set ) ) {
		header( $set[1] . ': ' . $set[2] );
	} elseif ( preg_match( '/^Header (?:always )?unset (\S+)$/', $line, $unset ) ) {
		header_remove( $unset[1] );
	} elseif ( 'Require all denied' === $line && str_starts_with( $scope, 'files:' ) ) {
		$denied = substr( $scope, 6 );
	}
}
if ( '' !== $denied && preg_match( '#' . $denied . '#', basename( $path ) ) ) {
	http_response_code( 403 );
	return true;
}
// "/../" (also as %2e%2e or with backslashes) must not reach files outside the WordPress folder.
// Checked on the request, not on realpath(): the theme and the mu-plugins are links to the repo.
$file = preg_match( '#(^|/)\.\.(/|$)#', str_replace( '\\', '/', $path ) ) ? false : realpath( $root . $path );

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
