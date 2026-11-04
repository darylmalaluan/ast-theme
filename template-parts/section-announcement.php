<?php
/**
 * Announcement, including the image band that overlaps the hero.
 *
 * @package Alpha_Solar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$button = ast_link( 'announce_button', ast_field( 'announce_label' ), '#contact' );
?>
<section class="announcement" id="announcement" aria-labelledby="announcement_heading">
	<div class="announcement-shade" aria-hidden="true">
		<div class="hex hex--announce-a"></div>
		<div class="hex hex--announce-b"></div>
	</div>

	<figure class="band">
		<?php
		echo ast_picture(
			array(
				'field'   => 'hero_band_image',
				'size'    => 'ast-band',
				'file'    => 'hero-image-band-1559x435.png',
				'width'   => 1559,
				'height'  => 435,
				'alt'     => __( 'Empty floor and a modern architectural passageway', 'alpha-solar' ),
				'class'   => 'band-img',
				'loading' => 'lazy',
			)
		);
		?>
		<span class="band-line band-line--top" aria-hidden="true"></span>
	</figure>

	<div class="announcement-content">
		<h2 id="announcement_heading" class="h2" data-reveal style="--i:0"><?php echo ast_highlight( 'announce_heading' ); ?></h2>
		<div data-reveal style="--i:1"><?php echo ast_button( $button, 'primary' ); ?></div>
	</div>
</section>
