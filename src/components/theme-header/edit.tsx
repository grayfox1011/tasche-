import React, { ReactElement } from 'react';
import { ModuleContainer } from '@divi/module';

import { NavPreviewItem, ThemeHeaderEditProps } from './types';
import metadata from '../../../modules-json/theme-header/module.json';
import { createModuleClassnames, createModuleScriptData, createModuleStyles } from '../../utils/module-boilerplate';
import { buildStyleVars, getColor, getString } from '../../utils/attrs';
import { useDynamicText } from '../../utils/dynamic';
import { getWpMenuItems } from '../../utils/wp-menus';

const MODULE_CLASSNAME = 'tp_theme_header';

const ModuleStyles = createModuleStyles(['tpDesignText', 'tpDesignLogo', 'tpDesignContent'], metadata.customCssFields);
const moduleClassnames = createModuleClassnames(MODULE_CLASSNAME);
const ModuleScriptData = createModuleScriptData();

const DEFAULT_NAV: NavPreviewItem[] = [
	{ title: 'Home', url: '#home' },
	{ title: 'I tramezzini', url: '#menu' },
	{ title: 'La nostra idea', url: '#chi-siamo' },
	{ title: 'Zena', url: '#materie' },
	{ title: 'Dove siamo', url: '#location' },
];

export const ThemeHeaderEdit = (props: ThemeHeaderEditProps): ReactElement => {
	const { attrs, elements, id, name } = props;

	const logoAlt = useDynamicText(getString(attrs.logoAlt, 'Tasche Piene'));
	const logoLink = useDynamicText(getString(attrs.logoLink, '#home'));
	const burgerOpenLabel = useDynamicText(getString(attrs.burgerOpenLabel, 'Apri menu'));
	const burgerCloseLabel = useDynamicText(getString(attrs.burgerCloseLabel, 'Chiudi menu'));
	const menuLocation = getString(attrs.menuLocation, 'tp_main_nav');
	const navItems = getWpMenuItems(menuLocation, DEFAULT_NAV);
	const navStyle = buildStyleVars({
		'--tp-nav-bg': getColor(attrs.navBackgroundColor, ''),
		'--tp-nav-link-color': getColor(attrs.navLinkColor, ''),
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
			<header className="site-header tp-theme-header" id="site-header">
				<nav className="tp-theme-nav" aria-label="Principale" style={navStyle}>
					<a href={logoLink} className="tp-theme-nav-logo" aria-label={logoAlt}>
						{elements.render({ attrName: 'logoImage' })}
					</a>
					<button
						className="tp-theme-nav-toggle"
						type="button"
						aria-label={burgerOpenLabel}
						aria-expanded="false"
						aria-controls="primary-navigation"
						data-open-label={burgerOpenLabel}
						data-close-label={burgerCloseLabel}
					>
						<span />
						<span />
						<span />
					</button>
					<ul id="primary-navigation" className="tp-theme-nav-links">
						{navItems.map((item) => (
							<li key={`${item.title}-${item.url}`}>
								<a href={item.url} target={item.target || undefined} rel={item.target === '_blank' ? 'noopener' : undefined}>
									<span className="tp-nav-link-text">{item.title}</span>
								</a>
							</li>
						))}
					</ul>
				</nav>
			</header>
		</ModuleContainer>
	);
};
