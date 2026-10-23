<?php
/**
 * Module: FooterCopyrightLine (child-module).
 */

namespace TaschePieneDiviChild\Modules\FooterCopyrightLine;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use TaschePieneDiviChild\Modules\ModuleBase;

class FooterCopyrightLine extends ModuleBase {

	use FooterCopyrightLineTrait\RenderCallbackTrait;

	protected const JSON_FOLDER = 'footer-copyright-line';

	protected const CLASS_NAME = 'tp_footer_copyright_line';

	protected const DESIGN_GROUPS = [ 'tpDesignText' ];
}
