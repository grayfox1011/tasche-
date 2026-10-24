<?php
/**
 * One-time migration: drop legacy nav link fields from chrome modules.
 *
 * Navigation labels/URLs now come from WordPress menus only.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Attribute keys to remove from chrome modules.
 */
function tp_child_chrome_legacy_nav_attr_keys( string $module_name ): array {
	if ( 'tp/theme-header' === $module_name ) {
		$keys = [];

		for ( $index = 1; $index <= 6; $index++ ) {
			$keys[] = "navItem{$index}Text";
			$keys[] = "navItem{$index}Link";
		}

		return $keys;
	}

	if ( 'tp/theme-footer' === $module_name ) {
		$keys = [];

		for ( $index = 1; $index <= 3; $index++ ) {
			$keys[] = "footerLeft{$index}Text";
			$keys[] = "footerLeft{$index}Link";
			$keys[] = "footerRight{$index}Text";
			$keys[] = "footerRight{$index}Link";
		}

		return $keys;
	}

	return [];
}

/**
 * Remove legacy nav attrs from a parsed block tree.
 */
function tp_child_chrome_strip_legacy_nav_attrs_from_blocks( array $blocks ): array {
	foreach ( $blocks as $index => $block ) {
		if ( ! is_array( $block ) ) {
			continue;
		}

		if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
			$blocks[ $index ]['innerBlocks'] = tp_child_chrome_strip_legacy_nav_attrs_from_blocks( $block['innerBlocks'] );
		}

		$block_name = $block['blockName'] ?? '';
		$keys       = tp_child_chrome_legacy_nav_attr_keys( $block_name );

		if ( empty( $keys ) || empty( $block['attrs'] ) || ! is_array( $block['attrs'] ) ) {
			continue;
		}

		$attrs = $block['attrs'];

		foreach ( $keys as $key ) {
			unset( $attrs[ $key ] );
		}

		$blocks[ $index ]['attrs'] = $attrs;
	}

	return $blocks;
}

/**
 * Strip legacy nav fields from Theme Builder chrome layouts.
 */
function tp_child_chrome_migrate_theme_builder_nav_attrs(): void {
	if ( get_option( 'tp_child_chrome_nav_wp_v1' ) ) {
		return;
	}

	if ( ! function_exists( 'parse_blocks' ) || ! function_exists( 'serialize_blocks' ) ) {
		return;
	}

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

			if ( '' === $content || false === strpos( $content, 'tp/theme-' ) ) {
				continue;
			}

			$blocks          = parse_blocks( $content );
			$migrated_blocks = tp_child_chrome_strip_legacy_nav_attrs_from_blocks( $blocks );
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

	update_option( 'tp_child_chrome_nav_wp_v1', '1', true );

	if ( $updated ) {
		tp_child_clear_divi_static_css();
	}
}
add_action( 'after_setup_theme', 'tp_child_chrome_migrate_theme_builder_nav_attrs', 22 );
