<?php
/**
 * Import .wp/seed/data.json (fetched from the public site) into the LOCAL WordPress.
 * Idempotent: existing items are matched by post type + slug and updated.
 * Run from the CLI: php dev/seed/import.php
 *
 * @package its-rossellini-dev
 */

if ( PHP_SAPI !== 'cli' ) {
	exit( 1 );
}

$_SERVER['HTTP_HOST']   = 'localhost:8080';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_NAME'] = 'localhost';

$root = dirname( __DIR__, 2 );
require $root . '/.wp/wordpress/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$seed_dir = $root . '/.wp/seed';
$data     = json_decode( (string) file_get_contents( $seed_dir . '/data.json' ), true );
if ( ! $data ) {
	fwrite( STDERR, "Missing .wp/seed/data.json — run dev/seed/fetch.py first.\n" );
	exit( 1 );
}

wp_set_current_user( 1 );

// Default content WordPress creates on install.
foreach ( array( 'hello-world', 'sample-page' ) as $default_slug ) {
	foreach ( get_posts( array( 'name' => $default_slug, 'post_type' => array( 'post', 'page' ), 'post_status' => 'any', 'fields' => 'ids' ) ) as $default_id ) {
		wp_delete_post( $default_id, true );
	}
}

/**
 * Create or update a post by type + slug.
 */
function its_seed_upsert( string $type, string $slug, array $args ): int {
	$existing = get_posts(
		array(
			'post_type'      => $type,
			'name'           => $slug,
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
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
		return (int) wp_update_post( wp_slash( $args ) );
	}
	return (int) wp_insert_post( wp_slash( $args ) );
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
 * Term ids for names, created when missing.
 */
function its_seed_terms( array $names, string $tax ): array {
	$ids = array();
	foreach ( $names as $name ) {
		$term = term_exists( $name, $tax );
		if ( ! $term ) {
			$term = wp_insert_term( $name, $tax );
		}
		if ( ! is_wp_error( $term ) ) {
			$ids[] = (int) $term['term_id'];
		}
	}
	return $ids;
}

/**
 * Parse an Italian date like "10 settembre 2026" into Y-m-d.
 */
function its_seed_date( string $text ): string {
	$months = array( 'gennaio', 'febbraio', 'marzo', 'aprile', 'maggio', 'giugno', 'luglio', 'agosto', 'settembre', 'ottobre', 'novembre', 'dicembre' );
	if ( preg_match( '/(\d{1,2})\s+([a-zà]+)\s+(\d{4})/iu', $text, $m ) ) {
		$month = array_search( mb_strtolower( $m[2] ), $months, true );
		if ( false !== $month ) {
			return sprintf( '%04d-%02d-%02d', $m[3], $month + 1, $m[1] );
		}
	}
	return '';
}

// Courses: structured sections become editor content (editable in WordPress).
$order = 0;
foreach ( $data['courses'] as $c ) {
	$html = '';
	if ( $c['intro'] ) {
		$html .= '<p class="is-style-lead">' . esc_html( $c['intro'] ) . "</p>\n";
	}
	$sections = array(
		'competenze'  => array( 'Competenze', 'ul' ),
		'professioni' => array( 'Professioni', 'ul' ),
		'programma'   => array( 'Programma', 'ol' ),
		'strumenti'   => array( 'Strumenti e tecnologie', 'ul' ),
	);
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
		)
	);
	wp_set_object_terms( $id, its_seed_terms( $c['area'], 'area_tecnologica' ), 'area_tecnologica' );
	its_seed_image( $id, $c['image'], $c['image_alt'], $seed_dir );
}

foreach ( $data['posts'] as $p ) {
	$id = its_seed_upsert(
		'post',
		$p['slug'],
		array(
			'post_title'   => $p['title'],
			'post_content' => $p['content'],
			'post_excerpt' => html_entity_decode( $p['excerpt'] ),
			'post_date'    => str_replace( 'T', ' ', $p['date'] ),
		)
	);
	wp_set_post_categories( $id, its_seed_terms( $p['categories'] ?: array( 'News' ), 'category' ) );
	its_seed_image( $id, $p['image'], $p['image_alt'], $seed_dir );
}

