<?php
/**
 * Page texts for the live site, in one page to open in the browser: for every page of the local site
 * (Italian and English), what goes into the live editor before the theme is activated, with copy buttons:
 * the block code, the excerpt, the featured image, the page template, and the direct link to the live editor.
 * A checklist remembers, in that browser, which pages are done. Nothing is sent to the live site.
 *
 * Run from the CLI, after dev/setup.sh: php dev/testi-pagine.php   ->   dist/testi-pagine/index.html
 *
 * @package its-rossellini-dev
 */

if ( PHP_SAPI !== 'cli' ) {
	exit( 1 );
}

$_SERVER['HTTP_HOST']   = 'localhost:8080';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_NAME'] = 'localhost';

require dirname( __DIR__ ) . '/.wp/wordpress/wp-load.php';

/**
 * Builds dist/testi-pagine/ and returns the number of pages listed.
 */
function its_page_texts(): int {
	$root  = dirname( __DIR__ );
	$live  = 'https://itsrossellini.it';
	$out   = $root . '/dist/testi-pagine';
	$seed  = $root . '/.wp/seed/data.json';
	$items = array();

	if ( ! is_file( $seed ) ) {
		fwrite( STDERR, "Manca .wp/seed/data.json: prima dev/setup.sh.\n" );
		exit( 1 );
	}
	$data      = json_decode( (string) file_get_contents( $seed ), true );
	$templates = wp_get_theme()->get_page_templates();
	wp_mkdir_p( $out . '/img' );

	$sources = array(
		'it' => $data['pages'] ?? array(),
		'en' => $data['en']['pages'] ?? array(),
	);
	foreach ( $sources as $lang => $pages ) {
		foreach ( $pages as $p ) {
			$front = ! empty( $p['front'] ) || 'homepage-it' === $p['slug'] || '/' === wp_parse_url( $p['link'], PHP_URL_PATH );
			$slug  = $front ? ( 'it' === $lang ? 'home' : 'home-' . $lang ) : $p['slug'];
			$found = get_posts(
				array(
					'post_type'      => 'page',
					'post_status'    => 'any',
					'posts_per_page' => 1,
					'lang'           => '',
					'meta_key'       => '_its_seed_key', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Local CLI script.
					'meta_value'     => 'page:' . $lang . ':' . $slug, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Local CLI script.
				)
			);
			if ( ! $found ) {
				continue;
			}
			$page    = $found[0];
			$content = (string) $page->post_content;
			// Links to the local site become relative, as they are on the live one.
			$content = str_replace( untrailingslashit( home_url() ), '', $content );
			// Files of the local media library used in the code: they must exist at the same path on the live site.
			preg_match_all( '#(?<=["\'\s,])/wp-content/uploads/[^"\'\s,)]+#', $content, $media );
			$media    = array_values( array_unique( array_map( 'basename', $media[0] ) ) );
			$template = (string) get_post_meta( $page->ID, '_wp_page_template', true );
			$image    = '';
			$preview  = '';
			$thumb    = (int) get_post_thumbnail_id( $page );
			if ( $thumb ) {
				$original = (string) wp_get_original_image_path( $thumb );
				$image    = basename( $original );
				$small    = image_get_intermediate_size( $thumb, 'medium' );
				$file     = $small ? dirname( $original ) . '/' . basename( $small['file'] ) : $original;
				if ( is_file( $file ) ) {
					copy( $file, $out . '/img/' . basename( $file ) );
					$preview = 'img/' . basename( $file );
				}
			}
			if ( str_contains( $content, '<!-- wp:' ) ) {
				$status = 'da-inserire';
			} elseif ( '' === trim( $content ) ) {
				$status = 'vuota';
			} else {
				$status = 'presente';
			}
			$items[] = array(
				'lang'     => $lang,
				'title'    => $front ? ( 'it' === $lang ? 'Home' : 'Home (English)' ) : get_the_title( $page ),
				'path'     => (string) wp_parse_url( $p['link'], PHP_URL_PATH ),
				'live'     => (string) $p['link'],
				'edit'     => $live . '/wp-admin/post.php?post=' . (int) $p['id'] . '&action=edit',
				'status'   => $status,
				'content'  => $content,
				'excerpt'  => (string) $page->post_excerpt,
				'image'    => $image,
				'preview'  => $preview,
				'template' => $template && 'default' !== $template ? ( $templates[ $template ] ?? $template ) : '',
				'media'    => $media,
			);
		}
	}
	usort( $items, static fn( $a, $b ) => array( 'it' !== $a['lang'], $a['path'] ) <=> array( 'it' !== $b['lang'], $b['path'] ) );

	$labels = array(
		'da-inserire' => 'Da inserire',
		'vuota'       => 'Solo il modello',
		'presente'    => 'Già sul live',
	);
	$counts = array_count_values( array_column( $items, 'status' ) );
	$cards  = '';
	foreach ( $items as $i => $item ) {
		$id     = 'p' . $i;
		$cards .= '<article class="card" data-lang="' . esc_attr( $item['lang'] ) . '" data-status="' . esc_attr( $item['status'] ) . '" data-key="' . esc_attr( $item['lang'] . ':' . $item['path'] ) . '">';
		$cards .= '<header class="card__head"><label class="done"><input type="checkbox"> Fatto</label>';
		$cards .= '<h2>' . esc_html( $item['title'] ) . '</h2>';
		$cards .= '<span class="tag">' . esc_html( strtoupper( $item['lang'] ) ) . '</span><span class="tag tag--' . esc_attr( $item['status'] ) . '">' . esc_html( $labels[ $item['status'] ] ) . '</span></header>';
		$cards .= '<p class="links"><a href="' . esc_url( $item['edit'] ) . '" target="_blank" rel="noopener">Apri nell\'editor del sito live ↗</a> · <a href="' . esc_url( $item['live'] ) . '" target="_blank" rel="noopener">' . esc_html( $item['path'] ) . '</a></p>';
		$cards .= '<dl class="facts">';
		if ( $item['template'] ) {
			$cards .= '<div><dt>Modello</dt><dd>' . esc_html( $item['template'] ) . ' <small>(Attributi pagina → Modello)</small></dd></div>';
		}
		if ( $item['excerpt'] ) {
			$cards .= '<div><dt>Riassunto</dt><dd><span id="' . $id . '-x">' . esc_html( $item['excerpt'] ) . '</span> <button type="button" data-copy="' . $id . '-x">Copia</button></dd></div>';
		}
		if ( $item['image'] ) {
			$cards .= '<div><dt>Immagine in evidenza</dt><dd>' . ( $item['preview'] ? '<img src="' . esc_attr( $item['preview'] ) . '" alt="" loading="lazy">' : '' ) . '<code id="' . $id . '-i">' . esc_html( $item['image'] ) . '</code> <button type="button" data-copy="' . $id . '-i">Copia il nome</button><small>Cercalo nella libreria media del live.</small></dd></div>';
		}
		if ( $item['media'] ) {
			$cards .= '<div><dt>File della libreria media</dt><dd class="warn">Il codice usa questi file con il loro percorso (/wp-content/uploads/…): ' . esc_html( implode( ', ', $item['media'] ) ) . '. Dopo aver incollato, controlla che si vedano; se no, sostituiscili scegliendoli dalla libreria media del live.</dd></div>';
		}
		$cards .= '</dl>';
		if ( 'da-inserire' === $item['status'] || 'presente' === $item['status'] ) {
			$cards .= '<div class="code"><div class="code__bar"><span>Codice dei blocchi</span><button type="button" data-copy="' . $id . '-c">Copia il codice</button></div>';
			$cards .= '<textarea id="' . $id . '-c" readonly rows="8" spellcheck="false">' . esc_textarea( $item['content'] ) . '</textarea></div>';
		} else {
			$cards .= '<p class="note">Nessun testo da inserire: la pagina mostra l\'elenco del suo modello.</p>';
		}
		$cards .= '</article>';
	}

	$date = wp_date( 'd/m/Y, H:i' );
	$html = '<!doctype html><html lang="it"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Testi delle pagine - ITS Rossellini</title><style>'
		. ':root{--blu:#004590;--notte:#002c64;--ciano:#49b7e9;--chiaro:#f4f7fb;--bordo:#e3e9f1;--testo:#0d1b2e;--muted:#55627a}'
		. '*{box-sizing:border-box}body{margin:0;font:16px/1.5 system-ui,Segoe UI,sans-serif;color:var(--testo);background:var(--chiaro)}'
		. '.wrap{max-width:1000px;margin:0 auto;padding:24px 16px 64px}h1{color:var(--notte);font-weight:600;margin:.2em 0}'
		. '.intro{background:#fff;border:1px solid var(--bordo);border-radius:22px;padding:16px 24px}.intro ol{padding-left:1.2em}'
		. '.bar{position:sticky;top:0;z-index:2;display:flex;flex-wrap:wrap;gap:8px 16px;align-items:center;background:var(--chiaro);padding:12px 0;border-bottom:1px solid var(--bordo)}'
		. '.bar label{display:flex;gap:6px;align-items:center}.progress{margin-left:auto;font-weight:600;color:var(--blu)}'
		. '.card{background:#fff;border:1px solid var(--bordo);border-radius:22px;padding:16px 24px;margin:16px 0}.card.is-done{opacity:.55}'
		. '.card__head{display:flex;flex-wrap:wrap;gap:8px 12px;align-items:center}.card h2{font-size:1.2rem;margin:0;flex:1 1 auto}'
		. '.tag{font-size:.8rem;font-weight:600;padding:2px 10px;border-radius:999px;background:var(--chiaro);color:var(--blu)}'
		. '.tag--da-inserire{background:var(--blu);color:#fff}.done{font-weight:600;color:var(--blu)}'
		. '.links{margin:.4em 0;font-size:.95rem}a{color:var(--blu)}.facts{margin:0}.facts div{display:grid;grid-template-columns:11rem 1fr;gap:12px;padding:6px 0;border-top:1px solid var(--bordo)}'
		. '.facts dt{font-weight:600;color:var(--muted)}.facts dd{margin:0}.facts img{display:block;max-width:220px;border-radius:14px;margin-bottom:6px}.facts small{display:block;color:var(--muted)}'
		. 'button{font:inherit;font-size:.85rem;font-weight:600;color:#fff;background:var(--blu);border:0;border-radius:999px;padding:4px 14px;cursor:pointer}button.ok{background:#1032cf}'
		. '.code{margin-top:10px}.code__bar{display:flex;justify-content:space-between;align-items:center;color:var(--muted);font-weight:600;margin-bottom:4px}'
		. 'textarea{width:100%;font:13px/1.45 ui-monospace,Consolas,monospace;border:1px solid var(--bordo);border-radius:14px;padding:10px;background:var(--chiaro)}'
		. '.note{color:var(--muted)}.warn{color:var(--notte);background:var(--chiaro);border-radius:14px;padding:6px 10px}@media (max-width:640px){.facts div{grid-template-columns:1fr;gap:2px}}'
		. '</style></head><body><div class="wrap">'
		. '<h1>Testi delle pagine per il sito live</h1>'
		. '<p>Creato il ' . esc_html( $date ) . ' dal sito locale. ' . count( $items ) . ' pagine: ' . (int) ( $counts['da-inserire'] ?? 0 ) . ' da inserire, ' . (int) ( $counts['vuota'] ?? 0 ) . ' con il solo modello, ' . (int) ( $counts['presente'] ?? 0 ) . ' già sul live.</p>'
		. '<div class="intro"><p><strong>Per ogni pagina «Da inserire»</strong>, prima di attivare il tema (README, «Messa in produzione», passaggio 4):</p><ol>'
		. '<li>«Apri nell\'editor del sito live» (serve l\'accesso all\'admin del live).</li>'
		. '<li>Menu ⋮ in alto a destra → <em>Editor del codice</em>; seleziona tutto il contenuto e sostituiscilo con «Copia il codice».</li>'
		. '<li><em>Esci dall\'editor del codice</em> e controlla che i blocchi si vedano.</li>'
		. '<li>Nella barra laterale, scheda <em>Pagina</em>: <em>Riassunto</em> (Copia), <em>Immagine in evidenza</em> (cerca il nome nella libreria media), e il <em>Modello</em> se indicato.</li>'
		. '<li><em>Aggiorna</em>, poi spunta «Fatto» qui: la spunta resta salvata in questo browser.</li></ol>'
		. '<p>Le pagine «Già sul live» hanno già il loro testo nell\'editor: basta controllarle. Se il tema è già attivo in un ambiente di prova, ogni pagina si può verificare subito dopo averla aggiornata.</p></div>'
		. '<div class="bar"><label>Lingua <select id="lang"><option value="">Tutte</option><option value="it">Italiano</option><option value="en">English</option></select></label>'
		. '<label><input type="checkbox" id="todo" checked> Solo da inserire</label><label><input type="checkbox" id="hide-done"> Nascondi le fatte</label><span class="progress" id="progress"></span></div>'
		. $cards
		. '</div><script>'
		. '(()=>{const K="its-testi-pagine";let done={};try{done=JSON.parse(localStorage.getItem(K)||"{}")}catch(e){}'
		. 'const cards=[...document.querySelectorAll(".card")];const save=()=>{try{localStorage.setItem(K,JSON.stringify(done))}catch(e){}};'
		. 'const apply=()=>{const lang=document.getElementById("lang").value,todo=document.getElementById("todo").checked,hide=document.getElementById("hide-done").checked;let n=0,d=0;'
		. 'cards.forEach(c=>{const isDone=!!done[c.dataset.key];c.classList.toggle("is-done",isDone);c.querySelector(".done input").checked=isDone;'
		. 'const show=(!lang||c.dataset.lang===lang)&&(!todo||c.dataset.status==="da-inserire")&&!(hide&&isDone);c.hidden=!show;'
		. 'if(c.dataset.status==="da-inserire"&&(!lang||c.dataset.lang===lang)){n++;if(isDone)d++}});'
		. 'document.getElementById("progress").textContent=d+" di "+n+" fatte"};'
		. 'cards.forEach(c=>c.querySelector(".done input").addEventListener("change",e=>{if(e.target.checked)done[c.dataset.key]=1;else delete done[c.dataset.key];save();apply()}));'
		. '["lang","todo","hide-done"].forEach(id=>document.getElementById(id).addEventListener("change",apply));'
		. 'document.addEventListener("click",async e=>{const b=e.target.closest("[data-copy]");if(!b)return;const el=document.getElementById(b.dataset.copy);const text=el.value!==undefined?el.value:el.textContent;'
		. 'try{await navigator.clipboard.writeText(text)}catch(err){if(el.select){el.select()}else{const r=document.createRange();r.selectNodeContents(el);getSelection().removeAllRanges();getSelection().addRange(r)}document.execCommand("copy")}'
		. 'const label=b.textContent;b.textContent="Copiato";b.classList.add("ok");setTimeout(()=>{b.textContent=label;b.classList.remove("ok")},1500)});apply()})();'
		. '</script></body></html>';
	file_put_contents( $out . '/index.html', $html );
	return count( $items );
}

$its_count = its_page_texts();
printf( "Pronto: dist/testi-pagine/index.html (%d pagine). Aprilo nel browser.\n", (int) $its_count );
