<?php
/**
 * Course grid with area filter. Filtering works as plain links (?area=) and is
 * enhanced in place by main.js.
 *
 * Args: 'heading' (h2|h3) level of each course title, h2 when the grid sits directly under the page h1.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

$areas   = its_course_areas();
$current = isset( $_GET['area'] ) ? sanitize_title( wp_unslash( $_GET['area'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$courses = its_courses();
$base    = remove_query_arg( 'area' );
?>
<?php if ( count( $areas ) > 1 ) : ?>
	<nav class="area-filter" aria-label="<?php esc_attr_e( 'Filtra i corsi per area', 'its-rossellini' ); ?>">
		<ul>
			<li><a href="<?php echo esc_url( $base ); ?>#corsi" data-area=""<?php echo '' === $current ? ' aria-current="true"' : ''; ?>><?php esc_html_e( 'Tutti', 'its-rossellini' ); ?></a></li>
			<?php foreach ( $areas as $term ) : ?>
				<li><a href="<?php echo esc_url( add_query_arg( 'area', $term->slug, $base ) ); ?>#corsi" data-area="<?php echo esc_attr( $term->slug ); ?>"<?php echo $current === $term->slug ? ' aria-current="true"' : ''; ?>><?php echo esc_html( $term->name ); ?></a></li>
			<?php endforeach; ?>
		</ul>
	</nav>
<?php endif; ?>

<div class="grid" data-wall>
	<?php
	$index = 0;
	while ( $courses->have_posts() ) :
		$courses->the_post();
		$area   = its_area( get_the_ID() );
		$hidden = $current && ( ! $area || $area->slug !== $current );
		?>
		<div class="grid__item"<?php echo $hidden ? ' hidden' : ''; ?> data-area="<?php echo esc_attr( $area ? $area->slug : '' ); ?>">
			<?php
			get_template_part(
				'template-parts/course-card',
				null,
				array(
					'loading' => $index < 4 ? 'eager' : 'lazy',
					'heading' => $args['heading'] ?? 'h3',
				)
			);
			?>
		</div>
		<?php
		++$index;
	endwhile;
	wp_reset_postdata();
	?>
</div>
<p class="screen-reader-text" role="status" data-wall-status></p>
