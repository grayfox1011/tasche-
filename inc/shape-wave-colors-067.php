<?php
/**
 * 0.6.7: "Colore wave" and "Colore sfondo" of the Onda module are Divi's colour picker instead of a list of five
 * names. Once after the update, a name saved from the old list becomes its colour (green #054726, burgundy
 * #560b22, cream #fff5e9, sand #f8ebdb), so the picker shows it as it is; "Automatico" (the shape's own colour)
 * becomes an empty field, which means the same. The module still reads the old names, so nothing changes on the
 * site.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The colour of a name of the old list; null for any other value.
 */
function tp_child_shape_wave_token_color( $value ): ?string {
	$colors = [
		'green'    => '#054726',
		'burgundy' => '#560b22',
		'cream'    => '#fff5e9',
		'sand'     => '#f8ebdb',
		'auto'     => '',
	];

	return is_string( $value ) && array_key_exists( $value, $colors ) ? $colors[ $value ] : null;
}

/**
 * Blocks: the colours of every Onda, in every device and state.
 */
function tp_child_shape_wave_colors_blocks( array $blocks, bool &$changed ): array {
	foreach ( $blocks as $i => $block ) {
		if ( 'tp/shape-wave' === ( $block['blockName'] ?? '' ) && is_array( $block['attrs'] ?? null ) ) {
			foreach ( [ 'fillColor', 'backgroundColor' ] as $key ) {
				$devices = $block['attrs'][ $key ]['innerContent'] ?? null;

				if ( ! is_array( $devices ) ) {
					continue;
				}

				foreach ( $devices as $device => $states ) {
					foreach ( is_array( $states ) ? $states : [] as $state => $value ) {
						$color = tp_child_shape_wave_token_color( $value );

						if ( null !== $color ) {
							$blocks[ $i ]['attrs'][ $key ]['innerContent'][ $device ][ $state ] = $color;
							$changed = true;
						}
					}
				}
			}
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			$blocks[ $i ]['innerBlocks'] = tp_child_shape_wave_colors_blocks( $block['innerBlocks'], $changed );
		}
	}

	return $blocks;
}

/**
 * Once after the update: the posts with an Onda (pages, Theme Builder layouts, Divi Library).
 */
function tp_child_migrate_shape_wave_colors_067(): void {
	$flag = 'tp_child_shape_wave_colors_067_v1';

	if ( get_option( $flag ) ) {
		return;
	}

	global $wpdb;

	$ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_content LIKE '%wp:tp/shape-wave%' AND post_status NOT IN ('trash','auto-draft','inherit')" );

	foreach ( array_map( 'intval', (array) $ids ) as $id ) {
		$content = (string) get_post_field( 'post_content', $id );

		if ( ! preg_match( '/"(fillColor|backgroundColor)":\{"innerContent":\{[^}]*"(green|burgundy|cream|sand|auto)"/', $content ) ) {
			continue;
		}

		$changed = false;
		$blocks  = tp_child_shape_wave_colors_blocks( parse_blocks( $content ), $changed );

		if ( $changed ) {
			wp_update_post(
				[
					'ID'           => $id,
					'post_content' => wp_slash( serialize_blocks( $blocks ) ),
				]
			);
		}
	}

	update_option( $flag, TP_CHILD_VERSION, true );
}
add_action( 'init', 'tp_child_migrate_shape_wave_colors_067', 49 );
