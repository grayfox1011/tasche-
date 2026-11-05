import React, { ReactElement, useMemo } from 'react';
import { useSelect } from '@divi/data';
import { ChildModulesContainer, ModuleContainer } from '@divi/module';

import { NavPreviewItem, ThemeHeaderEditProps } from './types';
import metadata from '../../../modules-json/theme-header/module.json';
import { createModuleClassnames, createModuleScriptData, createModuleStyles } from '../../utils/module-boilerplate';
import { buildStyleVars, getColor, getString } from '../../utils/attrs';
import { useDynamicText } from '../../utils/dynamic';
import { getWpMenuItems, WpNavItem } from '../../utils/wp-menus';
import { NAV_ITEM_DEFAULTS, navItemValue } from '../header-nav-item/values';
import { HeaderNavItemAttrs } from '../header-nav-item/types';

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
	const { attrs, childrenIds = [], elements, id, name } = props;

	const logoAlt = useDynamicText(getString(attrs.logoAlt, 'Tasche Piene'));
	const logoLink = useDynamicText(getString(attrs.logoLink, '#home'));
	const burgerOpenLabel = useDynamicText(getString(attrs.burgerOpenLabel, 'Apri menu'));
	const burgerCloseLabel = useDynamicText(getString(attrs.burgerCloseLabel, 'Chiudi menu'));
	const menuLocation = getString(attrs.menuLocation, 'tp_main_nav');
	const childrenKey = childrenIds.join('|');

	// The Voci (Header sito > Voci del menu), one line each (text, link, new tab), so that the store answer
	// compares equal while nothing changes; read as on the site (tp_child_header_nav_items_from_block()).
	const voci = useSelect(
		(select) => {
			if (!childrenIds.length) {
				return [] as string[];
			}

			const getAttrs = select('divi/edit-post').getModuleAttrs;

			return childrenIds.map((childId) => {
				const voce = (getAttrs(childId) ?? {}) as HeaderNavItemAttrs;

				return [
					navItemValue(voce.text, NAV_ITEM_DEFAULTS.text),
					navItemValue(voce.link, NAV_ITEM_DEFAULTS.link),
					navItemValue(voce.target, NAV_ITEM_DEFAULTS.target),
				].join('\u0000');
			});
		},
		[childrenKey]
	);

	const navItems: WpNavItem[] = useMemo(
		() =>
			childrenIds.length
				? voci
						.map((line) => line.split('\u0000'))
						.filter(([title]) => title)
						.map(([title, url, target]) => ({ title, url: url || '#', target: target === 'on' ? '_blank' : '' }))
				: getWpMenuItems(menuLocation, DEFAULT_NAV),
		[childrenKey, voci.join('\u0001'), menuLocation]
	);
	const navStyle = buildStyleVars({
		'--tp-nav-bg': getColor(attrs.navBackgroundColor, ''),
		'--tp-nav-link-color': getColor(attrs.navLinkColor, ''),
	});

	return (
		<ModuleContainer
			attrs={attrs}
			childrenIds={childrenIds}
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
					{childrenIds.length ? (
						<div className="tp-theme-nav-voci-vb" hidden>
							<ChildModulesContainer ids={childrenIds} />
						</div>
					) : null}
				</nav>
			</header>
		</ModuleContainer>
	);
};
