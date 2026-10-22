import React, { ReactElement } from 'react';
import { ModuleContainer } from '@divi/module';

import { ThemeFooterShellEditProps } from './types';
import { createModuleClassnames, createModuleScriptData, createModuleStyles } from '../../utils/module-boilerplate';
import { buildStyleVars, getString } from '../../utils/attrs';

const MODULE_CLASSNAME = 'tp_theme_footer_shell';

const ModuleStyles = createModuleStyles(['tpDesignContent']);
const moduleClassnames = createModuleClassnames(MODULE_CLASSNAME);
const ModuleScriptData = createModuleScriptData();

export const ThemeFooterShellEdit = (props: ThemeFooterShellEditProps): ReactElement => {
	const { attrs, elements, id, name } = props;
	const footerStyle = buildStyleVars({
		'--tp-footer-bg': getString(attrs.footerBackgroundColor, ''),
		'--tp-footer-link-color': getString(attrs.footerLinkColor, ''),
	});

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
			<footer className="site-footer tp-theme-footer" style={footerStyle} />
		</ModuleContainer>
	);
};
