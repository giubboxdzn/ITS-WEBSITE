<?php
/**
 * Import .wp/seed/data.json (fetched from the public site) into the LOCAL WordPress.
 * Idempotent: existing items are matched by post type + slug and updated.
 * With Polylang active, the Italian and the English content are both imported, each item in
 * its language and linked to its translation, with menus per language, as on the live site.
 * Run from the CLI: php dev/seed/import.php
 *
 * @package its-rossellini-dev
 */

if ( PHP_SAPI !== 'cli' ) {
	exit( 1 );
}

define( 'WP_ADMIN', true ); // Polylang's admin context: queries are not filtered by a front-end language.
$_SERVER['HTTP_HOST']   = 'localhost:8080';
$_SERVER['REQUEST_URI'] = '/wp-admin/';
$_SERVER['SERVER_NAME'] = 'localhost';

$root = dirname( __DIR__, 2 );
require $root . '/.wp/wordpress/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
ini_set( 'display_errors', '0' ); // Image warnings (odd Exif in some photos) go to the debug log, not the terminal.

$seed_dir = $root . '/.wp/seed';
$data     = json_decode( (string) file_get_contents( $seed_dir . '/data.json' ), true );
if ( ! $data ) {
	fwrite( STDERR, "Missing .wp/seed/data.json — run dev/seed/fetch.py first.\n" );
	exit( 1 );
}

wp_set_current_user( 1 );

$has_polylang = function_exists( 'pll_set_post_language' ) && function_exists( 'pll_save_post_translations' );
$multilingual = $has_polylang && ! empty( $data['en'] );

// Default content WordPress creates on install.
foreach ( array( 'hello-world', 'sample-page' ) as $default_slug ) {
	foreach ( get_posts( array( 'name' => $default_slug, 'post_type' => array( 'post', 'page' ), 'post_status' => 'any', 'fields' => 'ids', 'lang' => '' ) ) as $default_id ) {
		wp_delete_post( $default_id, true );
	}
}

/**
 * Create or update a post by type + slug, in a language when Polylang is active.
 */
function its_seed_upsert( string $type, string $slug, array $args, string $lang = '' ): int {
	// Each imported item carries a key (type, language, live slug): the two languages can share a slug.
	$key      = $type . ':' . ( $lang ?: 'it' ) . ':' . $slug;
	$existing = get_posts(
		array(
			'post_type'      => $type,
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'lang'           => '',
			'meta_key'       => '_its_seed_key',
			'meta_value'     => $key,
		)
	);
	if ( ! $existing ) {
		// Imported before the keys: same slug, not claimed by the other language.
		$candidates = get_posts(
			array(
				'post_type'      => $type,
				'name'           => $slug,
				'post_status'    => 'any',
				'posts_per_page' => 10,
				'fields'         => 'ids',
				'lang'           => '',
			)
		);
		foreach ( $candidates as $candidate ) {
			$candidate_lang = function_exists( 'pll_get_post_language' ) ? pll_get_post_language( $candidate ) : '';
			if ( ! get_post_meta( $candidate, '_its_seed_key', true ) && ( ! $candidate_lang || ! $lang || $candidate_lang === $lang ) ) {
				$existing = array( $candidate );
				break;
			}
		}
	}
	$args = array_merge(
		array(
			'post_type'   => $type,
			'post_name'   => $slug,
			'post_status' => 'publish',
		),
		$args
	);
	if ( $existing ) {
		$args['ID'] = $existing[0];
		$id         = (int) wp_update_post( wp_slash( $args ) );
	} else {
		$id = (int) wp_insert_post( wp_slash( $args ) );
	}
	if ( $id ) {
		update_post_meta( $id, '_its_seed_key', $key );
	}
	if ( $id && $lang && function_exists( 'pll_set_post_language' ) ) {
		pll_set_post_language( $id, $lang );
	}
	return $id;
}

