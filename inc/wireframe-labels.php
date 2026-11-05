<?php
/**
 * Names in the Visual Builder: every section, row, column and module of the home gets an admin label that says
 * what it is ("Menù", "Piatto: Polpette al sugo", "Prezzo M", "Foto al passaggio del mouse"...). Divi shows the
 * admin label (Metadata > Etichetta elemento) instead of the module type ("Gruppo", "Testo") in the wireframe
 * view, in the Layers panel and in the settings; on the site nothing changes.
 *
 * The content "Crea/aggiorna home" creates already has the names; the home gets them once, with the same
 * transform. A label the owner wrote stays: only blocks without a label get one, and the theme's earlier names
 * of the sections ("Marquee Verde", "Location"...) become the ones of the guide. The labels that older versions
 * wrote where Divi does not read them (module.advanced.meta) move to module.meta.
 *
 * The names made from the content ("Piatto: ...", "Card: ...", "Giorno: ...", "Scritta: ...", "Voce: ..." for the
 * links of the menu at the top, "Sinistra: ..." and "Destra: ..." for those of the footer) follow it: every
 * time the builder opens a page or the header or footer of the Theme Builder, a dish renamed or copied from
 * another one shows its own name, and a piece added
 * by hand with one of the theme's classes (a photo on the hover of a dish) gets its name; the page keeps them when
 * the owner saves. Divi does the same with its own updates of the content (on load, not on save: the builder
 * checks that the page saved is the one it sent).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Label by CSS class, the most specific first (a module with several classes takes the first that matches).
 */
