<?php
/**
 * FAQ accordion: native <details>, first item open. Works without JavaScript.
 *
 * Args: 'items' (array of q/a), 'heading' (h3|h4).
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

$items   = $args['items'] ?? array();
$heading = $args['heading'] ?? 'h3';
$heading = in_array( $heading, array( 'h3', 'h4' ), true ) ? $heading : 'h3';
if ( ! $items ) {
	return;
}
?>
<div class="faq">
	<?php foreach ( $items as $i => $item ) : ?>
		<details class="faq__item"<?php echo 0 === $i ? ' open' : ''; ?>>
			<summary>
				<<?php echo tag_escape( $heading ); ?> class="faq__q"><?php echo esc_html( $item['q'] ); ?></<?php echo tag_escape( $heading ); ?>>
				<?php echo its_icon( 'chevron', 'faq__chevron' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</summary>
			<div class="faq__a"><?php echo wp_kses_post( $item['a'] ); ?></div>
		</details>
	<?php endforeach; ?>
</div>
