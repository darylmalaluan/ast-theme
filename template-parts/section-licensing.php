<?php
/**
 * Licensing model and image carousel.
 *
 * @package Alpha_Solar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$button = ast_link( 'licensing_button', ast_field( 'licensing_label' ), '#contact' );
$ids    = ast_slide_ids();
$count  = max( 1, count( $ids ) );
?>
<section class="licensing" aria-labelledby="licensing_heading">
	<div class="licensing-texture" aria-hidden="true"></div>
	<h2 id="licensing_heading" class="h2" data-reveal style="--i:0"><?php echo ast_highlight( 'licensing_heading' ); ?></h2>

	<div class="carousel" data-carousel data-count="<?php echo esc_attr( (string) ( $ids ? count( $ids ) : 1 ) ); ?>" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Licensing imagery', 'alpha-solar' ); ?>" data-reveal style="--i:1">
		<?php /* === AST-FIXES-01 L1: always show both arrows (Figma 72:361 / 72:358); aria-disabled with a single slide. === */ ?>
		<button class="btn-round carousel-btn carousel-btn--prev" type="button" aria-label="<?php esc_attr_e( 'Previous slide', 'alpha-solar' ); ?>" aria-controls="licensing_track"<?php echo $count > 1 ? '' : ' aria-disabled="true"'; ?>>
			<?php echo ast_chevron_svg(); ?>
		</button>

		<div class="carousel-viewport" id="licensing_track" tabindex="0">
			<div class="carousel-track">
				<?php if ( $ids ) : ?>
					<?php foreach ( $ids as $index => $id ) : ?>
						<figure class="carousel-slide" role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( sprintf( '%1$d of %2$d', $index + 1, count( $ids ) ) ); ?>">
							<?php
							echo wp_get_attachment_image(
								$id,
								'ast-slide',
								false,
								array(
									'class'    => 'carousel-img',
									'loading'  => 'lazy',
									'decoding' => 'async',
								)
							);
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
								'class'   => 'carousel-img',
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
		<div class="licensing-body" data-reveal style="--i:2"><?php echo ast_rich_text( 'licensing_body' ); ?></div>
		<div data-reveal style="--i:3"><?php echo ast_button( $button, 'dark' ); ?></div>
	</div>
</section>
