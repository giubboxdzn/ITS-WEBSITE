<?php
/**
 * Default page.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	if ( its_has_sections() ) :
		$content = (string) apply_filters( 'the_content', get_the_content() );
		$parsed  = its_page_sections( $content );
		$cards   = its_child_cards( get_the_ID(), $content );
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
		// Hero actions: the page's own call first, then a meeting, as on the home page.
		$meet       = array( its_page_url( 'incontriamoci', its_page_url( 'contatti' ) ), __( 'Prenota un incontro', 'its-rossellini' ) );
		$hero_links = $links ?: array( $meet );
		if ( count( $hero_links ) < 2 && $hero_links[0][0] !== $meet[0] ) {
			$hero_links[] = $meet;
		}
		if ( count( $hero_links ) < 2 ) {
			$hero_links[] = array( its_page_url( 'iscrizioni' ), __( 'Preiscriviti', 'its-rossellini' ) );
		}
		// The glass figure: the page's first figure, else the employment rate (home).
		$stat = array();
		foreach ( $parsed['sections'] as $section ) {
			if ( $section['facts'] ) {
				$stat = $section['facts'][0];
				break;
			}
		}
		get_template_part(
			'template-parts/page-hero',
			null,
			array(
				'title'    => get_the_title(),
				'lead'     => has_excerpt() ? get_the_excerpt() : '',
				'image_id' => (int) get_post_thumbnail_id(),
				'index'    => $index,
				'links'    => $hero_links,
				'stat'     => $stat,
			)
		);
		get_template_part(
			'template-parts/page-sections',
			null,
			array(
				'sections' => $parsed['sections'],
				'next'     => 'panel',
			)
		);
		get_template_part( 'template-parts/news-panel' );
		get_template_part( 'template-parts/faq-section' );
		get_template_part(
			'template-parts/cta-card',
			null,
			$callout ? array(
				'title' => $callout['title'],
				'text'  => implode( ' ', $callout['text'] ),
				'links' => $links,
			) : array()
		);
		continue;
	endif;

	get_template_part(
		'template-parts/page-head',
		null,
		array(
			'title'    => get_the_title(),
			'lead'     => has_excerpt() ? get_the_excerpt() : '',
			'image_id' => (int) get_post_thumbnail_id(),
		)
	);
	?>
	<div class="wrap page-body">
		<div class="prose">
			<?php
			the_content();
			wp_link_pages();
			?>
		</div>
		<?php get_template_part( 'template-parts/child-pages' ); ?>
	</div>
	<?php
endwhile;

get_footer();
