import React, { ReactElement } from 'react';
import { ModuleContainer } from '@divi/module';

import { FooterCharacterAttrs, FooterCharacterEditProps } from './types';
import { createModuleClassnames, createModuleScriptData, createModuleStyles } from '../../utils/module-boilerplate';
import { getString, getUploadUrl } from '../../utils/attrs';

const MODULE_CLASSNAME = 'tp_footer_character';

const ModuleStyles = createModuleStyles(['tpDesignImage']);
const moduleClassnames = createModuleClassnames(MODULE_CLASSNAME);
const ModuleScriptData = createModuleScriptData();

export const FooterCharacterEdit = (props: FooterCharacterEditProps): ReactElement => {
	const { attrs, elements, id, name } = props;
	const slot = getString(attrs.slot, 'one');
	const url = getUploadUrl(attrs.image, getString(attrs.imageUrl, ''), '');

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
			<div className={`tp-theme-footer-character-slot tp-theme-footer-character-slot--${slot}`}>
				{url ? (
					<img className="tp-theme-footer-character" src={url} alt="" aria-hidden="true" />
				) : (
					<span className="tp-theme-footer-character tp-footer-character-placeholder" />
				)}
			</div>
		</ModuleContainer>
	);
};
