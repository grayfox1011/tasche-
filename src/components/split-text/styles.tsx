import React, { ReactElement } from 'react';
import { StyleContainer } from '@divi/module';
import { optionalDesignStyles } from '../../utils/optional-design-styles';

export const ModuleStyles = (props: any): ReactElement => {
	const { attrs, elements, settings, mode, state, noStyleTag } = props;

	return (
		<StyleContainer mode={mode} state={state} noStyleTag={noStyleTag}>
			{elements.style({
				attrName: 'module',
				styleProps: {
					disabledOn: {
						disabledModuleVisibility: settings?.disabledModuleVisibility ?? null,
					},
				},
			})}
			{optionalDesignStyles(elements, attrs, ['tpDesignTitle', 'tpDesignContent'])}
		</StyleContainer>
	);
};