foreach ( $data['events'] as $e ) {
	$id = its_seed_upsert(
		'evento',
		$e['slug'],
		array(
			'post_title'   => $e['title'],
			'post_content' => $e['description'] ? '<p>' . esc_html( $e['description'] ) . '</p>' : '',
			'post_excerpt' => $e['description'],
			'post_date'    => str_replace( 'T', ' ', $e['date'] ),
		)
	);
	update_post_meta( $id, 'data_evento', its_seed_date( $e['data_text'] ) );
	update_post_meta( $id, 'ora_evento', $e['orario'] );
	update_post_meta( $id, 'modalita', $e['modalita'] );
	update_post_meta( $id, 'luogo', $e['luogo'] );
	if ( $e['tipo_evento'] ) {
		wp_set_object_terms( $id, its_seed_terms( $e['tipo_evento'], 'tipo_evento' ), 'tipo_evento' );
	}
	its_seed_image( $id, $e['image'], $e['image_alt'], $seed_dir );
}

foreach ( $data['documents'] as $d ) {
	$id = its_seed_upsert(
		'documento',
		$d['slug'],
		array(
			'post_title' => $d['title'],
			'post_date'  => str_replace( 'T', ' ', $d['date'] ),
		)
	);
	wp_set_object_terms( $id, its_seed_terms( $d['tipo'], 'tipo' ), 'tipo' );
	wp_set_object_terms( $id, its_seed_terms( $d['biennio'], 'biennio' ), 'biennio' );
	foreach ( array( 'file_url', 'formato', 'dimensione' ) as $key ) {
		if ( ! empty( $d[ $key ] ) ) {
			update_post_meta( $id, $key, $d[ $key ] );
		}
	}
}

foreach ( $data['editions'] as $ed ) {
	$id = its_seed_upsert(
		'edizione_corso',
		$ed['slug'],
		array(
			'post_title' => $ed['title'],
			'post_date'  => str_replace( 'T', ' ', $ed['date'] ),
		)
	);
	wp_set_object_terms( $id, its_seed_terms( $ed['famiglia'], 'famiglia' ), 'famiglia' );
	wp_set_object_terms( $id, its_seed_terms( $ed['biennio'], 'biennio' ), 'biennio' );
}

// Pages: parents first.
$pages = $data['pages'];
usort( $pages, static fn( $a, $b ) => (int) ( '' !== $a['parent_slug'] ) <=> (int) ( '' !== $b['parent_slug'] ) );
$page_ids  = array();
$templates = array(
	'corsi'                            => 'templates/template-corsi.php',
	'eventi'                           => 'templates/template-eventi.php',
	'bandi'                            => 'templates/template-documenti.php',
	'bandi-albi'                       => 'templates/template-documenti.php',
	'corsi-in-svolgimento-e-conclusi' => 'templates/template-edizioni.php',
);
foreach ( $pages as $p ) {
	$slug = 'homepage-it' === $p['slug'] || '/' === wp_parse_url( $p['link'], PHP_URL_PATH ) ? 'home' : $p['slug'];
	// Scraped sections start at h3 on the live markup; under the page h1 they are h2.
	if ( ! str_contains( $p['content'], '<h2' ) ) {
		$p['content'] = preg_replace( array( '/<h3(\b[^>]*)>/', '/<\/h3>/' ), array( '<h2$1>', '</h2>' ), $p['content'] );
	}
	$args = array(
		'post_title'   => 'home' === $slug ? 'Home' : $p['title'],
		'post_content' => 'home' === $slug ? '' : $p['content'],
		'post_parent'  => $page_ids[ $p['parent_slug'] ] ?? 0,
		'menu_order'   => $p['menu_order'],
	);
	$id                 = its_seed_upsert( 'page', $slug, $args );
	$page_ids[ $slug ]  = $id;
	if ( isset( $templates[ $slug ] ) ) {
		update_post_meta( $id, '_wp_page_template', $templates[ $slug ] );
	}
}

