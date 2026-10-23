<?php
/**
 * Module: FooterDecor (child-module).
 */

namespace TaschePieneDiviChild\Modules\FooterDecor;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use TaschePieneDiviChild\Modules\ModuleBase;

class FooterDecor extends ModuleBase {

	use FooterDecorTrait\RenderCallbackTrait;

	protected const JSON_FOLDER = 'footer-decor';

	protected const CLASS_NAME = 'tp_footer_decor';

	protected const DESIGN_GROUPS = [ 'tpDesignImage', 'tpDesignContent' ];
}
