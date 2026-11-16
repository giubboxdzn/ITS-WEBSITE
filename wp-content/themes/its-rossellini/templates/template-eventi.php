<?php
/**
 * Template Name: Eventi
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$events = its_events_by_date( 12 );
	get_template_part(
		'template-parts/page-head',
		null,
		array(
			'title' => get_the_title(),
			'lead'  => has_excerpt() ? get_the_excerpt() : __( 'Open day, incontri di orientamento e i set dove lavorano gli studenti.', 'its-rossellini' ),
		)
	);
	?>
	<div class="wrap page-body page-body--wide">
		<section class="listing" aria-labelledby="upcoming-title">
			<h2 id="upcoming-title" class="listing__title"><?php esc_html_e( 'In programma', 'its-rossellini' ); ?></h2>
			<?php if ( $events['upcoming'] ) : ?>
				<div class="listing__items">
					<?php
					foreach ( $events['upcoming'] as $post ) :
						setup_postdata( $GLOBALS['post'] = $post ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						get_template_part( 'template-parts/dated-item', null, array( 'show_excerpt' => true ) );
					endforeach;
					wp_reset_postdata();
					?>
				</div>
			<?php else : ?>
				<div class="empty">
					<p><?php esc_html_e( 'Nessun evento in calendario in questo momento. Puoi visitare la sede con un incontro individuale, su appuntamento.', 'its-rossellini' ); ?></p>
					<a class="button button--blue" href="<?php echo esc_url( its_page_url( 'incontriamoci', its_page_url( 'contatti' ) ) ); ?>"><?php esc_html_e( 'Prenota un incontro', 'its-rossellini' ); ?></a>
				</div>
			<?php endif; ?>
		</section>

		<?php if ( $events['past'] ) : ?>
			<section class="listing listing--past" aria-labelledby="past-title">
				<h2 id="past-title" class="listing__title"><?php esc_html_e( 'Già andati in scena', 'its-rossellini' ); ?></h2>
				<div class="listing__items">
					<?php
					foreach ( $events['past'] as $post ) :
						setup_postdata( $GLOBALS['post'] = $post ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						get_template_part( 'template-parts/dated-item', null, array( 'show_excerpt' => true ) );
					endforeach;
					wp_reset_postdata();
					?>
				</div>
			</section>
		<?php endif; ?>
	</div>
	<?php
endwhile;

get_footer();
