<?php
/**
 * Module: FooterCharacter (child-module).
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
}
