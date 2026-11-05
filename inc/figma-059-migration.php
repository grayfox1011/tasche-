<?php
/**
 * The contents of the new Figma (0.5.9): the words, photos and dishes the designer changed, once, on the
 * home page and on the header of the Theme Builder.
 *
 * Every change is made only where the content is still the one the theme put there: a text, a price or a
 * photo changed by hand in Divi stays as it is (WordPress keeps the previous content as a revision). The
 * same transform makes the content of "Crea/aggiorna home" (inc/seed/home-content.php), so a new site and
 * an updated one are the same.
 *
 * - Hero: "Scopri il menù" and "Ne abbiamo le tasche piene degli “assaggini”" under the title.
 * - Striscia: five phrases ("Acciughe di Luciano", without "Gastronomia. Genova." and "Fatto nel
 *   laboratorio, ogni mattina").
 * - Introduzione: the photo of Alberto at work and the three paragraphs of the Figma.
 * - Alberto: the photos of the pot and of Alberto with the tomatoes.
 * - Tasca o piatto: the title without the full stops.
 * - Menù: the title "Il nostro menu’", the legend with Regular and Large, the dishes, allergens and prices
 *   of the Figma (no S size), an English line under each dish, Vitello tonnato instead of the salsiccia, the
 *   "Proposta del mese" row, "Contorni", "Verdure del giorno", the drinks in the Figma's order.
 * - Materie prime: each card with the photo of its ingredient.
 * - Dove siamo: the photo by the lions of San Lorenzo instead of the map, the title on three lines, the
 *   address "Via Di Scurreria 42R".
 * - Header: the links "Chi siamo", "Menù", "Ingredienti", "Come mangi", "Dove trovarci", the logo on one line
 *   and the grey of the Figma for the links.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The block comments as WordPress's own parser reads them (class-wp-block-parser.php).
 */
function tp_child_f59_block_pattern(): string {
	return '#<!--\s+(/)?wp:((?:[a-z][a-z0-9_-]*/)?[a-z][a-z0-9_-]*)\s+(?:(\{(?:(?:[^}]+|}+(?=})|(?!}\s+/?-->).)*+)?\})\s+)?(/)?-->#s';
}

/**
 * The blocks of $content, in document order, with where each one starts and ends (the end of its closing
 * comment, or of its own comment when it closes on itself) and the index of its parent.
 */
function tp_child_f59_blocks( string $content ): array {
	if ( ! preg_match_all( tp_child_f59_block_pattern(), $content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE ) ) {
		return [];
	}

	$blocks = [];
	$stack  = [];

	foreach ( $matches as $match ) {
		$offset = $match[0][1];
		$length = strlen( $match[0][0] );

		if ( '/' === ( $match[1][0] ?? '' ) && -1 !== $match[1][1] ) {
			$open = array_pop( $stack );

			if ( null !== $open ) {
				$blocks[ $open ]['end'] = $offset + $length;
			}

			continue;
		}

		$void  = isset( $match[4] ) && '/' === $match[4][0] && -1 !== $match[4][1];
		$json  = isset( $match[3] ) && -1 !== $match[3][1] ? $match[3][0] : '';
		$attrs = '' !== $json ? json_decode( $json ) : new stdClass();

		$blocks[] = [
			'name'     => $match[2][0],
			'start'    => $offset,
			'open_end' => $offset + $length,
			'end'      => $offset + $length,
			'void'     => $void,
			'attrs'    => $attrs instanceof stdClass ? $attrs : new stdClass(),
			'parent'   => empty( $stack ) ? -1 : end( $stack ),
		];

		if ( ! $void ) {
			$stack[] = count( $blocks ) - 1;
		}
	}

	return $blocks;
}

/**
 * A value in the decoded attributes, by path ("content.innerContent.desktop.value"); null when missing.
 */
function tp_child_f59_get( $node, string $path ) {
	foreach ( explode( '.', $path ) as $key ) {
		if ( $node instanceof stdClass && property_exists( $node, $key ) ) {
			$node = $node->$key;
		} elseif ( is_array( $node ) && array_key_exists( $key, $node ) ) {
			$node = $node[ $key ];
		} else {
			return null;
		}
	}

	return $node;
}

/**
 * $node with $value at $path, the objects on the way created when missing.
 */
function tp_child_f59_set( stdClass $node, string $path, $value ): void {
	$keys = explode( '.', $path );
	$last = array_pop( $keys );

	foreach ( $keys as $key ) {
		if ( ! isset( $node->$key ) || ! $node->$key instanceof stdClass ) {
			$node->$key = new stdClass();
		}

		$node = $node->$key;
	}

	$node->$last = $value;
}

/**
 * The CSS classes of a block (the "CSS Class" field of Divi).
 */
function tp_child_f59_classes( array $block ): array {
	$classes = tp_child_f59_get( $block['attrs'], 'module.advanced.htmlAttributes.desktop.value.class' );

	return is_string( $classes ) ? preg_split( '/\s+/', trim( $classes ), -1, PREG_SPLIT_NO_EMPTY ) : [];
}

function tp_child_f59_has_class( array $block, string $class ): bool {
	return in_array( $class, tp_child_f59_classes( $block ), true );
}

/**
 * Whether block $index lies inside block $ancestor.
 */
function tp_child_f59_inside( array $blocks, int $index, int $ancestor ): bool {
	for ( $parent = $blocks[ $index ]['parent']; $parent >= 0; $parent = $blocks[ $parent ]['parent'] ) {
		if ( $parent === $ancestor ) {
			return true;
		}
	}

	return false;
}

/**
 * The indexes of the blocks with $class, in document order; inside $ancestor when given.
 */
function tp_child_f59_find( array $blocks, string $class, int $ancestor = -1 ): array {
	$found = [];

	foreach ( $blocks as $index => $block ) {
		if ( tp_child_f59_has_class( $block, $class ) && ( $ancestor < 0 || tp_child_f59_inside( $blocks, $index, $ancestor ) ) ) {
			$found[] = $index;
		}
	}

	return $found;
}

/**
 * The opening comment of a block with these attributes, serialized as WordPress does.
 */
function tp_child_f59_opener( array $block, stdClass $attrs ): string {
	return '<!-- wp:' . $block['name'] . ' ' . serialize_block_attributes( $attrs ) . ( $block['void'] ? ' /-->' : ' -->' );
}

/**
 * $content with the opening comment of $block rewritten from its (changed) attributes.
 */
