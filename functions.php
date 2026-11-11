<?php
/**
 * Tasche Piene, child theme of Divi.
 *
 * The site is made of Divi modules only: what one sees is set in Divi's panels and stored in the
 * pages. The theme brings the fonts, a few effects (assets/css/tp.css, assets/js/tp.js) and
 * creates the site (home, header, footer, legal pages) the first time: inc/setup-site.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TP_VERSION', '1.0.0' );
define( 'TP_PATH', trailingslashit( get_stylesheet_directory() ) );
define( 'TP_URL', trailingslashit( get_stylesheet_directory_uri() ) );

require_once TP_PATH . 'inc/fonts.php';
require_once TP_PATH . 'inc/svg-upload.php';
require_once TP_PATH . 'inc/setup-site.php';

/**
 * A file's version for the browser cache: it changes when the file does.
 */
function tp_asset_version( string $relative_path ): string {
	$file = TP_PATH . ltrim( $relative_path, '/' );

	return file_exists( $file ) ? TP_VERSION . '.' . filemtime( $file ) : TP_VERSION;
}

/**
 * The Visual Builder draws the page in a frame: the effects stay still there, so one can write in peace.
 */
function tp_is_visual_builder(): bool {
	return function_exists( 'et_core_is_fb_enabled' ) && et_core_is_fb_enabled();
}

/**
 * Fonts and effects, after Divi's own styles. GSAP only moves the titles that have the class tp-lettere.
 */
function tp_enqueue_assets(): void {
	wp_enqueue_style( 'tp', TP_URL . 'assets/css/tp.css', [], tp_asset_version( 'assets/css/tp.css' ) );

	if ( tp_is_visual_builder() ) {
		return;
	}

	wp_register_script( 'tp-gsap', TP_URL . 'assets/vendor/gsap.min.js', [], '3.14.2', true );
	wp_register_script( 'tp-split-text', TP_URL . 'assets/vendor/SplitText.min.js', [ 'tp-gsap' ], '3.14.2', true );
	wp_enqueue_script( 'tp', TP_URL . 'assets/js/tp.js', [ 'tp-gsap', 'tp-split-text' ], tp_asset_version( 'assets/js/tp.js' ), true );
}
add_action( 'wp_enqueue_scripts', 'tp_enqueue_assets', 30 );

/**
 * Tells the page, before it is drawn, that the effects will run: what waits for them (a title that
 * comes in letter by letter) is hidden from the first moment and not after a flash. Not in the
 * Visual Builder, nor for who asked the device for less motion.
 */
function tp_print_motion_flag(): void {
	if ( tp_is_visual_builder() ) {
		return;
	}

	echo "<script>(function(d,w){if(!(w.matchMedia&&w.matchMedia('(prefers-reduced-motion: reduce)').matches)){d.documentElement.classList.add('tp-anima');}})(document,window);</script>\n";
}
add_action( 'wp_head', 'tp_print_motion_flag', 1 );
