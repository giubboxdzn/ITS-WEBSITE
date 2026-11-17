<?php
/**
 * Default page: sections when the content uses the theme's block styles, else the text as written.
 * Both end with the closing call.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$editorial = its_editorial( get_the_ID() );

	get_template_part(
		'template-parts/page-head',
		null,
		array(
			'title'    => get_the_title(),
			'lead'     => has_excerpt() ? get_the_excerpt() : '',
			'image_id' => (int) get_post_thumbnail_id(),
			'aside'    => $editorial['actions'] ?? '',
			'float'    => $editorial['float'] ?? '',
		)
	);

	if ( $editorial ) :
		get_template_part(
			'template-parts/page-sections',
			null,
			array(
				'sections' => $editorial['sections'],
				'theme'    => 'light',
			)
		);
	else :
		$prose = its_prose( (string) apply_filters( 'the_content', get_the_content() ) );
		$faq   = str_contains( $prose['html'], '<details' );
		?>
		<div class="wrap page-body reading">
			<div class="prose prose--reading reading__main<?php echo $faq ? ' prose--faq' : ''; ?>">
				<?php
				echo $prose['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				wp_link_pages();
				?>
				<?php get_template_part( 'template-parts/child-pages' ); ?>
			</div>
			<aside class="reading__aside" aria-label="<?php esc_attr_e( 'In questa pagina', 'its-rossellini' ); ?>">
				<div class="aside-card">
					<?php if ( $faq ) : ?>
						<p class="aside-card__title"><?php esc_html_e( 'Non trovi la tua domanda?', 'its-rossellini' ); ?></p>
						<p class="aside-card__text"><?php esc_html_e( 'Scrivi all’ufficio giusto o prenota un incontro: ti rispondiamo noi.', 'its-rossellini' ); ?></p>
					<?php else : ?>
						<?php echo its_toc( $prose['toc'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<dl class="aside-card__facts">
							<div><dt><?php esc_html_e( 'Aggiornato', 'its-rossellini' ); ?></dt><dd><time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>"><?php echo esc_html( get_the_modified_date() ); ?></time></dd></div>
						</dl>
					<?php endif; ?>
					<div class="aside-card__actions">
						<a class="btn btn--grad" href="<?php echo esc_url( its_page_url( 'contatti' ) ); ?>"><?php esc_html_e( 'Contatti', 'its-rossellini' ); ?><?php echo its_icon( 'arrow-up' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
						<a class="btn btn--outline" href="<?php echo esc_url( its_page_url( 'incontriamoci', its_page_url( 'contatti' ) ) ); ?>"><?php esc_html_e( 'Prenota un incontro', 'its-rossellini' ); ?></a>
					</div>
				</div>
			</aside>
		</div>
		<?php
	endif;

	get_template_part( 'template-parts/cta-card', null, $editorial['cta'] ?? array() );
endwhile;

get_footer();
