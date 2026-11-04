<?php
/**
 * Theme supports, menus, and image sizes.
 *
 * @package Alpha_Solar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', 'ast_setup' );

/**
 * Register theme supports and menus.
 */
function ast_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array( 'search-form', 'gallery', 'caption', 'style', 'script', 'comment-list', 'comment-form' )
	);
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 54,
			'width'       => 170,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Header CTA', 'alpha-solar' ),
			'footer'  => __( 'Footer links', 'alpha-solar' ),
		)
	);

	add_image_size( 'ast-band', 1559, 435, true );
	add_image_size( 'ast-band-2x', 3118, 870, true );
	add_image_size( 'ast-feature', 771, 533, false );
	add_image_size( 'ast-slide', 1372, 435, true );
	add_image_size( 'ast-hero', 1920, 1234, false );
}

add_filter( 'nav_menu_link_attributes', 'ast_primary_menu_link_class', 10, 3 );

/**
 * Style the header menu item as the yellow pill.
 *
 * @param array<string, string> $atts  Link attributes.
 * @param WP_Post               $item  Menu item.
 * @param stdClass              $args  Menu args.
 * @return array<string, string>
 */
function ast_primary_menu_link_class( $atts, $item, $args ) {
	if ( isset( $args->theme_location ) && 'primary' === $args->theme_location ) {
		$atts['class'] = trim( ( $atts['class'] ?? '' ) . ' btn btn--cta-sm' );
	}
	return $atts;
}

add_filter( 'nav_menu_css_class', 'ast_primary_menu_item_class', 10, 3 );

/**
 * Drop the default list styling hook by marking header items.
 *
 * @param string[] $classes Classes.
 * @param WP_Post  $item    Menu item.
 * @param stdClass $args    Menu args.
 * @return string[]
 */
function ast_primary_menu_item_class( $classes, $item, $args ) {
	if ( isset( $args->theme_location ) && 'primary' === $args->theme_location ) {
		$classes[] = 'header-cta-item';
	}
	return $classes;
}

/**
 * Hard-coded header CTA when no menu is assigned.
 */
function ast_primary_menu_fallback() {
	$label = function_exists( 'ast_field' ) ? ast_field( 'header_cta_label' ) : __( 'Contact Us', 'alpha-solar' );
	$url   = function_exists( 'ast_field' ) ? ast_field( 'header_cta_url' ) : '#contact';
	if ( '' === $url ) {
		$url = '#contact';
	}
	if ( '#' === substr( $url, 0, 1 ) ) {
		$url = home_url( '/' . $url );
	}

	echo '<a class="btn btn--cta-sm" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
}

add_action( 'admin_init', 'ast_hide_front_page_editor' );
add_filter( 'use_block_editor_for_post', 'ast_disable_front_page_block_editor', 100, 2 );

/**
 * Drop the content editor while the Home page edit screen is loading.
 * Doing this on admin_init is early enough for WordPress to skip Gutenberg.
 */
function ast_hide_front_page_editor() {
	if ( ! ast_is_front_page_edit_request() ) {
		return;
	}
	remove_post_type_support( 'page', 'editor' );
}

/**
 * Keep the static front page on the classic editor so the ACF fields are on screen.
 *
 * @param bool    $use  Whether the block editor should load.
 * @param WP_Post $post Post being edited.
 * @return bool
 */
function ast_disable_front_page_block_editor( $use, $post ) {
	$front = (int) get_option( 'page_on_front' );
	if ( $post instanceof WP_Post && $front && (int) $post->ID === $front ) {
		return false;
	}
	return $use;
}

/**
 * True when this admin request is editing the static front page.
 */
function ast_is_front_page_edit_request() {
	$front = (int) get_option( 'page_on_front' );
	if ( ! $front ) {
		return false;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0;
	return $post_id === $front;
}

add_action( 'wp_head', 'ast_seo_fallback', 1 );

/**
 * Minimal meta description and Organization schema when no SEO plugin is active.
 */
function ast_seo_fallback() {
	if ( ! is_front_page() ) {
		return;
	}
	if ( defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) ) {
		return;
	}

	$description = 'ArchiSolar brings architectural design and solar energy together. Alpha Solar Technologies is preparing its building-integrated photovoltaic technology for commercial licensing.';
	echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";

	$logo = AST_URI . '/assets/img/logo-ast.svg';
	$data = array(
		'@context' => 'https://schema.org',
		'@type'    => 'Organization',
		'name'     => 'Alpha Solar Technologies Limited',
		'url'      => home_url( '/' ),
		'logo'     => $logo,
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}

add_action( 'wp_enqueue_scripts', 'ast_trim_front_page_assets', 100 );

/**
 * Drop block and emoji assets the front page does not use.
 */
function ast_trim_front_page_assets() {
	if ( ! is_front_page() ) {
		return;
	}
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'classic-theme-styles' );
	wp_dequeue_style( 'global-styles' );
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
}
