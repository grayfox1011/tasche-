import React, { ReactElement } from 'react';
import { ModuleContainer } from '@divi/module';

import { FooterCopyrightLineAttrs, FooterCopyrightLineEditProps } from './types';
import { createModuleClassnames, createModuleScriptData, createModuleStyles } from '../../utils/module-boilerplate';
import { getString } from '../../utils/attrs';

const MODULE_CLASSNAME = 'tp_footer_copyright_line';

const ModuleStyles = createModuleStyles(['tpDesignText']);
const moduleClassnames = createModuleClassnames(MODULE_CLASSNAME);
const ModuleScriptData = createModuleScriptData();

export const FooterCopyrightLineEdit = (props: FooterCopyrightLineEditProps): ReactElement => {
	const { attrs, elements, id, name } = props;
	const text = getString(attrs.text, 'Nuova riga');
	const link = getString(attrs.link, '');

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
			<span>{link ? <a href={link}>{text}</a> : text}</span>
		</ModuleContainer>
	);
};
