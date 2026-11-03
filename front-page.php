<?php
/**
 * Front page. Sections render in the R05 frame order.
 *
 * @package Alpha_Solar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="content">
	<?php
	get_template_part( 'template-parts/section', 'hero' );
	get_template_part( 'template-parts/section', 'announcement' );
	get_template_part( 'template-parts/section', 'business-model' );
	get_template_part( 'template-parts/section', 'licensing' );
	get_template_part( 'template-parts/section', 'contact' );
	?>
</main>
<?php
get_footer();
