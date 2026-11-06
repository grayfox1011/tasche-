import React, { ReactElement } from 'react';
import { ModuleContainer } from '@divi/module';

import { MarqueeItemEditProps } from './types';
import metadata from '../../../modules-json/marquee-item/module.json';
import { createModuleClassnames, createModuleScriptData, createModuleStyles } from '../../utils/module-boilerplate';
import { getSeparator, isMaterieMarquee, toPlainAttrs } from '../../utils/marquee';

const MODULE_CLASSNAME = 'tp_marquee_item';

// What a phrase in the strip can take (module.json; MarqueeItem::PHRASE_DECORATIONS on the site): values saved
// earlier for other options stay out of the preview too.
const PHRASE_DECORATIONS = ['background', 'spacing', 'border', 'boxShadow', 'filters', 'transform', 'overflow', 'transition', 'disabledOn'];

const ModuleStyles = createModuleStyles([], metadata.customCssFields, PHRASE_DECORATIONS);
const moduleClassnames = createModuleClassnames(MODULE_CLASSNAME, PHRASE_DECORATIONS);
const ModuleScriptData = createModuleScriptData(PHRASE_DECORATIONS);

export const MarqueeItemEdit = (props: MarqueeItemEditProps): ReactElement => {
	const { attrs, elements, id, name } = props;
	const parentAttrs = toPlainAttrs((props as { parentAttrs?: unknown }).parentAttrs);
	// In the strip of Materie prime the Scritta is one of the phrases of the strip, as on the site (a phrase and
	// its separator): the strip's rules give it the look of the site, and a click on the text selects it.
	const inMaterie = isMaterieMarquee(parentAttrs);

	const container = (
		<ModuleContainer
			attrs={attrs}
			elements={elements}
			id={id}
			name={name}
			className={inMaterie ? 'tp-marquee__phrase' : undefined}
			moduleClassName={MODULE_CLASSNAME}
			moduleOrderClassName={MODULE_CLASSNAME}
			stylesComponent={ModuleStyles}
			classnamesFunction={moduleClassnames}
			scriptDataComponent={ModuleScriptData}
		>
			{elements.styleComponents({ attrName: 'module' })}
			<div className="tp-marquee-item__row">
				<div className="tp-marquee-item__field">{elements.render({ attrName: 'phrase', allowEmptyValue: true })}</div>
			</div>
		</ModuleContainer>
	);

	if (!inMaterie) {
		return container;
	}

	return (
		<>
			{container}
			<span className="tp-marquee__separator" aria-hidden="true">
				{getSeparator(parentAttrs)}
			</span>
		</>
	);
};
