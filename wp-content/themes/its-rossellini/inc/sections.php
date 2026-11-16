<?php
/**
 * Interior pages in the home page's language: the editor content is read as sections
 * and each section takes the home component that fits what it contains.
 *
 * Content convention (core blocks, the theme's block styles):
 * - Paragraph "Occhiello" (is-style-eyebrow) right before a Heading 2: the section label;
 *   right before a Heading 3: the card label.
 * - Heading 2 opens a section (its anchor feeds the hero index); Heading 3 opens a card,
 *   a link inside it makes the whole card a link; the paragraphs after it are the card text.
 * - List "Numeri" (is-style-facts): one figure per item, the figure in bold, then its label.
 * - Group "Invito finale" (is-style-callout): heading, text and buttons of the closing call.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

/**
 * Pages drawn with sections. Pilot: Stage & Placement (and its translations) only, until the layout is approved.
 */
function its_has_sections( ?int $post_id = null ): bool {
	$post_id = $post_id ?: (int) get_queried_object_id();
	if ( ! $post_id || 'page' !== get_post_type( $post_id ) || get_page_template_slug( $post_id ) ) {
		return false;
	}
	$pilot = get_page_by_path( 'servizi/placement' );
	if ( ! $pilot ) {
		return false;
	}
	$ids = function_exists( 'pll_get_post_translations' ) ? array_map( 'intval', pll_get_post_translations( $pilot->ID ) ) : array();
	$ids[] = (int) $pilot->ID;
	return (bool) apply_filters( 'its_has_sections', in_array( $post_id, $ids, true ), $post_id );
}

add_action(
	'init',
	static function (): void {
		register_block_style(
			'core/paragraph',
			array(
				'name'  => 'eyebrow',
				'label' => __( 'Occhiello', 'its-rossellini' ),
			)
		);
		register_block_style(
			'core/list',
			array(
				'name'  => 'facts',
				'label' => __( 'Numeri', 'its-rossellini' ),
			)
		);
		register_block_style(
			'core/group',
			array(
				'name'  => 'callout',
				'label' => __( 'Invito finale', 'its-rossellini' ),
			)
		);
	}
);

/**
 * A heading in the display style: light, with its closing phrase emphasised as on the home page
 * ("Non una classe. <strong>Un set aperto.</strong>"). Returns escaped HTML.
 */
function its_display_text( string $text ): string {
	$text = trim( wp_strip_all_tags( $text ) );
	if ( '' === $text ) {
		return '';
	}
	// Second sentence, else what follows the last comma, colon or dash.
	if ( preg_match( '/^(.+?[.!?])\s+(\S.*)$/u', $text, $m ) || preg_match( '/^(.+[,:;–—])\s+(\S.*)$/u', $text, $m ) ) {
		return esc_html( $m[1] ) . ' <strong>' . esc_html( $m[2] ) . '</strong>';
	}
	$words = preg_split( '/\s+/u', $text );
	$count = count( $words );
	if ( $count < 2 ) {
		return esc_html( $text );
	}
	// The last two words (one in a short title); a conjunction never opens the emphasis.
	$take = $count <= 3 ? 1 : 2;
	if ( $count > $take + 1 && preg_match( '/^(e|ed|o|and|or|&)$/iu', $words[ $count - $take ] ) ) {
		++$take;
	}
	return esc_html( implode( ' ', array_slice( $words, 0, $count - $take ) ) ) . ' <strong>' . esc_html( implode( ' ', array_slice( $words, $count - $take ) ) ) . '</strong>';
}

/**
 * Inner HTML of a node, limited to inline formatting and links.
 */
function its_sections_inline( DOMNode $node ): string {
	$html = '';
	foreach ( $node->childNodes as $child ) {
		$html .= $node->ownerDocument->saveHTML( $child );
	}
	return trim( wp_kses( $html, array( 'a' => array( 'href' => true ), 'strong' => array(), 'em' => array(), 'br' => array(), 'code' => array() ) ) );
}

function its_sections_has_class( DOMNode $node, string $class ): bool {
	return $node instanceof DOMElement && in_array( $class, preg_split( '/\s+/', (string) $node->getAttribute( 'class' ) ), true );
}

/**
 * The page content as sections.
 *
 * @return array{sections: array<int, array<string, mixed>>, callout: ?array<string, mixed>}
 */