/**
 * Attach a local seed image as featured image (imported once, reused after).
 */
function its_seed_image( int $post_id, ?string $file, ?string $alt, string $seed_dir ): ?int {
	if ( ! $file || ! is_file( $seed_dir . '/media/' . $file ) ) {
		return null;
	}
	$found = get_posts(
		array(
			'post_type'      => 'attachment',
			'meta_key'       => '_its_seed_file',
			'meta_value'     => $file,
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'lang'           => '',
		)
	);
	if ( $found ) {
		$id = $found[0];
	} else {
		$tmp = wp_tempnam( $file );
		copy( $seed_dir . '/media/' . $file, $tmp );
		$id = media_handle_sideload(
			array(
				'name'     => $file,
				'tmp_name' => $tmp,
			),
			0
		);
		if ( is_wp_error( $id ) ) {
			return null;
		}
		update_post_meta( $id, '_its_seed_file', $file );
		update_post_meta( $id, '_wp_attachment_image_alt', (string) $alt );
	}
	if ( $post_id ) {
		set_post_thumbnail( $post_id, $id );
	}
	return (int) $id;
}

/**
 * Term ids for names, created when missing. Categories carry a language under Polylang.
 */
function its_seed_terms( array $names, string $tax, string $lang = '' ): array {
	$ids          = array();
	$translatable = $lang && 'category' === $tax && function_exists( 'pll_set_term_language' );
	foreach ( $names as $name ) {
		$id      = 0;
		$matches = get_terms(
			array(
				'taxonomy'   => $tax,
				'name'       => $name,
				'hide_empty' => false,
				'fields'     => 'ids',
				'lang'       => '',
			)
		);
		$matches = is_array( $matches ) ? $matches : array();
		// A term in this language first, then one no language has claimed yet.
		foreach ( $matches as $match ) {
			if ( ! $translatable || pll_get_term_language( $match ) === $lang ) {
				$id = (int) $match;
				break;
			}
		}
		foreach ( $translatable && ! $id ? $matches : array() as $match ) {
			if ( ! pll_get_term_language( $match ) ) {
				$id = (int) $match;
				break;
			}
		}
		$created = false;
		if ( ! $id ) {
			$term = wp_insert_term( $name, $tax, $translatable && $matches ? array( 'slug' => sanitize_title( $name . '-' . $lang ) ) : array() );
			if ( is_wp_error( $term ) ) {
				// Same name already used by the other language: share that term.
				$id = (int) ( $term->get_error_data( 'term_exists' ) ?: ( $matches[0] ?? 0 ) );
			} else {
				$id      = (int) $term['term_id'];
				$created = true;
			}
		}
		// New terms get this language explicitly (Polylang would give them the default one).
		if ( $id && $translatable && ( $created || ! pll_get_term_language( $id ) ) ) {
			pll_set_term_language( $id, $lang );
		}
		if ( $id ) {
			$ids[] = $id;
		}
	}
	return $ids;
}

/**
 * Parse a date like "10 settembre 2026" or "10 September 2026" into Y-m-d.
 */
function its_seed_date( string $text ): string {
	$months = array(
		array( 'gennaio', 'febbraio', 'marzo', 'aprile', 'maggio', 'giugno', 'luglio', 'agosto', 'settembre', 'ottobre', 'novembre', 'dicembre' ),
		array( 'january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december' ),
	);
	if ( preg_match( '/(\d{1,2})\s+([a-zà]+)\s+(\d{4})/iu', $text, $m ) ) {
		foreach ( $months as $names ) {
			$month = array_search( mb_strtolower( $m[2] ), $names, true );
			if ( false !== $month ) {
				return sprintf( '%04d-%02d-%02d', $m[3], $month + 1, $m[1] );
			}
		}
	}
	return '';
}

/**
 * Courses: structured sections become editor content (editable in WordPress).
 *
 * @return array<string,int> slug => id
 */
