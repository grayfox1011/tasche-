<?php
/**
 * Tasca/Piatto made of Divi modules (since 0.5.4). Before, the theme rebuilt the section on the front end
 * from the texts and photos of its modules (tp_child_replace_choice_section()): Divi changed what the
 * section says and shows, not how it looks. Now the section is the modules themselves, drawn by the theme
 * as before and by what their Design settings say:
 * - "Titolo e onde": the green and the bordeaux wave (TP Shape Wave modules) and the title (Text, H2);
 * - "Card": one column per card (Tasca, Piatto) with the photo (Image) and, in the group "Testi", the name
 *   (Heading, H3), the features (Text, a bulleted list) and the description (Text).
 * The home moves to it by itself, with the texts and photos it has now. A section changed by hand, so
 * that it no longer has the modules of before, stays as it is: the rebuild still draws it.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * One Divi block: self-closing, or wrapping the blocks of $inner.
 */
function tp_child_scelta_block( string $name, array $attrs, ?array $inner = null ): string {
	$attrs['builderVersion'] = '5.2.0';
	$open                    = '<!-- wp:' . $name . ' ' . serialize_block_attributes( $attrs );

	if ( null === $inner ) {
		return $open . ' /-->';
	}

	return $open . " -->\n" . implode( "\n", $inner ) . "\n<!-- /wp:" . $name . ' -->';
}

/**
 * The attributes every block of the section starts from: its class and the name shown in the builder.
 */
function tp_child_scelta_attrs( string $class, string $label, array $attrs = [] ): array {
	$attrs['module']['advanced']['htmlAttributes']['desktop']['value']['class'] = $class;

	if ( '' !== $label ) {
		$attrs['module']['meta']['adminLabel']['desktop']['value'] = $label;
	}

	return $attrs;
}

/**
 * The section as Divi blocks, from data shaped as tp_child_choice_default_data(); a card may also carry
 * the attachment of its photo ("image_id").
 */
function tp_child_scelta_section_markup( array $data ): string {
	$lines = array_map( 'esc_html', array_map( 'strval', array_values( $data['title_lines'] ) ) );
	$cards = [];

	foreach ( [ 'tasca' => 'Tasca', 'piatto' => 'Piatto' ] as $key => $label ) {
		$card  = $data['cards'][ $key ];
		$image = [
			'src' => (string) $card['image'],
			'alt' => (string) $card['alt'],
		];

		if ( ! empty( $card['image_id'] ) ) {
			$image['id'] = (string) $card['image_id'];
		}

		$chips = implode( '', array_map( static fn( string $chip ): string => '<li>' . esc_html( $chip ) . '</li>', array_values( $card['chips'] ) ) );

		$cards[] = tp_child_scelta_block(
			'divi/column',
			tp_child_scelta_attrs(
				'tp-scelta-scheda tp-scelta-scheda--' . $key,
				$label,
				[
					'module' => [
						'advanced'   => [ 'type' => [ 'desktop' => [ 'value' => '1_2' ] ] ],
						'decoration' => [ 'sizing' => [ 'desktop' => [ 'value' => [ 'flexType' => '12_24' ] ] ] ],
					],
				]
			),
			[
				tp_child_scelta_block( 'divi/image', tp_child_scelta_attrs( 'tp-scelta-foto', 'Foto', [ 'image' => [ 'innerContent' => [ 'desktop' => [ 'value' => $image ] ] ] ] ) ),
				tp_child_scelta_block(
					'divi/group',
					tp_child_scelta_attrs( 'tp-scelta-testo', 'Testi' ),
					[
						tp_child_scelta_block(
							'divi/heading',
							tp_child_scelta_attrs(
								'tp-scelta-nome',
								'Nome',
								[
									'title' => [
										'innerContent' => [ 'desktop' => [ 'value' => esc_html( $card['title'] ) ] ],
										'decoration'   => [ 'font' => [ 'font' => [ 'desktop' => [ 'value' => [ 'headingLevel' => 'h3' ] ] ] ] ],
									],
								]
							)
						),
						tp_child_scelta_block( 'divi/text', tp_child_scelta_attrs( 'tp-scelta-chip', 'Caratteristiche', [ 'content' => [ 'innerContent' => [ 'desktop' => [ 'value' => '<ul>' . $chips . '</ul>' ] ] ] ] ) ),
						tp_child_scelta_block( 'divi/text', tp_child_scelta_attrs( 'tp-scelta-descrizione', 'Descrizione', [ 'content' => [ 'innerContent' => [ 'desktop' => [ 'value' => '<p>' . esc_html( $card['desc'] ) . '</p>' ] ] ] ] ) ),
					]
				),
			]
		);
	}

	return tp_child_scelta_block(
		'divi/section',
		tp_child_scelta_attrs( 'tp-scelta', 'Tasca o piatto' ),
		[
			tp_child_scelta_block(
				'divi/row',
				tp_child_scelta_attrs(
					'tp-scelta-testata',
					'Titolo e onde',
					[
						'module' => [
							'advanced' => [
								'columnStructure'     => [ 'desktop' => [ 'value' => '4_4' ] ],
								'flexColumnStructure' => [ 'desktop' => [ 'value' => 'equal-columns_1' ] ],
							],
						],
					]
				),
				[
					tp_child_scelta_block(
						'divi/column',
						[ 'module' => [ 'advanced' => [ 'type' => [ 'desktop' => [ 'value' => '4_4' ] ] ] ] ],
						[
							tp_child_scelta_block( 'tp/shape-wave', tp_child_scelta_attrs( 'tp-scelta-onda tp-scelta-onda--verde', 'Onda verde', [ 'variant' => [ 'innerContent' => [ 'desktop' => [ 'value' => 'choice-green-wave' ] ] ] ] ) ),
							tp_child_scelta_block( 'tp/shape-wave', tp_child_scelta_attrs( 'tp-scelta-onda tp-scelta-onda--bordeaux', 'Onda bordeaux', [ 'variant' => [ 'innerContent' => [ 'desktop' => [ 'value' => 'choice-burgundy-wave' ] ] ] ] ) ),
							tp_child_scelta_block( 'divi/text', tp_child_scelta_attrs( 'tp-scelta-titolo', 'Titolo', [ 'content' => [ 'innerContent' => [ 'desktop' => [ 'value' => '<h2>' . implode( '<br />', $lines ) . '</h2>' ] ] ] ] ) ),
						]
					),
				]
			),
			tp_child_scelta_block(
				'divi/row',
				tp_child_scelta_attrs(
					'tp-scelta-schede',
					'Card',
					[
						'module' => [
							'advanced' => [
								'columnStructure'     => [ 'desktop' => [ 'value' => '1_2,1_2' ] ],
								'flexColumnStructure' => [ 'desktop' => [ 'value' => 'equal-columns_2' ] ],
							],
						],
					]
				),
				$cards
			),
		]
	);
}