function tp_child_f59_save( string $content, array $block ): string {
	return substr_replace( $content, tp_child_f59_opener( $block, $block['attrs'] ), $block['start'], $block['open_end'] - $block['start'] );
}

/**
 * The text of a module (the HTML of a Text module, the title of a Heading, the phrase of a marquee item).
 */
function tp_child_f59_text_path( array $block ): string {
	if ( 'divi/heading' === $block['name'] ) {
		return 'title.innerContent.desktop.value';
	}

	if ( 'tp/marquee-item' === $block['name'] ) {
		return 'phrase.innerContent.desktop.value';
	}

	if ( 'divi/button' === $block['name'] ) {
		return 'button.innerContent.desktop.value.text';
	}

	return 'content.innerContent.desktop.value';
}

function tp_child_f59_text( array $block ): string {
	$text = tp_child_f59_get( $block['attrs'], tp_child_f59_text_path( $block ) );

	return is_string( $text ) ? $text : '';
}

/**
 * $content with the text of block $index changed from $from to $to; when the text is not $from (changed
 * by hand), as it is. $from may be a list of the texts the theme wrote over time.
 */
function tp_child_f59_swap_text( string $content, array $blocks, int $index, $from, string $to ): string {
	$block = $blocks[ $index ];

	if ( ! in_array( tp_child_f59_text( $block ), (array) $from, true ) ) {
		return $content;
	}

	tp_child_f59_set( $block['attrs'], tp_child_f59_text_path( $block ), $to );

	return tp_child_f59_save( $content, $block );
}

/**
 * $content with the admin label of block $index changed from $from to $to (a label written by hand stays).
 */
function tp_child_f59_swap_label( string $content, array $blocks, int $index, string $from, string $to ): string {
	$block = $blocks[ $index ];

	if ( $from !== tp_child_f59_get( $block['attrs'], 'module.meta.adminLabel.desktop.value' ) ) {
		return $content;
	}

	tp_child_f59_set( $block['attrs'], 'module.meta.adminLabel.desktop.value', $to );

	return tp_child_f59_save( $content, $block );
}

/**
 * The file name of the photo of an Image module.
 */
function tp_child_f59_image_file( array $block ): string {
	$src = tp_child_f59_get( $block['attrs'], 'image.innerContent.desktop.value.src' );

	return is_string( $src ) ? wp_basename( (string) wp_parse_url( $src, PHP_URL_PATH ) ) : '';
}

/**
 * The media of a theme asset: in the media library (imported once, as "Crea/aggiorna home" does), or the
 * placeholder of the seed content when the transform makes it.
 */
function tp_child_f59_media( string $relative_path ): array {
	if ( ! empty( $GLOBALS['tp_child_f59_seed_mode'] ) ) {
		return [ 'url' => '{{TP_CHILD_URL}}' . $relative_path, 'id' => '' ];
	}

	$asset = function_exists( 'tp_child_seed_import_media_asset' ) ? tp_child_seed_import_media_asset( $relative_path, true ) : [];

	return ! empty( $asset['url'] )
		? [ 'url' => (string) $asset['url'], 'id' => (string) (int) ( $asset['id'] ?? 0 ) ]
		: [ 'url' => TP_CHILD_URL . $relative_path, 'id' => '' ];
}

/**
 * $content with the photo of Image module $index changed to $relative_path (a theme asset), when it is still
 * $from_file; $alt and $title replace the texts of the photo the theme wrote.
 */
function tp_child_f59_swap_image( string $content, array $blocks, int $index, string $from_file, string $relative_path, string $alt, ?string $title = null ): string {
	$block = $blocks[ $index ];

	if ( $from_file !== tp_child_f59_image_file( $block ) ) {
		return $content;
	}

	$media = tp_child_f59_media( $relative_path );
	$value = tp_child_f59_get( $block['attrs'], 'image.innerContent.desktop.value' );
	$value = $value instanceof stdClass ? $value : new stdClass();
	$old   = clone $value;

	$value->src = $media['url'];

	if ( '' !== $media['id'] ) {
		$value->id = $media['id'];
	} else {
		unset( $value->id );
	}

	$value->alt = $alt;

	if ( isset( $value->titleText ) || null !== $title ) {
		$value->titleText = (string) $title;
	}

	tp_child_f59_set( $block['attrs'], 'image.innerContent.desktop.value', $value );

	// The title and alt attributes the theme wrote for the old photo (Avanzate > Attributi).
	foreach ( [ 'module.decoration.attributes.attributes', 'module.decoration.attributes.desktop.value.attributes' ] as $path ) {
		$list = tp_child_f59_get( $block['attrs'], $path );

		foreach ( is_array( $list ) ? $list : [] as $entry ) {
			if ( ! $entry instanceof stdClass ) {
				continue;
			}

			if ( 'alt' === ( $entry->name ?? '' ) && ( $entry->value ?? null ) === ( $old->alt ?? null ) ) {
				$entry->value = $alt;
			}

			if ( 'title' === ( $entry->name ?? '' ) && ( $entry->value ?? null ) === ( $old->titleText ?? null ) ) {
				$entry->value = (string) $title;
			}
		}
	}

	return tp_child_f59_save( $content, $block );
}

/**
 * A self-closing Divi Text module with $class, $html and $admin_label, as the theme writes them.
 */
function tp_child_f59_text_block( string $class, string $html, string $admin_label, string $builder_version ): string {
	$attrs = (object) [
		'module'         => (object) [
			'advanced' => (object) [ 'htmlAttributes' => (object) [ 'desktop' => (object) [ 'value' => (object) [ 'class' => $class ] ] ] ],
			'meta'     => (object) [ 'adminLabel' => (object) [ 'desktop' => (object) [ 'value' => $admin_label ] ] ],
		],
		'content'        => (object) [ 'innerContent' => (object) [ 'desktop' => (object) [ 'value' => $html ] ] ],
		'builderVersion' => $builder_version,
	];

	return '<!-- wp:divi/text ' . serialize_block_attributes( $attrs ) . ' /-->';
}

/**
 * The texts of the home as the theme wrote them before 0.5.9 and as the Figma has them now.
 */
function tp_child_f59_hero_subline(): array {
	return [ 'Siamo in Via di Scurreria 42/R<br>Nei vicoli zenesi', 'Ne abbiamo le tasche piene<br>degli “assaggini”' ];
}