function its_seed_courses( array $items, string $lang, string $seed_dir ): array {
	$labels = array(
		'it' => array( 'Competenze', 'Professioni', 'Programma', 'Strumenti e tecnologie' ),
		'en' => array( 'Skills', 'Careers', 'Programme', 'Tools and technologies' ),
	);
	[ $skills, $careers, $programme, $tools ] = $labels[ 'en' === $lang ? 'en' : 'it' ];
	$sections                                 = array(
		'competenze'  => array( $skills, 'ul' ),
		'professioni' => array( $careers, 'ul' ),
		'programma'   => array( $programme, 'ol' ),
		'strumenti'   => array( $tools, 'ul' ),
	);
	$ids   = array();
	$order = 0;
	foreach ( $items as $c ) {
		$html = $c['intro'] ? '<p class="is-style-lead">' . esc_html( $c['intro'] ) . "</p>\n" : '';
		foreach ( $sections as $key => [ $title, $list ] ) {
			if ( empty( $c[ $key ] ) ) {
				continue;
			}
			$html .= "<h2>{$title}</h2>\n<{$list}>\n";
			foreach ( $c[ $key ] as $item ) {
				$html .= '<li>' . esc_html( $item ) . "</li>\n";
			}
			$html .= "</{$list}>\n";
		}
		$id = its_seed_upsert(
			'corso',
			$c['slug'],
			array(
				'post_title'   => $c['title'],
				'post_excerpt' => $c['excerpt'],
				'post_content' => $html,
				'menu_order'   => $order++,
			),
			$lang
		);
		wp_set_object_terms( $id, its_seed_terms( $c['area'], 'area_tecnologica' ), 'area_tecnologica' );
		its_seed_image( $id, $c['image'], $c['image_alt'], $seed_dir );
		$ids[ $c['slug'] ] = $id;
	}
	return $ids;
}

/**
 * @return array<string,int> slug => id
 */
function its_seed_posts( array $items, string $lang, string $seed_dir ): array {
	$ids = array();
	foreach ( $items as $p ) {
		$id = its_seed_upsert(
			'post',
			$p['slug'],
			array(
				'post_title'   => $p['title'],
				'post_content' => $p['content'],
				'post_excerpt' => html_entity_decode( $p['excerpt'] ),
				'post_date'    => str_replace( 'T', ' ', $p['date'] ),
			),
			$lang
		);
		wp_set_post_categories( $id, its_seed_terms( $p['categories'] ?: array( 'News' ), 'category', $lang ) );
		its_seed_image( $id, $p['image'], $p['image_alt'], $seed_dir );
		$ids[ $p['slug'] ] = $id;
	}
	return $ids;
}

/**
 * @return array<string,int> slug => id
 */
function its_seed_events( array $items, string $lang, string $seed_dir ): array {
	$ids = array();
	foreach ( $items as $e ) {
		$id = its_seed_upsert(
			'evento',
			$e['slug'],
			array(
				'post_title'   => $e['title'],
				'post_content' => $e['description'] ? '<p>' . esc_html( $e['description'] ) . '</p>' : '',
				'post_excerpt' => $e['description'],
				'post_date'    => str_replace( 'T', ' ', $e['date'] ),
			),
			$lang
		);
		update_post_meta( $id, 'data_evento', its_seed_date( $e['data_text'] ) );
		update_post_meta( $id, 'ora_evento', $e['orario'] );
		update_post_meta( $id, 'modalita', $e['modalita'] );
		update_post_meta( $id, 'luogo', $e['luogo'] );
		if ( $e['tipo_evento'] ) {
			wp_set_object_terms( $id, its_seed_terms( $e['tipo_evento'], 'tipo_evento' ), 'tipo_evento' );
		}
		its_seed_image( $id, $e['image'], $e['image_alt'], $seed_dir );
		$ids[ $e['slug'] ] = $id;
	}
	return $ids;
}

/**
 * @return array<string,int> slug => id
 */
