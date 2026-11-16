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
		'top'      => '<path d="M12 20V5M6 11l6-6 6 6"/>',
		'download' => '<path d="M12 4v11M7 10l5 5 5-5M5 20h14"/>',
		'menu'     => '<path d="M3 7h18M3 12h18M3 17h18"/>',
		'close'    => '<path d="M5 5l14 14M19 5 5 19"/>',
		'search'   => '<circle cx="11" cy="11" r="6.5"/><path d="m16 16 4.5 4.5"/>',
		'pin'      => '<path d="M12 21s-6.5-6.2-6.5-11a6.5 6.5 0 0 1 13 0c0 4.8-6.5 11-6.5 11Z"/><circle cx="12" cy="10" r="2.25"/>',
		'phone'    => '<path d="M6.5 3.5h3l1.5 4-2 1.5a11 11 0 0 0 6 6l1.5-2 4 1.5v3a2 2 0 0 1-2 2A16.5 16.5 0 0 1 4.5 5.5a2 2 0 0 1 2-2Z"/>',
		'mail'     => '<rect x="3.5" y="5.5" width="17" height="13"/><path d="m4 6.5 8 6 8-6"/>',
		'play'     => '<path d="M8 5.5v13l10-6.5Z"/>',
		'pause'    => '<path d="M8.5 5.5v13M15.5 5.5v13"/>',
		'check'    => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
		'chevron'  => '<path d="m6 9.5 6 6 6-6"/>',
		'clock'    => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
		'globe'    => '<circle cx="12" cy="12" r="8.5"/><path d="M3.5 12h17M12 3.5c2.5 2.6 3.8 5.4 3.8 8.5s-1.3 5.9-3.8 8.5c-2.5-2.6-3.8-5.4-3.8-8.5s1.3-5.9 3.8-8.5Z"/>',
		'train'    => '<rect x="6" y="3.5" width="12" height="13" rx="3"/><path d="M6 11h12M9 20l-1.5 1.5M15 20l1.5 1.5M9.5 14h.01M14.5 14h.01"/>',
		'calendar' => '<rect x="4" y="5.5" width="16" height="14" rx="2"/><path d="M4 10h16M8.5 3.5v4M15.5 3.5v4"/>',
		'euro'     => '<path d="M17 7.5a6 6 0 1 0 0 9M5 10.5h8M5 13.5h8"/>',
		'users'    => '<circle cx="9" cy="9" r="3.25"/><path d="M3.5 19a5.5 5.5 0 0 1 11 0M15.5 6a3.25 3.25 0 0 1 0 6.5M17 14a5.5 5.5 0 0 1 3.5 5"/>',
		'cap'      => '<path d="M2.5 9.5 12 5l9.5 4.5L12 14Z"/><path d="M6.5 11.5V16c0 1.5 2.5 3 5.5 3s5.5-1.5 5.5-3v-4.5"/>',
		'instagram' => '<rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.25 6.75h.01"/>',
		'x'        => '<path d="M4.5 4.5h4l11 15h-4ZM19.5 4.5l-6.25 7M4.5 19.5l6.25-7"/>',
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
