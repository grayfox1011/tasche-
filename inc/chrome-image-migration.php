<?php
/**
 * One-time migration: copy legacy image URL fields into Divi upload src attrs.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Image field definitions for header/footer chrome modules.
 */
function tp_child_chrome_image_definitions( string $module_name ): array {
	$base = trailingslashit( TP_CHILD_URL ) . 'assets/';

	if ( 'tp/theme-header' === $module_name ) {
		return [
			[ 'logoImage', 'logoUrl', $base . 'svg/logo-tasche-piene-nav.svg' ],
		];
	}

	if ( 'tp/theme-footer' === $module_name ) {
		return [
			[ 'logoImage', 'logoUrl', $base . 'images/logo-footer.webp' ],
			[ 'characterOneImage', 'characterOneUrl', $base . 'images/footer/personaggi-1.webp' ],
			[ 'characterThreeImage', 'characterThreeUrl', $base . 'images/footer/personaggi-3.webp' ],
			[ 'characterFourImage', 'characterFourUrl', $base . 'images/footer/personaggi-4.webp' ],
		];
	}

	return [];
}

/**
 * Sync WordPress attachment IDs for Divi upload fields.
 */
function tp_child_chrome_sync_image_attachment_ids( array $attrs, array $definitions ): array {
	foreach ( $definitions as $definition ) {
		$upload_key = $definition[0];
		$src        = tp_child_chrome_get_image_src_from_attr( $attrs, $upload_key );

		if ( '' === $src || ! function_exists( 'attachment_url_to_postid' ) ) {
			continue;
		}

		$attachment_id = (int) attachment_url_to_postid( $src );

		if ( $attachment_id > 0 ) {
			$attrs[ $upload_key ]['innerContent']['desktop']['value']['id'] = $attachment_id;
		}
	}

	return $attrs;
}

/**
 * Recursively migrate image attrs inside parsed blocks.
 */
function tp_child_chrome_migrate_blocks_images( array $blocks ): array {
	foreach ( $blocks as $index => $block ) {
		if ( ! is_array( $block ) ) {
			continue;
		}

		if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
			$blocks[ $index ]['innerBlocks'] = tp_child_chrome_migrate_blocks_images( $block['innerBlocks'] );
		}

		$block_name = $block['blockName'] ?? '';
		$definitions = tp_child_chrome_image_definitions( $block_name );

		if ( empty( $definitions ) || empty( $block['attrs'] ) || ! is_array( $block['attrs'] ) ) {
			continue;
		}

		$attrs = tp_child_chrome_migrate_image_attrs( $block['attrs'], $definitions );
		$attrs = tp_child_chrome_sync_image_attachment_ids( $attrs, $definitions );
		$blocks[ $index ]['attrs'] = $attrs;
	}

	return $blocks;
}

/**
 * Migrate Theme Builder layouts so images are clickable in the Visual Builder.
 */
function tp_child_chrome_migrate_theme_builder_images(): void {
	if ( get_option( 'tp_child_chrome_images_v4' ) ) {
		return;
	}

	delete_option( 'tp_child_chrome_images_v3' );

	if ( ! function_exists( 'parse_blocks' ) || ! function_exists( 'serialize_blocks' ) ) {
		return;
	}

	$post_types = [ 'et_header_layout', 'et_footer_layout', 'et_body_layout' ];
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
			$migrated_blocks = tp_child_chrome_migrate_blocks_images( $blocks );
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

	update_option( 'tp_child_chrome_images_v4', '1', true );

	if ( $updated ) {
		tp_child_clear_divi_static_css();
	}
}
add_action( 'after_setup_theme', 'tp_child_chrome_migrate_theme_builder_images', 20 );

/**
 * Re-sync attachment IDs when uploads have src but id:0 (VB shows only one image).
 */
function tp_child_chrome_migrate_theme_builder_image_ids_v5(): void {
	if ( get_option( 'tp_child_chrome_images_v5' ) ) {
		return;
	}

	if ( ! function_exists( 'parse_blocks' ) || ! function_exists( 'serialize_blocks' ) ) {
		return;
	}

	$post_types = [ 'et_header_layout', 'et_footer_layout', 'et_body_layout' ];
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
			$migrated_blocks = tp_child_chrome_migrate_blocks_images( $blocks );
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

	update_option( 'tp_child_chrome_images_v5', '1', true );

	if ( $updated ) {
		tp_child_clear_divi_static_css();
	}
}
add_action( 'after_setup_theme', 'tp_child_chrome_migrate_theme_builder_image_ids_v5', 21 );