function its_seed_documents( array $items, string $lang ): array {
	$ids = array();
	foreach ( $items as $d ) {
		$id = its_seed_upsert(
			'documento',
			$d['slug'],
			array(
				'post_title' => $d['title'],
				'post_date'  => str_replace( 'T', ' ', $d['date'] ),
			),
			$lang
		);
		wp_set_object_terms( $id, its_seed_terms( $d['tipo'], 'tipo' ), 'tipo' );
		wp_set_object_terms( $id, its_seed_terms( $d['biennio'], 'biennio' ), 'biennio' );
		foreach ( array( 'file_url', 'formato', 'dimensione' ) as $key ) {
			if ( ! empty( $d[ $key ] ) ) {
				update_post_meta( $id, $key, $d[ $key ] );
			}
		}
		$ids[ $d['slug'] ] = $id;
	}
	return $ids;
}

/**
 * @return array<string,int> slug => id
 */
function its_seed_editions( array $items, string $lang ): array {
	$ids = array();
	foreach ( $items as $ed ) {
		$id = its_seed_upsert(
			'edizione_corso',
			$ed['slug'],
			array(
				'post_title' => $ed['title'],
				'post_date'  => str_replace( 'T', ' ', $ed['date'] ),
			),
			$lang
		);
		wp_set_object_terms( $id, its_seed_terms( $ed['famiglia'], 'famiglia' ), 'famiglia' );
		wp_set_object_terms( $id, its_seed_terms( $ed['biennio'], 'biennio' ), 'biennio' );
		$ids[ $ed['slug'] ] = $id;
	}
	return $ids;
}

/**
 * Pages, parents first. The front page of each language is imported as "home" / "home-en".
 *
 * @param callable $template Page slug (and item) to page template, or ''.
 * @return array<string,int> slug => id
 */
function its_seed_pages( array $pages, string $lang, callable $template, string $seed_dir ): array {
	usort( $pages, static fn( $a, $b ) => (int) ( '' !== $a['parent_slug'] ) <=> (int) ( '' !== $b['parent_slug'] ) );
	$ids = array();
	foreach ( $pages as $p ) {
		$front = ! empty( $p['front'] ) || 'homepage-it' === $p['slug'] || '/' === wp_parse_url( $p['link'], PHP_URL_PATH );
		$slug  = $front ? ( 'it' === $lang ? 'home' : 'home-' . $lang ) : $p['slug'];
		// Scraped sections start at h3 on the live markup; under the page h1 they are h2.
		if ( ! str_contains( $p['content'], '<h2' ) ) {
			$p['content'] = preg_replace( array( '/<h3(\b[^>]*)>/', '/<\/h3>/' ), array( '<h2$1>', '</h2>' ), $p['content'] );
		}
		$id           = its_seed_upsert(
			'page',
			$slug,
			array(
				'post_title'   => $front ? 'Home' : $p['title'],
				'post_content' => $front ? '' : $p['content'],
				'post_excerpt' => $front ? '' : (string) ( $p['excerpt'] ?? '' ),
				'post_parent'  => $ids[ $p['parent_slug'] ] ?? 0,
				'menu_order'   => $p['menu_order'],
			),
			$lang
		);
		$ids[ $slug ] = $id;
		if ( ! $front && ! empty( $p['image'] ) ) {
			its_seed_image( $id, $p['image'], '', $seed_dir );
		}
		if ( ! $front && $p['slug'] !== $slug ) {
			$ids[ $p['slug'] ] = $id;
		}
		$file = $template( $slug, $p );
		if ( $file ) {
			update_post_meta( $id, '_wp_page_template', $file );
		}
	}
	return $ids;
}

/**
 * FAQ page as native Details blocks (one question per block), editable in the block editor.
 */
