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
 * Pages drawn with sections: every page whose content uses the theme's block styles or cards
 * (Heading 3 blocks). Text written as plain HTML (legal notes) and Details blocks (FAQ) stay as written.
 */
function its_has_sections( ?int $post_id = null ): bool {
	$post_id = $post_id ?: (int) get_queried_object_id();
	if ( ! $post_id || 'page' !== get_post_type( $post_id ) ) {
		return false;
	}
	$editorial = (bool) preg_match( '/is-style-(eyebrow|facts|callout)\b|<!-- wp:heading \{"level":3/', (string) get_post_field( 'post_content', $post_id ) );
	return (bool) apply_filters( 'its_has_sections', $editorial, $post_id );
}

/**
 * Everything an editorial page needs: its sections (with the child pages it does not link yet),
 * the page-head actions and index, and the closing call. Null for pages without sections.
 *
 * @return array{sections: array, actions: string, float: string, cta: array}|null
 */
function its_editorial( int $post_id ): ?array {
	if ( ! its_has_sections( $post_id ) ) {
		return null;
	}
	$content = (string) apply_filters( 'the_content', (string) get_post_field( 'post_content', $post_id ) );
	$parsed  = its_page_sections( $content );
	$cards   = its_child_cards( $post_id, $content );
	if ( $cards ) {
		$parsed['sections'][] = array(
			'title'   => __( 'In questa sezione', 'its-rossellini' ),
			'id'      => 'in-questa-sezione',
			'eyebrow' => '',
			'body'    => array(),
			'facts'   => array(),
			'cards'   => $cards,
			'kind'    => 'cards',
		);
	}
	$index = array();
	foreach ( $parsed['sections'] as $section ) {
		if ( $section['title'] ) {
			$index[] = array( $section['id'], $section['eyebrow'] ?: $section['title'] );
		}
	}
	$callout = $parsed['callout'];
	$links   = $callout['links'] ?? array();

	// Actions: the page's own call first, then a meeting (or pre-enrolment).
	$meet    = array( its_page_url( 'incontriamoci', its_page_url( 'contatti' ) ), __( 'Prenota un incontro', 'its-rossellini' ) );
	$actions = $links ?: array( $meet );
	if ( count( $actions ) < 2 && $actions[0][0] !== $meet[0] ) {
		$actions[] = $meet;
	}
	if ( count( $actions ) < 2 ) {
		$actions[] = array( its_page_url( 'iscrizioni' ), __( 'Preiscriviti', 'its-rossellini' ) );
	}
	$html = '<div class="page-head__actions">';
	foreach ( array_slice( $actions, 0, 2 ) as $n => [ $url, $label ] ) {
		$icon  = its_link_icon( $url );
		$html .= 0 === $n
			? '<a class="btn btn--grad" href="' . esc_url( $url ) . '">' . esc_html( $label ) . its_icon( 'arrow' === $icon ? 'arrow-up' : $icon ) . '</a>'
			: '<a class="btn btn--outline" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
	}
	$html .= '</div>';

	$float = '';
	if ( count( $index ) > 2 ) {
		$float = '<nav class="glass-card page-index" aria-labelledby="page-index-title"><p id="page-index-title" class="page-index__title">' . esc_html__( 'In questa pagina', 'its-rossellini' ) . '</p><ol>';
		foreach ( $index as $n => [ $anchor, $label ] ) {
			$float .= '<li><a href="#' . esc_attr( $anchor ) . '"><span class="page-index__n" aria-hidden="true">' . sprintf( '%02d', $n + 1 ) . '</span>' . esc_html( $label ) . '</a></li>';
		}
		$float .= '</ol></nav>';
	}

	return array(
		'sections' => $parsed['sections'],
		'actions'  => $html,
		'float'    => $float,
		'cta'      => $callout ? array(
			'title' => $callout['title'],
			'text'  => implode( ' ', $callout['text'] ),
			'links' => $links,
		) : array(),
	);
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
 * A first-view title as words that rise from a mask, already in the HTML: the animation starts with the
 * first paint instead of waiting for the script (the title is the page's largest element, its LCP).
 * Same markup the script builds for the other titles: the plain text for screen readers, the words hidden
 * from them. Input: inline HTML already escaped (text and <strong>), as its_display_text() returns it.
 */
function its_split_words( string $html ): string {
	if ( '' === trim( $html ) || ! class_exists( 'DOMDocument' ) ) {
		return $html;
	}
	$doc = new DOMDocument();
	libxml_use_internal_errors( true );
	$doc->loadHTML( '<?xml encoding="utf-8"?><div id="its-words">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
	libxml_clear_errors();
	$root = $doc->getElementById( 'its-words' );
	if ( ! $root ) {
		return $html;
	}
	$n    = 0;
	$walk = static function ( DOMNode $from ) use ( &$walk, &$n ): string {
		$out = '';
		foreach ( $from->childNodes as $node ) {
			if ( $node instanceof DOMText ) {
				foreach ( preg_split( '/(\s+)/u', $node->textContent, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY ) as $part ) {
					$out .= '' === trim( $part ) ? ' ' : '<span class="word"><span class="word__in" style="--w:' . $n++ . '">' . esc_html( $part ) . '</span></span>';
				}
			} elseif ( $node instanceof DOMElement && in_array( strtolower( $node->tagName ), array( 'strong', 'em', 'br' ), true ) ) {
				$tag  = strtolower( $node->tagName );
				$out .= 'br' === $tag ? '<br>' : "<{$tag}>" . $walk( $node ) . "</{$tag}>";
			}
		}
		return $out;
	};
	$plain = trim( preg_replace( '/\s+/u', ' ', $root->textContent ) );
	return '<span class="screen-reader-text">' . esc_html( $plain ) . '</span><span aria-hidden="true">' . $walk( $root ) . '</span>';
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
