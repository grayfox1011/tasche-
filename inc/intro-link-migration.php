<?php
/**
 * The title of the introduction ("Tasche piene di qualcosa di buono") came with a module link to "#ma", an
 * anchor that is on no page: the title showed the hand pointer and changed the address for nothing. The
 * content the theme creates no longer has it; this removes it once from the home, and only that link.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The home content without the "#ma" link of the introduction title (unchanged when it is not there exactly once).
 */
function tp_child_intro_title_link_transform( string $content ): string {
	$search = '"tp-intro-title"}}},"link":{"desktop":{"value":{"url":"#ma"}}}';

	return 1 === substr_count( $content, $search ) ? str_replace( $search, '"tp-intro-title"}}}', $content ) : $content;
}

function tp_child_migrate_intro_title_link(): void {
	tp_child_menu_size_s_migrate_home( 'tp_child_intro_title_link_v1', 'tp_child_intro_title_link_transform' );
}

add_action( 'init', 'tp_child_migrate_intro_title_link', 31 );
