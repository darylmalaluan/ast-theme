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
<section class="licensing" aria-labelledby="licensing-heading">
	<div class="licensing__texture" aria-hidden="true"></div>
	<h2 id="licensing-heading" class="h2" data-reveal style="--i:0"><?php echo ast_highlight( 'licensing_heading' ); ?></h2>

	<div class="carousel" data-carousel data-count="<?php echo esc_attr( (string) ( $ids ? count( $ids ) : 1 ) ); ?>" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Licensing imagery', 'alpha-solar' ); ?>" data-reveal style="--i:1">
		<?php if ( $count > 1 ) : ?>
			<button class="btn-round carousel__btn carousel__btn--prev" type="button" aria-label="<?php esc_attr_e( 'Previous slide', 'alpha-solar' ); ?>" aria-controls="licensing-track">
				<?php echo ast_chevron_svg(); ?>
			</button>
		<?php endif; ?>

		<div class="carousel__viewport" id="licensing-track" tabindex="0">
			<div class="carousel__track">
				<?php if ( $ids ) : ?>
					<?php foreach ( $ids as $index => $id ) : ?>
						<figure class="carousel__slide" role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( sprintf( '%1$d of %2$d', $index + 1, count( $ids ) ) ); ?>">
							<?php
							echo wp_get_attachment_image(
								$id,
								'ast-slide',
								false,
								array(
									'class'    => 'carousel__img',
									'loading'  => 'lazy',
									'decoding' => 'async',
								)
							);
							?>
						</figure>
					<?php endforeach; ?>
				<?php else : ?>
					<figure class="carousel__slide" role="group" aria-roledescription="slide" aria-label="<?php esc_attr_e( '1 of 1', 'alpha-solar' ); ?>">
						<?php
						echo ast_picture(
							array(
								'file'    => 'licensing-slide-1-1372x435.png',
								'width'   => 1372,
								'height'  => 435,
								'alt'     => __( 'Towers against a bright sky', 'alpha-solar' ),
								'class'   => 'carousel__img',
								'loading' => 'lazy',
							)
						);
						?>
					</figure>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $count > 1 ) : ?>
			<button class="btn-round carousel__btn carousel__btn--next" type="button" aria-label="<?php esc_attr_e( 'Next slide', 'alpha-solar' ); ?>" aria-controls="licensing-track">
				<?php echo ast_chevron_svg(); ?>
			</button>
		<?php endif; ?>
	</div>

	<div class="licensing__foot">
		<p class="licensing__body" data-reveal style="--i:2"><?php echo esc_html( ast_field( 'licensing_body' ) ); ?></p>
		<div data-reveal style="--i:3"><?php echo ast_button( $button, 'dark' ); ?></div>
	</div>
</section>
