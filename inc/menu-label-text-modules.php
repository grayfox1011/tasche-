<?php
/**
 * Menu row labels: plain Divi text modules for Tasca / Piatto (editable in Layers).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build a plain Divi text block for a menu price label.
 */
function tp_child_menu_label_text_block( string $text, string $class, string $admin_label = '' ): string {
	$text         = sanitize_text_field( $text );
	$admin_label  = '' !== $admin_label ? $admin_label : $text;
	$builder_ver  = defined( 'TP_CHILD_SEED_BUILDER_VERSION' ) ? TP_CHILD_SEED_BUILDER_VERSION : '5.2.0';
	$html_content = '<p>' . esc_html( $text ) . '</p>';

	return tp_child_seed_block_single(
		'divi/text',
		[
			'module'  => [
				'advanced' => [
					'htmlAttributes' => [
						'desktop' => [
							'value' => [
								'class' => $class,
							],
						],
					],
					'meta'         => [
						'adminLabel' => [
							'desktop' => [
								'value' => $admin_label,
							],
						],
					],
				],
			],
			'content' => [
				'innerContent' => [
					'desktop' => [
						'value' => $html_content,
					],
				],
			],
			'builderVersion' => $builder_ver,
		]
	);
}

/**
 * Build a plain Divi text block for a menu price value.
 */
function tp_child_menu_price_value_block( string $price, string $class ): string {
	$price       = sanitize_text_field( $price );
	$builder_ver = defined( 'TP_CHILD_SEED_BUILDER_VERSION' ) ? TP_CHILD_SEED_BUILDER_VERSION : '5.2.0';
	$html_content = '<p>' . esc_html( $price ) . '</p>';

	return tp_child_seed_block_single(
		'divi/text',
		[
			'module'  => [
				'advanced' => [
					'htmlAttributes' => [
						'desktop' => [
							'value' => [
								'class' => $class,
							],
						],
					],
				],
			],
			'content' => [
				'innerContent' => [
					'desktop' => [
						'value' => $html_content,
					],
				],
			],
			'builderVersion' => $builder_ver,
		]
	);
}

/**
 * Replace legacy combined Tasca/Piatto markup with separate text modules.
 */
function tp_child_menu_label_text_modules_transform( string $content ): string {
	$tasca_pattern = '/<!-- wp:divi\/text \{[^\/]+tp-menu-price-label-tasca[^\/]+\/-->/';

	$content = (string) preg_replace_callback(
		$tasca_pattern,
		static function ( string $block ): string {
			$price = '5,50€';

			if ( preg_match( '/tp-menu-price-value\\u0022\\u003e([^"\\\\]+)/', $block, $matches ) ) {
				$price = html_entity_decode( (string) $matches[1], ENT_QUOTES, 'UTF-8' );
			}

			return tp_child_menu_label_text_block(
				'Tasca',
				'tp-menu-label tp-menu-label-tasca tp-menu-price-label-tasca',
				'Etichetta Tasca'
			) . tp_child_menu_price_value_block(
				$price,
				'tp-menu-price tp-menu-price--tasca tp-menu-price-value-tasca'
			);
		},
		$content
	);

	$piatto_pattern = '/<!-- wp:divi\/text \{[^\/]+tp-menu-price-label-s[^\/]+Piatto[^\/]+\/-->/';

	return (string) preg_replace(
		$piatto_pattern,
		tp_child_menu_label_text_block(
			'Piatto',
			'tp-menu-label tp-menu-label-piatto tp-menu-price-label-s',
			'Etichetta Piatto'
		),
		$content
	);
}

/**
 * Migrate homepage menu rows to plain label text modules.
 */
function tp_child_migrate_menu_label_text_modules(): void {
	$flag = 'tp_child_menu_label_modules_v1';

	if ( get_option( $flag ) ) {
		return;
	}

	$home_id = (int) get_option( 'page_on_front' );

	if ( $home_id <= 0 ) {
		return;
	}

	$content = (string) get_post_field( 'post_content', $home_id );

	if ( '' === $content ) {
		update_option( $flag, TP_CHILD_VERSION );
		return;
	}

	if ( false !== strpos( $content, 'tp-menu-label-tasca' ) ) {
		update_option( $flag, TP_CHILD_VERSION );
		return;
	}

	if ( false === strpos( $content, 'tp-menu-price--tasca tp-menu-price-label-tasca' ) ) {
		update_option( $flag, TP_CHILD_VERSION );
		return;
	}

	$updated = tp_child_menu_label_text_modules_transform( $content );

	if ( $updated !== $content ) {
		wp_update_post(
			[
				'ID'           => $home_id,
				'post_content' => wp_slash( $updated ),
			]
		);
		tp_child_seed_clear_divi_cache_meta( $home_id );
	}

	update_option( $flag, TP_CHILD_VERSION );
}

add_action( 'init', 'tp_child_migrate_menu_label_text_modules', 25 );
