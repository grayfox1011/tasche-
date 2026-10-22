import React, { ReactElement } from 'react';
import { ModuleContainer } from '@divi/module';

import { FooterCharactersAttrs, FooterCharactersEditProps } from './types';
import { createModuleClassnames, createModuleScriptData, createModuleStyles } from '../../utils/module-boilerplate';
import { getUploadUrl } from '../../utils/attrs';

const MODULE_CLASSNAME = 'tp_footer_characters';
const ASSET_BASE = '/wp-content/themes/tasche-piene-divi-child/assets';

const ModuleStyles = createModuleStyles(['tpDesignCharacterOne', 'tpDesignCharacterFour', 'tpDesignCharacterThree']);
const moduleClassnames = createModuleClassnames(MODULE_CLASSNAME);
const ModuleScriptData = createModuleScriptData();

const FOOTER_CHARACTERS: Array<{
	attrName: keyof FooterCharactersAttrs;
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

export const FooterCharactersEdit = (props: FooterCharactersEditProps): ReactElement => {
	const { attrs, elements, id, name } = props;

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
			<div className="tp-theme-footer-characters">
				{FOOTER_CHARACTERS.map((character) => {
					const src = getUploadUrl(attrs[character.attrName], '', character.fallbackSrc);

					return (
						<div
							key={character.attrName}
							className={`tp-theme-footer-character-slot ${character.slotClass}`}
						>
							<img
								className="tp-theme-footer-character"
								src={src}
								alt=""
								aria-hidden="true"
							/>
							<div className="tp-theme-footer-character-edit">
								{elements.render({
									attrName: String(character.attrName),
									allowEmptyValue: true,
								})}
							</div>
						</div>
					);
				})}
			</div>
		</ModuleContainer>
	);
};
