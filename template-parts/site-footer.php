<?php
/**
 * Footer bar.
 *
 * @package Alpha_Solar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<footer class="site-footer">
	<div class="container site-footer-bar">
		<p<?php echo ast_attr_html( 'footer_text', 'site-footer-text' ); ?>><?php echo ast_inline_html( 'footer_text' ); ?></p>
		<?php if ( has_nav_menu( 'footer' ) ) : ?>
			<nav class="site-footer-nav" aria-label="<?php esc_attr_e( 'Footer', 'alpha-solar' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'depth'          => 1,
						'container'      => false,
						'menu_class'     => 'footer-links',
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
		<?php endif; ?>
	</div>
</footer>
