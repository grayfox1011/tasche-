<?php
/**
 * Module: TP Split Text.
 */

namespace TaschePieneDiviChild\Modules\SplitText;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use TaschePieneDiviChild\Modules\ModuleBase;

class SplitText extends ModuleBase {

	use SplitTextTrait\RenderCallbackTrait;

	protected const JSON_FOLDER = 'split-text';

	protected const CLASS_NAME = 'tp_split_text';

	protected const DESIGN_GROUPS = [ 'tpDesignTitle', 'tpDesignContent' ];
}