function tp_child_wireframe_label_map(): array {
	return [
		// Sections.
		'tp-hero-section'                 => 'Hero',
		'tp-marquee-section'              => 'Striscia',
		'tp-intro-section'                => 'Introduzione',
		'tp-alberto-section'              => 'Alberto',
		'tp-scelta'                       => 'Tasca o piatto',
		'tp-menu-section'                 => 'Menù',
		'tp-materie-section'              => 'Materie prime',
		'tp-location-section'             => 'Dove siamo',
		'tp-hours-section'                => 'Orari',
		// Rows.
		'tp-hero-content'                 => 'Riga hero',
		'tp-marquee-row'                  => 'Riga striscia',
		'tp-intro-row'                    => 'Riga introduzione',
		'tp-alberto-row'                  => 'Riga Alberto',
		'tp-scelta-testata'               => 'Titolo e onde',
		'tp-scelta-schede'                => 'Card',
		'tp-menu-wrapper'                 => 'Riga menù',
		'tp-materie-row'                  => 'Riga materie prime',
		'tp-location-row'                 => 'Riga dove siamo',
		'tp-hours-row'                    => 'Riga orari',
		// Columns.
		'tp-intro-media-column'           => 'Colonna foto',
		'tp-intro-copy-column'            => 'Colonna testi',
		'tp-alberto-copy-column'          => 'Colonna testi',
		'tp-alberto-photos-column'        => 'Colonna foto',
		'tp-scelta-scheda--tasca'         => 'Tasca',
		'tp-scelta-scheda--piatto'        => 'Piatto',
		'tp-menu-column'                  => 'Colonna menù',
		'tp-location-map-column'          => 'Colonna foto',
		'tp-location-content-column'      => 'Colonna testi',
		// Hero, striscia, introduzione, Alberto.
		'tp-hero-title'                   => 'Titolo animato',
		'tp-hero-button'                  => 'Pulsante',
		'tp-hero-subline'                 => 'Sottotitolo',
		'tp-marquee-module'               => 'Striscia scorrevole',
		'tp-intro-image'                  => 'Foto',
		'tp-intro-title'                  => 'Titolo',
		'tp-intro-copy'                   => 'Testo',
		'tp-intro-button'                 => 'Pulsante',
		'tp-alberto-title'                => 'Titolo',
		'tp-alberto-copy'                 => 'Testo',
		'tp-alberto-photo-main'           => 'Foto grande',
		'tp-alberto-photo-sub'            => 'Foto piccola',
		// Tasca o piatto.
		'tp-scelta-onda--verde'           => 'Onda verde',
		'tp-scelta-onda--bordeaux'        => 'Onda bordeaux',
		'tp-scelta-titolo'                => 'Titolo',
		'tp-scelta-foto'                  => 'Foto',
		'tp-scelta-testo'                 => 'Testi',
		'tp-scelta-nome'                  => 'Nome',
		'tp-scelta-chip'                  => 'Caratteristiche',
		'tp-scelta-descrizione'           => 'Descrizione',
		// Menù.
		'tp-menu-header'                  => 'Intestazione del menù',
		'tp-menu-title'                   => 'Titolo',
		'tp-menu-legend-tasca'            => 'Legenda Tasca',
		'tp-menu-legend-piatto'           => 'Legenda Piatto',
		'tp-menu-legend'                  => 'Legenda',
		'tp-menu-feature-image--main'     => 'Foto grande',
		'tp-menu-feature-image--desserts' => 'Foto dei dolci',
		'tp-menu-table--main'             => 'Piatti',
		'tp-menu-table--sides'            => 'Contorni',
		'tp-menu-table--desserts'         => 'Dolci',
		'tp-menu-table--drinks'           => 'Bevande',
		'tp-menu-sectionhead--sides'      => 'Titolo dei contorni',
		'tp-menu-sectionhead--desserts'   => 'Titolo dei dolci',
		'tp-menu-sectionhead--drinks'     => 'Titolo delle bevande',
		'tp-menu-sectionhead-title'       => 'Titolo',
		'tp-menu-sectionhead-subtitle'    => 'Sottotitolo',
		'tp-menu-item'                    => 'Nome e descrizione',
		'tp-menu-name'                    => 'Nome',
		'tp-menu-allergeni'               => 'Allergeni',
		'tp-menu-meta'                    => 'Descrizione',
		'tp-menu-row-image'               => 'Foto al passaggio del mouse',
		'tp-menu-prices'                  => 'Prezzi',
		'tp-menu-label-tasca'             => 'Etichetta Tasca',
		'tp-menu-price-value-tasca'       => 'Prezzo Tasca',
		'tp-menu-label-piatto'            => 'Etichetta Piatto',
		'tp-menu-label-s'                 => 'Etichetta S',
		'tp-menu-price-value-s'           => 'Prezzo S',
		'tp-menu-label-m'                 => 'Etichetta M',
		'tp-menu-price-value-m'           => 'Prezzo M',
		'tp-menu-label-l'                 => 'Etichetta L',
		'tp-menu-price-value-l'           => 'Prezzo L',
		'tp-menu-price'                   => 'Prezzo',
		'tp-menu-note'                    => 'Note e allergeni',
		// Materie prime.
		'tp-materie-onda--verde'          => 'Onda verde',
		'tp-materie-onda--bordeaux'       => 'Onda bordeaux',
		'tp-materie-header'               => 'Intestazione',
		'tp-materie-title'                => 'Titolo',
		'tp-materie-desc'                 => 'Testo',
		'tp-materie-marquee'              => 'Striscia scorrevole',
		'tp-materie-cards'                => 'Card',
		'tp-materie-card-image'           => 'Foto',
		'tp-materie-card-title'           => 'Titolo',
		'tp-materie-card-body'            => 'Testo',
		'tp-materie-chip'                 => 'Parola in genovese',
		// Dove siamo, orari.
		'tp-location-map-frame'           => 'Foto',
		'tp-location-title'               => 'Titolo',
		'tp-location-text'                => 'Testo',
		'tp-location-address'             => 'Indirizzo',
		'tp-location-button'              => 'Pulsante Mappe',
		'tp-hours-card'                   => 'Card degli orari',
		'tp-hours-title'                  => 'Titolo',
	];
}

/**
 * The names the theme gave the sections up to 0.5.5: a section that still has one gets the name of the guide.
 */
function tp_child_wireframe_old_labels(): array {
	return [
		'tp-hero-section'     => 'Hero Tasche Piene',
		'tp-marquee-section'  => 'Marquee Verde',
		'tp-intro-section'    => 'Intro - Tasche piene di qualcosa di buono',
		'tp-alberto-section'  => 'Intro - Una gastronomia com era una volta',
		'tp-menu-section'     => 'Il nostro menu',
		'tp-materie-section'  => 'Le materie prime',
		'tp-location-section' => 'Location',
		'tp-hours-section'    => 'Quando siamo aperti',
	];
}

