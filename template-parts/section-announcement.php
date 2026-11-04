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
$button_attrs = ast_content_attrs( 'announce_button' );
?>
<section class="announcement" id="announcement" aria-labelledby="<?php echo esc_attr( ast_content_id( 'announce_heading', 'announcement_heading' ) ); ?>">
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
				'class'      => 'band-img',
				'attr_field' => 'hero_band_image',
				'loading' => 'lazy',
			)
		);
		?>
		<span class="band-line band-line--top" aria-hidden="true"></span>
	</figure>

	<div class="announcement-content">
		<h2<?php echo ast_attr_html( 'announce_heading', 'h2', 'announcement_heading' ); ?> data-reveal style="--i:0"><?php echo ast_highlight( 'announce_heading' ); ?></h2>
		<div data-reveal style="--i:1"><?php echo ast_button( $button, 'primary', $button_attrs['class'], $button_attrs['id'] ); ?></div>
	</div>
</section>
