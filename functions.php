<?php
/**
 * Alpha Solar theme bootstrap.
 *
 * @package Alpha_Solar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AST_VERSION', '1.0.11' );
define( 'AST_DIR', get_template_directory() );
define( 'AST_URI', get_template_directory_uri() );

require AST_DIR . '/inc/setup.php';
require AST_DIR . '/inc/enqueue.php';
require AST_DIR . '/inc/helpers.php';
require AST_DIR . '/inc/acf-fields.php';
require AST_DIR . '/inc/cf7.php';
