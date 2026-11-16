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
		get_template_part(
			'template-parts/page-hero',
			null,
			array(
				'title'    => get_the_title(),
				'lead'     => has_excerpt() ? get_the_excerpt() : '',
				'image_id' => (int) get_post_thumbnail_id(),
				'index'    => $index,
				'links'    => $links ?: array(
					array( its_page_url( 'incontriamoci', its_page_url( 'contatti' ) ), __( 'Prenota un incontro', 'its-rossellini' ) ),
					array( its_page_url( 'iscrizioni' ), __( 'Preiscriviti', 'its-rossellini' ) ),
				),
			)
		);
		get_template_part( 'template-parts/page-sections', null, array( 'sections' => $parsed['sections'] ) );
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
