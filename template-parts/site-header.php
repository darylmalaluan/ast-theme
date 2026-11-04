<?php
/**
 * Header. Logo left, Contact Us pill right. Not sticky.
 *
 * @package Alpha_Solar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<header class="site-header">
	<div class="container site-header-bar">
		<?php
		$logo_id = 0;
		if ( function_exists( 'get_field' ) ) {
			$logo_id = ast_attachment_id( get_field( 'header_logo', ast_context_id() ) );
		}
		?>
		<?php $logo_attrs = ast_content_attrs( 'header_logo' ); ?>
		<?php if ( $logo_id && ast_attachment_file_exists( $logo_id ) ) : ?>
			<a class="site-header-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Alpha Solar Technologies — home', 'alpha-solar' ); ?>">
				<img src="<?php echo esc_url( wp_get_attachment_image_url( $logo_id, 'full' ) ); ?>" width="170" height="54" alt=""<?php echo '' !== $logo_attrs['class'] ? ' class="' . esc_attr( $logo_attrs['class'] ) . '"' : ''; ?><?php echo '' !== $logo_attrs['id'] ? ' id="' . esc_attr( $logo_attrs['id'] ) . '"' : ''; ?>>
			</a>
		<?php elseif ( has_custom_logo() ) : ?>
			<div class="site-header-logo"><?php the_custom_logo(); ?></div>
		<?php else : ?>
			<a class="site-header-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Alpha Solar Technologies — home', 'alpha-solar' ); ?>">
				<img src="<?php echo esc_url( AST_URI . '/assets/img/logo-ast.svg' ); ?>" width="170" height="54" alt=""<?php echo '' !== $logo_attrs['class'] ? ' class="' . esc_attr( $logo_attrs['class'] ) . '"' : ''; ?><?php echo '' !== $logo_attrs['id'] ? ' id="' . esc_attr( $logo_attrs['id'] ) . '"' : ''; ?>>
			</a>
		<?php endif; ?>

		<nav class="site-header-nav" aria-label="<?php esc_attr_e( 'Header', 'alpha-solar' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'depth'          => 1,
					'container'      => false,
					'menu_class'     => 'header-cta',
					'fallback_cb'    => 'ast_primary_menu_fallback',
				)
			);
			?>
		</nav>
	</div>
</header>