function tp_child_f59_marquee_phrases(): array {
	return [
		[ 'Stocco ogni giovedì', 'Trippa ogni venerdì', 'Polpette al sugo', 'Le acciughe di Luciano', 'Tasche piene di cucinato', 'Gastronomia. Genova.', 'Fatto nel laboratorio, ogni mattina' ],
		[ 'Stocco ogni giovedì', 'Trippa ogni venerdì', 'Polpette al sugo', 'Acciughe di Luciano', 'Tasche piene di cucinato' ],
	];
}

function tp_child_f59_intro_copy(): array {
	return [
		"<p>Un’idea di Davide e Alberto, imprenditori genovesi che di stare con le mani in mano non sono capaci.</p>\n<p>Genova ha un centro storico che molte città si sognano. Ogni anno diventa più bella e più curata… ma un po’ meno sé stessa. I locali si moltiplicano, le insegne si assomigliano tutte, i menù cominciano sempre con «street food» e finiscono con «concept». Di quella musica lì, ne abbiamo le tasche piene.</p>\n<p>Alberto ha la testa tra gli ingredienti da che si ricorda. Al mercato conosce il nome dei produttori, si ferma a parlare, torna con qualcosa di bello e ci costruisce un piatto.</p>",
		"<p>Genova ha un centro storico che molte città si sognano. Ogni anno diventa più bella e più curata… ma un po’ meno sé stessa. I locali si moltiplicano, le insegne si assomigliano tutte, i menù cominciano sempre con «street food» e finiscono con «concept». Di quella musica lì, ne abbiamo le tasche piene.</p>\n<p>Alberto ha la testa tra gli ingredienti da che si ricorda. Al mercato conosce il nome dei produttori, si ferma a parlare, torna con qualcosa di bello e ci costruisce un piatto.</p>\n<p>Tasche Piene è una gastronomia. Di quelle vere: banco, vaschette, cucinato pronto, odore di sugo. Tutto fatto nel nostro laboratorio, ogni mattina. Non deleghiamo, non compriamo cose pronte da riscaldare. Se c’è lo stoccafisso il giovedì, è perché qualcuno l’ha messo sul fuoco il giorno prima.</p>",
	];
}

/**
 * The dishes of the menu: the name before 0.5.9 => [ name, allergens before, allergens, description before,
 * description, Tasca before, Tasca, S before (removed), M before, M (Regular), L before, L (Large),
 * photo before, photo ]. A null photo keeps the photo of the row.
 */
function tp_child_f59_dishes(): array {
	return [
		'MELANZANA ALLA PARMIGIANA'                => [ 'MELANZANE ALLA PARMIGIANA', '7,8', '7,8', 'Ripieni liguri stagionali. Cambia con la stagione', 'baked layers of eggplant, tomato and cheese', '5,50€', '6,00€', '', '10,50€', '7,00€', '14,50€', '10,00€', null, null ],
		'RIPIENI LIGURI DI STAGIONE'               => [ 'RIPIENI ALLA GENOVESE', '1,3,7,12', '1,3,7,12', 'Zucchina, cipolla, peperone. Cambia con la stagione', 'genovese-style stuffed vegetables', '5,50€', '6,00€', '7,00€', '10,50€', '7,00€', '14,50€', '10,00€', null, null ],
		'POLPETTE AL SUGO'                         => [ 'POLPETTE AL SUGO', '1,3,7', '1,3,7', 'Tre polpette, circa 100 g, ricetta nostra', 'meatballs in tomato sauce', '6,50€', '6,00€', '8,00€', '12,00€', '7,00€', '16,50€', '10,00€', null, null ],
		'POLLO ALLA LIGURE, OLIVE TAGGIASCHE, PINOLI' => [ 'POLLO ALLA LIGURE', '8,12', '8,12', 'Pollo doc, olive della Riviera e pinoli', 'ligurian-style chicken with olives and pine nuts', '6,50€', '6,00€', '8,00€', '12,00€', '7,00€', '16,50€', '10,00€', 'flow-menu-pollo.webp', 'assets/images/dishes/flow-menu-pollo-ligure.webp' ],
		'TRIPPA - OGNI VENERDI'                    => [ 'TRIPPA IN UMIDO ALLA GENOVESE (di Venerdì)', '8,9,12', '8,9,12', 'Quella genovese. Max 10-12 porzioni', 'traditional slow-cooked beef tripe stew', '7,50€', '6,50€', '9,00€', '13,50€', '7,50€', '18,00€', '10,50€', null, null ],
		'STOCCAFISSO - OGNI GIOVEDI'               => [ 'STOCCAFISSO ACCOMODATO (di Giovedì)', '4,8,9,12', '4,8,9,12', 'Ricetta tradizionale ligure. Max 10-12 porzioni', 'ligurian-style stockfish stew (thursday)', '7,50€', '6,50€', '9,00€', '13,50€', '7,50€', '18,00€', '10,50€', null, null ],
		'CAPRESE, PESTO GENOVESE, BUFALA'          => [ 'POMODORO, MOZZARELLA, PESTO', '7,8', '7,8', 'Pomodoro fresco, stracciatella e pesto', 'confit tomato, mozzarella, basil pesto', '5,50€', '5,50€', '7,00€', '10,50€', '6,00€', '14,50€', '9,00€', null, null ],
		'LE ACCIUGHE DI LUCIANO + STRACCIATELLA'   => [ 'ACCIUGHE SOTT’OLIO, STRACCIATELLA', '4,7', '4,7', 'Olive, capperi e ricetta di Luciano', 'anchovies in oil with stracciatella cheese', '6,50€', '5,50€', '8,00€', '12,00€', '6,00€', '16,50€', '9,00€', null, null ],
		'SALSICCIA, ALLORO E POMODORI CONFIT'      => [ 'VITELLO TONNATO', '1,7', '3,4,7', 'IN ROTAZIONE · PULLED PORK, CAVOLO VIOLA E MAYO SPICY', 'sliced veal with creamy tuna dressing', '7,50€', '6,50€', '9,00€', '13,50€', '7,50€', '18,00€', '10,50€', 'flow-menu-proposta-mese.webp', 'assets/images/dishes/flow-menu-vitello-tonnato.webp' ],
	];
}

/**
 * The rows of contorni, dolci and bevande: the name before => [ name, the English line under it ].
 */
