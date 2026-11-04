<?php
/**
 * Licensing model and image carousel.
 *
 * @package Alpha_Solar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$button       = ast_link( 'licensing_button', ast_field( 'licensing_label' ), '#contact' );
$button_attrs = ast_content_attrs( 'licensing_button' );
$slides       = ast_licensing_slides();
$count        = max( 1, count( $slides ) );
?>
<section class="licensing" aria-labelledby="<?php echo esc_attr( ast_content_id( 'licensing_heading', 'licensing_heading' ) ); ?>">
	<div class="licensing-texture" aria-hidden="true"></div>
	<h2<?php echo ast_attr_html( 'licensing_heading', 'h2', 'licensing_heading' ); ?> data-reveal style="--i:0"><?php echo ast_highlight( 'licensing_heading' ); ?></h2>

	<div class="carousel" data-carousel data-count="<?php echo esc_attr( (string) ( $slides ? count( $slides ) : 1 ) ); ?>" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Licensing imagery', 'alpha-solar' ); ?>" data-reveal style="--i:1">
		<?php /* === AST-FIXES-01 L1: always show both arrows (Figma 72:361 / 72:358); aria-disabled with a single slide. === */ ?>
		<button class="btn-round carousel-btn carousel-btn--prev" type="button" aria-label="<?php esc_attr_e( 'Previous slide', 'alpha-solar' ); ?>" aria-controls="licensing_track"<?php echo $count > 1 ? '' : ' aria-disabled="true"'; ?>>
			<?php echo ast_chevron_svg(); ?>
		</button>

		<div class="carousel-viewport" id="licensing_track" tabindex="0">
			<div class="carousel-track">
				<?php if ( $slides ) : ?>
					<?php foreach ( $slides as $index => $slide ) : ?>
						<?php $slide_attrs = $slide['field'] ? ast_content_attrs( $slide['field'] ) : array( 'class' => '', 'id' => '' ); ?>
						<figure class="carousel-slide" role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( sprintf( '%1$d of %2$d', $index + 1, count( $slides ) ) ); ?>">
							<?php
							$image_attrs = array(
								'class'    => trim( 'carousel-img ' . $slide_attrs['class'] ),
								'loading'  => 'lazy',
								'decoding' => 'async',
							);
							if ( '' !== $slide_attrs['id'] ) {
								$image_attrs['id'] = $slide_attrs['id'];
							}
							echo wp_get_attachment_image( $slide['id'], 'ast-slide', false, $image_attrs );
							?>
						</figure>
					<?php endforeach; ?>
				<?php else : ?>
					<figure class="carousel-slide" role="group" aria-roledescription="slide" aria-label="<?php esc_attr_e( '1 of 1', 'alpha-solar' ); ?>">
						<?php
						echo ast_picture(
							array(
								'file'    => 'licensing-slide-1-1372x435.png',
								'width'   => 1372,
								'height'  => 435,
								'alt'     => __( 'Towers against a bright sky', 'alpha-solar' ),
								'class'      => 'carousel-img',
								'attr_field' => 'licensing_slide_1',
								'loading' => 'lazy',
							)
						);
						?>
					</figure>
				<?php endif; ?>
			</div>
		</div>

		<button class="btn-round carousel-btn carousel-btn--next" type="button" aria-label="<?php esc_attr_e( 'Next slide', 'alpha-solar' ); ?>" aria-controls="licensing_track"<?php echo $count > 1 ? '' : ' aria-disabled="true"'; ?>>
			<?php echo ast_chevron_svg(); ?>
		</button>
		<?php /* === /AST-FIXES-01 L1 === */ ?>
	</div>

	<div class="licensing-foot">
		<div<?php echo ast_attr_html( 'licensing_body', 'licensing-body' ); ?> data-reveal style="--i:2"><?php echo ast_rich_text( 'licensing_body' ); ?></div>
		<div data-reveal style="--i:3"><?php echo ast_button( $button, 'dark', $button_attrs['class'], $button_attrs['id'] ); ?></div>
	</div>
</section>
