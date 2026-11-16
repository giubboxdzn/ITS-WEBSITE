<?php
/**
 * Install the local WordPress (idempotent) and activate the theme.
 * Run from the CLI: php dev/install.php
 *
 * @package its-rossellini-dev
 */

if ( PHP_SAPI !== 'cli' ) {
	exit( 1 );
}

define( 'WP_INSTALLING', true );
$_SERVER['HTTP_HOST']   = 'localhost:8080';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_NAME'] = 'localhost';

require dirname( __DIR__ ) . '/.wp/wordpress/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';

if ( ! is_blog_installed() ) {
	wp_install( 'ITS Academy Roberto Rossellini', 'admin', 'admin@example.test', false, '', 'admin' );
	echo "WordPress installed.\n";
}

update_option( 'blogdescription', 'Alta formazione tecnologica per cinema, TV, digital, live e innovazione.' );
update_option( 'permalink_structure', '/%postname%/' );
update_option( 'timezone_string', 'Europe/Rome' );
update_option( 'date_format', 'j F Y' );
update_option( 'posts_per_page', 12 );
switch_theme( 'its-rossellini' );
flush_rewrite_rules();
echo 'Theme: ' . wp_get_theme()->get( 'Name' ) . "\n";
