<?php
/**
 * Course card: full-height photo, area chip, title and line over a scrim, arrow.
 *
 * Args: 'heading' (h2|h3), 'loading' (lazy|eager).
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

$heading = $args['heading'] ?? 'h3';
$heading = in_array( $heading, array( 'h2', 'h3' ), true ) ? $heading : 'h3';
$area    = its_area( get_the_ID() );
?>
<article class="card">
	<a class="card__link" href="<?php the_permalink(); ?>">
		<div class="card__media">
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail(
					'its-portrait',
					array(
						'loading' => $args['loading'] ?? 'lazy',
						'sizes'   => '(max-width: 640px) 92vw, (max-width: 1100px) 46vw, 330px',
						'alt'     => '',
					)
				);
			}
			?>
		</div>
		<div class="card__body">
			<<?php echo tag_escape( $heading ); ?> class="card__title"><?php the_title(); ?></<?php echo tag_escape( $heading ); ?>>
			<?php if ( has_excerpt() ) : ?>
				<p class="card__text"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
		</div>
		<?php if ( $area ) : ?>
			<span class="card__area glass-chip"><?php echo esc_html( $area->name ); ?></span>
		<?php endif; ?>
		<span class="card__arrow" aria-hidden="true"><?php echo its_icon( 'arrow-up' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
	</a>
</article>
