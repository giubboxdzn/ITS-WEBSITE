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
 * Status of a dated item, computed from its date.
 *
 * @return array{0:string,1:string}|null [label, variant: soon|live|spent]
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
 * Status badge markup.
 */
function its_stamp( ?array $state ): string {
	if ( ! $state ) {
		return '';
	}
	return sprintf( '<span class="badge badge--%1$s">%2$s</span>', esc_attr( $state[1] ), esc_html( $state[0] ) );
}

/**
 * Hero video URL (Customizer), if any.
 */
function its_hero_video(): string {
	$value = get_theme_mod( 'its_hero_video', '' );
	if ( is_numeric( $value ) ) {
		$value = wp_get_attachment_url( (int) $value );
	}
	return is_string( $value ) ? $value : '';
}

/**
 * Date cell: same place on every dated item.
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
 * Thousands written the visitor's way: "1.000" in Italian, "1,000" in English.
 */
function its_number( string $figure ): string {
	if ( str_starts_with( determine_locale(), 'it' ) || ! preg_match( '/^\d{1,3}(\.\d{3})+\+?$/', $figure ) ) {
		return $figure;
	}
	return str_replace( '.', ',', $figure );
}

/**
 * Site-wide published facts (editable in the Customizer).
 *
 * @return array<string,string>
 */
function its_facts(): array {
	return array(
		'years'      => (string) get_theme_mod( 'its_fact_years', '2' ),
		'hours'      => its_number( (string) get_theme_mod( 'its_fact_hours', '1.000' ) ),
		'internship' => (string) get_theme_mod( 'its_fact_internship', '800+' ),
		'places'     => (string) get_theme_mod( 'its_fact_places', '28' ),
		'employment' => (string) get_theme_mod( 'its_fact_employment', '87%' ),
		'source'     => its_tr( (string) get_theme_mod( 'its_fact_employment_source', 'Dato pubblicato da ITS Rossellini' ) ),
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
		'address' => its_tr( (string) get_theme_mod( 'its_address', 'Via Tiburtina 691, Roma' ) ),
		'transit' => its_tr( (string) get_theme_mod( 'its_transit', 'Metro B Pietralata o Monti Tiburtini' ) ),
		'hours'   => its_tr( (string) get_theme_mod( 'its_hours', 'Lun–Ven 9:30–13:30 / 14:30–18:30' ) ),
		'phone'   => (string) get_theme_mod( 'its_phone', '06 5576493' ),
		'email'   => (string) get_theme_mod( 'its_email', 'info@itsrossellini.it' ),
		'instagram' => (string) get_theme_mod( 'its_instagram', 'https://www.instagram.com/itsrossellini/' ),
		'x'         => (string) get_theme_mod( 'its_x', 'https://x.com/Its_Rossellini' ),
	);
}

/**
 * A Customizer text in the visitor's language: Polylang's string translations first (Languages →
 * Translations), then the theme's own translation of the default text.
 */
function its_tr( string $text ): string {
	if ( '' === $text ) {
		return $text;
	}
	if ( function_exists( 'pll__' ) ) {
		$translated = pll__( $text );
		if ( $translated !== $text ) {
			return $translated;
		}
	}
	return translate( $text, 'its-rossellini' ); // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText -- Defaults ship in the theme's own translations.
}

/**
 * Round flag for a language slug (inline SVG, decorative). Unknown languages get a globe.
 */
function its_flag( string $slug ): string {
	$flags = array(
		'it' => '<rect width="10" height="30" fill="#009246"/><rect x="10" width="10" height="30" fill="#fff"/><rect x="20" width="10" height="30" fill="#ce2b37"/>',
		'en' => '<rect x="-15" width="60" height="30" fill="#012169"/><path d="M-15 0 45 30M45 0-15 30" stroke="#fff" stroke-width="6"/><path d="M-15 0 45 30M45 0-15 30" stroke="#c8102e" stroke-width="2"/><path d="M15 0v30M-15 15h60" stroke="#fff" stroke-width="10"/><path d="M15 0v30M-15 15h60" stroke="#c8102e" stroke-width="6"/>',
	);
	if ( ! isset( $flags[ $slug ] ) ) {
		return '<span class="flag flag--none" aria-hidden="true">' . its_icon( 'globe' ) . '</span>';
	}
	return '<span class="flag" aria-hidden="true"><svg viewBox="0 0 30 30" focusable="false">' . $flags[ $slug ] . '</svg></span>';
}

/**
 * Language switcher (Polylang): flag and language, opening a list of the site's languages.
 * Each link leads to the same page in that language, or to that language's home page.
 *
 * @param string $variant "header" (flag + code) or "footer" (flag + name, opens upwards).
 */
