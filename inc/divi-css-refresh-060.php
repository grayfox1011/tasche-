<?php
/**
 * 0.6.0: the theme's own modules (Striscia scorrevole, Onda, Testo animato, the header and footer
 * modules) print their Spacing with !important, as Divi's Text and Heading modules do, so a margin
 * or padding set in Divi wins on every device. Divi keeps the CSS it built for each page in files:
 * once after the update they are rebuilt, so the pages pick the new rules up.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tp_child_refresh_divi_css_060(): void {
	$flag = 'tp_child_divi_css_060_v1';

	if ( get_option( $flag ) ) {
		return;
	}

	tp_child_clear_divi_static_css();
	update_option( $flag, TP_CHILD_VERSION, true );
}

add_action( 'init', 'tp_child_refresh_divi_css_060', 44 );
