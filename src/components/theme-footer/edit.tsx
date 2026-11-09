import React, { ReactElement, useEffect, useMemo } from 'react';
import { dispatch, useSelect } from '@divi/data';
import { ChildModulesContainer, ModuleContainer } from '@divi/module';

import { ThemeFooterAttrs, ThemeFooterEditProps } from './types';
import metadata from '../../../modules-json/theme-footer/module.json';
import { createModuleClassnames, createModuleScriptData, createModuleStyles } from '../../utils/module-boilerplate';
import { buildStyleVars, getColor, getString, getUploadUrl } from '../../utils/attrs';
import { ASSET_BASE } from '../../utils/asset-base';
import { DynamicText, useDynamicText } from '../../utils/dynamic';
import { getWpMenuItems, WpNavItem } from '../../utils/wp-menus';
import { NAV_ITEM_DEFAULTS, navItemValue } from '../header-nav-item/values';
import { FooterNavItemAttrs } from '../footer-nav-item/types';

const MODULE_CLASSNAME = 'tp_theme_footer';

const ModuleStyles = createModuleStyles(['tpDesignText', 'tpDesignCopyright', 'tpDesignLogo', 'tpDesignCharacterOne', 'tpDesignCharacterFour', 'tpDesignCharacterThree', 'tpDesignContent'], metadata.customCssFields);
const moduleClassnames = createModuleClassnames(MODULE_CLASSNAME);
const ModuleScriptData = createModuleScriptData();

// The theme's three places of a Footer sito not converted to Personaggi yet (made before 0.6.8).
const FOOTER_CHARACTERS: Array<{
	attrName: keyof ThemeFooterAttrs;
	slotClass: string;
	fallbackSrc: string;
}> = [
	{
		attrName: 'characterOneImage',
		slotClass: 'tp-theme-footer-character-slot--one',
		fallbackSrc: `${ASSET_BASE}/images/footer/personaggi-1.webp`,
	},
	{
		attrName: 'characterFourImage',
		slotClass: 'tp-theme-footer-character-slot--four',
		fallbackSrc: `${ASSET_BASE}/images/footer/personaggi-4.webp`,
	},
	{
		attrName: 'characterThreeImage',
		slotClass: 'tp-theme-footer-character-slot--three',
		// Empty since 0.5.9: the Figma has the couple and the pigeons only.
		fallbackSrc: '',
	},
];

const DEFAULT_NAV_LEFT = [
	{ title: 'MENÙ', url: '#menu' },
	{ title: 'DOVE SIAMO', url: '#location' },
	{ title: 'SU DI NOI', url: '#chi-siamo' },
];

const DEFAULT_NAV_RIGHT = [
	{ title: 'TORNA SÙ', url: '#home' },
	{ title: 'FACEBOOK', url: 'https://facebook.com', target: '_blank' },
	{ title: 'INSTAGRAM', url: 'https://instagram.com', target: '_blank' },
];

const COPYRIGHT_LINES: Array<{ textKey: keyof ThemeFooterAttrs; linkKey: keyof ThemeFooterAttrs; fallback: string }> = [
	{ textKey: 'copyrightLine1', linkKey: 'copyrightLink1', fallback: 'Copyright 2026, Tasche piene tutti i diritti riservati' },
	{ textKey: 'copyrightLine2', linkKey: 'copyrightLink2', fallback: 'P.IVA 03942309300' },
	{ textKey: 'copyrightLine3', linkKey: 'copyrightLink3', fallback: 'Privacy' },
	{ textKey: 'copyrightLine4', linkKey: 'copyrightLink4', fallback: 'Cookie' },
	{ textKey: 'copyrightLine5', linkKey: 'copyrightLink5', fallback: 'Designed By Mochi' },
];

const CopyrightLine = ({ text, link }: { text: string; link: string }): ReactElement => {
	const href = useDynamicText(link);

	return <span>{href ? <a href={href}><DynamicText value={text} /></a> : <DynamicText value={text} />}</span>;
};

