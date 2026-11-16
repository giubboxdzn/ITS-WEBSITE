<?php
/**
 * The sections of an interior page (see inc/sections.php), alternating the dark canvas and the
 * light panels as the home page does: text and figures on the dark, card grids on the light.
 *
 * Args: 'sections' (array from its_page_sections()).
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

$sections = $args['sections'] ?? array();
$light    = 'light' === ( $args['theme'] ?? '' );

// Surfaces: card grids go on a light panel, never two panels in a row; course cards always do.
$surfaces = array();
foreach ( $sections as $n => $section ) {
	$wants = 'text' === $section['kind'] ? 'dark' : 'panel';
	if ( 'panel' === $wants && 'panel' === ( $surfaces[ $n - 1 ] ?? '' ) ) {
		if ( 'courses' === $section['kind'] ) {
			$surfaces[ $n - 1 ] = 'dark';
		} else {
			$wants = 'dark';
		}
	}
	$surfaces[ $n ] = $wants;
}
// On a white page: text on the white, card grids on a pale panel, never two panels in a row.
if ( $light ) {
	foreach ( $sections as $n => $section ) {
		$surfaces[ $n ] = 'text' !== $section['kind'] && 'tint' !== ( $surfaces[ $n - 1 ] ?? '' ) ? 'tint' : 'plain';
	}
}

foreach ( $sections as $n => $section ) :
	$surface = $surfaces[ $n ];
	$panel   = in_array( $surface, array( 'panel', 'tint' ), true );
	$id      = $section['id'] ?: 'sezione-' . ( $n + 1 );
	$body    = $section['body'];
	$title   = $section['title'];
	$heading = $title ? $id . '-title' : '';
	$split   = (bool) $body;
	$classes = array( $panel ? 'panel' : 'section', 'psec', 'psec--' . $section['kind'], 'psec--' . $surface );
	if ( $light ) {
		$classes[] = 'psec--light';
	}
	?>
	<section class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" id="<?php echo esc_attr( $id ); ?>"<?php echo $heading ? ' aria-labelledby="' . esc_attr( $heading ) . '"' : ''; ?>>
		<div class="wrap">
			<?php if ( $title || $body ) : ?>
				<div class="psec__head<?php echo $split ? ' psec__head--split' : ''; ?>">
					<?php if ( $title ) : ?>
						<div class="psec__title">
							<?php if ( $section['eyebrow'] ) : ?>
								<p class="eyebrow"><?php echo esc_html( $section['eyebrow'] ); ?></p>
							<?php endif; ?>
							<h2 id="<?php echo esc_attr( $heading ); ?>" class="display display--m<?php echo $panel || $light ? ' display--dark' : ''; ?>"><?php echo its_display_text( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
						</div>
					<?php endif; ?>
					<?php if ( $body ) : ?>
						<div class="psec__body">
							<?php
							foreach ( $body as $block ) {
								if ( is_array( $block ) ) {
									echo '<ul class="checks">';
									foreach ( $block['list'] as $item ) {
										echo '<li><span class="checks__icon" aria-hidden="true">' . its_icon( 'check' ) . '</span><span>' . wp_kses_post( $item ) . '</span></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									}
									echo '</ul>';
								} else {
									echo wp_kses_post( $block );
								}
							}
							?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $section['facts'] ) : ?>
				<div class="figures figures--<?php echo esc_attr( (string) min( 4, count( $section['facts'] ) ) ); ?>">
					<?php foreach ( $section['facts'] as $f => [ $value, $label ] ) : ?>
						<div class="tile tile--fact<?php echo count( $section['facts'] ) - 1 === $f ? ' tile--accent' : ''; ?>">
							<p class="tile__num"><?php echo esc_html( $value ); ?></p>
							<p class="tile__note"><?php echo esc_html( $label ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( 'courses' === $section['kind'] ) : ?>
				<div class="grid">
					<?php
					foreach ( $section['courses'] as $course_id ) {
						$GLOBALS['post'] = get_post( $course_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						setup_postdata( $GLOBALS['post'] );
						echo '<div class="grid__item">';
						get_template_part( 'template-parts/course-card' );
						echo '</div>';
					}
					wp_reset_postdata();
					?>
				</div>
			<?php elseif ( 'steps' === $section['kind'] ) : ?>
				<ol class="psteps psteps--<?php echo esc_attr( (string) count( $section['cards'] ) ); ?>">
					<?php foreach ( $section['cards'] as $card ) : ?>
						<li class="pstep">
							<?php if ( $card['label'] ) : ?>
								<p class="pstep__label"><?php echo esc_html( $card['label'] ); ?></p>
							<?php endif; ?>
							<h3 class="pstep__title">
								<?php if ( $card['href'] ) : ?>
									<a href="<?php echo esc_url( $card['href'] ); ?>"><?php echo esc_html( $card['title'] ); ?></a>
								<?php else : ?>
									<?php echo esc_html( $card['title'] ); ?>
								<?php endif; ?>
							</h3>
							<?php foreach ( $card['text'] as $text ) : ?>
								<p><?php echo wp_kses_post( $text ); ?></p>
							<?php endforeach; ?>
						</li>
					<?php endforeach; ?>
				</ol>
			<?php elseif ( 'cards' === $section['kind'] ) : ?>
				<?php $count = count( $section['cards'] ); ?>
				<div class="pcards pcards--<?php echo esc_attr( (string) ( $count > 4 && 0 === $count % 3 ? 3 : min( 4, $count ) ) ); ?>">
					<?php foreach ( $section['cards'] as $card ) : ?>
						<article class="pcard<?php echo $card['href'] ? ' pcard--link' : ''; ?>">
							<?php if ( $card['label'] ) : ?>
								<p class="pcard__label"><?php echo esc_html( $card['label'] ); ?></p>
							<?php endif; ?>
							<h3 class="pcard__title">
								<?php if ( $card['href'] ) : ?>
									<a class="pcard__link" href="<?php echo esc_url( $card['href'] ); ?>"><?php echo esc_html( $card['title'] ); ?></a>
								<?php else : ?>
									<?php echo esc_html( $card['title'] ); ?>
								<?php endif; ?>
							</h3>
							<?php foreach ( $card['text'] as $text ) : ?>
								<p class="pcard__text"><?php echo wp_kses( $text, array( 'strong' => array(), 'em' => array(), 'br' => array() ) ); ?></p>
							<?php endforeach; ?>
							<?php if ( $card['href'] ) : ?>
								<span class="pcard__icon" aria-hidden="true"><?php echo its_icon( its_link_icon( $card['href'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
endforeach;
