<?php
/**
 * Module: TP Shape Wave.
 */

namespace TaschePieneDiviChild\Modules\ShapeWave;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use TaschePieneDiviChild\Modules\ModuleBase;

class ShapeWave extends ModuleBase {

	use ShapeWaveTrait\RenderCallbackTrait;

	protected const JSON_FOLDER = 'shape-wave';

	protected const CLASS_NAME = 'tp_shape_wave';

	protected const DESIGN_GROUPS = [ 'tpDesignContent' ];
}