function its_seed_faq( int $page_id, array $faq, string $excerpt ): void {
	if ( ! $page_id || ! $faq ) {
		return;
	}
	$blocks = array();
	foreach ( $faq as $item ) {
		$blocks[] = "<!-- wp:details -->\n<details class=\"wp-block-details\"><summary>" . esc_html( $item['q'] ) . "</summary><!-- wp:paragraph -->\n<p>" . esc_html( $item['a'] ) . "</p>\n<!-- /wp:paragraph --></details>\n<!-- /wp:details -->";
	}
	wp_update_post(
		array(
			'ID'           => $page_id,
			'post_excerpt' => $excerpt,
			'post_content' => implode( "\n\n", $blocks ),
		)
	);
}

/**
 * A menu of pages; items without a page are skipped.
 *
 * @param array<int, array{0: string, 1: int}> $items Label and page id.
 */
function its_seed_menu( string $name, array $items ): int {
	$menu = wp_get_nav_menu_object( $name );
	if ( $menu ) {
		wp_delete_nav_menu( $menu->term_id );
	}
	$menu_id = wp_create_nav_menu( $name );
	foreach ( $items as [ $label, $page_id ] ) {
		if ( ! $page_id ) {
			continue;
		}
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'     => $label,
				'menu-item-object'    => 'page',
				'menu-item-object-id' => $page_id,
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
			)
		);
	}
	return (int) $menu_id;
}

/**
 * Link each English item to its Italian original. The live site prints the Italian URL in its
 * hreflang alternates (it_slug); a few items only have the "-2" suffix Polylang gave the copy.
 *
 * @param array<string,int>    $it_ids Italian slug => id.
 * @param array<string,int>    $en_ids English slug => id.
 * @param array<string,string> $alias  Italian URL slug => Italian post slug, where they differ.
 */
function its_seed_link( array $en_items, array $it_ids, array $en_ids, array $alias = array() ): int {
	$linked = 0;
	foreach ( $en_items as $item ) {
		$it = (string) ( $item['it_slug'] ?? '' );
		$it = $alias[ $it ] ?? $it;
		if ( ! isset( $it_ids[ $it ] ) && str_ends_with( $item['slug'], '-2' ) ) {
			$it = substr( $item['slug'], 0, -2 );
		}
		$en_slug = ! empty( $item['front'] ) ? 'home-en' : $item['slug'];
		if ( isset( $it_ids[ $it ], $en_ids[ $en_slug ] ) ) {
			pll_save_post_translations(
				array(
					'it' => $it_ids[ $it ],
					'en' => $en_ids[ $en_slug ],
				)
			);
			++$linked;
		}
	}
	return $linked;
}

// ---------- Italian ----------

$it_lang   = $has_polylang ? 'it' : '';
$templates = array(
	'corsi'                           => 'templates/template-corsi.php',
	'eventi'                          => 'templates/template-eventi.php',
	'bandi'                           => 'templates/template-documenti.php',
	'bandi-albi'                      => 'templates/template-documenti.php',
	'corsi-in-svolgimento-e-conclusi' => 'templates/template-edizioni.php',
);
$it = array(
	'corso'          => its_seed_courses( $data['courses'], $it_lang, $seed_dir ),
	'post'           => its_seed_posts( $data['posts'], $it_lang, $seed_dir ),
	'evento'         => its_seed_events( $data['events'], $it_lang, $seed_dir ),
	'documento'      => its_seed_documents( $data['documents'], $it_lang ),
	'edizione_corso' => its_seed_editions( $data['editions'], $it_lang ),
	'page'           => its_seed_pages( $data['pages'], $it_lang, static fn( $slug ) => $templates[ $slug ] ?? '', $seed_dir ),
);
$page_ids = $it['page'];
its_seed_faq( $page_ids['faq'] ?? 0, $data['faq'] ?? array(), 'Risposte su corsi, ammissioni, frequenza, stage e servizi.' );

