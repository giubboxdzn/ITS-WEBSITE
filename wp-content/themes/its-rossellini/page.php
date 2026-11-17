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
	endif;

	get_template_part( 'template-parts/cta-card', null, $editorial['cta'] ?? array() );
endwhile;

get_footer();
