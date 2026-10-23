<?php
/**
 * Module: TP Theme Footer.
 *
 * Renders the global Theme Builder footer with editable fields
 * while keeping the original markup/classes so the existing CSS
 * stays pixel-perfect.
 */

namespace TaschePieneDiviChild\Modules\ThemeFooter;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use TaschePieneDiviChild\Modules\ModuleBase;

class ThemeFooter extends ModuleBase {

	use ThemeFooterTrait\RenderCallbackTrait;

	protected const JSON_FOLDER = 'theme-footer';

	protected const CLASS_NAME = 'tp_theme_footer';

	protected const DESIGN_GROUPS = [ 'tpDesignText', 'tpDesignCopyright', 'tpDesignLogo', 'tpDesignCharacterOne', 'tpDesignCharacterFour', 'tpDesignCharacterThree', 'tpDesignContent' ];
}
