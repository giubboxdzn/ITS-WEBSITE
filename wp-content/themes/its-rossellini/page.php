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
		// Actions: the page's own call first, then a meeting.
		$meet       = array( its_page_url( 'incontriamoci', its_page_url( 'contatti' ) ), __( 'Prenota un incontro', 'its-rossellini' ) );
		$hero_links = $links ?: array( $meet );
		if ( count( $hero_links ) < 2 && $hero_links[0][0] !== $meet[0] ) {
			$hero_links[] = $meet;
		}
		if ( count( $hero_links ) < 2 ) {
			$hero_links[] = array( its_page_url( 'iscrizioni' ), __( 'Preiscriviti', 'its-rossellini' ) );
		}
		$actions = '<div class="page-head__actions">';
		foreach ( array_slice( $hero_links, 0, 2 ) as $n => [ $url, $label ] ) {
			$icon     = its_link_icon( $url );
			$actions .= 0 === $n
				? '<a class="btn btn--grad" href="' . esc_url( $url ) . '">' . esc_html( $label ) . its_icon( 'arrow' === $icon ? 'arrow-up' : $icon ) . '</a>'
				: '<a class="btn btn--outline" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
		}
		$actions .= '</div>';
		$float    = '';
		if ( count( $index ) > 2 ) {
			$float = '<nav class="glass-card page-index" aria-labelledby="page-index-title"><p id="page-index-title" class="page-index__title">' . esc_html__( 'In questa pagina', 'its-rossellini' ) . '</p><ol>';
			foreach ( $index as $n => [ $anchor, $label ] ) {
				$float .= '<li><a href="#' . esc_attr( $anchor ) . '"><span class="page-index__n" aria-hidden="true">' . sprintf( '%02d', $n + 1 ) . '</span>' . esc_html( $label ) . '</a></li>';
			}
			$float .= '</ol></nav>';
		}
		get_template_part(
			'template-parts/page-head',
			null,
			array(
				'title'      => get_the_title(),
				'title_html' => its_display_text( get_the_title() ),
				'lead'       => has_excerpt() ? get_the_excerpt() : '',
				'image_id'   => (int) get_post_thumbnail_id(),
				'aside'      => $actions,
				'float'      => $float,
			)
		);
		get_template_part(
			'template-parts/page-sections',
			null,
			array(
				'sections' => $parsed['sections'],
				'theme'    => 'light',
			)
		);
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
