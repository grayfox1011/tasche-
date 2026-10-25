import React, { ReactElement } from 'react';
import { ModuleContainer } from '@divi/module';

import { SplitTextEditProps } from './types';
import { ModuleStyles } from './styles';
import { moduleClassnames } from './module-classnames';
import { ModuleScriptData } from './module-script-data';
import { getString } from '../../utils/attrs';
import { DynamicText, withoutDynamicContent } from '../../utils/dynamic';

const MODULE_CLASSNAME = 'tp_split_text';
const ALLOWED_TAGS = ['h1', 'h2', 'h3', 'p', 'div'];

const LINE_FIELDS = [
	{ attrName: 'lineOne', fallback: 'Titolo animato' },
	{ attrName: 'lineTwo', fallback: '' },
	{ attrName: 'lineThree', fallback: '' },
	{ attrName: 'lineFour', fallback: '' },
	{ attrName: 'lineFive', fallback: '' },
	{ attrName: 'lineSix', fallback: '' },
] as const;

export const SplitTextEdit = (props: SplitTextEditProps): ReactElement => {
	const { attrs, elements, id, name } = props;
	const tagValue = getString(attrs.tagName, 'h1');
	const Tag = (ALLOWED_TAGS.includes(tagValue) ? tagValue : 'h1') as keyof JSX.IntrinsicElements;
	const variant = getString(attrs.variant, 'hero');
	const lines = LINE_FIELDS.map(({ attrName, fallback }) =>
		getString(attrs[attrName as keyof typeof attrs], fallback).trim()
	).filter(Boolean);
	const label = lines.map(withoutDynamicContent).filter(Boolean).join(' ') || 'Titolo animato';

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
			<Tag className={`tp-split-text tp-split-text--${variant}`} aria-label={label}>
				{LINE_FIELDS.map(({ attrName, fallback }, index) => {
					const line = getString(attrs[attrName as keyof typeof attrs], fallback).trim();

					if (!line) {
						return null;
					}

					return (
						<span className="tp-split-text__line" aria-hidden="true" key={attrName}>
							<span className="tp-split-text__line-inner">
								<DynamicText value={line} />
							</span>
						</span>
					);
				})}
			</Tag>
		</ModuleContainer>
	);
};
