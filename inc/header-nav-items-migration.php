<?php
/**
 * The links of the menu at the top become Voci of Header sito (0.5.7), so that their words and links
 * change from Divi: Theme Builder > Header > Header sito > Contenuto > Elementi, one Voce per link
 * (Testo, Link o ancora, Apri in una nuova scheda), to add, remove and reorder.
 *
 * Until 0.5.6 the links came from the WordPress menu "Tasche Piene — Header" (Aspetto > Menu), and the
 * fields of Header sito for them were hidden. Once, on the first request after the update, every header
 * layout of the Theme Builder with Header sito and no Voci gets one Voce for each link it shows today, with
 * the same words, links and new-tab setting: the site does not change. A Header sito without Voci still
 * takes the WordPress menu, as before (tp_child_header_nav_items_without_voci()).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Voci for the given links, as block comments (Divi's serialization).
 */
function tp_child_header_nav_item_blocks( array $items, string $builder_version ): string {
	$blocks = '';

	foreach ( $items as $item ) {
		$title = isset( $item->title ) ? trim( (string) $item->title ) : '';

		if ( '' === $title ) {
			continue;
		}

		$attrs = [
			'text' => [ 'innerContent' => [ 'desktop' => [ 'value' => $title ] ] ],
			'link' => [ 'innerContent' => [ 'desktop' => [ 'value' => isset( $item->url ) && '' !== (string) $item->url ? (string) $item->url : '#' ] ] ],
		];

		if ( '_blank' === ( $item->target ?? '' ) ) {
			$attrs['target'] = [ 'innerContent' => [ 'desktop' => [ 'value' => 'on' ] ] ];
		}

		$attrs['module'] = [ 'meta' => [ 'adminLabel' => [ 'desktop' => [ 'value' => 'Voce: ' . tp_child_wireframe_plain( $title ) ] ] ] ];

		if ( '' !== $builder_version ) {
			$attrs['builderVersion'] = $builder_version;
		}

		$blocks .= '<!-- wp:tp/header-nav-item ' . serialize_block_attributes( $attrs ) . " /-->\n";
	}

	return $blocks;
}

/**
 * A header layout with the Voci of each Header sito that has none: the links it shows today.
 */
function tp_child_header_nav_items_transform( string $content ): string {
	if ( false === strpos( $content, 'wp:tp/theme-header' ) || false !== strpos( $content, 'wp:tp/header-nav-item' ) ) {
		return $content;
	}

	$updated = preg_replace_callback(
		'#<!--\s+wp:tp/theme-header\s+(\{(?:(?:[^}]+|}+(?=})|(?!}\s+/?-->).)*+)?\})\s+(/)?-->#s',
		static function ( array $match ): string {
			$attrs = json_decode( $match[1], true );

			if ( ! is_array( $attrs ) ) {
				return $match[0];
			}

			$voci = tp_child_header_nav_item_blocks( tp_child_header_nav_items_without_voci( $attrs ), (string) ( $attrs['builderVersion'] ?? '' ) );

			if ( '' === $voci ) {
				return $match[0];
			}

			// A Header sito saved without children closes on itself: it opens and closes around the Voci.
			return '' !== ( $match[2] ?? '' )
				? '<!-- wp:tp/theme-header ' . $match[1] . " -->\n" . $voci . '<!-- /wp:tp/theme-header -->'
				: $match[0] . "\n" . $voci;
		},
		$content
	);

	return is_string( $updated ) ? $updated : $content;
}

function tp_child_migrate_header_nav_items(): void {
	$flag = 'tp_child_header_nav_items_v1';

	if ( get_option( $flag ) ) {
		return;
	}

	$layouts = get_posts(
		[
			'post_type'        => 'et_header_layout',
			'post_status'      => 'any',
			'numberposts'      => -1,
			'suppress_filters' => true,
		]
	);

	$changed = false;

	foreach ( $layouts as $layout ) {
		$content = (string) $layout->post_content;
		$updated = tp_child_header_nav_items_transform( $content );

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

add_action( 'init', 'tp_child_migrate_header_nav_items', 41 );
