<?php
/**
 * Default page template. Used for the Privacy Policy and other simple pages.
 *
 * @package Alpha_Solar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="content" class="default-page">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class( 'default-page-article container' ); ?>>
			<h1 class="default-page-title"><?php the_title(); ?></h1>
			<div class="default-page-content entry-content">
				<?php the_content(); ?>
			</div>
		</article>
	<?php endwhile; ?>
</main>
<?php
get_footer();
