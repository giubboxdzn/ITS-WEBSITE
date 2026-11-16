<?php
/**
 * Index of child pages, when the current page has any.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

$children = get_pages(
	array(
		'parent'      => get_the_ID(),
		'sort_column' => 'menu_order,post_title',
	)
);
if ( ! $children ) {
	return;
}
?>
<nav class="index-list" aria-labelledby="index-title-<?php the_ID(); ?>">
	<h2 id="index-title-<?php the_ID(); ?>" class="index-list__title"><?php esc_html_e( 'In questa sezione', 'its-rossellini' ); ?></h2>
	<ul>
		<?php foreach ( $children as $child ) : ?>
			<li>
				<a href="<?php echo esc_url( get_permalink( $child ) ); ?>">
					<span><?php echo esc_html( get_the_title( $child ) ); ?></span>
					<?php echo its_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
