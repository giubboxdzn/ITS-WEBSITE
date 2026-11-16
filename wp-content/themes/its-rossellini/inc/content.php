<?php
/**
 * Content queries shared by templates.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

/**
 * Courses, in editorial order.
 */
function its_courses( int $limit = -1, array $exclude = array() ): WP_Query {
	return new WP_Query(
		array(
			'post_type'      => 'corso',
			'posts_per_page' => $limit,
			'post__not_in'   => $exclude,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'no_found_rows'  => true,
		)
	);
}

/**
 * Areas that actually have courses.
 *
 * @return WP_Term[]
 */
function its_course_areas(): array {
	if ( ! taxonomy_exists( 'area_tecnologica' ) ) {
		return array();
	}
	$terms = get_terms(
		array(
			'taxonomy'   => 'area_tecnologica',
			'hide_empty' => true,
		)
	);
	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * Events with a date, upcoming first (dated today or later), then the most recent past ones.
 *
 * @return array{upcoming: WP_Post[], past: WP_Post[]}
 */
function its_events_by_date( int $limit_past = 6 ): array {
	$posts = get_posts(
		array(
			'post_type'      => 'evento',
			'posts_per_page' => 200,
			'no_found_rows'  => true,
		)
	);
	$today    = new DateTimeImmutable( 'today', wp_timezone() );
	$upcoming = array();
	$past     = array();
	foreach ( $posts as $post ) {
		$date = its_event_date( $post->ID );
		if ( ! $date ) {
			continue;
		}
		$post->its_date = $date;
		if ( $date >= $today ) {
			$upcoming[] = $post;
		} else {
			$past[] = $post;
		}
	}
	usort( $upcoming, static fn( $a, $b ) => $a->its_date <=> $b->its_date );
	usort( $past, static fn( $a, $b ) => $b->its_date <=> $a->its_date );
	return array(
		'upcoming' => $upcoming,
		'past'     => array_slice( $past, 0, $limit_past ),
	);
}

/**
 * Next dated event, if any.
 */
function its_next_event(): ?WP_Post {
	$events = its_events_by_date( 0 );
	return $events['upcoming'][0] ?? null;
}

/**
 * Documents filtered by the request (?tipo=, ?biennio=, ?q=), paginated.
 */
function its_documents_query(): WP_Query {
	$tax_query = array();
	foreach ( array( 'tipo', 'biennio' ) as $tax ) {
		$slug = isset( $_GET[ $tax ] ) ? sanitize_title( wp_unslash( $_GET[ $tax ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( $slug && taxonomy_exists( $tax ) ) {
			$tax_query[] = array(
				'taxonomy' => $tax,
				'field'    => 'slug',
				'terms'    => $slug,
			);
		}
	}
	$search = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return new WP_Query(
		array(
			'post_type'      => 'documento',
			'posts_per_page' => 25,
			'paged'          => max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) ),
			's'              => $search,
			'tax_query'      => $tax_query, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		)
	);
}

/**
 * Terms for a document filter.
 *
 * @return WP_Term[]
 */
function its_filter_terms( string $tax ): array {
	if ( ! taxonomy_exists( $tax ) ) {
		return array();
	}
	$terms = get_terms(
		array(
			'taxonomy'   => $tax,
			'hide_empty' => true,
			'orderby'    => 'name',
			'order'      => 'biennio' === $tax ? 'DESC' : 'ASC',
		)
	);
	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * Questions and answers from the FAQ page: native Details blocks, or heading + paragraph pairs.
 *
 * @return array<int, array{q: string, a: string}>
 */
function its_faq_items( int $limit = 6 ): array {
	$page = get_page_by_path( 'faq' );
	if ( $page && function_exists( 'pll_get_post' ) ) {
		$translated = pll_get_post( $page->ID );
		$page       = $translated ? get_post( $translated ) : $page;
	}
	if ( ! $page ) {
		return array();
	}
	$html  = (string) $page->post_content;
	$items = array();
	if ( preg_match_all( '#<details\b[^>]*>\s*<summary[^>]*>(.*?)</summary>(.*?)</details>#s', $html, $rows, PREG_SET_ORDER ) ) {
		foreach ( $rows as $row ) {
			$items[] = array(
				'q' => wp_strip_all_tags( $row[1] ),
				'a' => trim( (string) preg_replace( '#<!--.*?-->#s', '', $row[2] ) ),
			);
		}
	} elseif ( preg_match_all( '#<h[23][^>]*>(.*?)</h[23]>\s*(<p[^>]*>.*?</p>)#s', $html, $rows, PREG_SET_ORDER ) ) {
		foreach ( $rows as $row ) {
			$items[] = array(
				'q' => wp_strip_all_tags( $row[1] ),
				'a' => $row[2],
			);
		}
	}
	return array_slice( $items, 0, $limit );
}
