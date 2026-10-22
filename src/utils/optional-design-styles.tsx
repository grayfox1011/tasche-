import React, { ReactNode } from 'react';

// StyleContainer clones every direct child. A component keeps literal CSS safe
// through cloneElement while rendering only text inside the resulting style tag.
const CSSLiteral = ({ css }: { css: string }): React.ReactElement => <>{css}</>;

export const hasDesignValues = (value: unknown): boolean => {
	if (value && typeof value === 'object') return Object.values(value).some(hasDesignValues);
	return value !== undefined && value !== null && value !== '';
};

/**
 * Must be inserted directly inside StyleContainer. Its Children.map flattens
 * this array and passes preview mode/state to each native ElementStyle child.
 * The layer wraps the rendered CSS, keeping responsive @media rules intact.
 */
export const optionalDesignStyles = (elements: any, attrs: any, names: string[]): ReactNode[] => {
	const styledNames = names.filter((name) => hasDesignValues(attrs?.[name]?.decoration));
	if (!styledNames.length) return [];

	return [
		<CSSLiteral key="tp-authored-open" css="@layer tp-authored {" />,
		...styledNames.map((attrName) => React.cloneElement(elements.style({
			attrName,
			styleProps: { attrsFilter: () => attrs[attrName].decoration },
		}), { key: attrName })),
		<CSSLiteral key="tp-authored-close" css="}" />,
	];
};
