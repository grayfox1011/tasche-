<?php
/**
 * 0.6.8: the characters of the footer become elements of Footer sito, next to the Voci: Theme Builder > Footer >
 * Footer sito > Contenuto > Elementi, one "Personaggio del footer" each (Posto, Immagine, Capovolgi, and Divi's
 * Design and Avanzate: Dimensioni, Trasforma, Visibilità...). Removed there, a character goes; duplicated, there
 * is one more; added with right click on the footer > Aggiungi elemento > Aggiungi elemento all'interno >
 * Personaggio del footer.
 *
 * Until 0.6.7 Footer sito drew three fixed places (the couple on the "ch" of Tasche, the pigeons on the "ne" of
 * Piene, a third one empty since 0.5.9) and the theme's CSS drew the man sitting at the right on the phones;
 * none of them could be removed or changed from Divi. Once, after the update, every Footer sito gets one
 * element for each character it shows today, with the same drawing (the one set in Divi, if any) and the same
 * Design settings, and the man is an element shown on the phones only: the site does not change. A Footer sito
 * not converted (an old layout imported later) still shows the three places as before.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A theme drawing as Divi's image value: its copy in the Media Library when the theme made one (new sites have
 * it), otherwise the file of the theme. $create also makes the copy (the site seeder does).
 */
function tp_child_footer_character_asset( string $relative_path, bool $create ): array {
	$asset = function_exists( 'tp_child_seed_import_media_asset' ) ? tp_child_seed_import_media_asset( $relative_path, $create ) : [];

	return ! empty( $asset['url'] )
		? [ 'src' => (string) $asset['url'], 'id' => (int) ( $asset['id'] ?? 0 ) ]
		: [ 'src' => trailingslashit( TP_CHILD_URL ) . $relative_path, 'id' => 0 ];
}

/**
 * One Personaggio (its block comment, as Divi saves it).
 *
 * @param array $image    Divi's image value: src, id.
 * @param array $hidden   The devices where it does not show (Avanzate > Visibilità): desktop, tablet, phone.
 * @param array $design   The decoration of its drawing (Design > Personaggio), if any.
 */
function tp_child_footer_character_block( string $label, string $slot, array $image, array $hidden, array $design, string $builder_version ): array {
	$attrs = [
		'slot'   => [ 'innerContent' => [ 'desktop' => [ 'value' => $slot ] ] ],
		'image'  => [ 'innerContent' => [ 'desktop' => [ 'value' => $image ] ] ],
		'module' => [ 'meta' => [ 'adminLabel' => [ 'desktop' => [ 'value' => $label ] ] ] ],
	];

	foreach ( $hidden as $device ) {
		$attrs['module']['decoration']['disabledOn'][ $device ] = [ 'value' => 'on' ];
	}

	if ( $design ) {
		$attrs['tpDesignImage'] = [ 'decoration' => $design ];
	}

	if ( '' !== $builder_version ) {
		$attrs['builderVersion'] = $builder_version;
	}

	return [
		'blockName'    => 'tp/footer-character',
		'attrs'        => $attrs,
		'innerBlocks'  => [],
		'innerHTML'    => '',
		'innerContent' => [],
	];
}

/**
 * The Personaggi a Footer sito shows today, from its attributes (saved, without Divi's defaults): the couple and
 * the pigeons with the drawing set in Divi (or the theme's), the third place if it has a drawing (it showed on
 * computers only: the smaller devices had it out of the footer), the man of the phones.
 */