/**
 * Names that keep their capital letter when a name in capitals becomes a sentence.
 */
function tp_child_wireframe_proper_names(): array {
	return [ 'Luciano', 'Alberto', 'Genova', 'Liguria', 'Tasche Piene' ];
}

/**
 * Plain text of a piece of the content (a title, a phrase), on one line, without tags; a name all in capitals
 * reads as a sentence ("POLPETTE AL SUGO" -> "Polpette al sugo", "LE ACCIUGHE DI LUCIANO" -> "Le acciughe di
 * Luciano"), also when a note in brackets follows it as typed ("TRIPPA IN UMIDO (di Venerdì)" -> "Trippa in
 * umido (di Venerdì)").
 */
function tp_child_wireframe_plain( string $html ): string {
	$text = (string) preg_replace( '#<br\s*/?>#i', ' ', $html );
	$text = trim( (string) preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( $text ), ENT_QUOTES, 'UTF-8' ) ) );

	if ( preg_match( '/^(.*\S)\s*(\([^()]*\))$/u', $text, $note ) && preg_match( '/\p{Lu}/u', $note[1] ) && ! preg_match( '/\p{Ll}/u', $note[1] ) ) {
		$text = tp_child_wireframe_plain( $note[1] ) . ' ' . $note[2];

		return mb_strlen( $text, 'UTF-8' ) > 60 ? rtrim( mb_substr( $text, 0, 59, 'UTF-8' ) ) . '…' : $text;
	}

	if ( '' !== $text && ! preg_match( '/\p{Ll}/u', $text ) ) {
		$text = mb_strtolower( $text, 'UTF-8' );
		$text = mb_strtoupper( mb_substr( $text, 0, 1, 'UTF-8' ), 'UTF-8' ) . mb_substr( $text, 1, null, 'UTF-8' );

		foreach ( tp_child_wireframe_proper_names() as $name ) {
			$text = (string) preg_replace( '/(?<!\p{L})' . preg_quote( $name, '/' ) . '(?!\p{L})/iu', $name, $text );
		}
	}

	return mb_strlen( $text, 'UTF-8' ) > 60 ? rtrim( mb_substr( $text, 0, 59, 'UTF-8' ) ) . '…' : $text;
}

/**
 * The first descendant of a block with the given class (parse_blocks() shape).
 */
function tp_child_wireframe_find( array $block, string $class ): ?array {
	foreach ( $block['innerBlocks'] ?? [] as $child ) {
		if ( empty( $child['blockName'] ) ) {
			continue;
		}

		if ( in_array( $class, explode( ' ', tp_child_block_class_names( $child['attrs'] ?? [] ) ), true ) ) {
			return $child;
		}

		$found = tp_child_wireframe_find( $child, $class );

		if ( $found ) {
			return $found;
		}
	}

	return null;
}

/**
 * The name of a block in the builder, from its classes and, for a dish, a card or a day, from its own text.
 * $ancestors: the class lists of the blocks around it, the closest last.
 */
