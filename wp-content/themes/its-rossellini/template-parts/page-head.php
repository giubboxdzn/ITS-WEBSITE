<?php
/**
 * Interior page head on deep blue.
 *
 * Args: 'title' (string), 'lead' (string), 'image_id' (int), 'aside' (html string).
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

$title    = $args['title'] ?? '';
$lead     = $args['lead'] ?? '';
$image_id = (int) ( $args['image_id'] ?? 0 );
$long     = mb_strlen( $title ) > 26;
?>
<header class="page-head<?php echo $image_id ? ' page-head--photo' : ''; ?>">
	<div class="wrap page-head__grid">
		<div class="page-head__text">
			<?php its_breadcrumbs(); ?>
			<h1 class="page-head__title<?php echo $long ? ' page-head__title--long' : ''; ?>"><?php echo esc_html( $title ); ?></h1>
			<?php if ( $lead ) : ?>
				<p class="page-head__lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
			<?php echo $args['aside'] ?? ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<?php if ( $image_id ) : ?>
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
