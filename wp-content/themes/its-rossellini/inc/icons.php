<?php
/**
 * Authored icon set: 24px grid, 1.75 stroke, square caps (printed-rule language).
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

/**
 * Inline SVG icon.
 */
function its_icon( string $name, string $class = '' ): string {
	$paths = array(
		'arrow'    => '<path d="M4 12h15M13 6l6 6-6 6"/>',
		'arrow-up' => '<path d="M7 17 17 7M8 7h9v9"/>',
		'download' => '<path d="M12 4v11M7 10l5 5 5-5M5 20h14"/>',
		'menu'     => '<path d="M3 7h18M3 12h18M3 17h18"/>',
		'close'    => '<path d="M5 5l14 14M19 5 5 19"/>',
		'search'   => '<circle cx="11" cy="11" r="6.5"/><path d="m16 16 4.5 4.5"/>',
		'pin'      => '<path d="M12 21s-6.5-6.2-6.5-11a6.5 6.5 0 0 1 13 0c0 4.8-6.5 11-6.5 11Z"/><circle cx="12" cy="10" r="2.25"/>',
		'phone'    => '<path d="M6.5 3.5h3l1.5 4-2 1.5a11 11 0 0 0 6 6l1.5-2 4 1.5v3a2 2 0 0 1-2 2A16.5 16.5 0 0 1 4.5 5.5a2 2 0 0 1 2-2Z"/>',
		'mail'     => '<rect x="3.5" y="5.5" width="17" height="13"/><path d="m4 6.5 8 6 8-6"/>',
		'play'     => '<path d="M8 5.5v13l10-6.5Z"/>',
		'pause'    => '<path d="M8.5 5.5v13M15.5 5.5v13"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return sprintf(
		'<svg class="icon icon--%1$s %2$s" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="square" stroke-linejoin="miter">%3$s</svg>',
		esc_attr( $name ),
		esc_attr( $class ),
		$paths[ $name ]
	);
}
