<?php
/**
 * Fotobusta: a course as a lobby card. Photo in the area's ink, white margin, title band.
 *
 * Args: 'heading' (h2|h3), 'loading' (lazy|eager).
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

$heading = $args['heading'] ?? 'h3';
$heading = in_array( $heading, array( 'h2', 'h3' ), true ) ? $heading : 'h3';
$area    = its_area( get_the_ID() );
$ink     = its_area_ink( $area );
?>
<article class="fotobusta" data-ink="<?php echo esc_attr( $ink ); ?>" data-area="<?php echo esc_attr( $area ? $area->slug : '' ); ?>">
	<a class="fotobusta__link" href="<?php the_permalink(); ?>">
		<figure class="fotobusta__photo duotone<?php echo has_post_thumbnail() ? '' : ' duotone--type'; ?>">
			<?php
			if ( ! has_post_thumbnail() ) {
				echo '<span aria-hidden="true">' . esc_html( $area ? $area->name : get_the_title() ) . '</span>';
			} else {
				the_post_thumbnail(
					'its-fotobusta',
					array(
						'loading' => $args['loading'] ?? 'lazy',
						'sizes'   => '(max-width: 720px) 92vw, (max-width: 1200px) 46vw, 560px',
						'alt'     => '',
					)
				);
			}
			?>
		</figure>
		<div class="fotobusta__band">
			<<?php echo tag_escape( $heading ); ?> class="fotobusta__title"><?php the_title(); ?></<?php echo tag_escape( $heading ); ?>>
			<?php if ( $area ) : ?>
				<p class="fotobusta__area"><?php echo esc_html( $area->name ); ?></p>
			<?php endif; ?>
			<?php if ( has_excerpt() ) : ?>
				<p class="fotobusta__line"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
			<span class="fotobusta__more"><?php esc_html_e( 'Scheda del corso', 'its-rossellini' ); ?><?php echo its_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		</div>
	</a>
</article>