function its_page_sections( string $content ): array {
	$out = array(
		'sections' => array(),
		'callout'  => null,
	);
	if ( '' === trim( $content ) || ! class_exists( 'DOMDocument' ) ) {
		return $out;
	}
	$doc = new DOMDocument();
	libxml_use_internal_errors( true );
	$doc->loadHTML( '<?xml encoding="utf-8"?><div id="its-root">' . $content . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
	libxml_clear_errors();
	$root = $doc->getElementById( 'its-root' );
	if ( ! $root ) {
		return $out;
	}

	$sections = array();
	$current  = null;
	$card     = null;
	$label    = '';
	$new      = static fn( string $title = '', string $id = '', string $eyebrow = '' ): array => array(
		'title'   => $title,
		'id'      => $id,
		'eyebrow' => $eyebrow,
		'body'    => array(),
		'facts'   => array(),
		'cards'   => array(),
	);
	$close    = static function () use ( &$current, &$card ): void {
		if ( $card && $current ) {
			$current['cards'][] = $card;
		}
		$card = null;
	};

	foreach ( iterator_to_array( $root->childNodes ) as $node ) {
		if ( ! $node instanceof DOMElement ) {
			continue;
		}
		$tag = strtolower( $node->tagName );

		if ( its_sections_has_class( $node, 'is-style-callout' ) ) {
			$callout = array(
				'title' => '',
				'text'  => array(),
				'links' => array(),
			);
			foreach ( $node->getElementsByTagName( '*' ) as $el ) {
				$el_tag = strtolower( $el->tagName );
				if ( 'h2' === $el_tag || 'h3' === $el_tag ) {
					$callout['title'] = $callout['title'] ?: trim( $el->textContent );
				} elseif ( 'p' === $el_tag && ! $el->getElementsByTagName( 'a' )->length ) {
					$callout['text'][] = its_sections_inline( $el );
				} elseif ( 'a' === $el_tag && $el->getAttribute( 'href' ) ) {
					$callout['links'][] = array( $el->getAttribute( 'href' ), trim( $el->textContent ) );
				}
			}
			$out['callout'] = $callout;
			continue;
		}
		if ( 'p' === $tag && its_sections_has_class( $node, 'is-style-eyebrow' ) ) {
			$label = trim( $node->textContent );
			continue;
		}
		if ( 'h2' === $tag ) {
			$close();
			if ( $current ) {
				$sections[] = $current;
			}
			$title   = trim( $node->textContent );
			$current = $new( $title, $node->getAttribute( 'id' ) ?: sanitize_title( $title ), $label );
			$label   = '';
			continue;
		}
		$current = $current ?? $new();
		if ( '' !== $label ) {
			// A label that opens nothing stays a paragraph.
			if ( 'h3' !== $tag ) {
				$current['body'][] = '<p>' . esc_html( $label ) . '</p>';
			}
		}
		if ( 'h3' === $tag ) {
			$close();
			$link = $node->getElementsByTagName( 'a' )->item( 0 );
			$card = array(
				'label' => $label,
				'title' => trim( $node->textContent ),
				'href'  => $link instanceof DOMElement ? (string) $link->getAttribute( 'href' ) : '',
				'text'  => array(),
			);
			$label = '';
			continue;
		}
		$label = '';
		if ( ( 'ul' === $tag || 'ol' === $tag ) && its_sections_has_class( $node, 'is-style-facts' ) ) {
			$close();
			foreach ( $node->getElementsByTagName( 'li' ) as $li ) {
				$strong = $li->getElementsByTagName( 'strong' )->item( 0 );
				$value  = $strong ? trim( $strong->textContent ) : '';
				$text   = trim( preg_replace( '/\s+/u', ' ', $value ? substr( $li->textContent, strpos( $li->textContent, $value ) + strlen( $value ) ) : $li->textContent ) );
				$current['facts'][] = array( $value, $text );
			}
			continue;
		}
		if ( $card && 'p' === $tag ) {
			$card['text'][] = its_sections_inline( $node );
			continue;
		}
		$close();
		if ( 'ul' === $tag || 'ol' === $tag ) {
			$items = array();
			foreach ( $node->getElementsByTagName( 'li' ) as $li ) {
				$items[] = its_sections_inline( $li );
			}
			$current['body'][] = array(
				'list'    => $items,
				'ordered' => 'ol' === $tag,
			);
			continue;
		}
		$current['body'][] = $doc->saveHTML( $node );
	}
	$close();
	if ( $current ) {
		$sections[] = $current;
	}

	foreach ( $sections as &$section ) {
		$section['kind'] = its_sections_kind( $section );
	}
	unset( $section );
	$out['sections'] = $sections;
	return $out;
}

/**
 * Which home component draws a section: courses (course cards), steps (numbered), cards, or text.
 */
function its_sections_kind( array &$section ): string {
	$cards = $section['cards'];
	if ( ! $cards ) {
		return 'text';
	}
	$courses = array();
	foreach ( $cards as $card ) {
		$id = $card['href'] ? url_to_postid( $card['href'] ) : 0;
		if ( ! $id || 'corso' !== get_post_type( $id ) ) {
			$courses = array();
			break;
		}
		$courses[] = $id;
	}
	if ( $courses ) {
		$section['courses'] = $courses;
		return 'courses';
	}
	$numbered = '/^\s*0?\d{1,2}\s*[·.\-–:)]\s*/u';
	if ( count( $cards ) >= 3 ) {
		$all = true;
		foreach ( $cards as $card ) {
			$all = $all && ( preg_match( $numbered, $card['title'] ) || preg_match( $numbered, $card['label'] ) );
		}
		if ( $all ) {
			foreach ( $section['cards'] as &$card ) {
				$card['title'] = preg_replace( $numbered, '', $card['title'] );
				$card['label'] = preg_replace( $numbered, '', $card['label'] );
			}
			unset( $card );
			return 'steps';
		}
	}
	return 'cards';
}

/**
 * Icon for where a card leads: mail, phone, a file, another site, or a page of this one.
 */
function its_link_icon( string $href ): string {
	if ( str_starts_with( $href, 'mailto:' ) ) {
		return 'mail';
	}
	if ( str_starts_with( $href, 'tel:' ) ) {
		return 'phone';
	}
	if ( preg_match( '/\.(pdf|docx?|xlsx?|zip)(\?|#|$)/i', $href ) ) {
		return 'download';
	}
	$host = wp_parse_url( $href, PHP_URL_HOST );
	if ( $host && wp_parse_url( home_url(), PHP_URL_HOST ) !== $host ) {
		return 'arrow-up';
	}
	return 'arrow';
}

/**
 * Child pages not already linked from the content, as cards.
 *
 * @return array<int, array<string, mixed>>
 */
function its_child_cards( int $post_id, string $content ): array {
	$cards = array();
	foreach ( get_pages( array( 'parent' => $post_id, 'sort_column' => 'menu_order,post_title' ) ) as $child ) {
		$url  = get_permalink( $child );
		$path = (string) wp_parse_url( $url, PHP_URL_PATH );
		if ( $path && str_contains( $content, 'href="' . $path . '"' ) || str_contains( $content, 'href="' . $url . '"' ) ) {
			continue;
		}
		$cards[] = array(
			'label' => '',
			'title' => get_the_title( $child ),
			'href'  => $url,
			'text'  => has_excerpt( $child ) ? array( esc_html( get_the_excerpt( $child ) ) ) : array(),
		);
	}
	return $cards;
}

/**
 * Footage for an interior hero, as on the home page: the page photo when it is large enough for the full
 * width, otherwise the home video (with the home cover as poster), otherwise the home cover photo.
 *
 * @return array{video: string, image: int}
 */
function its_page_hero_media( int $image_id ): array {
	$meta = $image_id ? wp_get_attachment_metadata( $image_id ) : array();
	if ( $image_id && (int) ( $meta['width'] ?? 0 ) >= 1400 ) {
		return array(
			'video' => '',
			'image' => $image_id,
		);
	}
	$front = (int) get_option( 'page_on_front' );
	$cover = $front && has_post_thumbnail( $front ) ? (int) get_post_thumbnail_id( $front ) : 0;
	if ( ! $cover ) {
		$widest = 0;
		foreach ( its_courses( 12 )->posts as $course ) {
			$id    = (int) get_post_thumbnail_id( $course );
			$width = $id ? (int) ( wp_get_attachment_metadata( $id )['width'] ?? 0 ) : 0;
			if ( $width > $widest ) {
				$widest = $width;
				$cover  = $id;
			}
		}
	}
	$video = its_hero_video();
	return array(
		'video' => $video,
		'image' => $video ? $cover : ( $cover ?: $image_id ),
	);
}
