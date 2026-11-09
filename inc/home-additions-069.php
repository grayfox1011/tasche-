<?php
/**
 * 0.6.9: what the site taschepiene.it has and the theme did not.
 * - Under the notes of the menu, Consegna a domicilio: a line ("Ti portiamo la vaschetta a casa o in ufficio:")
 *   and the buttons of Just Eat and Deliveroo, pictures with their link (Divi Image modules: a picture copied is
 *   one more button).
 * - After Orari, Informazioni utili (id faq, as the "Domande" link of the site): a title in a box and Divi's
 *   Accordion module with the questions and answers of the site, the first one open.
 * The home the theme makes has them (inc/seed/home-content.php); once, after the update, the theme's home of a
 * site made before gets them too, the same pieces, if it does not have them yet. Other pages are not touched.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The blocks of the theme's home with one of these classes, as the home the theme makes has them (pictures from
 * the theme's folder).
 *
 * @return array<string, array> Class => parsed block.
 */
function tp_child_home_additions_069_blocks(): array {
	$content = str_replace( '{{TP_CHILD_URL}}', esc_url_raw( TP_CHILD_URL ), tp_child_seed_raw_home_content() );
	$found   = [];
	$walk    = static function ( array $blocks ) use ( &$walk, &$found ): void {
		foreach ( $blocks as $block ) {
			$classes = explode( ' ', tp_child_block_class_names( is_array( $block['attrs'] ?? null ) ? $block['attrs'] : [] ) );

			foreach ( [ 'tp-menu-delivery', 'tp-faq-section' ] as $class ) {
				if ( ! isset( $found[ $class ] ) && in_array( $class, $classes, true ) ) {
					$found[ $class ] = $block;
				}
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$walk( $block['innerBlocks'] );
			}
		}
	};

	$walk( parse_blocks( $content ) );

	return $found;
}

/**
 * Whether a block (or one inside it) has the class.
 */
function tp_child_home_additions_069_has( array $blocks, string $class ): bool {
	foreach ( $blocks as $block ) {
		if ( in_array( $class, explode( ' ', tp_child_block_class_names( is_array( $block['attrs'] ?? null ) ? $block['attrs'] : [] ) ), true ) ) {
			return true;
		}

		if ( ! empty( $block['innerBlocks'] ) && tp_child_home_additions_069_has( $block['innerBlocks'], $class ) ) {
			return true;
		}
	}

	return false;
}

/**
 * The blocks with $new after the first block with the class $after (at any depth): its parent gets one more inner
 * block, and in its innerContent the place of it, after the place of the block it follows.
 */
function tp_child_home_additions_069_insert_after( array $blocks, string $after, array $new, bool &$done ): array {
	// At the top of the page (a page without Divi's placeholder around its sections).
	foreach ( $blocks as $i => $block ) {
		if ( in_array( $after, explode( ' ', tp_child_block_class_names( is_array( $block['attrs'] ?? null ) ? $block['attrs'] : [] ) ), true ) && 'divi/section' === ( $block['blockName'] ?? '' ) ) {
			array_splice( $blocks, $i + 1, 0, [ $new ] );
			$done = true;

			return $blocks;
		}
	}

	foreach ( $blocks as $i => $block ) {
		if ( $done ) {
			break;
		}

		$inner = $block['innerBlocks'] ?? [];

		foreach ( $inner as $k => $child ) {
			if ( ! in_array( $after, explode( ' ', tp_child_block_class_names( is_array( $child['attrs'] ?? null ) ? $child['attrs'] : [] ) ), true ) ) {
				continue;
			}

			// The place of the k-th inner block in innerContent (the k-th null).
			$content = is_array( $block['innerContent'] ?? null ) ? $block['innerContent'] : [];
			$seen    = -1;
			$at      = null;

			foreach ( $content as $c => $piece ) {
				if ( null === $piece && ++$seen === $k ) {
					$at = $c;
					break;
				}
			}

			if ( null === $at ) {
				return $blocks;
			}

			array_splice( $inner, $k + 1, 0, [ $new ] );
			array_splice( $content, $at + 1, 0, [ "\n\n", null ] );

			$blocks[ $i ]['innerBlocks']  = $inner;
			$blocks[ $i ]['innerContent'] = $content;
			$done                         = true;

			return $blocks;
		}

		if ( $inner ) {
			$blocks[ $i ]['innerBlocks'] = tp_child_home_additions_069_insert_after( $inner, $after, $new, $done );
		}
	}

	return $blocks;
}

/**
 * The theme's home: the front page and the page the theme made as home, when they have its menu.
 *
 * @return int[]
 */
function tp_child_home_additions_069_pages(): array {
	$ids = [];

	if ( 'page' === get_option( 'show_on_front' ) ) {
		$ids[] = (int) get_option( 'page_on_front' );
	}

	$made = get_page_by_path( 'tasche-piene-hero', OBJECT, 'page' );

	if ( $made instanceof WP_Post ) {
		$ids[] = (int) $made->ID;
	}

	return array_values(
		array_filter(
			array_unique( $ids ),
			static function ( int $id ): bool {
				$post = $id ? get_post( $id ) : null;

				return $post instanceof WP_Post && ! in_array( $post->post_status, [ 'trash', 'auto-draft' ], true ) && false !== strpos( (string) $post->post_content, 'tp-menu-section' );
			}
		)
	);
}

/**
 * Once after the update.
 */
function tp_child_migrate_home_additions_069(): void {
	$flag = 'tp_child_home_additions_069_v1';

	if ( get_option( $flag ) ) {
		return;
	}

	$pieces  = tp_child_home_additions_069_blocks();
	$updated = false;

	foreach ( tp_child_home_additions_069_pages() as $id ) {
		$blocks  = parse_blocks( (string) get_post_field( 'post_content', $id ) );
		$changed = false;

		if ( isset( $pieces['tp-menu-delivery'] ) && ! tp_child_home_additions_069_has( $blocks, 'tp-menu-delivery' ) ) {
			$blocks = tp_child_home_additions_069_insert_after( $blocks, 'tp-menu-note', $pieces['tp-menu-delivery'], $changed );
		}

		if ( isset( $pieces['tp-faq-section'] ) && ! tp_child_home_additions_069_has( $blocks, 'tp-faq-section' ) ) {
			$done   = false;
			$blocks = tp_child_home_additions_069_insert_after( $blocks, 'tp-hours-section', $pieces['tp-faq-section'], $done );
			$changed = $changed || $done;
		}

		if ( ! $changed ) {
			continue;
		}

		// The block JSON must be saved verbatim, whoever triggers the request.
		kses_remove_filters();
		wp_update_post(
			[
				'ID'           => $id,
				'post_content' => wp_slash( serialize_blocks( $blocks ) ),
			]
		);
		kses_init();

		if ( function_exists( 'tp_child_seed_clear_divi_cache_meta' ) ) {
			tp_child_seed_clear_divi_cache_meta( $id );
		}

		$updated = true;
	}

	if ( $updated && function_exists( 'tp_child_clear_divi_static_css' ) ) {
		tp_child_clear_divi_static_css();
	}

	update_option( $flag, TP_CHILD_VERSION, true );
}
add_action( 'init', 'tp_child_migrate_home_additions_069', 52 );