/**
 * The class of a parsed Divi block.
 */
function tp_child_scelta_class_of( array $block ): string {
	$class = $block['attrs']['module']['advanced']['htmlAttributes']['desktop']['value']['class'] ?? '';

	return is_string( $class ) ? $class : '';
}

/**
 * The blocks inside $block, without the blank text between them.
 */
function tp_child_scelta_children( array $block ): array {
	return array_values( array_filter( $block['innerBlocks'] ?? [], static fn( array $child ): bool => null !== $child['blockName'] ) );
}

/**
 * Whether $blocks are, in order, Divi blocks of these names with these classes.
 */
function tp_child_scelta_blocks_are( array $blocks, array $expected ): bool {
	if ( count( $blocks ) !== count( $expected ) ) {
		return false;
	}

	foreach ( array_values( $expected ) as $index => list( $name, $class ) ) {
		if ( $name !== $blocks[ $index ]['blockName'] || ! preg_match( '/(?:^| )' . preg_quote( $class, '/' ) . '(?: |$)/', tp_child_scelta_class_of( $blocks[ $index ] ) ) ) {
			return false;
		}
	}

	return true;
}

/**
 * The HTML that a text value of a module gives on the page.
 */
function tp_child_scelta_value( array $block, array $path ): string {
	$value = $block['attrs'];

	foreach ( $path as $key ) {
		$value = is_array( $value ) && array_key_exists( $key, $value ) ? $value[ $key ] : '';
	}

	return is_string( $value ) ? $value : '';
}

/**
 * The texts and photos of a section of before, as the rebuild reads them on the page, or null when the
 * section does not have the modules of before (and only them).
 */
