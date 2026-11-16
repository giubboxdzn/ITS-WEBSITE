<?php
/**
 * Interior page head on deep blue.
 *
 * Args: 'title' (string), 'lead' (string), 'image_id' (int), 'aside' (html string),
 *       'cover' (bool) full-bleed photo behind the title.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

$title    = $args['title'] ?? '';
$lead     = $args['lead'] ?? '';
$image_id = (int) ( $args['image_id'] ?? 0 );
$long     = mb_strlen( $title ) > 26;
// A full-bleed head needs a photo that holds up at full width; smaller ones sit beside the title.
$meta     = $image_id ? wp_get_attachment_metadata( $image_id ) : array();
$cover    = ! empty( $args['cover'] ) && $image_id && (int) ( $meta['width'] ?? 0 ) >= 1200;
$classes  = 'page-head' . ( $cover ? ' page-head--cover' : ( $image_id ? ' page-head--photo' : '' ) );
?>
<header class="<?php echo esc_attr( $classes ); ?>">
	<?php if ( $cover ) : ?>
		<div class="page-head__cover">
			<?php
			echo wp_get_attachment_image(
				$image_id,
				'full',
				false,
				array(
					'loading'       => 'eager',
					'fetchpriority' => 'high',
					'sizes'         => '100vw',
					'alt'           => '',
				)
			);
			?>
		</div>
	<?php endif; ?>
	<div class="wrap page-head__grid">
		<div class="page-head__text">
			<?php its_breadcrumbs(); ?>
			<h1 class="page-head__title<?php echo $long ? ' page-head__title--long' : ''; ?>"><?php echo esc_html( $title ); ?></h1>
			<?php if ( $lead ) : ?>
				<p class="page-head__lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
			<?php echo $args['aside'] ?? ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<?php if ( $image_id && ! $cover ) : ?>
			<figure class="page-head__photo">
				<?php
				echo wp_get_attachment_image(
					$image_id,
					'its-portrait',
					false,
					array(
						'loading'       => 'eager',
						'fetchpriority' => 'high',
						'sizes'         => '(max-width: 900px) 92vw, 40vw',
					)
				);
				?>
			</figure>
		<?php endif; ?>
	</div>
</header>
