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
			'lead'     => has_excerpt() ? get_the_excerpt() : '',
			'image_id' => (int) get_post_thumbnail_id(),
			'aside'    => its_stamp( $state ),
		)
	);
	?>
	<div class="wrap page-body page-body--split">
		<?php if ( $facts ) : ?>
			<dl class="facts">
				<?php if ( $date ) : ?>
					<div class="facts__date"><?php echo its_date_slot( $date, 'span' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<?php endif; ?>
				<?php foreach ( $facts as $label => $value ) : ?>
					<div><dt><?php echo esc_html( $label ); ?></dt><dd><?php echo esc_html( $value ); ?></dd></div>
				<?php endforeach; ?>
			</dl>
		<?php endif; ?>
		<div class="prose">
			<?php the_content(); ?>
			<p class="prose__actions">
				<a class="btn btn--blue" href="<?php echo esc_url( its_page_url( 'incontriamoci', its_page_url( 'contatti' ) ) ); ?>"><?php esc_html_e( 'Prenota un incontro', 'its-rossellini' ); ?></a>
				<a class="link-arrow" href="<?php echo esc_url( its_page_url( 'eventi' ) ); ?>"><?php esc_html_e( 'Tutti gli eventi', 'its-rossellini' ); ?><?php echo its_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			</p>
		</div>
	</div>
	<?php
endwhile;

get_footer();