function tp_child_f59_side_rows(): array {
	return [
		'PATATE AL FORNO'         => [ 'PATATE AL FORNO', 'ROASTED POTATOES' ],
		'VERDURE AL FORNO'        => [ 'VERDURE DEL GIORNO', 'VEGETABLE OF THE DAY' ],
		'TIRAMISÙ IN TASCA'       => [ 'TIRAMISÙ IN TASCA', 'FILLED WITH FRESH TIRAMISÙ CREAM' ],
		'BEVANDE'                 => [ 'BIBITE', '' ],
		'SPUMETTA (AL BICCHIERE)' => [ 'SPUMETTA (A BICCHIERE)', '' ],
	];
}

/**
 * The name of a menu row (its Nome heading), as written.
 */
function tp_child_f59_row_name( array $blocks, int $row ): string {
	foreach ( tp_child_f59_find( $blocks, 'tp-menu-name', $row ) as $name ) {
		return trim( tp_child_f59_text( $blocks[ $name ] ) );
	}

	return '';
}

/**
 * The indexes of the rows of a menu table (tp-menu-table--main, --sides...), in order.
 */
function tp_child_f59_rows( array $blocks, string $table ): array {
	$tables = tp_child_f59_find( $blocks, $table );

	return $tables ? tp_child_f59_find( $blocks, 'tp-menu-row', $tables[0] ) : [];
}

/**
 * One change of the main menu, applied to the first row named $old: name, allergens, description and
 * prices of the Figma where the row still has the theme's, the S size out, the photo of the Figma.
 */
function tp_child_f59_dish( string $content, string $old, array $dish ): string {
	list( $name, $allergens_from, $allergens, $meta_from, $meta, $tasca_from, $tasca, $s_from, $m_from, $m, $l_from, $l, $photo_from, $photo ) = $dish;

	$blocks = tp_child_f59_blocks( $content );
	$row    = null;

	foreach ( tp_child_f59_rows( $blocks, 'tp-menu-table--main' ) as $index ) {
		if ( strtoupper( tp_child_f59_row_name( $blocks, $index ) ) === $old ) {
			$row = $index;
			break;
		}
	}

	if ( null === $row ) {
		return $content;
	}

	// From the end of the row to its start, so the offsets of the blocks still to change stay right.
	$steps = [
		[ 'tp-menu-price-value-l', "<p>{$l_from}</p>", "<p>{$l}</p>" ],
		[ 'tp-menu-price-value-m', "<p>{$m_from}</p>", "<p>{$m}</p>" ],
		[ 'tp-menu-price-value-tasca', "<p>{$tasca_from}</p>", "<p>{$tasca}</p>" ],
		[ 'tp-menu-meta', "<span>{$meta_from}</span>", "<span>{$meta}</span>" ],
		[ 'tp-menu-allergeni', "<p>{$allergens_from}</p>", "<p>{$allergens}</p>" ],
	];

	foreach ( $steps as list( $class, $from, $to ) ) {
		foreach ( tp_child_f59_find( $blocks, $class, $row ) as $index ) {
			$content = tp_child_f59_swap_text( $content, $blocks, $index, $from, $to );
		}
	}

	$blocks = tp_child_f59_blocks( $content );
	$row    = tp_child_f59_row_by_name( $blocks, $old );

	if ( null !== $row && null !== $photo_from ) {
		foreach ( tp_child_f59_find( $blocks, 'tp-menu-row-image', $row ) as $index ) {
			$content = tp_child_f59_swap_image( $content, $blocks, $index, $photo_from, $photo, '' );
		}
	}

	// The S size: label and price go when the price is still the theme's.
	$blocks = tp_child_f59_blocks( $content );
	$row    = tp_child_f59_row_by_name( $blocks, $old );

	if ( null !== $row && '' !== $s_from ) {
		$price = tp_child_f59_find( $blocks, 'tp-menu-price-value-s', $row );
		$label = tp_child_f59_find( $blocks, 'tp-menu-label-s', $row );

		if ( $price && $label && tp_child_f59_text( $blocks[ $price[0] ] ) === "<p>{$s_from}</p>" ) {
			foreach ( [ $price[0], $label[0] ] as $index ) {
				$block   = $blocks[ $index ];
				$end     = $block['end'];
				// The line break after the block goes with it.
				$end    += strspn( $content, "\n", $end );
				$content = substr_replace( $content, '', $block['start'], $end - $block['start'] );
			}
		}
	}

	// The name last: the row is found by its old name until here.
	$blocks = tp_child_f59_blocks( $content );
	$row    = tp_child_f59_row_by_name( $blocks, $old );

	if ( null !== $row ) {
		foreach ( tp_child_f59_find( $blocks, 'tp-menu-name', $row ) as $index ) {
			$current = tp_child_f59_text( $blocks[ $index ] );

			if ( strtoupper( trim( $current ) ) === $old ) {
				$content = tp_child_f59_swap_text( $content, $blocks, $index, $current, $name );
			}
		}
	}

	return $content;
}

function tp_child_f59_row_by_name( array $blocks, string $name ): ?int {
	foreach ( tp_child_f59_rows( $blocks, 'tp-menu-table--main' ) as $index ) {
		if ( strtoupper( tp_child_f59_row_name( $blocks, $index ) ) === $name ) {
			return $index;
		}
	}

	return null;
}

/**
 * The "Proposta del mese" row of the Figma after Vitello tonnato: a copy of that row with the name, the
 * English line, the Tasca price "6-8€" only and the photo of the proposal. Only on a menu the transform
 * has just brought to the Figma's dishes, and only once.
 */
