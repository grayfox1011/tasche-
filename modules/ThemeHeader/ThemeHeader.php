<?php
/**
 * Module: TP Theme Header.
 *
 * Renders the global Theme Builder header with editable fields
 * while keeping the original markup/classes so the existing CSS
 * stays pixel-perfect.
 */

namespace TaschePieneDiviChild\Modules\ThemeHeader;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use TaschePieneDiviChild\Modules\ModuleBase;

class ThemeHeader extends ModuleBase {

	use ThemeHeaderTrait\RenderCallbackTrait;

	protected const JSON_FOLDER = 'theme-header';

	protected const CLASS_NAME = 'tp_theme_header';

	protected const DESIGN_GROUPS = [ 'tpDesignText', 'tpDesignLogo', 'tpDesignContent' ];
}
