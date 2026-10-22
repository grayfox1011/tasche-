import React, { ReactElement } from 'react';
import { ModuleContainer } from '@divi/module';

import { FooterNavLeftEditProps, NavPreviewItem } from './types';
import { createModuleClassnames, createModuleScriptData, createModuleStyles } from '../../utils/module-boilerplate';
import { collectLinkFields, getString } from '../../utils/attrs';

const MODULE_CLASSNAME = 'tp_footer_nav_left';

const ModuleStyles = createModuleStyles(['tpDesignText']);
const moduleClassnames = createModuleClassnames(MODULE_CLASSNAME);
const ModuleScriptData = createModuleScriptData();

const DEFAULT_LEFT: NavPreviewItem[] = [
	{ title: 'MENÙ', url: '#menu' },
	{ title: 'DOVE SIAMO', url: '#location' },
	{ title: 'SU DI NOI', url: '#chi-siamo' },
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

export const FooterNavLeftEdit = (props: FooterNavLeftEditProps): ReactElement => {
	const { attrs, elements, id, name } = props;
	const navAriaLabel = getString(attrs.navAriaLabel, 'Footer');
	const leftItems = collectLinkFields(attrs as Record<string, unknown>, 'footerLeft', 3, DEFAULT_LEFT);

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
			<nav className="tp-theme-footer-nav tp-theme-footer-nav--left-rail" aria-label={navAriaLabel}>
				{renderNavColumn(leftItems)}
			</nav>
		</ModuleContainer>
	);
};