function tp_child_f59_add_proposal_row( string $content ): string {
	$blocks = tp_child_f59_blocks( $content );

	if ( null !== tp_child_f59_row_by_name( $blocks, 'PROPOSTA DEL MESE' ) ) {
		return $content;
	}

	$row = tp_child_f59_row_by_name( $blocks, 'VITELLO TONNATO' );

	if ( null === $row ) {
		return $content;
	}

	$copy        = substr( $content, $blocks[ $row ]['start'], $blocks[ $row ]['end'] - $blocks[ $row ]['start'] );
	$copy_blocks = tp_child_f59_blocks( $copy );
	$edits       = [];

	foreach ( $copy_blocks as $index => $block ) {
		$classes = tp_child_f59_classes( $block );

		if ( in_array( 'tp-menu-row', $classes, true ) && -1 === $block['parent'] ) {
			tp_child_f59_set( $block['attrs'], 'module.advanced.htmlAttributes.desktop.value.class', 'tp-menu-row tp-menu-row--proposal' );
			tp_child_f59_set( $block['attrs'], 'module.meta.adminLabel.desktop.value', 'Piatto: Proposta del mese' );
			$edits[] = [ $block['start'], $block['open_end'], tp_child_f59_opener( $block, $block['attrs'] ) ];
		} elseif ( in_array( 'tp-menu-name', $classes, true ) ) {
			tp_child_f59_set( $block['attrs'], 'title.innerContent.desktop.value', 'PROPOSTA DEL MESE' );
			$edits[] = [ $block['start'], $block['open_end'], tp_child_f59_opener( $block, $block['attrs'] ) ];
		} elseif ( in_array( 'tp-menu-meta', $classes, true ) ) {
			tp_child_f59_set( $block['attrs'], 'content.innerContent.desktop.value', '<span>monthly special</span>' );
			$edits[] = [ $block['start'], $block['open_end'], tp_child_f59_opener( $block, $block['attrs'] ) ];
		} elseif ( in_array( 'tp-menu-price-value-tasca', $classes, true ) ) {
			tp_child_f59_set( $block['attrs'], 'content.innerContent.desktop.value', '<p>6-8€</p>' );
			$edits[] = [ $block['start'], $block['open_end'], tp_child_f59_opener( $block, $block['attrs'] ) ];
		} elseif ( in_array( 'tp-menu-row-image', $classes, true ) ) {
			$media = tp_child_f59_media( 'assets/images/dishes/flow-menu-proposta-mese.webp' );
			$value = (object) [ 'src' => $media['url'], 'alt' => '' ];

			if ( '' !== $media['id'] ) {
				$value = (object) [ 'src' => $media['url'], 'id' => $media['id'], 'alt' => '' ];
			}

			tp_child_f59_set( $block['attrs'], 'image.innerContent.desktop.value', $value );
			$edits[] = [ $block['start'], $block['open_end'], tp_child_f59_opener( $block, $block['attrs'] ) ];
		} elseif ( array_intersect( [ 'tp-menu-allergeni', 'tp-menu-label-piatto', 'tp-menu-label-m', 'tp-menu-price-value-m', 'tp-menu-label-l', 'tp-menu-price-value-l' ], $classes ) ) {
			// No allergens and no plate in the Figma: the block goes, with the line break after it.
			$end     = $block['end'] + strspn( $copy, "\n", $block['end'] );
			$edits[] = [ $block['start'], $end, '' ];
		}
	}

	usort(
		$edits,
		static function ( array $a, array $b ): int {
			return $b[0] <=> $a[0];
		}
	);

	foreach ( $edits as list( $from, $to, $with ) ) {
		$copy = substr_replace( $copy, $with, $from, $to - $from );
	}

	return substr_replace( $content, "\n\n" . $copy, $blocks[ $row ]['end'], 0 );
}

/**
 * A row of contorni, dolci or bevande: the Figma's name, and its English line (a Descrizione module after
 * the name, as the dishes have) when the row has none.
 */
function tp_child_f59_side_row( string $content, string $table, string $old, string $name, string $meta ): string {
	$blocks = tp_child_f59_blocks( $content );

	foreach ( tp_child_f59_rows( $blocks, $table ) as $row ) {
		if ( strtoupper( tp_child_f59_row_name( $blocks, $row ) ) !== $old ) {
			continue;
		}

		$names = tp_child_f59_find( $blocks, 'tp-menu-name', $row );

		if ( ! $names ) {
			return $content;
		}

		if ( '' !== $meta && ! tp_child_f59_find( $blocks, 'tp-menu-meta', $row ) ) {
			$version = (string) ( $blocks[ $names[0] ]['attrs']->builderVersion ?? '5.2.0' );
			$content = substr_replace( $content, "\n" . tp_child_f59_text_block( 'tp-menu-meta', '<span>' . esc_html( $meta ) . '</span>', 'Descrizione', $version ), $blocks[ $names[0] ]['end'], 0 );
			$blocks  = tp_child_f59_blocks( $content );
			$row     = tp_child_f59_side_row_index( $blocks, $table, $old );

			if ( $row < 0 ) {
				return $content;
			}

			$names = tp_child_f59_find( $blocks, 'tp-menu-name', $row );
		}

		$current = tp_child_f59_text( $blocks[ $names[0] ] );

		return strtoupper( trim( $current ) ) === $old ? tp_child_f59_swap_text( $content, $blocks, $names[0], $current, $name ) : $content;
	}

	return $content;
}

function tp_child_f59_side_row_index( array $blocks, string $table, string $name ): int {
	foreach ( tp_child_f59_rows( $blocks, $table ) as $row ) {
		if ( strtoupper( tp_child_f59_row_name( $blocks, $row ) ) === $name ) {
			return $row;
		}
	}

	return -1;
}

/**
 * The drinks in the Figma's order (Bibite, Spumetta, Acqua): Acqua goes last when it is still first.
 */
function tp_child_f59_drinks_order( string $content ): string {
	$blocks = tp_child_f59_blocks( $content );
	$rows   = tp_child_f59_rows( $blocks, 'tp-menu-table--drinks' );
	$names  = array_map(
		static function ( int $row ) use ( $blocks ): string {
			return strtoupper( tp_child_f59_row_name( $blocks, $row ) );
		},
		$rows
	);

	if ( [ 'ACQUA', 'BIBITE', 'SPUMETTA (A BICCHIERE)' ] !== $names ) {
		return $content;
	}

	list( $acqua, $bibite, $spumetta ) = array_map(
		static function ( int $row ) use ( $blocks ): array {
			return $blocks[ $row ];
		},
		$rows
	);

	$part = static function ( int $from, int $to ) use ( $content ): string {
		return substr( $content, $from, $to - $from );
	};

	// The same text, the rows in the new order and the blank lines between them where they were.
	$reordered = $part( $bibite['start'], $bibite['end'] ) . $part( $acqua['end'], $bibite['start'] )
		. $part( $spumetta['start'], $spumetta['end'] ) . $part( $bibite['end'], $spumetta['start'] )
		. $part( $acqua['start'], $acqua['end'] );

	return substr_replace( $content, $reordered, $acqua['start'], $spumetta['end'] - $acqua['start'] );
}

/**
 * The home content with the texts, photos and dishes of the new Figma where it still has the theme's.
 */