function tp_child_wireframe_label( array $block, array $ancestors ): string {
	$attrs   = $block['attrs'] ?? [];
	$classes = explode( ' ', tp_child_block_class_names( $attrs ) );

	if ( 'tp/marquee-item' === ( $block['blockName'] ?? '' ) ) {
		$phrase = $attrs['phrase']['innerContent']['desktop']['value'] ?? '';
		$phrase = is_string( $phrase ) ? tp_child_wireframe_plain( $phrase ) : '';

		return '' !== $phrase ? 'Scritta: ' . $phrase : 'Scritta';
	}

	if ( 'tp/header-nav-item' === ( $block['blockName'] ?? '' ) ) {
		$text = tp_child_wireframe_plain( tp_child_nav_item_value( $attrs, 'text', 'Nuova voce' ) );

		return '' !== $text ? 'Voce: ' . $text : 'Voce';
	}

	if ( 'tp/footer-nav-item' === ( $block['blockName'] ?? '' ) ) {
		$side = 'right' === tp_child_nav_item_value( $attrs, 'column', 'left' ) ? 'Destra' : 'Sinistra';
		$text = tp_child_wireframe_plain( tp_child_nav_item_value( $attrs, 'text', 'Nuova voce' ) );

		return '' !== $text ? $side . ': ' . $text : $side;
	}

	if ( in_array( 'tp-menu-row', $classes, true ) ) {
		$kind = 'Piatto';

		foreach ( array_reverse( $ancestors ) as $around ) {
			foreach ( [ 'tp-menu-table--sides' => 'Contorno', 'tp-menu-table--desserts' => 'Dolce', 'tp-menu-table--drinks' => 'Bevanda', 'tp-menu-table--main' => 'Piatto' ] as $table => $name ) {
				if ( in_array( $table, $around, true ) ) {
					$kind = $name;
					break 2;
				}
			}
		}

		$heading = tp_child_wireframe_find( $block, 'tp-menu-name' );
		$title   = $heading['attrs']['title']['innerContent']['desktop']['value'] ?? '';
		$title   = is_string( $title ) ? tp_child_wireframe_plain( $title ) : '';

		return '' !== $title ? $kind . ': ' . $title : $kind;
	}

	if ( in_array( 'tp-materie-card', $classes, true ) ) {
		$text  = tp_child_wireframe_find( $block, 'tp-materie-card-title' );
		$title = $text['attrs']['content']['innerContent']['desktop']['value'] ?? '';
		$title = is_string( $title ) ? tp_child_wireframe_plain( $title ) : '';

		return '' !== $title ? 'Card: ' . $title : 'Card';
	}

	if ( in_array( 'tp-hours-item', $classes, true ) ) {
		$html = $attrs['content']['innerContent']['desktop']['value'] ?? '';
		$day  = is_string( $html ) && preg_match( '#<h4[^>]*>(.*?)</h4>#is', $html, $found ) ? tp_child_wireframe_plain( $found[1] ) : '';

		return '' !== $day ? 'Giorno: ' . $day : 'Giorno';
	}

	foreach ( tp_child_wireframe_label_map() as $class => $label ) {
		if ( in_array( $class, $classes, true ) ) {
			return $label;
		}
	}

	return '';
}

/**
 * Whether a block takes its name from its own text (a dish, a card, a day, a phrase of the strip) and whether a
 * label is one of those names made by the theme: those follow the text; any other label the owner wrote stays.
 */
function tp_child_wireframe_named_by_content( array $block, array $classes ): bool {
	return in_array( $block['blockName'] ?? '', [ 'tp/marquee-item', 'tp/header-nav-item', 'tp/footer-nav-item' ], true ) || [] !== array_intersect( [ 'tp-menu-row', 'tp-materie-card', 'tp-hours-item' ], $classes );
}

function tp_child_wireframe_is_content_name( string $label ): bool {
	return 1 === preg_match( '/^(?:Piatto|Contorno|Dolce|Bevanda|Card|Giorno|Scritta|Voce|Sinistra|Destra)(?:: |$)/u', $label );
}

/**
 * What to change on each block, in document order: the new label (null: keep the one there) and whether the
 * misplaced module.advanced.meta goes.
 */
function tp_child_wireframe_label_plan( array $blocks, array $ancestors, array &$plan ): void {
	foreach ( $blocks as $block ) {
		if ( empty( $block['blockName'] ) ) {
			continue;
		}

		$attrs     = $block['attrs'] ?? [];
		$classes   = explode( ' ', tp_child_block_class_names( $attrs ) );
		$current   = $attrs['module']['meta']['adminLabel']['desktop']['value'] ?? '';
		$current   = is_string( $current ) ? $current : '';
		$misplaced = isset( $attrs['module']['advanced']['meta'] ) && [ 'adminLabel' ] === array_keys( (array) $attrs['module']['advanced']['meta'] );
		$label     = null;
		$old       = '';

		foreach ( tp_child_wireframe_old_labels() as $class => $name ) {
			if ( in_array( $class, $classes, true ) ) {
				$old = $name;
			}
		}

		$follows = tp_child_wireframe_named_by_content( $block, $classes ) && tp_child_wireframe_is_content_name( $current );

		if ( '' === $current || ( '' !== $old && $old === $current ) || $follows ) {
			$computed = tp_child_wireframe_label( $block, $ancestors );
			$label    = '' !== $computed && $computed !== $current ? $computed : null;
		}

		$plan[] = [ 'label' => $label, 'misplaced' => $misplaced ];
		tp_child_wireframe_label_plan( $block['innerBlocks'] ?? [], array_merge( $ancestors, [ $classes ] ), $plan );
	}
}