// Front page: partner logos as editable content.
$partners = '';
foreach ( $data['partners'] as $partner ) {
	$att = its_seed_image( 0, $partner['image'], $partner['name'], $seed_dir );
	if ( ! $att ) {
		continue;
	}
	$meta      = wp_get_attachment_metadata( $att );
	$wide      = ! empty( $meta['width'] ) && ! empty( $meta['height'] ) && $meta['width'] / $meta['height'] > 5;
	$img       = wp_get_attachment_image( $att, $wide ? 'full' : 'medium', false, array( 'alt' => $partner['name'] ) );
	$img       = str_replace( untrailingslashit( home_url() ), '', $img ); // Root-relative: works on any local address.
	$partners .= '<li' . ( $wide ? ' class="is-wide"' : '' ) . '>' . ( $partner['url'] ? '<a href="' . esc_url( $partner['url'] ) . '">' . $img . '</a>' : $img ) . "</li>\n";
}
$partners_html = "<!-- wp:html -->\n<ul class=\"partner-logos\">\n{$partners}</ul>\n<!-- /wp:html -->";
$home_id       = $page_ids['home'] ?? its_seed_upsert( 'page', 'home', array( 'post_title' => 'Home' ), $it_lang );
wp_update_post(
	array(
		'ID'           => $home_id,
		'post_content' => $partners_html,
	)
);
// Front page image (video poster): the editing-room photo the live site uses as its video poster.
$poster = has_post_thumbnail( $it['corso']['editor-post-production-specialist'] ?? 0 ) ? get_post_thumbnail_id( $it['corso']['editor-post-production-specialist'] ) : 0;
if ( $poster ) {
	set_post_thumbnail( $home_id, $poster );
}
// Hero video (the live site's own footage), muted, with the front page image as poster.
$video_id = its_seed_image( 0, is_file( $seed_dir . '/media/hero.mp4' ) ? 'hero.mp4' : null, '', $seed_dir );
if ( $video_id ) {
	set_theme_mod( 'its_hero_video', $video_id );
}
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home_id );
if ( isset( $page_ids['news'] ) ) {
	update_option( 'page_for_posts', $page_ids['news'] );
}

// Menus: [label, Italian page slug].
$menus = array(
	'primary' => array(
		array( 'Menu principale', 'Main menu' ),
		array(
			array( 'Corsi', 'Courses', 'corsi' ),
			array( 'Academy', 'Academy', 'academy' ),
			array( 'Servizi', 'Services', 'servizi' ),
			array( 'Bandi e albi', 'Tenders', 'bandi-albi' ),
			array( 'News', 'News', 'news' ),
			array( 'Contatti', 'Contact', 'contatti' ),
		),
	),
	'footer'  => array(
		array( 'Footer', 'Footer (EN)' ),
		array(
			array( 'Offerta formativa', 'Courses', 'offerta-formativa' ),
			array( 'Corsi finanziati', 'Costs and funding', 'corsi-finanziati' ),
			array( 'Eventi', 'Events', 'eventi' ),
			array( 'Placement', 'Internships and placement', 'placement' ),
			array( 'Erasmus', 'Erasmus', 'erasmus' ),
			array( 'Borse di studio', 'Scholarships', 'borse-di-studio' ),
			array( 'Amministrazione trasparente', 'Transparent administration', 'amministrazione-trasparente' ),
			array( 'FAQ', 'FAQ', 'faq' ),
		),
	),
	'legal'   => array(
		array( 'Note legali', 'Legal (EN)' ),
		array(
			array( 'Privacy policy', 'Privacy policy', 'privacy-policy-2' ),
			array( 'Cookie policy', 'Cookie policy', 'cookie-policy-2' ),
		),
	),
);
$locations = array();
foreach ( $menus as $location => [ $names, $items ] ) {
	$locations[ $location ] = its_seed_menu( $names[0], array_map( static fn( $i ) => array( $i[0], $page_ids[ $i[2] ] ?? 0 ), $items ) );
}
set_theme_mod( 'nav_menu_locations', $locations );