function tp_child_f59_home_transform( string $content ): string {
	if ( false === strpos( $content, 'tp-menu-section' ) && false === strpos( $content, 'tp-hero-section' ) ) {
		return $content;
	}

	// One change at a time, each on the content as the previous one left it.
	$changes = [
		// Hero.
		static function ( string $c, array $b ): string {
			foreach ( tp_child_f59_find( $b, 'tp-hero-button' ) as $i ) {
				$c = tp_child_f59_swap_text( $c, $b, $i, 'Guarda il menù', 'Scopri il menù' );
			}

			return $c;
		},
		static function ( string $c, array $b ): string {
			list( $from, $to ) = tp_child_f59_hero_subline();

			foreach ( tp_child_f59_find( $b, 'tp-hero-subline' ) as $i ) {
				$c = tp_child_f59_swap_text( $c, $b, $i, $from, $to );
			}

			return $c;
		},
		static function ( string $c, array $b ): string {
			foreach ( tp_child_f59_find( $b, 'tp-hero-subline' ) as $i ) {
				$c = tp_child_f59_swap_label( $c, $b, $i, 'Indirizzo', 'Sottotitolo' );
			}

			return $c;
		},
		// Striscia: the five phrases, when the seven of the theme are all there in their order.
		static function ( string $c, array $b ): string {
			list( $from, $to ) = tp_child_f59_marquee_phrases();

			foreach ( tp_child_f59_find( $b, 'tp-marquee-module' ) as $marquee ) {
				$items = [];

				foreach ( $b as $i => $block ) {
					if ( 'tp/marquee-item' === $block['name'] && $marquee === $block['parent'] ) {
						$items[] = $i;
					}
				}

				$phrases = array_map(
					static function ( int $i ) use ( $b ): string {
						return tp_child_f59_text( $b[ $i ] );
					},
					$items
				);

				if ( $phrases !== $from ) {
					continue;
				}

				// From the last item to the first.
				foreach ( array_reverse( $items, true ) as $n => $i ) {
					$block = $b[ $i ];

					if ( ! in_array( $from[ $n ], $to, true ) && 'Le acciughe di Luciano' !== $from[ $n ] ) {
						$end = $block['end'] + strspn( $c, "\n", $block['end'] );
						$c   = substr_replace( $c, '', $block['start'], $end - $block['start'] );
					} elseif ( 'Le acciughe di Luciano' === $from[ $n ] ) {
						tp_child_f59_set( $block['attrs'], 'phrase.innerContent.desktop.value', 'Acciughe di Luciano' );

						if ( 'Scritta: Le acciughe di Luciano' === tp_child_f59_get( $block['attrs'], 'module.meta.adminLabel.desktop.value' ) ) {
							tp_child_f59_set( $block['attrs'], 'module.meta.adminLabel.desktop.value', 'Scritta: Acciughe di Luciano' );
						}

						$c = tp_child_f59_save( $c, $block );
					}
				}

				// Removing items can leave the last one followed by an empty line before the closing comment.
				$c = preg_replace( '#(/-->)\n\n(<!-- /wp:tp/marquee -->)#', "$1\n$2", $c ) ?? $c;
			}

			return $c;
		},
		// Introduzione.
		static function ( string $c, array $b ): string {
			foreach ( tp_child_f59_find( $b, 'tp-intro-image' ) as $i ) {
				$c = tp_child_f59_swap_image( $c, $b, $i, 'map-decorativa.webp', 'assets/images/intro/fig-intro-alberto-impasto.webp', 'Alberto prepara il ripieno nel laboratorio di Tasche Piene', 'Alberto al lavoro' );
			}

			return $c;
		},
		static function ( string $c, array $b ): string {
			foreach ( tp_child_f59_find( $b, 'tp-intro-image' ) as $i ) {
				$c = tp_child_f59_swap_label( $c, $b, $i, 'Mappa', 'Foto' );
			}

			return $c;
		},
		static function ( string $c, array $b ): string {
			foreach ( tp_child_f59_find( $b, 'tp-intro-media-column' ) as $i ) {
				$c = tp_child_f59_swap_label( $c, $b, $i, 'Colonna mappa', 'Colonna foto' );
			}

			return $c;
		},
		static function ( string $c, array $b ): string {
			list( $from, $to ) = tp_child_f59_intro_copy();

			foreach ( tp_child_f59_find( $b, 'tp-intro-copy' ) as $i ) {
				$c = tp_child_f59_swap_text( $c, $b, $i, $from, $to );
			}

			return $c;
		},
		// Alberto: the big photo is the pot, the small one Alberto with the tomatoes.
		static function ( string $c, array $b ): string {
			foreach ( tp_child_f59_find( $b, 'tp-alberto-photo-main' ) as $i ) {
				$c = tp_child_f59_swap_image( $c, $b, $i, 'fig-intro-caruggi.webp', 'assets/images/intro/fig-alberto-pentola.webp', 'Il ripieno preparato nella pentola' );
			}

			return $c;
		},
		static function ( string $c, array $b ): string {
			foreach ( tp_child_f59_find( $b, 'tp-alberto-photo-sub' ) as $i ) {
				$c = tp_child_f59_swap_image( $c, $b, $i, 'fig-intro-melina-kiefer.webp', 'assets/images/dishes/flow-menu-pomodoro-mozzarella-pesto.webp', 'Alberto con una teglia di pomodori' );
			}

			return $c;
		},
		// Tasca o piatto: the title without full stops; the section can be reached from "Come mangi".
		static function ( string $c, array $b ): string {
			foreach ( tp_child_f59_find( $b, 'tp-scelta-titolo' ) as $i ) {
				$c = tp_child_f59_swap_text( $c, $b, $i, [ '<h2>Tasca o piatto.<br />Mangi qui o a casa.<br />Sta a te.</h2>', '<h2>Tasca o piatto.<br>Mangi qui o a casa.<br>Sta a te.</h2>' ], '<h2>Tasca o piatto<br />Mangi qui o a casa<br />Sta a te</h2>' );
			}

			return $c;
		},
		static function ( string $c, array $b ): string {
			foreach ( tp_child_f59_find( $b, 'tp-scelta' ) as $i ) {
				if ( 'divi/section' === $b[ $i ]['name'] && null === tp_child_f59_get( $b[ $i ]['attrs'], 'module.advanced.htmlAttributes.desktop.value.id' ) ) {
					$block = $b[ $i ];
					tp_child_f59_set( $block['attrs'], 'module.advanced.htmlAttributes.desktop.value.id', 'come-mangi' );
					$c = tp_child_f59_save( $c, $block );
				}
			}

			return $c;
		},
		// Menù: title and legend.
		static function ( string $c, array $b ): string {
			foreach ( tp_child_f59_find( $b, 'tp-menu-title' ) as $i ) {
				$c = tp_child_f59_swap_text( $c, $b, $i, '<h2>Il nostro menù</h2>', '<h2>Il nostro menu’</h2>' );
			}

			return $c;
		},
		static function ( string $c, array $b ): string {
			foreach ( tp_child_f59_find( $b, 'tp-menu-legend-tasca' ) as $i ) {
				$c = tp_child_f59_swap_text( $c, $b, $i, '<h4>TASCA</h4><p>IL PANINO</p>', '<h4>TASCA</h4><p>Il panino</p>' );
			}

			return $c;
		},
		static function ( string $c, array $b ): string {
			foreach ( tp_child_f59_find( $b, 'tp-menu-legend-piatto' ) as $i ) {
				$c = tp_child_f59_swap_text( $c, $b, $i, '<h4>PIATTO</h4><p>S - 150g</p><p>M - 200g</p><p>L - 400g</p>', '<h4>PIATTO</h4><p>Regular - 200g</p><p>Large - 400g</p>' );
			}

			return $c;
		},
	];

	foreach ( $changes as $change ) {
		$content = $change( $content, tp_child_f59_blocks( $content ) );
	}

	// The dishes.
	foreach ( tp_child_f59_dishes() as $old => $dish ) {
		$content = tp_child_f59_dish( $content, $old, $dish );
	}

	$content = tp_child_f59_add_proposal_row( $content );

	// Contorni, dolci, bevande.
	$heads = [
		[ 'tp-menu-sectionhead-title', 'I nostri contorni', 'Contorni' ],
		[ 'tp-menu-sectionhead-subtitle', '<p>Cosa ci vuoi vicino?</p>', '<p>Cosa vuoi vicino?</p>' ],
	];

	foreach ( $heads as list( $class, $from, $to ) ) {
		$blocks = tp_child_f59_blocks( $content );

		foreach ( tp_child_f59_find( $blocks, $class ) as $i ) {
			$content = tp_child_f59_swap_text( $content, $blocks, $i, $from, $to );
		}
	}

	$tables = [
		'PATATE AL FORNO'         => 'tp-menu-table--sides',
		'VERDURE AL FORNO'        => 'tp-menu-table--sides',
		'TIRAMISÙ IN TASCA'       => 'tp-menu-table--desserts',
		'BEVANDE'                 => 'tp-menu-table--drinks',
		'SPUMETTA (AL BICCHIERE)' => 'tp-menu-table--drinks',
	];

	foreach ( tp_child_f59_side_rows() as $old => list( $name, $meta ) ) {
		$content = tp_child_f59_side_row( $content, $tables[ $old ], $old, $name, $meta );
	}

	$content = tp_child_f59_drinks_order( $content );

	// Materie prime: each card with the photo of its ingredient (card number => [ photo before, photo ]).
	$cards = [
		1 => [ '5277ad78.webp', 'assets/images/materie-prime/e0d905c6.webp', 'Acciughe liguri appena pescate' ],
		2 => [ '5c9cc96f.webp', 'assets/images/materie-prime/83b65fe4.webp', 'Olive taggiasche' ],
		3 => [ '79537578.webp', 'assets/images/materie-prime/5277ad78.webp', 'Stracciatella di bufala' ],
		4 => [ '9d821c0e.webp', 'assets/images/dishes/fig-menu-feature.webp', 'Ripieni di verdure di stagione' ],
		5 => [ 'e0d905c6.webp', 'assets/images/materie-prime/79537578.webp', 'Pollo alla ligure' ],
		6 => [ 'f7325986.webp', 'assets/images/materie-prime/9d821c0e.webp', 'Pomodori dei fornitori scelti' ],
	];

	foreach ( $cards as $number => list( $from, $to, $alt ) ) {
		$blocks = tp_child_f59_blocks( $content );

		foreach ( tp_child_f59_find( $blocks, 'tp-materie-card--' . $number ) as $card ) {
			foreach ( tp_child_f59_find( $blocks, 'tp-materie-card-image', $card ) as $i ) {
				if ( $from === tp_child_f59_image_file( $blocks[ $i ] ) && '' === (string) tp_child_f59_get( $blocks[ $i ]['attrs'], 'image.innerContent.desktop.value.alt' ) ) {
					$content = tp_child_f59_swap_image( $content, $blocks, $i, $from, $to, $alt );
				}
			}
		}
	}

	// Dove siamo.
	$location = [
		static function ( string $c, array $b ): string {
			foreach ( tp_child_f59_find( $b, 'tp-location-map-frame' ) as $i ) {
				$c = tp_child_f59_swap_image( $c, $b, $i, 'map-location-big.webp', 'assets/images/intro/fig-intro-caruggi.webp', 'Tasche Piene davanti ai leoni di San Lorenzo, a Genova', 'Tasche Piene nei caruggi di Genova' );
			}

			return $c;
		},
		static function ( string $c, array $b ): string {
			foreach ( tp_child_f59_find( $b, 'tp-location-map-frame' ) as $i ) {
				$c = tp_child_f59_swap_label( $c, $b, $i, 'Mappa', 'Foto' );
			}

			return $c;
		},
		static function ( string $c, array $b ): string {
			foreach ( tp_child_f59_find( $b, 'tp-location-map-column' ) as $i ) {
				$c = tp_child_f59_swap_label( $c, $b, $i, 'Colonna mappa', 'Colonna foto' );
			}

			return $c;
		},
		static function ( string $c, array $b ): string {
			foreach ( tp_child_f59_find( $b, 'tp-location-title' ) as $i ) {
				$c = tp_child_f59_swap_text( $c, $b, $i, [ '<h2>Siamo nei caruggi.<br>Non troppo nascosti.</h2>', '<h2>Siamo nei caruggi.<br />Non troppo nascosti.</h2>' ], '<h2>Siamo nei caruggi. Non troppo nascosti.</h2>' );
			}

			return $c;
		},
		static function ( string $c, array $b ): string {
			foreach ( tp_child_f59_find( $b, 'tp-location-text' ) as $i ) {
				$c = tp_child_f59_swap_text( $c, $b, $i, '<p>Siamo in Piazza San Lorenzo a due passi dalla Cattedrale.<br>Ci trovi anche su Google Maps, ma lasciando il cellulare in tasca e venendo a piedi si capisce meglio.</p>', '<p>Siamo in Piazza San Lorenzo a due passi dalla Cattedrale.<br>Ci trovi anche su Google Maps, ma lasciando il cellulare in tasca e venendo a piedi si capisce meglio</p>' );
			}

			return $c;
		},
		static function ( string $c, array $b ): string {
			foreach ( tp_child_f59_find( $b, 'tp-location-address' ) as $i ) {
				$c = tp_child_f59_swap_text( $c, $b, $i, '<p>Via di Scurreria 42/R</p>', '<p>Via Di Scurreria 42R</p>' );
			}

			return $c;
		},
	];

	foreach ( $location as $change ) {
		$content = $change( $content, tp_child_f59_blocks( $content ) );
	}

	// The names in the builder follow the new dishes ("Piatto: Vitello tonnato"...).
	return function_exists( 'tp_child_wireframe_labels_transform' ) ? tp_child_wireframe_labels_transform( $content ) : $content;
}

