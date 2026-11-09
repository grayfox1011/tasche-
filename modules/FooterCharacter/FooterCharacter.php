<?php
/**
 * Module: Personaggio del footer (child of Footer sito, 0.6.8).
 *
 * One of the drawings sitting on the big "Tasche Piene" of the footer (the couple, the pigeons, the man of the
 * phones...). Footer sito > Contenuto > Elementi lists them next to the Voci: an element removed there is a
 * character less, a duplicated one is a character more. The module's own box is the place of the character
 * (the classes tp-theme-footer-character-slot--four/one/three of the theme's CSS), so Divi's Design and
 * Avanzate settings of the element (Dimensioni, Trasforma, Visibilità...) act on it.
 */

namespace TaschePieneDiviChild\Modules\FooterCharacter;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use TaschePieneDiviChild\Modules\ModuleBase;

class FooterCharacter extends ModuleBase {

	use FooterCharacterTrait\RenderCallbackTrait;

	protected const JSON_FOLDER = 'footer-character';

	protected const CLASS_NAME = 'tp_footer_character';

	protected const DESIGN_GROUPS = [ 'tpDesignImage' ];

	/** The places of the theme's CSS. */
	public const SLOTS = [ 'four', 'one', 'three' ];

	/** The place chosen in "Posto" (the third, the free one, when the value is not one of the theme's). */
	public static function slot( array $attrs ): string {
		$slot = sanitize_key( tp_child_chrome_get_string( $attrs, 'slot', 'three' ) );

		return in_array( $slot, self::SLOTS, true ) ? $slot : 'three';
	}

	public static function module_classnames( array $args ): void {
		parent::module_classnames( $args );

		$attrs = $args['attrs'] ?? [];

		$args['classnamesInstance']->add( 'tp-theme-footer-character-slot', true );
		$args['classnamesInstance']->add( 'tp-theme-footer-character-slot--' . self::slot( $attrs ), true );
		$args['classnamesInstance']->add( 'tp-theme-footer-character-slot--flip', 'on' === tp_child_chrome_get_string( $attrs, 'flip', 'off' ) );
	}
}
