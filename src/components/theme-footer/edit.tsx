import React, { ReactElement } from 'react';
import { ModuleContainer } from '@divi/module';

import { ThemeFooterAttrs, ThemeFooterEditProps } from './types';
import { createModuleClassnames, createModuleScriptData, createModuleStyles } from '../../utils/module-boilerplate';
import { buildStyleVars, getColor, getString, getUploadUrl } from '../../utils/attrs';
import { getWpMenuItems } from '../../utils/wp-menus';

const MODULE_CLASSNAME = 'tp_theme_footer';
const ASSET_BASE = '/wp-content/themes/tasche-piene-divi-child/assets';

const ModuleStyles = createModuleStyles(['tpDesignText', 'tpDesignCopyright', 'tpDesignLogo', 'tpDesignCharacterOne', 'tpDesignCharacterFour', 'tpDesignCharacterThree', 'tpDesignContent']);
const moduleClassnames = createModuleClassnames(MODULE_CLASSNAME);
const ModuleScriptData = createModuleScriptData();

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
		fallbackSrc: `${ASSET_BASE}/images/footer/personaggi-3.webp`,
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

export const ThemeFooterEdit = (props: ThemeFooterEditProps): ReactElement => {
	const { attrs, elements, id, name } = props;

	const footerStyle = buildStyleVars({
		'--tp-footer-bg': getColor(attrs.footerBackgroundColor, '#054726'),
		'--tp-footer-link-color': getColor(attrs.footerLinkColor, '#fff5e9'),
	});
	const navAriaLabel = getString(attrs.navAriaLabel, 'Footer');
	const logoLink = getString(attrs.logoLink, '#home');
	const menuLeftLocation = getString(attrs.menuLeftLocation, 'tp_footer_left');
	const menuRightLocation = getString(attrs.menuRightLocation, 'tp_footer_right');
	const navLeft = getWpMenuItems(menuLeftLocation, DEFAULT_NAV_LEFT);
	const navRight = getWpMenuItems(menuRightLocation, DEFAULT_NAV_RIGHT);

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
			<footer className="site-footer tp-theme-footer" style={footerStyle}>
				<div className="tp-theme-footer-container">
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
					<div className="tp-theme-footer-copyright">
						{COPYRIGHT_LINES.map((line) => {
							const text = getString(attrs[line.textKey], line.fallback);
							const link = getString(attrs[line.linkKey], '');
							return (
								<span key={line.textKey}>
									{link ? <a href={link}>{text}</a> : text}
								</span>
							);
						})}
					</div>
				</div>
			</footer>
		</ModuleContainer>
	);
};
