<?php
/**
 * Configure Polylang locally like the live site: Italian (default, no prefix) and English (/en/).
 * Idempotent. Run from the CLI after dev/install.php: php dev/languages.php
 *
 * @package its-rossellini-dev
 */

if ( PHP_SAPI !== 'cli' ) {
	exit( 1 );
}

define( 'WP_ADMIN', true ); // Polylang's admin context, where its model can write.
$_SERVER['HTTP_HOST']   = 'localhost:8080';
$_SERVER['REQUEST_URI'] = '/wp-admin/';
$_SERVER['SERVER_NAME'] = 'localhost';

require dirname( __DIR__ ) . '/.wp/wordpress/wp-load.php';

if ( ! function_exists( 'PLL' ) || ! PLL() ) {
	fwrite( STDERR, "Polylang is not active.\n" );
	exit( 1 );
}

$model    = PLL()->model;
$existing = wp_list_pluck( $model->get_languages_list(), 'slug' );
$wanted   = array(
	array(
		'name'       => 'Italiano',
		'slug'       => 'it',
		'locale'     => 'it_IT',
		'rtl'        => false,
		'term_group' => 0,
	),
	array(
		'name'       => 'English',
		'slug'       => 'en',
		'locale'     => 'en_GB',
		'rtl'        => false,
		'term_group' => 1,
	),
);
foreach ( $wanted as $language ) {
	if ( in_array( $language['slug'], $existing, true ) ) {
		continue;
	}
	$result = $model->languages->add( $language );
	if ( is_wp_error( $result ) ) {
		fwrite( STDERR, $language['slug'] . ': ' . $result->get_error_message() . "\n" );
		exit( 1 );
	}
}

$options = PLL()->options;
$values  = array(
	'default_lang'  => 'it',
	'hide_default'  => true,
	'force_lang'    => 1,
	'rewrite'       => true,
	'browser'       => false,
	'redirect_lang' => true, // The English front page is /en/, as live, not /en/home-en/.
	'post_types'    => array( 'corso', 'evento', 'edizione_corso', 'documento' ),
	'taxonomies'    => array(),
);
foreach ( $values as $key => $value ) {
	$result = $options->set( $key, $value );
	if ( is_wp_error( $result ) && $result->has_errors() ) {
		fwrite( STDERR, $key . ': ' . $result->get_error_message() . "\n" );
	}
}
$options->save();
delete_transient( 'pll_activation_redirect' );
flush_rewrite_rules();

echo 'Languages: ' . implode( ', ', wp_list_pluck( $model->get_languages_list(), 'slug' ) ) . "\n";