function tp_child_footer_character_blocks( array $attrs, string $builder_version, bool $create_assets = false ): array {
	$design = static function ( string $key ) use ( $attrs ): array {
		$decoration = $attrs[ $key ]['decoration'] ?? [];

		return is_array( $decoration ) && class_exists( '\\TaschePieneDiviChild\\Modules\\OptionalDesignStyles' ) && \TaschePieneDiviChild\Modules\OptionalDesignStyles::has_values( $decoration ) ? $decoration : [];
	};

	$image = static function ( string $upload_key, string $legacy_key, string $default_path ) use ( $attrs, $create_assets ): array {
		$src = tp_child_chrome_get_image_src_from_attr( $attrs, $upload_key ) ?: tp_child_chrome_get_string( $attrs, $legacy_key, '' );

		if ( '' !== $src ) {
			$id = (int) ( $attrs[ $upload_key ]['innerContent']['desktop']['value']['id'] ?? 0 );

			return [ 'src' => $src, 'id' => $id ];
		}

		return '' !== $default_path ? tp_child_footer_character_asset( $default_path, $create_assets ) : [];
	};

	$blocks = [
		tp_child_footer_character_block( 'Personaggio: coppia', 'four', $image( 'characterFourImage', 'characterFourUrl', 'assets/images/footer/personaggi-4.webp' ), [], $design( 'tpDesignCharacterFour' ), $builder_version ),
		tp_child_footer_character_block( 'Personaggio: piccioni', 'one', $image( 'characterOneImage', 'characterOneUrl', 'assets/images/footer/personaggi-1.webp' ), [], $design( 'tpDesignCharacterOne' ), $builder_version ),
	];

	$third = $image( 'characterThreeImage', 'characterThreeUrl', '' );

	if ( ! empty( $third['src'] ) ) {
		$blocks[] = tp_child_footer_character_block( 'Personaggio: terzo posto', 'three', $third, [ 'tablet', 'phone' ], $design( 'tpDesignCharacterThree' ), $builder_version );
	}

	$blocks[] = tp_child_footer_character_block(
		'Personaggio: uomo seduto (solo telefono)',
		'three',
		[ 'src' => trailingslashit( TP_CHILD_URL ) . 'assets/svg/footer-character.svg', 'id' => 0 ],
		[ 'desktop', 'tablet' ],
		[],
		$builder_version
	);

	return $blocks;
}

/**
 * A Footer sito block with its Personaggi: marked as converted (characterItems), the Design settings of the old
 * places moved to the elements.
 */
function tp_child_footer_add_character_items( array $block, bool $create_assets = false ): array {
	$attrs = is_array( $block['attrs'] ?? null ) ? $block['attrs'] : [];

	$characters = tp_child_footer_character_blocks( $attrs, (string) ( $attrs['builderVersion'] ?? '' ), $create_assets );

	unset( $attrs['tpDesignCharacterOne'], $attrs['tpDesignCharacterFour'], $attrs['tpDesignCharacterThree'] );
	$attrs['characterItems'] = [ 'innerContent' => [ 'desktop' => [ 'value' => 'on' ] ] ];

	$block['attrs'] = $attrs;

	// A Footer sito saved without children closes on itself: it opens and closes around them.
	$inner_content = is_array( $block['innerContent'] ?? null ) ? $block['innerContent'] : [];

	if ( ! $inner_content ) {
		$inner_content = [ "\n" ];
	}

	$closing = array_pop( $inner_content );

	if ( ! is_string( $closing ) ) {
		$inner_content[] = $closing;
		$closing         = "\n";
	}

	foreach ( $characters as $character ) {
		$block['innerBlocks'][] = $character;
		$inner_content[]        = $closing;
		$inner_content[]        = null;
	}

	$inner_content[]       = $closing;
	$block['innerContent'] = $inner_content;

	return $block;
}

/**
 * Blocks: every Footer sito not converted yet gets its Personaggi.
 */
function tp_child_footer_characters_blocks( array $blocks, bool &$changed ): array {
	foreach ( $blocks as $i => $block ) {
		if ( 'tp/theme-footer' === ( $block['blockName'] ?? '' ) ) {
			if ( ! tp_child_footer_characters_as_items( $block ) ) {
				$blocks[ $i ] = tp_child_footer_add_character_items( $block );
				$changed      = true;
			}

			continue;
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			$blocks[ $i ]['innerBlocks'] = tp_child_footer_characters_blocks( $block['innerBlocks'], $changed );
		}
	}

	return $blocks;
}

/**
 * Once after the update: the posts with a Footer sito (the footers of the Theme Builder, pages, Divi Library).
 */
function tp_child_migrate_footer_characters_068(): void {
	$flag = 'tp_child_footer_characters_068_v1';

	if ( get_option( $flag ) ) {
		return;
	}

	global $wpdb;

	$ids     = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_content LIKE '%wp:tp/theme-footer%' AND post_status NOT IN ('trash','auto-draft','inherit')" );
	$updated = false;

	foreach ( array_map( 'intval', (array) $ids ) as $id ) {
		$changed = false;
		$blocks  = tp_child_footer_characters_blocks( parse_blocks( (string) get_post_field( 'post_content', $id ) ), $changed );

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
add_action( 'init', 'tp_child_migrate_footer_characters_068', 50 );
