import React, { ReactElement } from 'react';
import { CssStyle, StyleContainer, elementClassnames, useElementScriptData } from '@divi/module';
import { optionalDesignStyles } from './optional-design-styles';

/** The module's decoration attributes, or only the listed ones, for a module that offers fewer options. */
const pickDecoration = (decoration: Record<string, any> = {}, only: string[] | null = null): Record<string, any> =>
	only ? Object.fromEntries(Object.entries(decoration ?? {}).filter(([key]) => only.includes(key))) : decoration ?? {};

export const createModuleStyles = (
	designAttrs: string[] = [],
	cssFields: Record<string, any> = {},
	decorations: string[] | null = null
) => (props: any): ReactElement => {
	const { attrs, elements, settings, mode, state, noStyleTag, orderClass } = props;

	return (
		<StyleContainer mode={mode} state={state} noStyleTag={noStyleTag}>
			{elements.style({
				attrName: 'module',
				styleProps: {
					disabledOn: {
						disabledModuleVisibility: settings?.disabledModuleVisibility ?? null,
					},
					...(decorations ? { attrsFilter: (decoration: Record<string, any>) => pickDecoration(decoration, decorations) } : {}),
				},
			})}
			{optionalDesignStyles(elements, attrs, designAttrs)}
			{/* "CSS personalizzato" (Advanced tab), as on the site: ModuleBase::module_styles(). */}
			<CssStyle selector={orderClass} attr={attrs?.css} cssFields={cssFields} />
		</StyleContainer>
	);
};

export const createModuleClassnames = (className: string, decorations: string[] | null = null) => (args: any): void => {
	const { classnamesInstance, attrs } = args;
	const decorationAttrs: Record<string, any> = {
		...pickDecoration(attrs?.module?.decoration, decorations),
		link: decorations ? {} : attrs?.module?.advanced?.link ?? {},
	};

	classnamesInstance.add(className, true);
	classnamesInstance.add(elementClassnames({ attrs: decorationAttrs }));
};

export const createModuleScriptData = (decorations: string[] | null = null) => (props: any): ReactElement | null => {
	const { attrs, id, selector } = props;

	useElementScriptData({
		attrs: pickDecoration(attrs?.module?.decoration, decorations),
		selector,
		id,
	});

	return null;
};
