<?php
/**
 * A dated row (news or event): date, title, meta, arrow. Events carry a status badge.
 *
 * Args: 'heading' (h2|h3|h4), 'show_excerpt' (bool).
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

$heading  = $args['heading'] ?? 'h3';
$heading  = in_array( $heading, array( 'h2', 'h3', 'h4' ), true ) ? $heading : 'h3';
$is_event = 'evento' === get_post_type();
$date     = $is_event ? its_event_date( get_the_ID() ) : null;
$state    = $is_event ? its_stamp_state( $date ) : null;
$meta     = array();
if ( $is_event ) {
	$meta[] = its_meta( get_the_ID(), its_meta_keys( 'event_time' ) );
	$meta[] = its_meta( get_the_ID(), its_meta_keys( 'event_mode' ) );
} else {
	$cats   = get_the_category();
	$meta[] = $cats ? $cats[0]->name : '';
}
$meta = array_filter( array_map( 'strval', $meta ) );
?>
<article class="row">
	<?php echo its_date_slot( $date ?? (int) get_post_timestamp() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<div class="row__body">
		<<?php echo tag_escape( $heading ); ?> class="row__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></<?php echo tag_escape( $heading ); ?>>
		<?php if ( $meta || $state ) : ?>
			<p class="row__meta">
				<?php echo its_stamp( $state ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo esc_html( implode( ' · ', $meta ) ); ?>
			</p>
		<?php endif; ?>
		<?php if ( ! empty( $args['show_excerpt'] ) && has_excerpt() ) : ?>
			<p class="row__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p>
		<?php endif; ?>
	</div>
	<span class="row__arrow" aria-hidden="true"><?php echo its_icon( 'arrow-up' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
</article>