/**
 * The content with the builder names. Only the block comments that change are rewritten (Divi's own
 * serialization, byte-identical for the rest); everything else stays as it is.
 */
function tp_child_wireframe_labels_transform( string $content ): string {
	if ( false === strpos( $content, 'wp:divi/' ) ) {
		return $content;
	}

	$plan = [];
	tp_child_wireframe_label_plan( parse_blocks( $content ), [], $plan );

	$index = 0;

	$updated = preg_replace_callback(
		// The block comments as WordPress's own parser reads them (class-wp-block-parser.php): name, attributes, void.
		'#<!--\s+wp:((?:[a-z][a-z0-9_-]*/)?[a-z][a-z0-9_-]*)\s+(?:(\{(?:(?:[^}]+|}+(?=})|(?!}\s+/?-->).)*+)?\})\s+)?(/)?-->#s',
		static function ( array $match ) use ( &$index, $plan ): string {
			$step = $plan[ $index ] ?? null;
			++$index;

			if ( ! $step || ( null === $step['label'] && ! $step['misplaced'] ) ) {
				return $match[0];
			}

			$attrs = '' !== ( $match[2] ?? '' ) ? json_decode( $match[2] ) : new stdClass();

			if ( ! $attrs instanceof stdClass ) {
				return $match[0];
			}

			if ( ! isset( $attrs->module ) || ! $attrs->module instanceof stdClass ) {
				$attrs->module = new stdClass();
			}

			if ( $step['misplaced'] && isset( $attrs->module->advanced ) && $attrs->module->advanced instanceof stdClass ) {
				$moved = $attrs->module->advanced->meta->adminLabel->desktop->value ?? '';
				unset( $attrs->module->advanced->meta );

				if ( null === $step['label'] && '' === ( $attrs->module->meta->adminLabel->desktop->value ?? '' ) && is_string( $moved ) && '' !== $moved ) {
					$step['label'] = $moved;
				}
			}

			if ( null !== $step['label'] ) {
				if ( ! isset( $attrs->module->meta ) || ! $attrs->module->meta instanceof stdClass ) {
					$attrs->module->meta = new stdClass();
				}

				$attrs->module->meta->adminLabel = (object) [ 'desktop' => (object) [ 'value' => $step['label'] ] ];
			}

			return '<!-- wp:' . $match[1] . ' ' . serialize_block_attributes( $attrs ) . ' ' . ( $match[3] ?? '' ) . '-->';
		},
		$content
	);

	// A count that does not match (block comments the parser read differently) leaves the content alone.
	return is_string( $updated ) && count( $plan ) === $index ? $updated : $content;
}

function tp_child_migrate_wireframe_labels(): void {
	tp_child_menu_size_s_migrate_home( 'tp_child_wireframe_labels_v1', 'tp_child_wireframe_labels_transform' );
}

add_action( 'init', 'tp_child_migrate_wireframe_labels', 40 );

/**
 * The content the builder opens, with the names up to date: any page with the theme's pieces and the
 * header and footer of the Theme Builder (Divi runs this filter on both, before its own updates of the
 * content are used).
 *
 * @param mixed $content Raw post content for the builder.
 * @return mixed
 */
function tp_child_wireframe_labels_for_builder( $content ) {
	return is_string( $content ) && false !== strpos( $content, 'tp-' ) ? tp_child_wireframe_labels_transform( $content ) : $content;
}

add_filter( 'et_fb_load_raw_post_content', 'tp_child_wireframe_labels_for_builder', 20 );
