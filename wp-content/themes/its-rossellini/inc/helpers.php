<?php
/**
 * Template helpers.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

/**
 * Inline institutional logo (monochrome, inherits currentColor).
 */
function its_logo(): string {
	static $svg = null;
	if ( null === $svg ) {
		$svg = (string) file_get_contents( ITS_ROSSELLINI_DIR . '/assets/img/logo.svg' );
		$svg = preg_replace( '/<svg /', '<svg aria-hidden="true" focusable="false" class="logo__svg" ', $svg, 1 );
		$svg = preg_replace( '/\s(width|height)="[^"]*"/', '', (string) $svg, 2 );
	}
	return $svg;
}

/**
 * First non-empty value among several meta keys (ACF fields are plain post meta too).
 *
 * @param int      $post_id Post id.
 * @param string[] $keys    Candidate keys, in order.
 */
function its_meta( int $post_id, array $keys ): mixed {
	foreach ( $keys as $key ) {
		$value = get_post_meta( $post_id, $key, true );
		if ( '' !== $value && null !== $value && array() !== $value && false !== $value ) {
			return $value;
		}
	}
	return null;
}

/**
 * Meta keys the theme reads, filterable to match Rossellini Core's field names on production.
 */
function its_meta_keys( string $field ): array {
	$defaults = array(
		'event_date'   => array( 'data_evento', 'data', 'event_date', 'data_inizio', 'start_date' ),
		'event_time'   => array( 'ora_evento', 'orario', 'ora', 'event_time' ),
		'event_mode'   => array( 'modalita', 'modalità', 'mode' ),
		'event_place'  => array( 'luogo', 'sede', 'location' ),
		'doc_file'     => array( 'file_url', 'file', 'allegato', 'documento', 'pdf' ),
		'doc_format'   => array( 'formato', 'format' ),
		'doc_size'     => array( 'dimensione', 'size' ),
		'doc_deadline' => array( 'scadenza', 'data_scadenza', 'deadline' ),
	);
	return (array) apply_filters( 'its_rossellini_meta_keys', $defaults[ $field ] ?? array(), $field );
}

/**
 * Parse a stored date (Ymd, Y-m-d, d/m/Y, timestamp) in the site timezone.
 */
function its_parse_date( mixed $value ): ?DateTimeImmutable {
	if ( ! is_scalar( $value ) || '' === (string) $value ) {
		return null;
	}
	$value = trim( (string) $value );
	$tz    = wp_timezone();
	foreach ( array( '!Ymd', '!Y-m-d', '!d/m/Y', 'Y-m-d H:i:s', 'Y-m-d\TH:i:s' ) as $format ) {
		$date = DateTimeImmutable::createFromFormat( $format, $value, $tz );
		if ( $date ) {
			return $date;
		}
	}
	return ctype_digit( $value ) ? ( new DateTimeImmutable( '@' . $value ) )->setTimezone( $tz ) : null;
}

/**
 * Event date, from meta when present, otherwise none (never the publish date).
 */
function its_event_date( int $post_id ): ?DateTimeImmutable {
	return its_parse_date( its_meta( $post_id, its_meta_keys( 'event_date' ) ) );
}

/**
 * Programme stamp state for a dated item.
 *
 * @return array{0:string,1:string}|null [label, variant]
 */
function its_stamp_state( ?DateTimeImmutable $date, bool $is_deadline = false ): ?array {
	if ( ! $date ) {
		return null;
	}
	$today = new DateTimeImmutable( 'today', wp_timezone() );
	$days  = (int) $today->diff( $date )->format( '%r%a' );
	if ( $days < 0 ) {
		return array( $is_deadline ? __( 'Chiuso', 'its-rossellini' ) : __( 'Concluso', 'its-rossellini' ), 'spent' );
	}
	if ( 0 === $days ) {
		return array( $is_deadline ? __( 'Scade oggi', 'its-rossellini' ) : __( 'Oggi', 'its-rossellini' ), 'live' );
	}
	if ( $is_deadline && $days <= 7 ) {
		return array( __( 'Ultimi giorni', 'its-rossellini' ), 'live' );
	}
	return array( __( 'Prossimamente', 'its-rossellini' ), 'soon' );
}

/**
 * Programme stamp markup.
 */
