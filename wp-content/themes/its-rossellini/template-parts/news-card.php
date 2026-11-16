<?php
/**
 * News card: rounded photo, date and category, title.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

$cats = get_the_category();
?>
<article class="news-card">
	<a class="news-card__link" href="<?php the_permalink(); ?>">
		<div class="news-card__media">
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail(
					'its-card',
					array(
						'sizes' => '(max-width: 760px) 92vw, 420px',
						'alt'   => '',
					)
				);
			}
			?>
		</div>
		<div class="news-card__body">
			<h3 class="news-card__title"><?php the_title(); ?></h3>
			<p class="news-card__meta">
				<time datetime="<?php echo esc_attr( get_the_date( 'Y-m-d' ) ); ?>"><?php echo esc_html( get_the_date( 'j M Y' ) ); ?></time>
				<?php if ( $cats ) : ?>
					<span class="chip"><?php echo esc_html( $cats[0]->name ); ?></span>
				<?php endif; ?>
			</p>
		</div>
		<span class="news-card__arrow" aria-hidden="true"><?php echo its_icon( 'arrow-up' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
	</a>
</article>
