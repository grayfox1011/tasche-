import React, { ReactElement } from 'react';
import { ModuleContainer } from '@divi/module';

import { FooterCopyrightEditProps } from './types';
import { createModuleClassnames, createModuleScriptData, createModuleStyles } from '../../utils/module-boilerplate';
import { getString } from '../../utils/attrs';

const MODULE_CLASSNAME = 'tp_footer_copyright';

const ModuleStyles = createModuleStyles(['tpDesignText']);
const moduleClassnames = createModuleClassnames(MODULE_CLASSNAME);
const ModuleScriptData = createModuleScriptData();

const renderCopyrightLine = (text: string, link: string): ReactElement => {
	if (link) {
		return <a href={link}>{text}</a>;
	}

	return <>{text}</>;
};

export const FooterCopyrightEdit = (props: FooterCopyrightEditProps): ReactElement => {
	const { attrs, elements, id, name } = props;
	const copyright1 = getString(attrs.copyrightLine1, 'Copyright 2026, Tasche piene tutti i diritti riservati');
	const copyright2 = getString(attrs.copyrightLine2, 'P.IVA 03942309300');
	const copyright3 = getString(attrs.copyrightLine3, 'Privacy-Cookie');
	const copyright4 = getString(attrs.copyrightLine4, 'Designed By Mochi');
	const copyrightLink1 = getString(attrs.copyrightLink1, '');
	const copyrightLink2 = getString(attrs.copyrightLink2, '');
	const copyrightLink3 = getString(attrs.copyrightLink3, '');
	const copyrightLink4 = getString(attrs.copyrightLink4, '');

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
			<div className="tp-theme-footer-copyright">
				<span>{renderCopyrightLine(copyright1, copyrightLink1)}</span>
				<span>{renderCopyrightLine(copyright2, copyrightLink2)}</span>
				<span>{renderCopyrightLine(copyright3, copyrightLink3)}</span>
				<span>{renderCopyrightLine(copyright4, copyrightLink4)}</span>
			</div>
		</ModuleContainer>
	);
};
