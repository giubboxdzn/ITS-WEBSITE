<?php
/**
 * News index, archives and search results.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( is_search() ) {
	/* translators: %s: search terms */
	$title = sprintf( __( 'Risultati per “%s”', 'its-rossellini' ), get_search_query() );
	$lead  = '';
} elseif ( is_home() && get_option( 'page_for_posts' ) ) {
	$title = get_the_title( (int) get_option( 'page_for_posts' ) );
	$lead  = __( 'Avvisi, graduatorie, progetti e risultati degli studenti.', 'its-rossellini' );
} elseif ( is_archive() ) {
	$title = wp_strip_all_tags( get_the_archive_title() );
	$lead  = wp_strip_all_tags( get_the_archive_description() );
} else {
	$title = __( 'News', 'its-rossellini' );
	$lead  = '';
}

$aside = is_search() ? get_search_form( array( 'echo' => false ) ) : '';
get_template_part(
	'template-parts/page-head',
	null,
	array(
		'title' => $title,
		'lead'  => $lead,
		'aside' => $aside,
	)
);
?>
<div class="wrap page-body page-body--wide">
	<?php if ( have_posts() ) : ?>
		<?php if ( is_search() ) : ?>
			<div class="rows">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part(
						'template-parts/dated-item',
						null,
						array(
							'heading'      => 'h2',
							'show_excerpt' => true,
						)
					);
				endwhile;
				?>
			</div>
		<?php else : ?>
			<?php // News and category archives: the home's photo cards. ?>
			<div class="news-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/news-card', null, array( 'heading' => 'h2' ) );
				endwhile;
				?>
			</div>
		<?php endif; ?>
		<?php its_pagination(); ?>
	<?php else : ?>
		<div class="empty">
			<p><?php esc_html_e( 'Nessun contenuto trovato. Prova con un’altra parola o torna alla home.', 'its-rossellini' ); ?></p>
			<?php get_search_form(); ?>
		</div>
	<?php endif; ?>
</div>
<?php
get_template_part( 'template-parts/cta-card' );
get_footer();
