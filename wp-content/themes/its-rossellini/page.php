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
