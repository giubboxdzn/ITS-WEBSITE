<?php
/**
 * ITS Rossellini theme bootstrap.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

define( 'ITS_ROSSELLINI_DIR', __DIR__ );
define( 'ITS_ROSSELLINI_URI', get_template_directory_uri() );

require ITS_ROSSELLINI_DIR . '/inc/helpers.php';
require ITS_ROSSELLINI_DIR . '/inc/icons.php';
require ITS_ROSSELLINI_DIR . '/inc/content.php';
require ITS_ROSSELLINI_DIR . '/inc/customizer.php';
require ITS_ROSSELLINI_DIR . '/inc/sections.php';


add_action(
	'after_setup_theme',
	static function (): void {
		load_theme_textdomain( 'its-rossellini', get_template_directory() . '/languages' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
		add_theme_support( 'editor-styles' );
		add_editor_style( 'assets/css/editor.css' );
		remove_theme_support( 'core-block-patterns' );

		register_nav_menus(
			array(
				'primary' => __( 'Menu principale', 'its-rossellini' ),
				'footer'  => __( 'Menu footer', 'its-rossellini' ),
				'legal'   => __( 'Note legali', 'its-rossellini' ),
			)
		);

		add_image_size( 'its-card', 960, 720, true );
		add_image_size( 'its-portrait', 960, 1200, true );
	}
);

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		$css = ITS_ROSSELLINI_DIR . '/assets/css/main.css';
		$js  = ITS_ROSSELLINI_DIR . '/assets/js/main.js';
		wp_enqueue_style( 'its-rossellini', ITS_ROSSELLINI_URI . '/assets/css/main.css', array(), (string) filemtime( $css ) );
		wp_enqueue_script(
			'its-rossellini',
			ITS_ROSSELLINI_URI . '/assets/js/main.js',
			array(),
			(string) filemtime( $js ),
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
		// Classic theme: the block library stays for editor content, global styles do not.
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'classic-theme-styles' );
	},
	20
);

add_action(
	'wp_head',
	static function (): void {
		foreach ( array( 'raleway-latin-wght-normal', 'raleway-latin-wght-italic' ) as $font ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( ITS_ROSSELLINI_URI . "/assets/fonts/{$font}.woff2" )
			);
		}
		echo '<meta name="theme-color" content="#002c64">' . "\n";
	},
	1
);

// No third-party requests from the theme: drop the emoji CDN loader.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
add_filter( 'emoji_svg_url', '__return_false' );

add_filter(
	'body_class',
	static function ( array $classes ): array {
		if ( is_front_page() ) {
			$classes[] = 'has-hero';
		}
		return $classes;
	}
);

add_filter(
	'excerpt_length',
	static fn(): int => 26
);
add_filter(
	'excerpt_more',
	static fn(): string => '…'
);
