<?php
/**
 * Module: FooterLogo (child-module).
 */

namespace TaschePieneDiviChild\Modules\FooterLogo;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use TaschePieneDiviChild\Modules\ModuleBase;

class FooterLogo extends ModuleBase {

	use FooterLogoTrait\RenderCallbackTrait;

	protected const JSON_FOLDER = 'footer-logo';

	protected const CLASS_NAME = 'tp_footer_logo';

	protected const DESIGN_GROUPS = [ 'tpDesignImage' ];
}
