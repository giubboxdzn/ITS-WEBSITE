<?php
/**
 * Course card: real photo, title, area, one line, link.
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
					'its-card',
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
			<?php if ( $area ) : ?>
				<p class="card__area"><?php echo esc_html( $area->name ); ?></p>
			<?php endif; ?>
			<?php if ( has_excerpt() ) : ?>
				<p class="card__text"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
			<span class="card__more"><?php esc_html_e( 'Scopri il corso', 'its-rossellini' ); ?><?php echo its_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		</div>
	</a>
</article>
