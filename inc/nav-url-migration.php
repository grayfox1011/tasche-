<?php
/**
 * Migrate hash-only nav URLs to absolute home URLs.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stored URL to persist in menus / Theme Builder (null = no change).
 */
function tp_child_nav_url_migrate_target( string $url ): ?string {
	$url = trim( $url );

	if ( '' === $url || '#' === $url || '#home' === $url ) {
		return '#home';
	}

	if ( tp_child_is_home_url( $url ) ) {
		return '#home';
	}

	if ( str_starts_with( $url, '#' ) ) {
		return tp_child_home_url() . $url;
	}

	return null;
}

/**
 * Update nav menu items that still use in-page anchors.
 */
function tp_child_nav_url_migrate_menu_items(): void {
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

			$current = isset( $item->url ) ? (string) $item->url : '';
			$target  = tp_child_nav_url_migrate_target( $current );

			if ( null === $target || $target === $current ) {
				continue;
			}

			wp_update_nav_menu_item(
				$menu_id,
				(int) $item->ID,
				[
					'menu-item-db-id'    => (int) $item->ID,
					'menu-item-title'    => $item->post_title,
					'menu-item-url'      => $target,
					'menu-item-target'   => $item->target ?? '',
					'menu-item-status'   => 'publish',
					'menu-item-type'     => 'custom',
				]
			);
		}
	}
}

/**
 * Rewrite logoLink / link attrs in Theme Builder chrome layouts.
 */
function tp_child_nav_url_migrate_chrome_blocks( array $blocks ): array {
	foreach ( $blocks as $index => $block ) {
		if ( ! is_array( $block ) ) {
			continue;
		}

		if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
			$blocks[ $index ]['innerBlocks'] = tp_child_nav_url_migrate_chrome_blocks( $block['innerBlocks'] );
		}

		$block_name = $block['blockName'] ?? '';
		$attrs      = $block['attrs'] ?? [];

		if ( ! is_array( $attrs ) || '' === $block_name ) {
			continue;
		}

		$attr_keys = [];

		if ( in_array( $block_name, [ 'tp/theme-header', 'tp/theme-footer' ], true ) ) {
			$attr_keys[] = 'logoLink';
		}

		if ( 'tp/footer-logo' === $block_name ) {
			$attr_keys[] = 'link';
		}

		foreach ( $attr_keys as $key ) {
			if ( empty( $attrs[ $key ]['innerContent']['desktop']['value'] ) ) {
				continue;
			}

			$current = (string) $attrs[ $key ]['innerContent']['desktop']['value'];
			$target  = tp_child_nav_url_migrate_target( $current );

			if ( null === $target || $target === $current ) {
				continue;
			}

			$attrs[ $key ]['innerContent']['desktop']['value'] = $target;
		}

		$blocks[ $index ]['attrs'] = $attrs;
	}

	return $blocks;
}

/**
 * One-time migration for menus and Theme Builder logo links.
 */
function tp_child_nav_url_migrate(): void {
	if ( get_option( 'tp_child_nav_urls_v1' ) ) {
		return;
	}

	if ( ! function_exists( 'tp_child_home_url' ) ) {
		return;
	}

	tp_child_nav_url_migrate_menu_items();

	if ( function_exists( 'parse_blocks' ) && function_exists( 'serialize_blocks' ) ) {
		$post_types = [ 'et_header_layout', 'et_footer_layout' ];
		$updated    = false;

		foreach ( $post_types as $post_type ) {
			$posts = get_posts(
				[
					'post_type'      => $post_type,
					'post_status'    => [ 'publish', 'draft', 'private' ],
					'posts_per_page' => -1,
					'fields'         => 'ids',
				]
			);

			foreach ( $posts as $post_id ) {
				$content = (string) get_post_field( 'post_content', $post_id );

				if ( '' === $content || false === strpos( $content, 'tp/' ) ) {
					continue;
				}

				$blocks          = parse_blocks( $content );
				$migrated_blocks = tp_child_nav_url_migrate_chrome_blocks( $blocks );
				$new_content     = serialize_blocks( $migrated_blocks );

				if ( $new_content === $content ) {
					continue;
				}

				wp_update_post(
					[
						'ID'           => (int) $post_id,
						'post_content' => wp_slash( $new_content ),
					]
				);

				$updated = true;
			}
		}

		if ( $updated && function_exists( 'et_core_clear_cache' ) ) {
			et_core_clear_cache();
		}
	}

	update_option( 'tp_child_nav_urls_v1', '1', false );
}
add_action( 'after_setup_theme', 'tp_child_nav_url_migrate', 23 );

/**
 * Restore menu labels wiped by an earlier URL-only migration.
 */
function tp_child_nav_menu_restore_titles(): void {
	if ( get_option( 'tp_child_nav_menu_titles_v2' ) ) {
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

			$current_title = trim( (string) $item->post_title );

			if ( '' !== $current_title ) {
				continue;
			}

			$url           = isset( $item->url ) ? (string) $item->url : '';
			$restored_title = tp_child_nav_menu_title_from_defaults( $location, $url );

			if ( '' === $restored_title ) {
				continue;
			}

			wp_update_nav_menu_item(
				$menu_id,
				(int) $item->ID,
				[
					'menu-item-db-id'    => (int) $item->ID,
					'menu-item-title'    => $restored_title,
					'menu-item-url'      => $url,
					'menu-item-target'   => $item->target ?? '',
					'menu-item-status'   => 'publish',
					'menu-item-type'     => 'custom',
				]
			);
		}
	}

	update_option( 'tp_child_nav_menu_titles_v2', '1', false );
}
add_action( 'after_setup_theme', 'tp_child_nav_menu_restore_titles', 24 );
