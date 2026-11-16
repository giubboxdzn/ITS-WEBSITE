<?php
/**
 * Template Name: Elenco corsi
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
			'title' => get_the_title(),
			'lead'  => has_excerpt() ? get_the_excerpt() : __( 'Otto percorsi biennali post-diploma: scegli l’area, poi il mestiere.', 'its-rossellini' ),
		)
	);
	?>
	<section class="section section--paper section--tight" id="corsi" aria-label="<?php esc_attr_e( 'Corsi', 'its-rossellini' ); ?>">
		<div class="wrap">
			<?php get_template_part( 'template-parts/course-wall', null, array( 'heading' => 'h2' ) ); ?>
		</div>
	</section>
	<?php if ( '' !== trim( get_the_content() ) ) : ?>
		<div class="wrap page-body">
			<div class="prose"><?php the_content(); ?></div>
		</div>
	<?php endif; ?>
	<?php
endwhile;

get_footer();