function its_language_switcher( string $variant = 'header' ): string {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return '';
	}
	$langs = pll_the_languages(
		array(
			'raw'                    => 1,
			'hide_if_empty'          => 0,
			'hide_if_no_translation' => 0,
		)
	);
	if ( ! is_array( $langs ) || count( $langs ) < 2 ) {
		return '';
	}
	$current = null;
	foreach ( $langs as $lang ) {
		if ( ! empty( $lang['current_lang'] ) ) {
			$current = $lang;
		}
	}
	$current = $current ?? reset( $langs );
	$items   = '';
	foreach ( $langs as $lang ) {
		$items .= sprintf(
			'<li><a href="%1$s" hreflang="%2$s" lang="%3$s"%4$s>%5$s<span>%6$s</span>%7$s</a></li>',
			esc_url( $lang['url'] ),
			esc_attr( str_replace( '_', '-', (string) ( $lang['locale'] ?? $lang['slug'] ) ) ),
			esc_attr( $lang['slug'] ),
			empty( $lang['current_lang'] ) ? '' : ' aria-current="true"',
			its_flag( $lang['slug'] ),
			esc_html( $lang['name'] ),
			empty( $lang['current_lang'] ) ? '' : its_icon( 'check', 'lang__check' )
		);
	}
	$label = 'footer' === $variant
		? '<span class="lang__name">' . esc_html( $current['name'] ) . '</span>'
		: '<span class="lang__code" aria-hidden="true">' . esc_html( strtoupper( $current['slug'] ) ) . '</span>';
	return sprintf(
		'<details class="lang lang--%1$s"><summary>%2$s%3$s<span class="screen-reader-text">%4$s</span>%5$s</summary><ul class="lang__menu">%6$s</ul></details>',
		esc_attr( $variant ),
		its_flag( $current['slug'] ),
		$label,
		/* translators: %s: current language name */
		esc_html( sprintf( __( 'Lingua: %s. Cambia lingua', 'its-rossellini' ), $current['name'] ) ),
		its_icon( 'chevron', 'lang__chevron' ),
		$items
	);
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
/**
 * The short label a page has in the main menu ("Servizi"), when it is there: page titles are often whole sentences.
 */
function its_menu_label( int $post_id ): string {
	static $labels = null;
	if ( null === $labels ) {
		$labels    = array();
		$locations = get_nav_menu_locations();
		$items     = empty( $locations['primary'] ) ? array() : (array) wp_get_nav_menu_items( $locations['primary'] );
		foreach ( $items as $item ) {
			if ( 'page' === $item->object && ! isset( $labels[ (int) $item->object_id ] ) ) {
				$labels[ (int) $item->object_id ] = (string) $item->title;
			}
		}
	}
	return $labels[ $post_id ] ?? '';
}

function its_breadcrumbs(): void {
	if ( function_exists( 'yoast_breadcrumb' ) && current_theme_supports( 'yoast-seo-breadcrumbs' ) ) {
		yoast_breadcrumb( '<nav class="crumbs" aria-label="' . esc_attr__( 'Percorso', 'its-rossellini' ) . '">', '</nav>' );
		return;
	}
	$items = array( array( home_url( '/' ), __( 'Home', 'its-rossellini' ) ) );
	if ( is_singular() ) {
		$post = get_queried_object();
		foreach ( array_reverse( get_post_ancestors( $post ) ) as $ancestor ) {
			$items[] = array( get_permalink( $ancestor ), its_menu_label( (int) $ancestor ) ?: get_the_title( $ancestor ) );
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

/**
 * Funding logos in the footer, as official vector files (assets/img/funding):
 * the institutional block of the ESF+ Lazio programme in the order its brand book sets
 * (Coesione Italia 21-27 Lazio, EU emblem with the co-funding statement, emblem of the Italian Republic,
 * Regione Lazio), then the PNRR Istruzione "Futura" band. Sources are listed in assets/img/funding/SOURCES.md.
 *
 * @return array<int, array{label: string, logos: array<int, array{src: string, alt: string, width: int, height: int}>}>
 */
function its_funding_logos(): array {
	$logo = static function ( string $file, string $alt, float $ratio, int $height ): array {
		return array(
			'src'    => ITS_ROSSELLINI_URI . '/assets/img/funding/' . $file . '.svg',
			'alt'    => $alt,
			'width'  => (int) round( $height * $ratio ),
			'height' => $height,
		);
	};
	$blocks = array(
		array(
			'label' => __( 'Programma regionale FSE+ Lazio 2021-2027', 'its-rossellini' ),
			'logos' => array(
				$logo( 'coesione-italia-lazio', __( 'Coesione Italia 21-27 Lazio', 'its-rossellini' ), 516.75 / 197.75, 44 ),
				$logo( 'ue-cofinanziato', __( 'Cofinanziato dall’Unione europea', 'its-rossellini' ), 937.75 / 190.75, 44 ),
				$logo( 'repubblica-italiana', __( 'Repubblica Italiana', 'its-rossellini' ), 702 / 800.3, 44 ),
				$logo( 'regione-lazio', __( 'Regione Lazio', 'its-rossellini' ), 103.3 / 30, 36 ),
			),
		),
		array(
			'label' => __( 'PNRR Istruzione', 'its-rossellini' ),
			'logos' => array(
				$logo( 'futura-pnrr', __( 'Futura, la scuola per l’Italia di domani. PNRR Istruzione, finanziato dall’Unione europea NextGenerationEU, Ministero dell’Istruzione e del Merito', 'its-rossellini' ), 1370.5 / 281.3, 58 ),
			),
		),
	);
	return (array) apply_filters( 'its_funding_logos', $blocks );
}

