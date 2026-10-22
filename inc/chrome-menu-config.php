<?php
/**
 * Canonical WordPress menu names and locations for Tasche Piene chrome.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Menu location slug => config.
 */
function tp_child_nav_menu_config(): array {
	return [
		'tp_main_nav'     => [
			'name'        => 'Tasche Piene — Header',
			'location'    => __( 'Header — barra menu in alto', 'tasche-piene-divi-child' ),
			'admin_label' => __( 'Menu header', 'tasche-piene-divi-child' ),
			'help'        => __( 'Home, I tramezzini, La nostra idea, Zena, Dove siamo.', 'tasche-piene-divi-child' ),
		],
		'tp_footer_left'  => [
			'name'        => 'Tasche Piene — Footer sinistra',
			'location'    => __( 'Footer — colonna sinistra', 'tasche-piene-divi-child' ),
			'admin_label' => __( 'Menu footer sinistra', 'tasche-piene-divi-child' ),
			'help'        => __( 'MENÙ, DOVE SIAMO, SU DI NOI.', 'tasche-piene-divi-child' ),
		],
		'tp_footer_right' => [
			'name'        => 'Tasche Piene — Footer destra',
			'location'    => __( 'Footer — colonna destra', 'tasche-piene-divi-child' ),
			'admin_label' => __( 'Menu footer destra', 'tasche-piene-divi-child' ),
			'help'        => __( 'TORNA SÙ, FACEBOOK, INSTAGRAM.', 'tasche-piene-divi-child' ),
		],
	];
}

/**
 * Legacy menu names replaced during cleanup migrations.
 */
function tp_child_nav_menu_legacy_names(): array {
	return [
		'Tasche Piene Main Nav',
		'Tasche Piene Footer Left',
		'Tasche Piene Footer Right',
		'Tasche Piene Main',
	];
}

/**
 * Resolve a menu term by canonical or legacy name for a location.
 */
function tp_child_get_nav_menu_by_config( string $location ) {
	$config = tp_child_nav_menu_config();

	if ( empty( $config[ $location ]['name'] ) ) {
		return false;
	}

	$menu = wp_get_nav_menu_object( $config[ $location ]['name'] );

	if ( $menu ) {
		return $menu;
	}

	$legacy_map = [
		'tp_main_nav'     => 'Tasche Piene Main Nav',
		'tp_footer_left'  => 'Tasche Piene Footer Left',
		'tp_footer_right' => 'Tasche Piene Footer Right',
	];

	if ( empty( $legacy_map[ $location ] ) ) {
		return false;
	}

	return wp_get_nav_menu_object( $legacy_map[ $location ] );
}

/**
 * Admin URL to edit a theme menu location.
 */
function tp_child_nav_menu_edit_url( string $location ): string {
	$config = tp_child_nav_menu_config();
	$menu   = tp_child_get_nav_menu_by_config( $location );

	if ( ! $menu || is_wp_error( $menu ) ) {
		return admin_url( 'nav-menus.php' );
	}

	return admin_url( 'nav-menus.php?action=edit&menu=' . (int) $menu->term_id );
}
