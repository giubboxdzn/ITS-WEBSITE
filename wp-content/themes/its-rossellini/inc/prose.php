<?php
/**
 * Text pages (articles, events, documents, legal notes, FAQ): the content read as it will be shown.
 * Display only: what is stored in the editor is never changed.
 *
 * - Headings get an anchor (for the side index).
 * - Optionally ($infer, for news imported from the old site) the text written without structure takes it:
 *   a short line standing alone before more text ("Cosa offriamo:") or a paragraph all in bold becomes
 *   a heading; consecutive paragraphs that start with a dash become a list, with "Label: text" in bold.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

/**
 * @return array{html: string, toc: array<int, array{0: string, 1: string}>, minutes: int}
 */
function its_prose( string $html, bool $infer = false ): array {
	$words = str_word_count( wp_strip_all_tags( $html ) );
	$out   = array(
		'html'    => $html,
		'toc'     => array(),
		'minutes' => max( 1, (int) round( $words / 200 ) ),
	);
	if ( '' === trim( $html ) || ! class_exists( 'DOMDocument' ) ) {
		return $out;
	}
	$doc = new DOMDocument();
	libxml_use_internal_errors( true );
	$doc->loadHTML( '<?xml encoding="utf-8"?><div id="its-prose">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
	libxml_clear_errors();
	$root = $doc->getElementById( 'its-prose' );
	if ( ! $root ) {
		return $out;
	}
	$blocks = array_values( array_filter( iterator_to_array( $root->childNodes ), static fn( $n ) => $n instanceof DOMElement ) );

	if ( $infer ) {
		$dash = '/^\s*[–—\-•·*]\s+/u';
		$text = static fn( DOMElement $n ): string => trim( preg_replace( '/\s+/u', ' ', $n->textContent ) );
		$n    = count( $blocks );
		for ( $i = 0; $i < $n; $i++ ) {
			$el = $blocks[ $i ];
			if ( 'p' !== strtolower( $el->tagName ) ) {
				continue;
			}
			$plain = $text( $el );

			// Dashed lines in a row: one list.
			if ( preg_match( $dash, $plain ) && isset( $blocks[ $i + 1 ] ) && 'p' === strtolower( $blocks[ $i + 1 ]->tagName ) && preg_match( $dash, $text( $blocks[ $i + 1 ] ) ) ) {
				$list = $doc->createElement( 'ul' );
				$el->parentNode->insertBefore( $list, $el );
				while ( $i < $n && 'p' === strtolower( $blocks[ $i ]->tagName ) && preg_match( $dash, $text( $blocks[ $i ] ) ) ) {
					$inner = '';
					foreach ( iterator_to_array( $blocks[ $i ]->childNodes ) as $child ) {
						$inner .= $doc->saveHTML( $child );
					}
					$inner = preg_replace( $dash, '', $inner, 1 );
					$inner = preg_replace( '/^([^<:.]{2,60}):\s/u', '<strong>$1:</strong> ', $inner, 1 );
					$item  = $doc->createElement( 'li' );
					$frag  = $doc->createDocumentFragment();
					if ( $frag->appendXML( $inner ) ) {
						$item->appendChild( $frag );
					} else {
						$item->textContent = wp_strip_all_tags( $inner );
					}
					$list->appendChild( $item );
					$blocks[ $i ]->parentNode->removeChild( $blocks[ $i ] );
					++$i;
				}
				--$i;
				continue;
			}

			// A short line standing alone before more text, or a paragraph all in bold: a heading.
			$next     = $blocks[ $i + 1 ] ?? null;
			$bold     = 1 === $el->childNodes->length && $el->firstChild instanceof DOMElement && in_array( strtolower( $el->firstChild->tagName ), array( 'strong', 'b' ), true );
			$standing = $i > 0 && mb_strlen( $plain ) >= 3 && mb_strlen( $plain ) <= 70 && str_word_count( $plain ) <= 10
				&& ! preg_match( '/[.,;]$/u', $plain ) && ! preg_match( $dash, $plain ) && ! $el->getElementsByTagName( 'a' )->length;
			if ( $next && ( $bold && mb_strlen( $plain ) <= 90 || $standing ) ) {
				$heading = $doc->createElement( 'h2' );
				$heading->appendChild( $doc->createTextNode( rtrim( $plain, ': ' ) ) );
				$el->parentNode->replaceChild( $heading, $el );
			}
		}
	}

	// Anchors for the side index.
	$used = array();
	foreach ( $root->getElementsByTagName( 'h2' ) as $heading ) {
		$label = trim( preg_replace( '/\s+/u', ' ', $heading->textContent ) );
		if ( '' === $label ) {
			continue;
		}
		$id = $heading->getAttribute( 'id' ) ?: sanitize_title( $label );
		while ( isset( $used[ $id ] ) ) {
			$id .= '-2';
		}
		$used[ $id ] = true;
		$heading->setAttribute( 'id', $id );
		$out['toc'][] = array( $id, $label );
	}

	$html = '';
	foreach ( $root->childNodes as $child ) {
		$html .= $doc->saveHTML( $child );
	}
	$out['html'] = $html;
	return $out;
}

/**
 * Side index of a text page ("In questa pagina"), from its headings.
 *
 * @param array<int, array{0: string, 1: string}> $toc Anchor and label.
 */
function its_toc( array $toc, string $title = '' ): string {
	if ( count( $toc ) < 2 ) {
		return '';
	}
	$html = '<nav class="aside-card__toc" aria-label="' . esc_attr( $title ?: __( 'In questa pagina', 'its-rossellini' ) ) . '"><p class="aside-card__label">' . esc_html( $title ?: __( 'In questa pagina', 'its-rossellini' ) ) . '</p><ol>';
	foreach ( $toc as $n => [ $id, $label ] ) {
		$html .= '<li><a href="#' . esc_attr( $id ) . '"><span class="page-index__n" aria-hidden="true">' . sprintf( '%02d', $n + 1 ) . '</span>' . esc_html( $label ) . '</a></li>';
	}
	return $html . '</ol></nav>';
}
