<?php
/**
 * The links of the two menus of the footer become Voci of Footer sito (0.5.8), as those of the header did in
 * 0.5.7: Theme Builder > Footer > Footer sito > Contenuto > Elementi, one Voce per link (Colonna, Testo, Link
 * o ancora, Apri in una nuova scheda), to add, remove, reorder and move from one column to the other.
 *
 * Until 0.5.7 the links came from the WordPress menus "Tasche Piene — Footer sinistra" and "Footer destra"
 * (Aspetto > Menu), and the fields of Footer sito for them were hidden. Once, on the first request after the
 * update, every footer layout of the Theme Builder with Footer sito and no Voci gets one Voce for each link it
 * shows today, column by column, with the same words, links and new-tab setting: the site does not change. A
 * Footer sito without Voci still takes the WordPress menus (tp_child_footer_nav_columns_without_voci()).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Voci for the given columns of links, left first, as block comments (Divi's serialization).
 */
function tp_child_footer_nav_item_blocks( array $columns, string $builder_version ): string {
	$blocks = '';

	foreach ( [ 'left' => 'Sinistra', 'right' => 'Destra' ] as $column => $side ) {
		foreach ( $columns[ $column ] ?? [] as $item ) {
			$title = isset( $item->title ) ? trim( (string) $item->title ) : '';

			if ( '' === $title ) {
				continue;
			}

			$attrs = [
				'column' => [ 'innerContent' => [ 'desktop' => [ 'value' => $column ] ] ],
				'text'   => [ 'innerContent' => [ 'desktop' => [ 'value' => $title ] ] ],
				'link'   => [ 'innerContent' => [ 'desktop' => [ 'value' => isset( $item->url ) && '' !== (string) $item->url ? (string) $item->url : '#' ] ] ],
			];

			if ( '_blank' === ( $item->target ?? '' ) ) {
				$attrs['target'] = [ 'innerContent' => [ 'desktop' => [ 'value' => 'on' ] ] ];
			}

			$attrs['module'] = [ 'meta' => [ 'adminLabel' => [ 'desktop' => [ 'value' => $side . ': ' . tp_child_wireframe_plain( $title ) ] ] ] ];

			if ( '' !== $builder_version ) {
				$attrs['builderVersion'] = $builder_version;
			}

			$blocks .= '<!-- wp:tp/footer-nav-item ' . serialize_block_attributes( $attrs ) . " /-->\n";
		}
	}

	return $blocks;
}

/**
 * A footer layout with the Voci of each Footer sito that has none: the links it shows today.
 */
function tp_child_footer_nav_items_transform( string $content ): string {
	if ( false === strpos( $content, 'wp:tp/theme-footer' ) || false !== strpos( $content, 'wp:tp/footer-nav-item' ) ) {
		return $content;
	}

	$updated = preg_replace_callback(
		'#<!--\s+wp:tp/theme-footer\s+(\{(?:(?:[^}]+|}+(?=})|(?!}\s+/?-->).)*+)?\})\s+(/)?-->#s',
		static function ( array $match ): string {
			$attrs = json_decode( $match[1], true );

			if ( ! is_array( $attrs ) ) {
				return $match[0];
			}

			$voci = tp_child_footer_nav_item_blocks( tp_child_footer_nav_columns_without_voci( $attrs ), (string) ( $attrs['builderVersion'] ?? '' ) );

			if ( '' === $voci ) {
				return $match[0];
			}

			// A Footer sito saved without children closes on itself: it opens and closes around the Voci.
			return '' !== ( $match[2] ?? '' )
				? '<!-- wp:tp/theme-footer ' . $match[1] . " -->\n" . $voci . '<!-- /wp:tp/theme-footer -->'
				: $match[0] . "\n" . $voci;
		},
		$content
	);

	return is_string( $updated ) ? $updated : $content;
}

function tp_child_migrate_footer_nav_items(): void {
	$flag = 'tp_child_footer_nav_items_v1';

	if ( get_option( $flag ) ) {
		return;
	}

	$layouts = get_posts(
		[
			'post_type'        => 'et_footer_layout',
			'post_status'      => 'any',
			'numberposts'      => -1,
			'suppress_filters' => true,
		]
	);

	$changed = false;

	foreach ( $layouts as $layout ) {
		$content = (string) $layout->post_content;
		$updated = tp_child_footer_nav_items_transform( $content );

		if ( $updated === $content ) {
			continue;
		}

		$changed = true;

		// The block JSON must be saved verbatim, whoever triggers the request.
		kses_remove_filters();
		wp_update_post(
			[
				'ID'           => $layout->ID,
				'post_content' => wp_slash( $updated ),
			]
		);
		kses_init();
		tp_child_seed_clear_divi_cache_meta( (int) $layout->ID );
	}

	if ( $changed ) {
		tp_child_clear_divi_static_css();
	}

	update_option( $flag, TP_CHILD_VERSION, true );
}

add_action( 'init', 'tp_child_migrate_footer_nav_items', 42 );