// FAQ page as native Details blocks (one question per block), editable in the block editor.
if ( ! empty( $data['faq'] ) && isset( $page_ids['faq'] ) ) {
	$blocks = array();
	foreach ( $data['faq'] as $item ) {
		$blocks[] = "<!-- wp:details -->\n<details class=\"wp-block-details\"><summary>" . esc_html( $item['q'] ) . "</summary><!-- wp:paragraph -->\n<p>" . esc_html( $item['a'] ) . "</p>\n<!-- /wp:paragraph --></details>\n<!-- /wp:details -->";
	}
	wp_update_post(
		array(
			'ID'           => $page_ids['faq'],
			'post_excerpt' => 'Risposte su corsi, ammissioni, frequenza, stage e servizi.',
			'post_content' => implode( "\n\n", $blocks ),
		)
	);
}

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
	$img       = str_replace( untrailingslashit( home_url() ), '', $img ); // Root-relative: works on any local port.
	$partners .= '<li' . ( $wide ? ' class="is-wide"' : '' ) . '>' . ( $partner['url'] ? '<a href="' . esc_url( $partner['url'] ) . '">' . $img . '</a>' : $img ) . "</li>\n";
}
$home_id = $page_ids['home'] ?? its_seed_upsert( 'page', 'home', array( 'post_title' => 'Home' ) );
wp_update_post(
	array(
		'ID'           => $home_id,
		'post_content' => "<!-- wp:html -->\n<ul class=\"partner-logos\">\n{$partners}</ul>\n<!-- /wp:html -->",
	)
);
// Front page image (video poster): the editing-room photo the live site uses as its video poster.
$producer = get_posts( array( 'post_type' => 'corso', 'name' => 'editor-post-production-specialist', 'fields' => 'ids' ) );
if ( $producer && has_post_thumbnail( $producer[0] ) ) {
	set_post_thumbnail( $home_id, get_post_thumbnail_id( $producer[0] ) );
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

// Menus.
$menus = array(
	'primary' => array(
		'Menu principale',
		array(
			array( 'Corsi', 'corsi' ),
			array( 'Academy', 'academy' ),
			array( 'Servizi', 'servizi' ),
			array( 'Bandi e albi', 'bandi-albi' ),
			array( 'News', 'news' ),
			array( 'Contatti', 'contatti' ),
		),
	),
	'footer'  => array(
		'Footer',
		array(
			array( 'Offerta formativa', 'offerta-formativa' ),
			array( 'Corsi finanziati', 'corsi-finanziati' ),
			array( 'Eventi', 'eventi' ),
			array( 'Placement', 'placement' ),
			array( 'Erasmus', 'erasmus' ),
			array( 'Borse di studio', 'borse-di-studio' ),
			array( 'Amministrazione trasparente', 'amministrazione-trasparente' ),
			array( 'FAQ', 'faq' ),
		),
	),
	'legal'   => array(
		'Note legali',
		array(
			array( 'Privacy policy', 'privacy-policy-2' ),
			array( 'Cookie policy', 'cookie-policy-2' ),
		),
	),
);
$locations = array();
foreach ( $menus as $location => [ $name, $items ] ) {
	$menu = wp_get_nav_menu_object( $name );
	if ( $menu ) {
		wp_delete_nav_menu( $menu->term_id );
	}
	$menu_id = wp_create_nav_menu( $name );
	foreach ( $items as [ $label, $slug ] ) {
		if ( empty( $page_ids[ $slug ] ) ) {
			continue;
		}
		wp_update_nav_menu_item(
			$menu_id,
			0,
			array(
				'menu-item-title'     => $label,
				'menu-item-object'    => 'page',
				'menu-item-object-id' => $page_ids[ $slug ],
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
			)
		);
	}
	$locations[ $location ] = $menu_id;
}
set_theme_mod( 'nav_menu_locations', $locations );
flush_rewrite_rules();

printf(
	"Imported: %d courses, %d posts, %d events, %d documents, %d editions, %d pages.\n",
	count( $data['courses'] ),
	count( $data['posts'] ),
	count( $data['events'] ),
	count( $data['documents'] ),
	count( $data['editions'] ),
	count( $pages )
);
