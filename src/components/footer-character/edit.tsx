import React, { ReactElement } from 'react';
import { ModuleContainer } from '@divi/module';

import { FooterCharacterEditProps } from './types';
import metadata from '../../../modules-json/footer-character/module.json';
import { createModuleClassnames, createModuleScriptData, createModuleStyles } from '../../utils/module-boilerplate';
import { getString, getUploadUrl } from '../../utils/attrs';
import { useDynamicText } from '../../utils/dynamic';
import { footerCharacterSlot } from './values';

const MODULE_CLASSNAME = 'tp_footer_character';

const ModuleStyles = createModuleStyles(['tpDesignImage'], metadata.customCssFields);
const baseClassnames = createModuleClassnames(MODULE_CLASSNAME);
const ModuleScriptData = createModuleScriptData();

// The module's box is the character's place, as on the site (FooterCharacter::module_classnames()).
const moduleClassnames = (args: any): void => {
	const { classnamesInstance, attrs } = args;

	baseClassnames(args);
	classnamesInstance.add('tp-theme-footer-character-slot', true);
	classnamesInstance.add(`tp-theme-footer-character-slot--${footerCharacterSlot(getString(attrs?.slot, 'three'))}`, true);
	classnamesInstance.add('tp-theme-footer-character-slot--flip', getString(attrs?.flip, 'off') === 'on');
};

// A Personaggio of Footer sito: the drawing in its place; without a drawing, an empty box to click in the builder.
export const FooterCharacterEdit = (props: FooterCharacterEditProps): ReactElement => {
	const { attrs, elements, id, name } = props;
	const url = useDynamicText(getUploadUrl(attrs.image, getString(attrs.imageUrl, ''), ''));

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
			{url ? (
				<img className="tp-theme-footer-character" src={url} alt="" aria-hidden="true" />
			) : (
				<span className="tp-theme-footer-character tp-footer-character-placeholder" />
			)}
		</ModuleContainer>
	);
};
