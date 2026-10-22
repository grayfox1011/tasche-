import React, { ReactElement } from 'react';
import { ModuleContainer } from '@divi/module';

import { FooterLogoAttrs, FooterLogoEditProps } from './types';
import { createModuleClassnames, createModuleScriptData, createModuleStyles } from '../../utils/module-boilerplate';
import { getString, getUploadUrl } from '../../utils/attrs';

const MODULE_CLASSNAME = 'tp_footer_logo';

const ModuleStyles = createModuleStyles(['tpDesignImage']);
const moduleClassnames = createModuleClassnames(MODULE_CLASSNAME);
const ModuleScriptData = createModuleScriptData();

export const FooterLogoEdit = (props: FooterLogoEditProps): ReactElement => {
	const { attrs, elements, id, name } = props;
	const url = getUploadUrl(attrs.image, getString(attrs.imageUrl, ''), '');
	const alt = getString(attrs.alt, 'Tasche Piene');
	const link = getString(attrs.link, '#home');

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
			<div className="tp-theme-footer-logo">
				<a href={link}>{url ? <img src={url} alt={alt} /> : <span className="tp-footer-logo-placeholder">Logo</span>}</a>
			</div>
		</ModuleContainer>
	);
};