/**
 * The links of the menu at the top before 0.5.9 and in the Figma: [ text, link ].
 */
function tp_child_f59_header_links(): array {
	return [
		[ [ 'Home', '#home' ], [ 'I tramezzini', '#menu' ], [ 'La nostra idea', '#chi-siamo' ], [ 'Zena', '#materie' ], [ 'Dove siamo', '#location' ] ],
		[ [ 'Chi siamo', '#chi-siamo' ], [ 'Menù', '#menu' ], [ 'Ingredienti', '#materie' ], [ 'Come mangi', '#come-mangi' ], [ 'Dove trovarci', '#location' ] ],
	];
}

/**
 * A header layout with the Figma's links, logo and link colour, where it still has the theme's.
 */
function tp_child_f59_header_transform( string $content ): string {
	if ( false === strpos( $content, 'wp:tp/theme-header' ) ) {
		return $content;
	}

	list( $from_links, $to_links ) = tp_child_f59_header_links();

	$blocks = tp_child_f59_blocks( $content );

	foreach ( $blocks as $index => $header ) {
		if ( 'tp/theme-header' !== $header['name'] ) {
			continue;
		}

		$items = [];

		foreach ( $blocks as $i => $block ) {
			if ( 'tp/header-nav-item' === $block['name'] && $index === $block['parent'] ) {
				$items[] = $i;
			}
		}

		$links = array_map(
			static function ( int $i ) use ( $blocks ): array {
				return [
					tp_child_nav_item_value( json_decode( wp_json_encode( $blocks[ $i ]['attrs'] ), true ), 'text', 'Nuova voce' ),
					tp_child_nav_item_value( json_decode( wp_json_encode( $blocks[ $i ]['attrs'] ), true ), 'link', '#home' ),
				];
			},
			$items
		);

		// The Voci, from the last to the first, when they are all the theme's.
		if ( $links === $from_links ) {
			foreach ( array_reverse( $items, true ) as $n => $i ) {
				$block = $blocks[ $i ];
				tp_child_f59_set( $block['attrs'], 'text.innerContent.desktop.value', $to_links[ $n ][0] );
				tp_child_f59_set( $block['attrs'], 'link.innerContent.desktop.value', $to_links[ $n ][1] );

				if ( 'Voce: ' . tp_child_wireframe_plain( $from_links[ $n ][0] ) === tp_child_f59_get( $block['attrs'], 'module.meta.adminLabel.desktop.value' ) ) {
					tp_child_f59_set( $block['attrs'], 'module.meta.adminLabel.desktop.value', 'Voce: ' . tp_child_wireframe_plain( $to_links[ $n ][0] ) );
				}

				$content = tp_child_f59_save( $content, $block );
			}
		}

		// The header itself (its comment comes before the Voci: its offsets did not move).
		$changed = false;

		if ( '#054726' === strtolower( (string) tp_child_f59_get( $header['attrs'], 'navLinkColor.innerContent.desktop.value' ) ) ) {
			tp_child_f59_set( $header['attrs'], 'navLinkColor.innerContent.desktop.value', '#404040' );
			$changed = true;
		}

		foreach ( [ 'logoUrl.innerContent.desktop.value', 'logoImage.innerContent.desktop.value.src' ] as $path ) {
			$url = tp_child_f59_get( $header['attrs'], $path );

			if ( is_string( $url ) && preg_match( '~/assets/svg/logo-tasche-piene-nav\.svg$~', $url ) ) {
				tp_child_f59_set( $header['attrs'], $path, substr( $url, 0, -strlen( 'logo-tasche-piene-nav.svg' ) ) . 'logo-tasche-piene-line.svg' );
				$changed = true;
			}
		}

		if ( $changed ) {
			$content = tp_child_f59_save( $content, $header );
		}

		break;
	}

	return $content;
}

