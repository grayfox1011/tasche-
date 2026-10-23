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
}
