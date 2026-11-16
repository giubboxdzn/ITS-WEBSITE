<?php
/**
 * News and upcoming events on a light panel, as on the home page.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

$em = array(
	'em'     => array(),
	'strong' => array(),
);
?>
<?php
$events = its_events_by_date( 0 );
$news   = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
?>
<section class="panel" aria-labelledby="news-title">
	<div class="wrap">
		<div class="panel__head">
			<h2 id="news-title" class="display display--dark"><?php echo wp_kses( __( 'News, eventi, <strong>occasioni.</strong>', 'its-rossellini' ), $em ); ?></h2>
			<a class="btn btn--outline" href="<?php echo esc_url( get_permalink( (int) get_option( 'page_for_posts' ) ) ?: its_page_url( 'news' ) ); ?>"><?php esc_html_e( 'Tutte le news', 'its-rossellini' ); ?><?php echo its_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
		</div>
		<?php if ( $events['upcoming'] ) : ?>
			<div class="rows rows--events">
				<?php
				foreach ( array_slice( $events['upcoming'], 0, 2 ) as $post ) :
					setup_postdata( $GLOBALS['post'] = $post ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					get_template_part( 'template-parts/dated-item' );
				endforeach;
				wp_reset_postdata();
				?>
			</div>
		<?php endif; ?>
		<div class="news-grid">
			<?php
			while ( $news->have_posts() ) :
				$news->the_post();
				get_template_part( 'template-parts/news-card' );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
