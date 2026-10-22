import React, { ReactElement } from 'react';
import { ModuleContainer } from '@divi/module';

import { FooterNavRightEditProps, NavPreviewItem } from './types';
import { createModuleClassnames, createModuleScriptData, createModuleStyles } from '../../utils/module-boilerplate';
import { collectLinkFields } from '../../utils/attrs';

const MODULE_CLASSNAME = 'tp_footer_nav_right';

const ModuleStyles = createModuleStyles(['tpDesignText']);
const moduleClassnames = createModuleClassnames(MODULE_CLASSNAME);
const ModuleScriptData = createModuleScriptData();

const DEFAULT_RIGHT: NavPreviewItem[] = [
	{ title: 'TORNA SÙ', url: '#home' },
	{ title: 'FACEBOOK', url: 'https://facebook.com' },
	{ title: 'INSTAGRAM', url: 'https://instagram.com' },
];

const renderNavColumn = (items: NavPreviewItem[]): ReactElement => (
	<div className="tp-theme-footer-nav-col">
		{items.map((item) => (
			<a key={`${item.title}-${item.url}`} href={item.url}>
				{item.title}
			</a>
		))}
	</div>
);

export const FooterNavRightEdit = (props: FooterNavRightEditProps): ReactElement => {
	const { attrs, elements, id, name } = props;
	const rightItems = collectLinkFields(attrs as Record<string, unknown>, 'footerRight', 3, DEFAULT_RIGHT);

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
			<div className="tp-theme-footer-nav-rail-right">
				{renderNavColumn(rightItems)}
			</div>
		</ModuleContainer>
	);
};
