<?php
/**
 * Add id="home" to the hero section and restore #home hash links in menus.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Patch the front page hero section with id="home".
 */
function tp_child_migrate_hero_home_id(): void {
	if ( get_option( 'tp_child_hero_home_id_v1' ) ) {
		return;
	}

	$page_id = (int) get_option( 'page_on_front' );

	if ( $page_id > 0 ) {
		$content = (string) get_post_field( 'post_content', $page_id );
		$search  = '"htmlAttributes":{"desktop":{"value":{"class":"tp-hero-section"}}}';
		$replace = '"htmlAttributes":{"desktop":{"value":{"class":"tp-hero-section","id":"home"}}}';

		if ( false !== strpos( $content, $search ) ) {
			$updated = str_replace( $search, $replace, $content );

			if ( $updated !== $content ) {
				wp_update_post(
					[
						'ID'           => $page_id,
						'post_content' => wp_slash( $updated ),
					]
				);

				if ( function_exists( 'tp_child_seed_clear_divi_cache_meta' ) ) {
					tp_child_seed_clear_divi_cache_meta( $page_id );
				}
			}
		}
	}

	update_option( 'tp_child_hero_home_id_v1', '1', false );

	if ( function_exists( 'et_core_clear_cache' ) ) {
		et_core_clear_cache();
	}
}

/**
 * Store scroll-to-hero links as #home instead of a bare homepage URL.
 */
function tp_child_nav_menu_restore_hash_home_links(): void {
	if ( get_option( 'tp_child_nav_hash_home_v3' ) ) {
		return;
	}

	foreach ( tp_child_nav_menu_config() as $location => $menu_config ) {
		$menu = tp_child_get_nav_menu_by_config( $location );

		if ( ! $menu || is_wp_error( $menu ) ) {
			continue;
		}

		$menu_id = (int) $menu->term_id;
		$items   = wp_get_nav_menu_items( $menu_id );

		if ( ! is_array( $items ) ) {
			continue;
		}

		foreach ( $items as $item ) {
			if ( ! $item instanceof WP_Post ) {
				continue;
			}

			$url = isset( $item->url ) ? (string) $item->url : '';

			if ( ! tp_child_is_home_url( $url ) || str_contains( $url, '#' ) ) {
				continue;
			}

			wp_update_nav_menu_item(
				$menu_id,
				(int) $item->ID,
				[
					'menu-item-db-id'    => (int) $item->ID,
					'menu-item-title'    => $item->post_title,
					'menu-item-url'      => '#home',
					'menu-item-target'   => $item->target ?? '',
					'menu-item-status'   => 'publish',
					'menu-item-type'     => 'custom',
				]
			);
		}
	}

	update_option( 'tp_child_nav_hash_home_v3', '1', false );
}

/**
 * One-time hero anchor bootstrap.
 */
function tp_child_migrate_hero_anchor(): void {
	tp_child_migrate_hero_home_id();
	tp_child_nav_menu_restore_hash_home_links();
}
add_action( 'after_setup_theme', 'tp_child_migrate_hero_anchor', 26 );
