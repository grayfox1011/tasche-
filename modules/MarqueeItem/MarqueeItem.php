<?php
/**
 * Module: TP Marquee Item.
 */

namespace TaschePieneDiviChild\Modules\MarqueeItem;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use TaschePieneDiviChild\Modules\ModuleBase;

class MarqueeItem extends ModuleBase {

	use MarqueeItemTrait\RenderCallbackTrait;

	protected const JSON_FOLDER = 'marquee-item';

	protected const CLASS_NAME = 'tp_marquee_item';

	/** What a phrase in the strip can take: module.json offers the same groups. */
	private const PHRASE_DECORATIONS = [ 'background', 'spacing', 'border', 'boxShadow', 'filters', 'transform', 'overflow', 'transition', 'disabledOn' ];

	/** Phrases as Divi rendered them (dynamic content resolved), by store instance and block id. */
	private static $rendered_phrases = [];

	public static function remember_phrase( string $key, string $phrase ): void {
		self::$rendered_phrases[ $key ] = $phrase;
	}

	/** The phrase the strip shows for a child block, or null if Divi has not rendered it. */
	public static function rendered_phrase( string $key ): ?string {
		return self::$rendered_phrases[ $key ] ?? null;
	}

	protected static function module_style_props( array $args ): array {
		$props       = parent::module_style_props( $args );
		$order_class = $args['orderClass'] ?? '';

		// On the site the phrase carries this module's class and is followed by its separator: both go.
		if ( '' !== $order_class ) {
			$props['disabledOn']['selector'] = $order_class . ', ' . $order_class . ' + .tp-marquee__separator';
		}

		// Values saved for other options before 0.5.0 never showed: they stay out.
		$props['attrsFilter'] = static function ( array $decoration ): array {
			return array_intersect_key( $decoration, array_flip( self::PHRASE_DECORATIONS ) );
		};

		return $props;
	}

	public static function module_script_data( array $args ): void {
		// No animation, link or scroll effect on a phrase (module.json does not offer them).
	}
}
