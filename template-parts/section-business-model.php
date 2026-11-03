<?php
/**
 * Business model.
 *
 * @package Alpha_Solar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="business" aria-labelledby="business-heading">
	<div class="business__shade" aria-hidden="true">
		<div class="hex hex--business-a"></div>
		<div class="hex hex--business-b"></div>
		<div class="hex hex--business-c"></div>
	</div>
	<div class="business__grid">
		<div class="business__copy">
			<h2 id="business-heading" class="h2" data-reveal style="--i:0"><?php echo ast_highlight( 'business_heading' ); ?></h2>
			<div class="business__body" data-reveal style="--i:1"><?php echo ast_paragraphs( 'business_body' ); ?></div>
		</div>
		<figure class="business__media" data-reveal style="--i:2">
			<?php
			echo ast_picture(
				array(
					'field'   => 'business_image',
					'size'    => 'ast-feature',
					'file'    => 'business-model-image-771x533.png',
					'width'   => 771,
					'height'  => 533,
					'alt'     => __( 'Glass building façade in teal light', 'alpha-solar' ),
					'class'   => 'business__img',
					'loading' => 'lazy',
				)
			);
			?>
		</figure>
	</div>
</section>
