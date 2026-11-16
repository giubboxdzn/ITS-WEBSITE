<?php
/**
 * Search form.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

$uid = wp_unique_id( 'search-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $uid ); ?>"><?php esc_html_e( 'Cerca nel sito', 'its-rossellini' ); ?></label>
	<input id="<?php echo esc_attr( $uid ); ?>" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" autocomplete="off" placeholder="<?php esc_attr_e( 'Cerca corsi, bandi, news…', 'its-rossellini' ); ?>">
	<button type="submit" class="button button--yellow"><?php echo its_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Cerca', 'its-rossellini' ); ?></span></button>
</form>
