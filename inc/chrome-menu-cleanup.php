<?php
/**
 * Rename theme menus, remove orphans, and simplify menu location assignments.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rename a nav menu term if needed.
 */
function tp_child_chrome_rename_nav_menu( string $from_name, string $to_name ): void {
	$menu = wp_get_nav_menu_object( $from_name );

	if ( ! $menu || is_wp_error( $menu ) ) {
		return;
	}

	if ( $from_name === $to_name ) {
		return;
	}

	$existing = wp_get_nav_menu_object( $to_name );

	if ( $existing && ! is_wp_error( $existing ) && (int) $existing->term_id !== (int) $menu->term_id ) {
		return;
	}

	wp_update_term(
		(int) $menu->term_id,
		'nav_menu',
		[
			'name' => $to_name,
			'slug' => sanitize_title( $to_name ),
		]
	);
}

/**
 * Delete orphan menus that are not wired to the child theme.
 */
function tp_child_chrome_delete_orphan_nav_menus(): void {
	$canonical_names = [];

	foreach ( tp_child_nav_menu_config() as $config ) {
		$canonical_names[] = $config['name'];
	}

	$menus = wp_get_nav_menus();

	if ( ! is_array( $menus ) ) {
		return;
	}

	foreach ( $menus as $menu ) {
		if ( ! $menu instanceof WP_Term ) {
			continue;
		}

		if ( in_array( $menu->name, $canonical_names, true ) ) {
			continue;
		}

		if ( ! in_array( $menu->name, tp_child_nav_menu_legacy_names(), true ) ) {
			continue;
		}

		$locations = array_values( array_filter( (array) get_nav_menu_locations() ) );

		if ( in_array( (int) $menu->term_id, $locations, true ) ) {
			continue;
		}

		wp_delete_nav_menu( (int) $menu->term_id );
	}
}

/**
 * Keep only Tasche Piene menu locations assigned.
 */
function tp_child_chrome_normalize_nav_menu_locations(): void {
	$config    = tp_child_nav_menu_config();
	$locations = get_theme_mod( 'nav_menu_locations', [] );
	$clean     = [];

	foreach ( $config as $location => $menu_config ) {
		$menu = tp_child_get_nav_menu_by_config( $location );

		if ( $menu && ! is_wp_error( $menu ) ) {
			$clean[ $location ] = (int) $menu->term_id;
		}
	}

	if ( $clean !== $locations ) {
		set_theme_mod( 'nav_menu_locations', $clean );
	}
}

/**
 * One-time menu rename and orphan cleanup.
 */
function tp_child_chrome_migrate_nav_menu_labels(): void {
	if ( get_option( 'tp_child_chrome_menus_v1' ) ) {
		return;
	}

	$legacy_map = [
		'tp_main_nav'     => 'Tasche Piene Main Nav',
		'tp_footer_left'  => 'Tasche Piene Footer Left',
		'tp_footer_right' => 'Tasche Piene Footer Right',
	];

	foreach ( tp_child_nav_menu_config() as $location => $menu_config ) {
		$legacy = $legacy_map[ $location ] ?? '';

		if ( $legacy && $legacy !== $menu_config['name'] ) {
			tp_child_chrome_rename_nav_menu( $legacy, $menu_config['name'] );
		}

		tp_child_chrome_rename_nav_menu( $menu_config['name'], $menu_config['name'] );
	}

	tp_child_chrome_delete_orphan_nav_menus();
	tp_child_chrome_normalize_nav_menu_locations();

	update_option( 'tp_child_chrome_menus_v1', '1', false );
}
add_action( 'after_setup_theme', 'tp_child_chrome_migrate_nav_menu_labels', 23 );
