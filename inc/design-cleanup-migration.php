<?php
/**
 * Remove the stale design values saved in the landing page sections and rows.
 *
 * Until 0.3.x nearly every rule of the stylesheet used !important, so the
 * padding, widths and colours stored in these Divi modules never showed on
 * the page: the Divi fields displayed numbers that had no effect. The
 * stylesheet now lets Divi win, so those values would suddenly appear. They
 * are removed once: the page keeps its look and every field starts from the
 * theme defaults, ready to be changed from the Visual Builder.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether a block belongs to the Tasche Piene landing layout.
 */
function tp_child_design_cleanup_is_theme_block( array $block ): bool {
	$class = $block['attrs']['module']['advanced']['htmlAttributes']['desktop']['value']['class'] ?? '';

	return is_string( $class ) && 1 === preg_match( '/(^|\s)tp-[a-z0-9-]+/', $class );
}

/**
 * Drop empty arrays left behind after removing values.
 */
function tp_child_design_cleanup_prune( array $value ): array {
	foreach ( $value as $key => $item ) {
		if ( is_array( $item ) ) {
			$value[ $key ] = tp_child_design_cleanup_prune( $item );

			if ( [] === $value[ $key ] ) {
				unset( $value[ $key ] );
			}
		}
	}

	return $value;
}

/**
 * Remove the desktop spacing/sizing/background values the stylesheet used to override.
 */
function tp_child_design_cleanup_blocks( array $blocks, int &$removed ): array {
	foreach ( $blocks as &$block ) {
		$name = $block['blockName'] ?? '';

		if ( in_array( $name, [ 'divi/section', 'divi/row' ], true ) && tp_child_design_cleanup_is_theme_block( $block ) ) {
			$decoration = $block['attrs']['module']['decoration'] ?? [];
			$is_hero    = str_contains( (string) ( $block['attrs']['module']['advanced']['htmlAttributes']['desktop']['value']['class'] ?? '' ), 'tp-hero-section' );
			$paths      = [ [ 'spacing', 'desktop', 'value', 'padding' ] ];

			if ( 'divi/section' === $name ) {
				$paths[] = [ 'sizing', 'desktop', 'value', 'minHeight' ];

				// The hero colour sits behind the video and was always visible.
				if ( ! $is_hero ) {
					$paths[] = [ 'background', 'desktop', 'value', 'color' ];
				}
			} else {
				$paths[] = [ 'sizing', 'desktop', 'value', 'width' ];
				$paths[] = [ 'sizing', 'desktop', 'value', 'maxWidth' ];
			}

			// The 0.3.1 candidate recorded the values changed by hand: those stay.
			$manual = array_keys( (array) ( $block['attrs']['tpDesignOverrides']['values'] ?? [] ) );

			foreach ( $paths as $path ) {
				$prefix = 'module.decoration.' . implode( '.', $path );

				foreach ( $manual as $edited ) {
					if ( is_string( $edited ) && ( $edited === $prefix || str_starts_with( $edited, $prefix . '.' ) ) ) {
						continue 2;
					}
				}

				$last = array_pop( $path );
				$node = &$decoration;

				foreach ( $path as $key ) {
					if ( ! isset( $node[ $key ] ) || ! is_array( $node[ $key ] ) ) {
						unset( $node );
						continue 2;
					}
					$node = &$node[ $key ];
				}

				if ( array_key_exists( $last, $node ) ) {
					unset( $node[ $last ] );
					$removed++;
				}
				unset( $node );
			}

			$block['attrs']['module']['decoration'] = $decoration;
			$block['attrs']                         = tp_child_design_cleanup_prune( $block['attrs'] );
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			$block['innerBlocks'] = tp_child_design_cleanup_blocks( $block['innerBlocks'], $removed );
		}
	}
	unset( $block );

	return $blocks;
}

/**
 * Clean one block-markup string; returns it unchanged when there is nothing to remove.
 */
function tp_child_design_cleanup_content( string $content ): string {
	if ( false === strpos( $content, 'tp-hero-section' ) ) {
		return $content;
	}

	$removed = 0;
	$blocks  = tp_child_design_cleanup_blocks( parse_blocks( $content ), $removed );

	return $removed > 0 ? serialize_blocks( $blocks ) : $content;
}

/**
 * One-time cleanup of every landing page (front page and its copies).
 */
function tp_child_migrate_design_cleanup(): void {
	if ( get_option( 'tp_child_design_cleanup_v1' ) ) {
		return;
	}

	global $wpdb;

	$page_ids = $wpdb->get_col(
		"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status NOT IN ( 'trash', 'auto-draft', 'inherit' ) AND post_content LIKE '%tp-hero-section%'"
	);

	// The block JSON must be saved verbatim, whoever triggers the request.
	kses_remove_filters();

	foreach ( array_map( 'intval', $page_ids ) as $page_id ) {
		$content = (string) get_post_field( 'post_content', $page_id );
		$updated = tp_child_design_cleanup_content( $content );

		if ( $updated === $content ) {
			continue;
		}

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

	kses_init();
	update_option( 'tp_child_design_cleanup_v1', '1', true );

	if ( function_exists( 'et_core_clear_cache' ) ) {
		et_core_clear_cache();
	}
}
add_action( 'after_setup_theme', 'tp_child_migrate_design_cleanup', 27 );
