<?php
/**
 * Module: TP Marquee.
 */

namespace TaschePieneDiviChild\Modules\Marquee;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use TaschePieneDiviChild\Modules\ModuleBase;

class Marquee extends ModuleBase {

	use MarqueeTrait\RenderCallbackTrait;

	protected const JSON_FOLDER = 'marquee';

	protected const CLASS_NAME = 'tp_marquee';

	protected const DESIGN_GROUPS = [ 'tpDesignText', 'tpDesignStrip' ];
}