function its_stamp( ?array $state ): string {
	if ( ! $state ) {
		return '';
	}
	return sprintf( '<span class="stamp stamp--%1$s">%2$s</span>', esc_attr( $state[1] ), esc_html( $state[0] ) );
}

/**
 * Fixed date slot: same cell, same place, on every dated item.
 */
function its_date_slot( DateTimeInterface|int $date, string $tag = 'time' ): string {
	$ts = $date instanceof DateTimeInterface ? $date->getTimestamp() : $date;
	return sprintf(
		'<%1$s class="date-slot" datetime="%2$s"><span class="date-slot__day">%3$s</span><span class="date-slot__month">%4$s</span><span class="date-slot__year">%5$s</span></%1$s>',
		tag_escape( $tag ),
		esc_attr( wp_date( 'Y-m-d', $ts ) ),
		esc_html( wp_date( 'j', $ts ) ),
		esc_html( wp_date( 'M', $ts ) ),
		esc_html( wp_date( 'Y', $ts ) )
	);
}

/**
 * Technological area of a course (first term).
 */
function its_area( int $post_id ): ?WP_Term {
	$terms = get_the_terms( $post_id, 'area_tecnologica' );
	return ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;
}

/**
 * Fixed printing ink per technological area.
 */
function its_area_ink( ?WP_Term $term ): string {
	if ( ! $term ) {
		return 'blu';
	}
	$name = sanitize_title( $term->name );
	$map  = (array) apply_filters(
		'its_rossellini_area_inks',
		array(
			'cinema'     => 'blu',
			'multimedia' => 'rosso',
			'audio'      => 'verde',
			'tech'       => 'nero',
		)
	);
	foreach ( $map as $needle => $ink ) {
		if ( str_contains( $name, $needle ) ) {
			return $ink;
		}
	}
	return 'blu';
}

/**
 * Downloadable file of a documento: URL meta, attachment id, ACF file array or first attached file.
 */
function its_document_file( int $post_id ): ?string {
	$value = its_meta( $post_id, its_meta_keys( 'doc_file' ) );
	if ( is_array( $value ) ) {
		$value = $value['url'] ?? ( $value['ID'] ?? null );
	}
	if ( is_numeric( $value ) ) {
		$value = wp_get_attachment_url( (int) $value );
	}
	if ( is_string( $value ) && '' !== $value ) {
		return $value;
	}
	$attached = get_attached_media( '', $post_id );
	return $attached ? (string) wp_get_attachment_url( (int) array_key_first( $attached ) ) : null;
}

/**
 * URL of a page by path, falling back to a search-free default.
 */
function its_page_url( string $path, string $fallback = '' ): string {
	$page = get_page_by_path( $path );
	if ( $page && function_exists( 'pll_get_post' ) ) {
		$translated = pll_get_post( $page->ID );
		$page       = $translated ? get_post( $translated ) : $page;
	}
	return $page ? (string) get_permalink( $page ) : ( $fallback ?: home_url( '/' ) );
}

/**
 * Site-wide published facts (editable in the Customizer).
 *
 * @return array<string,string>
 */
function its_facts(): array {
	return array(
		'years'      => (string) get_theme_mod( 'its_fact_years', '2' ),
		'hours'      => (string) get_theme_mod( 'its_fact_hours', '1.000' ),
		'internship' => (string) get_theme_mod( 'its_fact_internship', '800+' ),
		'places'     => (string) get_theme_mod( 'its_fact_places', '28' ),
		'employment' => (string) get_theme_mod( 'its_fact_employment', '87%' ),
		'source'     => (string) get_theme_mod( 'its_fact_employment_source', __( 'Dato pubblicato da ITS Rossellini', 'its-rossellini' ) ),
	);
}

/**
 * Italian definite article before a figure: "L’87%", "L’11%", "Il 90%". Other locales: none.
 */
function its_article_for_number( string $figure ): string {
	if ( ! str_starts_with( get_locale(), 'it' ) && 'en_US' !== get_locale() ) {
		return '';
	}
	$digits = preg_replace( '/\D/', '', $figure );
	$elide  = '' !== $digits && ( str_starts_with( $digits, '8' ) || in_array( $digits, array( '1', '11' ), true ) );
	return $elide ? 'L’' : 'Il ';
}

