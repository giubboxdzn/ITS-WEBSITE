<?php
/**
 * Admin menu icons for the content types of Rossellini Core, drawn for the theme from the logo's shapes
 * (two bars, the circle, the play triangle). 20×20 filled glyphs, so WordPress recolours them with the
 * admin colour scheme like its own Dashicons. Applied by filter: they work whoever registers the types.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

/**
 * SVG markup of each icon, by post type.
 *
 * @return array<string, string>
 */
function its_admin_icons(): array {
	$svg = static fn( string $paths ): string => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="#a7aaad">' . $paths . '</svg>';
	return array(
		// Corsi: a clapperboard, the logo's play triangle cut into the slate.
		'corso'          => $svg( '<path d="M2.2 6.6 16.9 3.5l-.5-2.3a1 1 0 0 0-1.2-.8L2.5 3.1a1 1 0 0 0-.8 1.2zm3-3.2 2.1 2.2 1.9-.4-2.1-2.2zm4.2-.9 2.1 2.2 1.9-.4-2.1-2.2z" fill-rule="evenodd"/><path d="M2 8h16v8.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 2 16.5zm6.2 2.6v4.8l4.1-2.4z" fill-rule="evenodd"/>' ),
		// Eventi: a ticket with a perforation and the logo's circle.
		'evento'         => $svg( '<path d="M2 5.5A1.5 1.5 0 0 1 3.5 4h13A1.5 1.5 0 0 1 18 5.5V8a2 2 0 0 0 0 4v2.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 2 14.5V12a2 2 0 0 0 0-4zm11 .5v1.6h1.2V6zm0 2.8v1.6h1.2V8.8zm0 2.8v1.6h1.2v-1.6zM7.5 7.6a2.4 2.4 0 1 0 0 4.8 2.4 2.4 0 0 0 0-4.8z" fill-rule="evenodd"/>' ),
		// Edizioni dei corsi: stacked cards, the front one marked with the logo's two bars (edition II).
		'edizione_corso' => $svg( '<path d="M6 2h8a1 1 0 0 1 1 1v.6H5V3a1 1 0 0 1 1-1zM4.2 4.8h11.6a1 1 0 0 1 1 1v.7H3.2v-.7a1 1 0 0 1 1-1z"/><path d="M2 9.2A1.5 1.5 0 0 1 3.5 7.7h13A1.5 1.5 0 0 1 18 9.2v7.3a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 2 16.5zm6 1.6v5h1.5v-5zm2.6 0v5h1.5v-5z" fill-rule="evenodd"/>' ),
		// Documenti e bandi: a sheet with a folded corner, lines of text and a round stamp.
		'documento'      => $svg( '<path d="M5.5 1.5h5.3v4.4a1.6 1.6 0 0 0 1.6 1.6H16v10a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 4 17.5v-14.5a1.5 1.5 0 0 1 1.5-1.5zm1.2 7.3v1.3h5.2V8.8zm0 2.8v1.3h7v-1.3zm0 2.8v1.3h3.4v-1.3zm7.2.4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3z" fill-rule="evenodd"/><path d="M12.2 1.8 15.7 5.3v.6h-2.9a.6.6 0 0 1-.6-.6z"/>' ),
	);
}

add_filter(
	'register_post_type_args',
	static function ( array $args, string $post_type ): array {
		$icons = its_admin_icons();
		if ( isset( $icons[ $post_type ] ) ) {
			$args['menu_icon'] = 'data:image/svg+xml;base64,' . base64_encode( $icons[ $post_type ] ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		}
		return $args;
	},
	10,
	2
);