/**
 * The WordPress menu of the header (Aspetto > Menu, used by a Header sito without Voci): the Figma's
 * links where it still has the theme's five.
 */
function tp_child_f59_update_wp_header_menu(): void {
	$locations = get_nav_menu_locations();
	$menu_id   = (int) ( $locations['tp_main_nav'] ?? 0 );
	$items     = $menu_id ? wp_get_nav_menu_items( $menu_id ) : [];

	if ( ! is_array( $items ) || 5 !== count( $items ) ) {
		return;
	}

	list( $from_links, $to_links ) = tp_child_f59_header_links();

	foreach ( array_values( $items ) as $n => $item ) {
		if ( $from_links[ $n ][0] !== $item->title || $from_links[ $n ][1] !== $item->url ) {
			return;
		}
	}

	foreach ( array_values( $items ) as $n => $item ) {
		wp_update_nav_menu_item(
			$menu_id,
			(int) $item->ID,
			[
				'menu-item-title'     => $to_links[ $n ][0],
				'menu-item-url'       => $to_links[ $n ][1],
				'menu-item-type'      => 'custom',
				'menu-item-status'    => 'publish',
				'menu-item-position'  => (int) $item->menu_order,
				'menu-item-parent-id' => (int) $item->menu_item_parent,
			]
		);
	}
}

/**
 * Once: the home, the header layouts of the Theme Builder and the WordPress menu of the header.
 */
function tp_child_migrate_figma_059(): void {
	$flag = 'tp_child_figma_059_v1';

	if ( get_option( $flag ) ) {
		return;
	}

	tp_child_menu_size_s_migrate_home( 'tp_child_figma_059_home_v1', 'tp_child_f59_home_transform' );

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
		$updated = tp_child_f59_header_transform( $content );

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

	tp_child_f59_update_wp_header_menu();

	update_option( $flag, TP_CHILD_VERSION, true );
}

add_action( 'init', 'tp_child_migrate_figma_059', 43 );