/**
 * Contact details (editable in the Customizer).
 *
 * @return array<string,string>
 */
function its_contacts(): array {
	return array(
		'address' => (string) get_theme_mod( 'its_address', 'Via Tiburtina 691, Roma' ),
		'transit' => (string) get_theme_mod( 'its_transit', __( 'Metro B Pietralata o Monti Tiburtini', 'its-rossellini' ) ),
		'hours'   => (string) get_theme_mod( 'its_hours', __( 'Lun–Ven 9:30–13:30 / 14:30–18:30', 'its-rossellini' ) ),
		'phone'   => (string) get_theme_mod( 'its_phone', '06 5576493' ),
		'email'   => (string) get_theme_mod( 'its_email', 'info@itsrossellini.it' ),
	);
}

/**
 * Language links (Polylang), rendered only when Polylang is active.
 */
function its_language_links(): string {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return '';
	}
	$langs = pll_the_languages(
		array(
			'raw'           => 1,
			'hide_if_empty' => 0,
		)
	);
	if ( ! $langs ) {
		return '';
	}
	$out = '<ul class="lang">';
	foreach ( $langs as $lang ) {
		$out .= sprintf(
			'<li><a href="%1$s" hreflang="%2$s" lang="%2$s"%3$s>%4$s</a></li>',
			esc_url( $lang['url'] ),
			esc_attr( $lang['locale'] ? str_replace( '_', '-', $lang['locale'] ) : $lang['slug'] ),
			$lang['current_lang'] ? ' aria-current="true"' : '',
			esc_html( strtoupper( $lang['slug'] ) )
		);
	}
	return $out . '</ul>';
}

/**
 * Pagination in the theme's vocabulary.
 */
function its_pagination( ?WP_Query $query = null ): void {
	$links = paginate_links(
		array(
			'total'     => $query ? (int) $query->max_num_pages : (int) $GLOBALS['wp_query']->max_num_pages,
			'current'   => max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) ),
			'type'      => 'list',
			'prev_text' => __( 'Precedenti', 'its-rossellini' ),
			'next_text' => __( 'Successivi', 'its-rossellini' ),
		)
	);
	if ( $links ) {
		echo '<nav class="pagination" aria-label="' . esc_attr__( 'Pagine', 'its-rossellini' ) . '">' . $links . '</nav>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * Breadcrumb trail: Home › ancestors › current. Yoast's trail wins when enabled.
 */
function its_breadcrumbs(): void {
	if ( function_exists( 'yoast_breadcrumb' ) && current_theme_supports( 'yoast-seo-breadcrumbs' ) ) {
		yoast_breadcrumb( '<nav class="crumbs" aria-label="' . esc_attr__( 'Percorso', 'its-rossellini' ) . '">', '</nav>' );
		return;
	}
	$items = array( array( home_url( '/' ), __( 'Home', 'its-rossellini' ) ) );
	if ( is_singular() ) {
		$post = get_queried_object();
		foreach ( array_reverse( get_post_ancestors( $post ) ) as $ancestor ) {
			$items[] = array( get_permalink( $ancestor ), get_the_title( $ancestor ) );
		}
		$parents = array(
			'corso'          => array( 'offerta-formativa/corsi', __( 'Corsi', 'its-rossellini' ) ),
			'evento'         => array( 'eventi', __( 'Eventi', 'its-rossellini' ) ),
			'documento'      => array( 'bandi-albi', __( 'Bandi e albi', 'its-rossellini' ) ),
			'edizione_corso' => array( 'offerta-formativa/corsi-in-svolgimento-e-conclusi', __( 'Edizioni dei corsi', 'its-rossellini' ) ),
			'post'           => array( 'news', __( 'News', 'its-rossellini' ) ),
		);
		if ( isset( $parents[ $post->post_type ] ) ) {
			$items[] = array( its_page_url( $parents[ $post->post_type ][0] ), $parents[ $post->post_type ][1] );
		}
	}
	echo '<nav class="crumbs" aria-label="' . esc_attr__( 'Percorso', 'its-rossellini' ) . '"><ol>';
	foreach ( $items as [ $url, $label ] ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	echo '</ol></nav>';
}
