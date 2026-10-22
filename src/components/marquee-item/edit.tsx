import React, { ReactElement } from 'react';
import { ModuleContainer } from '@divi/module';

import { MarqueeItemEditProps } from './types';
import { createModuleClassnames, createModuleScriptData, createModuleStyles } from '../../utils/module-boilerplate';

const MODULE_CLASSNAME = 'tp_marquee_item';

const ModuleStyles = createModuleStyles();
const moduleClassnames = createModuleClassnames(MODULE_CLASSNAME);
const ModuleScriptData = createModuleScriptData();

export const MarqueeItemEdit = (props: MarqueeItemEditProps): ReactElement => {
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
			<div className="tp-marquee-item__row">
				<div className="tp-marquee-item__field">{elements.render({ attrName: 'phrase', allowEmptyValue: true })}</div>
			</div>
		</ModuleContainer>
	);
};
