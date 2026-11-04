<?php
/**
 * Hero.
 *
 * @package Alpha_Solar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="hero" aria-labelledby="hero_heading">
	<div class="hero-media" aria-hidden="true">
		<?php
		echo ast_picture(
			array(
				'field'         => 'hero_bg_image',
				'size'          => 'ast-hero',
				'file'          => 'hero-bg-photo-1920x1234.png',
				'width'         => 1920,
				'height'        => 1234,
				'class'         => 'hero-bg',
				'loading'       => 'eager',
				'fetchpriority' => 'high',
				'decorative'    => true,
			)
		);
		?>
		<div class="hero-glow"></div>
		<div class="hex hex--hero-a"></div>
		<div class="hex hex--hero-b"></div>
		<?php
		echo ast_picture(
			array(
				'field'         => 'hero_fg_image',
				'size'          => 'ast-hero',
				'file'          => 'hero-building-foreground-1920x1234.png',
				'width'         => 1920,
				'height'        => 1234,
				'class'         => 'hero-fg',
				'loading'       => 'eager',
				'fetchpriority' => 'high',
				'decorative'    => true,
			)
		);
		?>
		<div class="hero-scrim"></div>
	</div>

	<div class="container hero-content">
		<p class="hero-eyebrow" data-reveal data-reveal-on="load" style="--i:0"><?php echo ast_inline_html( 'hero_eyebrow' ); ?></p>
		<h1 id="hero_heading"><?php echo ast_highlight( 'hero_heading' ); ?></h1>
		<div class="hero-body" data-reveal data-reveal-on="load" style="--i:1"><?php echo ast_rich_text( 'hero_body' ); ?></div>
		<a class="btn-round hero-scroll" href="#announcement" aria-label="<?php esc_attr_e( 'Scroll to next section', 'alpha-solar' ); ?>" data-reveal data-reveal-on="load" style="--i:2">
			<?php echo ast_chevron_svg(); ?>
		</a>
	</div>
</section>