function tp_child_choice_divi_data( array $section ): ?array {
	$rows = tp_child_scelta_children( $section );

	if ( ! tp_child_scelta_blocks_are( $rows, [ [ 'divi/row', 'tp-choice-header-row' ], [ 'divi/row', 'tp-choice-cards-row' ] ] ) ) {
		return null;
	}

	$header = tp_child_scelta_children( $rows[0] );
	$title  = 1 === count( $header ) ? tp_child_scelta_children( $header[0] ) : [];

	if ( 'divi/column' !== ( $header[0]['blockName'] ?? '' ) || ! tp_child_scelta_blocks_are( $title, [ [ 'divi/text', 'tp-choice-title' ] ] ) ) {
		return null;
	}

	$html = '<div class="tp-choice-title">' . wpautop( tp_child_scelta_value( $title[0], [ 'content', 'innerContent', 'desktop', 'value' ] ) ) . '</div>';
	$ids  = [];

	foreach ( tp_child_scelta_children( $rows[1] ) as $column ) {
		$class = tp_child_scelta_class_of( $column );
		$key   = preg_match( '/(?:^| )tp-choice-card--(tasca|piatto)(?: |$)/', $class, $match ) ? $match[1] : '';
		$parts = tp_child_scelta_children( $column );
		$texts = isset( $parts[1] ) ? tp_child_scelta_children( $parts[1] ) : [];

		if ( 'divi/column' !== $column['blockName'] || '' === $key || isset( $ids[ $key ] )
			|| ! tp_child_scelta_blocks_are( $parts, [ [ 'divi/image', 'tp-choice-card-image' ], [ 'divi/group', 'tp-choice-card-content' ] ] )
			|| ! tp_child_scelta_blocks_are( $texts, [ [ 'divi/heading', 'tp-choice-card-title' ], [ 'divi/text', 'tp-choice-card-chips' ], [ 'divi/text', 'tp-choice-card-description' ] ] ) ) {
			return null;
		}

		$image       = $parts[0]['attrs']['image']['innerContent']['desktop']['value'] ?? [];
		$image       = is_array( $image ) ? $image : [];
		$src         = is_string( $image['src'] ?? null ) ? $image['src'] : '';
		$alt         = is_string( $image['alt'] ?? null ) ? $image['alt'] : '';
		$ids[ $key ] = is_scalar( $image['id'] ?? null ) && (int) $image['id'] > 0 ? (int) $image['id'] : 0;

		$html .= sprintf(
			'<div class="tp-choice-card tp-choice-card--%1$s"><div class="tp-choice-card-image">%2$s</div><div class="tp-choice-card-title">%3$s</div><div class="tp-choice-card-chips">%4$s</div><div class="tp-choice-card-description">%5$s</div></div>',
			$key,
			'' !== $src ? '<img src="' . esc_attr( $src ) . '" alt="' . esc_attr( $alt ) . '" />' : '',
			tp_child_scelta_value( $texts[0], [ 'title', 'innerContent', 'desktop', 'value' ] ),
			wpautop( tp_child_scelta_value( $texts[1], [ 'content', 'innerContent', 'desktop', 'value' ] ) ),
			wpautop( tp_child_scelta_value( $texts[2], [ 'content', 'innerContent', 'desktop', 'value' ] ) )
		);
	}

	if ( 2 !== count( $ids ) ) {
		return null;
	}

	$data = tp_child_choice_data_from_divi_section( $html );
	unset( $data['_extracted'] );

	// The photos keep their attachment, for the sizes WordPress made of them.
	foreach ( $ids as $key => $id ) {
		$id = $id ?: (int) attachment_url_to_postid( $data['cards'][ $key ]['image'] );

		if ( $id > 0 ) {
			$data['cards'][ $key ]['image_id'] = $id;
		}
	}

	return $data;
}

/**
 * $content with the Tasca/Piatto section of before made of the new modules; everything else stays byte
 * for byte.
 */
function tp_child_choice_divi_transform( string $content ): string {
	$offset = 0;

	while ( preg_match( '#<!-- wp:divi/section \{(?:(?!-->).)*?"class":"(?:[^"]* )?tp-choice-section(?: [^"]*)?"(?:(?!-->).)*?\} -->#s', $content, $open, PREG_OFFSET_CAPTURE, $offset ) ) {
		$start = $open[0][1];
		$end   = strpos( $content, '<!-- /wp:divi/section -->', $start );

		if ( false === $end ) {
			break;
		}

		$end    += strlen( '<!-- /wp:divi/section -->' );
		$blocks  = parse_blocks( substr( $content, $start, $end - $start ) );
		$data    = 1 === count( $blocks ) ? tp_child_choice_divi_data( $blocks[0] ) : null;
		$markup  = null !== $data ? tp_child_scelta_section_markup( $data ) : substr( $content, $start, $end - $start );
		$content = substr_replace( $content, $markup, $start, $end - $start );
		$offset  = $start + strlen( $markup );
	}

	return $content;
}

function tp_child_migrate_choice_divi(): void {
	tp_child_menu_size_s_migrate_home( 'tp_child_choice_divi_v1', 'tp_child_choice_divi_transform' );
}

add_action( 'init', 'tp_child_migrate_choice_divi', 30 );
