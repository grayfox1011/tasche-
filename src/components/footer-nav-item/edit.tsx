import React, { ReactElement } from 'react';
import { ModuleContainer } from '@divi/module';

import { FooterNavItemAttrs, FooterNavItemEditProps } from './types';
import { createModuleClassnames, createModuleScriptData, createModuleStyles } from '../../utils/module-boilerplate';
import { getString } from '../../utils/attrs';

const MODULE_CLASSNAME = 'tp_footer_nav_item';

const ModuleStyles = createModuleStyles(['tpDesignText']);
const moduleClassnames = createModuleClassnames(MODULE_CLASSNAME);
const ModuleScriptData = createModuleScriptData();

export const FooterNavItemEdit = (props: FooterNavItemEditProps): ReactElement => {
	const { attrs, elements, id, name } = props;
	const text = getString(attrs.text, 'Nuova voce');
	const url = getString(attrs.link, '#');
	const blank = getString(attrs.target, 'off') === 'on';

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
			<a href={url} target={blank ? '_blank' : undefined} rel={blank ? 'noopener' : undefined}>
				{text}
			</a>
		</ModuleContainer>
	);
};
