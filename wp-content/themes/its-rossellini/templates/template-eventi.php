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
			<h2 id="upcoming-title" class="display display--m display--dark listing__title"><?php echo its_display_text( __( 'In programma', 'its-rossellini' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
			<?php if ( $events['upcoming'] ) : ?>
				<div class="rows">
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
					<a class="btn btn--blue" href="<?php echo esc_url( its_page_url( 'incontriamoci', its_page_url( 'contatti' ) ) ); ?>"><?php esc_html_e( 'Prenota un incontro', 'its-rossellini' ); ?></a>
				</div>
			<?php endif; ?>
		</section>

		<?php if ( $events['past'] ) : ?>
			<section class="listing listing--past" aria-labelledby="past-title">
				<h2 id="past-title" class="display display--m display--dark listing__title"><?php echo its_display_text( __( 'Già andati in scena', 'its-rossellini' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
				<div class="rows">
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
	get_template_part( 'template-parts/cta-card' );
endwhile;

get_footer();
