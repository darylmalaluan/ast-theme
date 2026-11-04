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
<section class="business" aria-labelledby="<?php echo esc_attr( ast_content_id( 'business_heading', 'business_heading' ) ); ?>">
	<div class="business-shade" aria-hidden="true">
		<div class="hex hex--business-a"></div>
		<div class="hex hex--business-b"></div>
		<div class="hex hex--business-c"></div>
	</div>
	<div class="business-grid">
		<div class="business-copy">
			<h2<?php echo ast_attr_html( 'business_heading', 'h2', 'business_heading' ); ?> data-reveal style="--i:0"><?php echo ast_highlight( 'business_heading' ); ?></h2>
			<div<?php echo ast_attr_html( 'business_body', 'business-body' ); ?> data-reveal style="--i:1"><?php echo ast_rich_text( 'business_body' ); ?></div>
		</div>
		<figure class="business-media" data-reveal style="--i:2">
			<?php
			echo ast_picture(
				array(
					'field'   => 'business_image',
					'size'    => 'ast-feature',
					'file'    => 'business-model-image-771x533.png',
					'width'   => 771,
					'height'  => 533,
					'alt'     => __( 'Glass building façade in teal light', 'alpha-solar' ),
					'class'      => 'business-img',
					'attr_field' => 'business_image',
					'loading' => 'lazy',
				)
			);
			?>
		</figure>
	</div>
</section>