export const ThemeFooterEdit = (props: ThemeFooterEditProps): ReactElement => {
	const { attrs, childrenIds = [], elements, id, name } = props;

	const footerStyle = buildStyleVars({
		'--tp-footer-bg': getColor(attrs.footerBackgroundColor, '#054726'),
		'--tp-footer-link-color': getColor(attrs.footerLinkColor, '#fff5e9'),
	});
	const navAriaLabel = useDynamicText(getString(attrs.navAriaLabel, 'Footer'));
	const logoLink = useDynamicText(getString(attrs.logoLink, '#home'));
	const menuLeftLocation = getString(attrs.menuLeftLocation, 'tp_footer_left');
	const menuRightLocation = getString(attrs.menuRightLocation, 'tp_footer_right');
	const childrenKey = childrenIds.join('|');

	// The elements (Footer sito > Elementi), one line each so that the store answer compares equal while nothing
	// changes: a Personaggio ("C", its id) or a Voce ("V", its id, column, text, link, new tab), the Voci read as on
	// the site (tp_child_footer_nav_items_from_block()).
	const children = useSelect(
		(select) => {
			if (!childrenIds.length) {
				return [] as string[];
			}

			const store = select('divi/edit-post');

			return childrenIds.map((childId) => {
				if (store.getModuleName?.(childId) === 'tp/footer-character') {
					return ['C', childId].join('\u0000');
				}

				const voce = (store.getModuleAttrs(childId) ?? {}) as FooterNavItemAttrs;

				return [
					'V',
					childId,
					navItemValue(voce.column, 'left') === 'right' ? 'right' : 'left',
					navItemValue(voce.text, NAV_ITEM_DEFAULTS.text),
					navItemValue(voce.link, NAV_ITEM_DEFAULTS.link),
					navItemValue(voce.target, NAV_ITEM_DEFAULTS.target),
				].join('\u0000');
			});
		},
		[childrenKey]
	);
	const childrenLines = children.join('\u0001');

	const [vociIds, characterIds, voci]: [string[], string[], string[][]] = useMemo(() => {
		const lines = children.map((line) => line.split('\u0000'));

		return [
			lines.filter((line) => line[0] === 'V').map((line) => line[1]),
			lines.filter((line) => line[0] === 'C').map((line) => line[1]),
			lines.filter((line) => line[0] === 'V').map((line) => line.slice(2)),
		];
	}, [childrenLines]);

	// Its characters are its Personaggi (0.6.8) when it has some or was made or converted with them; otherwise
	// the theme's three places, as on the site (tp_child_footer_characters_as_items()).
	const itemsMarked = getString(attrs.characterItems, '') === 'on';
	const asItems = itemsMarked || characterIds.length > 0;

	// A Footer sito with Personaggi but not marked (added with Divi's placeholder content off): marked now, so
	// that removing the last one leaves it without characters, as on the site.
	useEffect(() => {
		if (!itemsMarked && characterIds.length > 0) {
			try {
				dispatch('divi/edit-post').editModuleAttribute({ id, attrName: 'characterItems.innerContent', value: { desktop: { value: 'on' } } });
			} catch (error) {
				// Not in the edited content (a preview): nothing to mark.
			}
		}
	}, [itemsMarked, characterIds.length, id]);

	const [navLeft, navRight]: [WpNavItem[], WpNavItem[]] = useMemo(() => {
		if (!vociIds.length) {
			return [getWpMenuItems(menuLeftLocation, DEFAULT_NAV_LEFT), getWpMenuItems(menuRightLocation, DEFAULT_NAV_RIGHT)];
		}

		const columns: { left: WpNavItem[]; right: WpNavItem[] } = { left: [], right: [] };

		voci.forEach(([column, title, url, target]) => {
			if (title) {
				columns[column === 'right' ? 'right' : 'left'].push({ title, url: url || '#', target: target === 'on' ? '_blank' : '' });
			}
		});

		return [columns.left, columns.right];
	}, [childrenLines, menuLeftLocation, menuRightLocation]);

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
			<footer className={`site-footer tp-theme-footer${asItems ? ' tp-theme-footer--items' : ''}`} style={footerStyle}>
				<div className="tp-theme-footer-container">
					{asItems ? (
						<div className="tp-theme-footer-characters">
							{characterIds.length ? <ChildModulesContainer ids={characterIds} /> : null}
						</div>
					) : (
						<div className="tp-theme-footer-characters tp-theme-footer-characters--static">
						{FOOTER_CHARACTERS.map((character) => {
							const src = getUploadUrl(attrs[character.attrName], '', character.fallbackSrc);

							return (
								<div key={character.attrName} className={`tp-theme-footer-character-slot ${character.slotClass}`}>
									{src ? (
										<img className="tp-theme-footer-character" src={src} alt="" aria-hidden="true" />
									) : null}
									<div className="tp-theme-footer-character-edit">
										{elements.render({ attrName: character.attrName, allowEmptyValue: true })}
									</div>
								</div>
							);
						})}
						</div>
					)}
					<div className="tp-theme-footer-logo">
						<a href={logoLink}>
							{elements.render({ attrName: 'logoImage', allowEmptyValue: true })}
						</a>
					</div>
					<nav className="tp-theme-footer-nav" aria-label={navAriaLabel}>
						<div className="tp-theme-footer-nav-col">
							{navLeft.map((item, index) => (
								<a
									key={`left-${index + 1}`}
									href={item.url}
									target={item.target || undefined}
									rel={item.target === '_blank' ? 'noopener' : undefined}
								>
									{item.title}
								</a>
							))}
						</div>
						<div className="tp-theme-footer-nav-col">
							{navRight.map((item, index) => (
								<a
									key={`right-${index + 1}`}
									href={item.url}
									target={item.target || undefined}
									rel={item.target === '_blank' ? 'noopener' : undefined}
								>
									{item.title}
								</a>
							))}
						</div>
					</nav>
					{vociIds.length ? (
						<div className="tp-theme-footer-voci-vb" hidden>
							<ChildModulesContainer ids={vociIds} />
						</div>
					) : null}
					<div className="tp-theme-footer-copyright">
						{COPYRIGHT_LINES.map((line) => (
							<CopyrightLine
								key={line.textKey}
								text={getString(attrs[line.textKey], line.fallback)}
								link={getString(attrs[line.linkKey], '')}
							/>
						))}
					</div>
				</div>
			</footer>
		</ModuleContainer>
	);
};
