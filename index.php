<?php
/**
 * Fallback index.
 *
 * @package Alpha_Solar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="content" class="default-page">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class( 'default-page-article' ); ?>>
					<h1 class="default-page-title"><?php the_title(); ?></h1>
					<div class="entry-content"><?php the_content(); ?></div>
				</article>
			<?php endwhile; ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing here yet.', 'alpha-solar' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
