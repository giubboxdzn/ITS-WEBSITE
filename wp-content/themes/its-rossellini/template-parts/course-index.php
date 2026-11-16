<?php
/**
 * Course index for the dark front page: one large title per course.
 * Desktop: the course photo previews on hover/focus (main.js makes it follow the pointer).
 * Mobile: the same list becomes a horizontal, snapping carousel of photo cards.
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
	<nav class="area-filter area-filter--dark" aria-label="<?php esc_attr_e( 'Filtra i corsi per area', 'its-rossellini' ); ?>">
		<ul>
			<li><a href="<?php echo esc_url( $base ); ?>#corsi" data-area=""<?php echo '' === $current ? ' aria-current="true"' : ''; ?>><?php esc_html_e( 'Tutti', 'its-rossellini' ); ?></a></li>
			<?php foreach ( $areas as $term ) : ?>
				<li><a href="<?php echo esc_url( add_query_arg( 'area', $term->slug, $base ) ); ?>#corsi" data-area="<?php echo esc_attr( $term->slug ); ?>"<?php echo $current === $term->slug ? ' aria-current="true"' : ''; ?>><?php echo esc_html( $term->name ); ?></a></li>
			<?php endforeach; ?>
		</ul>
	</nav>
<?php endif; ?>

<ol class="course-index" data-wall data-course-index>
	<?php
	while ( $courses->have_posts() ) :
		$courses->the_post();
		$area   = its_area( get_the_ID() );
		$hidden = $current && ( ! $area || $area->slug !== $current );
		?>
		<li class="course-index__item grid__item"<?php echo $hidden ? ' hidden' : ''; ?> data-area="<?php echo esc_attr( $area ? $area->slug : '' ); ?>">
			<a class="course-index__link" href="<?php the_permalink(); ?>">
				<span class="course-index__media">
					<?php
					if ( has_post_thumbnail() ) {
						the_post_thumbnail(
							'its-portrait',
							array(
								'loading' => 'lazy',
								'sizes'   => '(max-width: 760px) 78vw, 360px',
								'alt'     => '',
							)
						);
					}
					?>
				</span>
				<span class="course-index__title"><?php the_title(); ?></span>
				<span class="course-index__meta">
					<?php if ( $area ) : ?>
						<span class="course-index__area"><?php echo esc_html( $area->name ); ?></span>
					<?php endif; ?>
					<?php if ( has_excerpt() ) : ?>
						<span class="course-index__line"><?php echo esc_html( get_the_excerpt() ); ?></span>
					<?php endif; ?>
				</span>
				<span class="course-index__arrow" aria-hidden="true"><?php echo its_icon( 'arrow-up' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			</a>
		</li>
		<?php
	endwhile;
	wp_reset_postdata();
	?>
</ol>
<div class="course-preview" aria-hidden="true" data-course-preview><img alt="" src="data:image/gif;base64,R0lGODlhAQABAAAAACw=" width="360" height="450"></div>
<p class="screen-reader-text" role="status" data-wall-status></p>
