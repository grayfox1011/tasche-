import React, { ReactElement } from 'react';
import { ModuleContainer } from '@divi/module';

import { FooterDecorAttrs, FooterDecorEditProps } from './types';
import { createModuleClassnames, createModuleScriptData, createModuleStyles } from '../../utils/module-boilerplate';
import { getString, getUploadUrl } from '../../utils/attrs';

const MODULE_CLASSNAME = 'tp_footer_decor';

const ModuleStyles = createModuleStyles(['tpDesignImage', 'tpDesignContent']);
const moduleClassnames = createModuleClassnames(MODULE_CLASSNAME);
const ModuleScriptData = createModuleScriptData();

export const FooterDecorEdit = (props: FooterDecorEditProps): ReactElement => {
	const { attrs, elements, id, name } = props;
	const url = getUploadUrl(attrs.image, getString(attrs.imageUrl, ''), '');
	const alt = getString(attrs.alt, '');
	const top = getString(attrs.posTop, '0');
	const left = getString(attrs.posLeft, '0');
	const width = getString(attrs.width, '120');
	const zIndex = getString(attrs.zIndex, '20');

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
			<div
				className="tp-theme-footer-decor-item"
				style={{ top, left, width: `${width}px`, zIndex } as React.CSSProperties}
			>
				{url ? (
					<img src={url} alt={alt} />
				) : (
					<span className="tp-footer-decor-placeholder">Decorazione</span>
				)}
			</div>
		</ModuleContainer>
	);
};
