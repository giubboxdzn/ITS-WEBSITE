<?php
/**
 * Single event: date in the fixed slot, programme stamp, practical facts.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$id    = get_the_ID();
	$date  = its_event_date( $id );
	$state = its_stamp_state( $date );
	$facts = array_filter(
		array(
			__( 'Data', 'its-rossellini' )     => $date ? wp_date( 'l j F Y', $date->getTimestamp() ) : '',
			__( 'Orario', 'its-rossellini' )   => (string) its_meta( $id, its_meta_keys( 'event_time' ) ),
			__( 'Modalità', 'its-rossellini' ) => (string) its_meta( $id, its_meta_keys( 'event_mode' ) ),
			__( 'Luogo', 'its-rossellini' )    => (string) its_meta( $id, its_meta_keys( 'event_place' ) ),
		)
	);
	get_template_part(
		'template-parts/page-head',
		null,
		array(
			'title'    => get_the_title(),
			// The excerpt is often the description itself: shown once, in the text.
			'lead'     => has_excerpt() && ! str_contains( wp_strip_all_tags( get_the_content() ), trim( get_the_excerpt() ) ) ? get_the_excerpt() : '',
			'image_id' => (int) get_post_thumbnail_id(),
			'aside'    => its_stamp( $state ),
		)
	);
	?>
	<div class="wrap page-body reading reading--facts-first">
		<div class="prose prose--reading reading__main">
			<?php the_content(); ?>
		</div>
		<aside class="reading__aside" aria-label="<?php esc_attr_e( 'Informazioni sull’evento', 'its-rossellini' ); ?>">
			<div class="aside-card">
				<?php if ( $date ) : ?>
					<div class="aside-card__date"><?php echo its_date_slot( $date, 'span' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<?php endif; ?>
				<?php if ( $facts ) : ?>
					<dl class="aside-card__facts">
						<?php foreach ( $facts as $label => $value ) : ?>
							<div><dt><?php echo esc_html( $label ); ?></dt><dd><?php echo esc_html( $value ); ?></dd></div>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>
				<div class="aside-card__actions">
					<a class="btn btn--grad" href="<?php echo esc_url( its_page_url( 'incontriamoci', its_page_url( 'contatti' ) ) ); ?>"><?php esc_html_e( 'Prenota un incontro', 'its-rossellini' ); ?><?php echo its_icon( 'arrow-up' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
					<a class="btn btn--outline" href="<?php echo esc_url( its_page_url( 'eventi' ) ); ?>"><?php esc_html_e( 'Tutti gli eventi', 'its-rossellini' ); ?></a>
				</div>
			</div>
		</aside>
	</div>
	<?php
	get_template_part( 'template-parts/cta-card' );
endwhile;

get_footer();
