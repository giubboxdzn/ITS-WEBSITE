<?php
/**
 * Interior page head on deep blue.
 *
 * Args: 'title' (string, drawn with its closing phrase emphasised), 'title_html' (escaped html, to override), 'lead' (string),
 *       'image_id' (int), 'aside' (html string), 'float' (html) a card floating over the photo.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

$title    = $args['title'] ?? '';
$lead     = $args['lead'] ?? '';
$image_id = (int) ( $args['image_id'] ?? 0 );
$long     = mb_strlen( $title ) > 26;
$classes  = 'page-head' . ( $image_id ? ' page-head--photo' : '' );
?>
<header class="<?php echo esc_attr( $classes ); ?>">
	<div class="wrap page-head__grid">
		<div class="page-head__text">
			<?php its_breadcrumbs(); ?>
			<h1 class="page-head__title<?php echo $long ? ' page-head__title--long' : ''; ?>" data-split="server"><?php echo its_split_words( $args['title_html'] ?? its_display_text( $title ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
			<?php if ( $lead ) : ?>
				<p class="page-head__lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
			<?php echo $args['aside'] ?? ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<?php if ( $image_id ) : ?>
			<figure class="page-head__photo">
				<span class="page-head__frame">
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
				</span>
				<?php
				echo $args['float'] ?? ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</figure>
		<?php endif; ?>
	</div>
</header>
