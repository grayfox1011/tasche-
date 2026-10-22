<?php
/**
 * Migrate legacy Divi text marquee (raw HTML) to tp/marquee in Materie section.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Extract the first phrase from legacy materie marquee HTML.
 */
function tp_child_extract_materie_marquee_phrase( string $html ): string {
	$text = html_entity_decode( wp_strip_all_tags( $html ), ENT_QUOTES, 'UTF-8' );
	$text = trim( preg_replace( '/\s+/u', ' ', $text ) ?: '' );

	if ( '' === $text ) {
		return 'Le cose che finiscono in tasca';
	}

	$parts = preg_split( '/\s*•\s*/u', $text ) ?: [];
	$phrase = trim( (string) ( $parts[0] ?? '' ) );

	return '' !== $phrase ? $phrase : 'Le cose che finiscono in tasca';
}

/**
 * Build modular marquee block markup for Materie section.
 */
function tp_child_materie_marquee_modular_block( string $phrase ): string {
	$phrase = sanitize_text_field( $phrase );

	if ( '' === $phrase ) {
		$phrase = 'Le cose che finiscono in tasca';
	}

	$builder_version = defined( 'TP_CHILD_SEED_BUILDER_VERSION' ) ? TP_CHILD_SEED_BUILDER_VERSION : '5.5.2';

	$marquee_attrs = [
		'module'       => [
			'advanced' => [
				'htmlAttributes' => [
					'desktop' => [
						'value' => [
							'class' => 'tp-materie-marquee',
						],
					],
				],
			],
		],
		'speed'        => [
			'innerContent' => [
				'desktop' => [
					'value' => '10',
				],
			],
		],
		'direction'    => [
			'innerContent' => [
				'desktop' => [
					'value' => 'left',
				],
			],
		],
		'repeatCount'  => [
			'innerContent' => [
				'desktop' => [
					'value' => '4',
				],
			],
		],
		'pauseOnHover' => [
			'innerContent' => [
				'desktop' => [
					'value' => 'off',
				],
			],
		],
		'variant'      => [
			'innerContent' => [
				'desktop' => [
					'value' => 'materie',
				],
			],
		],
		'separator'    => [
			'innerContent' => [
				'desktop' => [
					'value' => '•',
				],
			],
		],
		'builderVersion' => $builder_version,
	];

	$item_attrs = [
		'phrase' => [
			'innerContent' => [
				'desktop' => [
					'value' => $phrase,
				],
			],
		],
		'builderVersion' => $builder_version,
	];

	return '<!-- wp:tp/marquee ' . wp_json_encode( $marquee_attrs ) . " -->\n"
		. '<!-- wp:tp/marquee-item ' . wp_json_encode( $item_attrs ) . " /-->\n"
		. '<!-- /wp:tp/marquee -->';
}

/**
 * Replace legacy materie marquee text blocks inside parsed content.
 *
 * @param array<int, array<string, mixed>> $blocks Parsed blocks.
 * @return array<int, array<string, mixed>>
 */
function tp_child_migrate_materie_marquee_blocks( array $blocks ): array {
	foreach ( $blocks as $index => $block ) {
		if ( ! is_array( $block ) ) {
			continue;
		}

		if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
			$blocks[ $index ]['innerBlocks'] = tp_child_migrate_materie_marquee_blocks( $block['innerBlocks'] );
		}

		if ( 'divi/text' !== ( $block['blockName'] ?? '' ) ) {
			continue;
		}

		$attrs  = is_array( $block['attrs'] ?? null ) ? $block['attrs'] : [];
		$class  = $attrs['module']['advanced']['htmlAttributes']['desktop']['value']['class'] ?? '';
		$class .= ' ' . ( $attrs['module']['decoration']['attributes']['desktop']['value']['class'] ?? '' );

		if ( false === strpos( $class, 'tp-materie-marquee' ) ) {
			continue;
		}

		$html   = $attrs['content']['innerContent']['desktop']['value'] ?? '';
		$phrase = tp_child_extract_materie_marquee_phrase( is_string( $html ) ? $html : '' );
		$new_blocks = array_values(
			array_filter(
				parse_blocks( tp_child_materie_marquee_modular_block( $phrase ) ),
				static function ( $parsed_block ) {
					return is_array( $parsed_block ) && ! empty( $parsed_block['blockName'] );
				}
			)
		);

		if ( empty( $new_blocks ) ) {
			continue;
		}

		array_splice( $blocks, $index, 1, $new_blocks );
	}

	return $blocks;
}

/**
 * Migrate post content when it still contains the legacy materie marquee block.
 */
function tp_child_migrate_post_materie_marquee( int $post_id ): bool {
	$post = get_post( $post_id );

	if ( ! $post instanceof WP_Post || '' === trim( (string) $post->post_content ) ) {
		return false;
	}

	if ( false === strpos( $post->post_content, 'tp-materie-marquee' ) ) {
		return false;
	}

	if ( false !== strpos( $post->post_content, 'wp:tp/marquee' ) && false !== strpos( $post->post_content, 'tp-materie-marquee' ) ) {
		// Already migrated unless a legacy text block is still present.
		if ( false === strpos( $post->post_content, 'wp:divi/text' ) || false === strpos( $post->post_content, 'tp-materie-marquee-track' ) ) {
			return false;
		}
	}

	$blocks          = parse_blocks( $post->post_content );
	$migrated_blocks = tp_child_migrate_materie_marquee_blocks( $blocks );
	$new_content     = serialize_blocks( $migrated_blocks );

	if ( $new_content === $post->post_content ) {
		return false;
	}

	wp_update_post(
		[
			'ID'           => $post_id,
			'post_content' => wp_slash( $new_content ),
		]
	);

	return true;
}

/**
 * Run one-time migration for home/front page content.
 */
function tp_child_migrate_materie_marquee_content(): void {
	if ( get_option( 'tp_child_materie_marquee_migrated_v1' ) ) {
		return;
	}

	$post_ids = [];

	$front_page_id = (int) get_option( 'page_on_front' );
	if ( $front_page_id > 0 ) {
		$post_ids[] = $front_page_id;
	}

	$seed_home_id = (int) get_option( 'tp_child_seed_home_page_id' );
	if ( $seed_home_id > 0 ) {
		$post_ids[] = $seed_home_id;
	}

	$pages = get_posts(
		[
			'post_type'      => 'page',
			'post_status'    => [ 'publish', 'draft', 'private' ],
			'posts_per_page' => -1,
			'fields'         => 'ids',
			's'              => 'tp-materie-marquee',
			'no_found_rows'  => true,
		]
	);

	if ( is_array( $pages ) ) {
		$post_ids = array_merge( $post_ids, array_map( 'intval', $pages ) );
	}

	foreach ( array_unique( array_filter( $post_ids ) ) as $post_id ) {
		tp_child_migrate_post_materie_marquee( $post_id );
	}

	update_option( 'tp_child_materie_marquee_migrated_v1', 1, false );
}

add_action( 'after_setup_theme', 'tp_child_migrate_materie_marquee_content', 22 );
