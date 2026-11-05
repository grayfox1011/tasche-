import React, { ReactElement } from 'react';
import { ModuleContainer } from '@divi/module';

import { FooterNavItemEditProps } from './types';
import metadata from '../../../modules-json/footer-nav-item/module.json';
import { createModuleClassnames, createModuleScriptData, createModuleStyles } from '../../utils/module-boilerplate';
import { NAV_ITEM_DEFAULTS, navItemValue } from '../header-nav-item/values';

const MODULE_CLASSNAME = 'tp_footer_nav_item';

const ModuleStyles = createModuleStyles([], metadata.customCssFields);
const moduleClassnames = createModuleClassnames(MODULE_CLASSNAME);
const ModuleScriptData = createModuleScriptData();

// A Voce of Footer sito: the footer draws the link from its fields; this element only stands for it in the builder.
export const FooterNavItemEdit = (props: FooterNavItemEditProps): ReactElement => {
	const { attrs, elements, id, name } = props;

	return (
		<ModuleContainer
			attrs={attrs}
			elements={elements}
			id={id}
			name={name}
			moduleClassName={MODULE_CLASSNAME}
			moduleOrderClassName={MODULE_CLASSNAME}
			stylesComponent={ModuleStyles}
			classnamesFunction={moduleClassnames}
			scriptDataComponent={ModuleScriptData}
		>
			{elements.styleComponents({ attrName: 'module' })}
			<span className="tp-footer-nav-item-vb">{navItemValue(attrs.text, NAV_ITEM_DEFAULTS.text)}</span>
		</ModuleContainer>
	);
};
