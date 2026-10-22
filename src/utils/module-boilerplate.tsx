import React, { ReactElement } from 'react';
import { StyleContainer, elementClassnames, useElementScriptData } from '@divi/module';
import { optionalDesignStyles } from './optional-design-styles';

export const createModuleStyles = (designAttrs: string[] = []) => (props: any): ReactElement => {
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
			{optionalDesignStyles(elements, attrs, designAttrs)}
		</StyleContainer>
	);
};

export const createModuleClassnames = (className: string) => (args: any): void => {
	const { classnamesInstance, attrs } = args;

	classnamesInstance.add(className, true);
	classnamesInstance.add(
		elementClassnames({
			attrs: {
				...(attrs?.module?.decoration ?? {}),
				link: attrs?.module?.advanced?.link ?? {},
			},
		})
	);
};

export const createModuleScriptData = () => (props: any): ReactElement | null => {
	const { attrs, id, selector } = props;

	useElementScriptData({
		attrs: attrs?.module?.decoration ?? {},
		selector,
		id,
	});

	return null;
};
