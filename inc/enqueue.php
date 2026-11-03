<?php
/**
 * Styles, scripts, and font preloads.
 *
 * @package Alpha_Solar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', 'ast_enqueue_assets' );

/**
 * Enqueue the theme stylesheet and deferred script.
 */
function ast_enqueue_assets() {
	wp_enqueue_style(
		'alpha-solar',
		AST_URI . '/assets/css/main.css',
		array(),
		AST_VERSION
	);

	wp_enqueue_script(
		'alpha-solar',
		AST_URI . '/assets/js/main.js',
		array(),
		AST_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}

add_action( 'wp_head', 'ast_preload_fonts', 2 );

/**
 * Preload the two weights used in the hero and section headings.
 */
function ast_preload_fonts() {
	foreach ( array( 'montserrat-700.woff2', 'montserrat-800.woff2' ) as $file ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( AST_URI . '/assets/fonts/' . $file )
		);
	}
}