// ---------- English (Polylang) ----------

$report = '';
if ( $multilingual ) {
	$en = $data['en'];
	// Italian pages whose URL slug differs from the post slug (e.g. /cookie-policy/ is "cookie-policy-2").
	$page_alias = array();
	foreach ( $data['pages'] as $p ) {
		$path = trim( (string) wp_parse_url( $p['link'], PHP_URL_PATH ), '/' );
		$url  = $path ? basename( $path ) : 'home';
		$slug = '' === $path || 'homepage-it' === $p['slug'] ? 'home' : $p['slug'];
		if ( $url !== $slug ) {
			$page_alias[ $url ] = $slug;
		}
	}
	$en_template = static function ( $slug, $p ) use ( $templates, $page_alias ) {
		$it_slug = $page_alias[ $p['it_slug'] ?? '' ] ?? ( $p['it_slug'] ?? '' );
		return $templates[ $it_slug ] ?? '';
	};
	$en_ids = array(
		'corso'          => its_seed_courses( $en['courses'], 'en', $seed_dir ),
		'post'           => its_seed_posts( $en['posts'], 'en', $seed_dir ),
		'evento'         => its_seed_events( $en['events'], 'en', $seed_dir ),
		'documento'      => its_seed_documents( $en['documents'], 'en' ),
		'edizione_corso' => its_seed_editions( $en['editions'], 'en' ),
		'page'           => its_seed_pages( $en['pages'], 'en', $en_template, $seed_dir ),
	);
	$linked = 0;
	foreach ( array( 'corso' => 'courses', 'post' => 'posts', 'evento' => 'events', 'documento' => 'documents', 'page' => 'pages' ) as $type => $key ) {
		$linked += its_seed_link( $en[ $key ], $it[ $type ], $en_ids[ $type ], 'page' === $type ? $page_alias : array() );
	}

	$en_home = $en_ids['page']['home-en'] ?? 0;
	if ( $en_home ) {
		wp_update_post(
			array(
				'ID'           => $en_home,
				'post_content' => $partners_html,
			)
		);
		if ( $poster ) {
			set_post_thumbnail( $en_home, $poster );
		}
	}
	$en_faq = pll_get_post( $page_ids['faq'] ?? 0, 'en' );
	its_seed_faq( (int) $en_faq, $en['faq'] ?? array(), 'Answers about courses, admissions, attendance, internships and services.' );

	// English menus on the same locations, through Polylang's per-language menu option.
	$nav   = array();
	$theme = get_option( 'stylesheet' );
	foreach ( $menus as $location => [ $names, $items ] ) {
		$en_menu                    = its_seed_menu(
			$names[1],
			array_map( static fn( $i ) => array( $i[1], (int) pll_get_post( $page_ids[ $i[2] ] ?? 0, 'en' ) ), $items )
		);
		$nav[ $theme ][ $location ] = array(
			'it' => $locations[ $location ],
			'en' => $en_menu,
		);
	}
	PLL()->options->set( 'nav_menus', $nav );
	PLL()->options->save();
	// Polylang caches each language's front page and posts page: rebuild it now they exist.
	PLL()->model->clean_languages_cache();

	$report = sprintf(
		"English: %d courses, %d posts, %d events, %d documents, %d editions, %d pages; %d translations linked.\n",
		count( $en['courses'] ),
		count( $en['posts'] ),
		count( $en['events'] ),
		count( $en['documents'] ),
		count( $en['editions'] ),
		count( $en['pages'] ),
		$linked
	);
}
flush_rewrite_rules();

printf(
	"Imported: %d courses, %d posts, %d events, %d documents, %d editions, %d pages.\n%s",
	count( $data['courses'] ),
	count( $data['posts'] ),
	count( $data['events'] ),
	count( $data['documents'] ),
	count( $data['editions'] ),
	count( $data['pages'] ),
	$report
);
