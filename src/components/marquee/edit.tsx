import React, { ReactElement, useMemo } from 'react';
import { useSelect } from '@divi/data';
import { ChildModulesContainer, ModuleContainer } from '@divi/module';

import { getModuleOrderClassName } from '@divi/module-utils';

import { MarqueeEditProps } from './types';
import metadata from '../../../modules-json/marquee/module.json';
import { createModuleClassnames, createModuleScriptData, createModuleStyles } from '../../utils/module-boilerplate';
import { clampNumber, getString } from '../../utils/attrs';
import { DynamicText, withoutDynamicContent } from '../../utils/dynamic';

const MODULE_CLASSNAME = 'tp_marquee';

// A strip without Scritte shows these on the site (RenderCallbackTrait::get_phrases()): the preview too.
const DEFAULT_PHRASES = ['Stocco ogni giovedì', 'Trippa ogni venerdì', 'Polpette al sugo', 'Le acciughe di Luciano', 'Tasche piene di cucinato'];
const MATERIE_DEFAULT_PHRASES = ['Le cose che finiscono in tasca'];

type PreviewPhrase = { text: string; className: string };

// The class of a Scritta: its options (background, spacing, custom CSS...) reach its phrase, as on the site.
const getChildClass = (childId: string): string =>
	typeof getModuleOrderClassName === 'function' ? getModuleOrderClassName(childId) : '';

const ModuleStyles = createModuleStyles(['tpDesignText', 'tpDesignStrip'], metadata.customCssFields);
const moduleClassnames = createModuleClassnames(MODULE_CLASSNAME);
const ModuleScriptData = createModuleScriptData();

const getPhrase = (attrs?: { phrase?: { innerContent?: { desktop?: { value?: unknown } }; desktop?: { value?: unknown } } }): string =>
	getString(attrs?.phrase, '').trim();

const getModuleClass = (attrs?: MarqueeEditProps['attrs']): string =>
	getString(attrs?.module?.advanced?.htmlAttributes?.desktop?.value?.class, '');

export const MarqueeEdit = (props: MarqueeEditProps): ReactElement => {
	const { attrs, childModules, childrenIds = [], elements, id, name } = props;

	const fallbackPhrases = useMemo(
		() => (childModules ?? []).map((child) => getPhrase(child.props?.attrs)).filter(Boolean),
		[childModules]
	);
	const childrenKey = childrenIds.join('|');

	// One phrase per child, in order (empty ones included), so that each keeps its child's id.
	const storePhrases = useSelect(
		(select) => {
			if (!childrenIds.length) {
				return [] as string[];
			}

			const getAttrs = select('divi/edit-post').getModuleAttrs;

			return childrenIds.map((childId) => getPhrase(getAttrs(childId) as Parameters<typeof getPhrase>[0]));
		},
		[childrenKey]
	);

	const childPhrases: PreviewPhrase[] = childrenIds.length
		? childrenIds
				.map((childId, index) => ({ text: storePhrases[index] ?? '', className: getChildClass(childId) }))
				.filter((phrase) => phrase.text)
		: fallbackPhrases.map((text) => ({ text, className: '' }));

	const isEmpty = childPhrases.length === 0;
	const variantValue = getString(attrs.variant, 'green');
	const variant = ['green', 'burgundy', 'cream', 'materie'].includes(variantValue) ? variantValue : 'green';
	const phrases: PreviewPhrase[] = isEmpty
		? (variant === 'materie' ? MATERIE_DEFAULT_PHRASES : DEFAULT_PHRASES).map((text) => ({ text, className: '' }))
		: childPhrases;
	const isMaterieLayout = variant === 'materie' || getModuleClass(attrs).includes('tp-materie-marquee');
	const duration = clampNumber(getString(attrs.speed, isMaterieLayout ? '10' : '28'), isMaterieLayout ? 10 : 28, 8, 120);
	const repeatCount = Math.max(
		2,
		Math.min(8, Math.round(clampNumber(getString(attrs.repeatCount, isMaterieLayout ? '4' : '2'), isMaterieLayout ? 4 : 2, 2, 8)))
	);
	const direction = getString(attrs.direction, 'left') === 'right' ? 'right' : 'left';
	// Emptied on purpose: no symbol, the space between the phrases stays (as on the site).
	const rawSeparator = attrs?.separator?.innerContent?.desktop?.value;
	const separator = typeof rawSeparator === 'string' && rawSeparator.trim() === '' ? '' : getString(attrs.separator, '·');
	const pauseOnHover = getString(attrs.pauseOnHover, isMaterieLayout ? 'off' : 'on') === 'on';
	const loopItems = Array.from({ length: repeatCount }).flatMap(() => phrases);
	const ariaLabel = phrases
		.map((phrase) => withoutDynamicContent(phrase.text))
		.filter(Boolean)
		.join(separator ? ` ${separator} ` : ' ');
	const shift = -100 / repeatCount;

	return (
		<ModuleContainer
			attrs={attrs}
			childrenIds={childrenIds}
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
			<div className={`tp-marquee-vb${isMaterieLayout ? ' tp-marquee-vb--materie' : ''}`}>
				{isMaterieLayout ? (
					<>
						<p className="tp-marquee-vb__materie-note">Modifica la scritta obliqua a scorrimento</p>
						<div className="tp-marquee-vb__live tp-marquee-vb__live--materie" aria-live="polite">
							<div className={`tp-marquee tp-marquee--${variant} tp-marquee--${direction}`}>
								<div className="tp-marquee__track tp-marquee__track--materie-edit">
									{childrenIds.length ? (
										<ChildModulesContainer ids={childrenIds} />
									) : (
										<span className="tp-marquee__phrase">{MATERIE_DEFAULT_PHRASES[0]}</span>
									)}
								</div>
							</div>
						</div>
					</>
				) : (
					<>
						<div className="tp-marquee-vb__live" aria-live="polite">
							<div
								className={`tp-marquee tp-marquee--${variant} tp-marquee--${direction}${pauseOnHover ? ' tp-marquee--pause-hover' : ''}`}
								style={
									{
										'--tp-marquee-duration': `${duration}s`,
										'--tp-marquee-shift': `${shift}%`,
									} as React.CSSProperties
								}
								aria-label={ariaLabel}
							>
								<div className="tp-marquee__track">
									{loopItems.map((phrase, index) => (
										<React.Fragment key={`${phrase.text}-${index}`}>
											<span className={`tp-marquee__phrase${phrase.className ? ` ${phrase.className}` : ''}`} aria-hidden="true">
												<DynamicText value={phrase.text} />
											</span>
											<span className="tp-marquee__separator" aria-hidden="true">
												{separator}
											</span>
										</React.Fragment>
									))}
								</div>
							</div>
						</div>
						<div className="tp-marquee-vb__panel">
							<p className="tp-marquee-vb__panel-title">Scritte della striscia a scorrimento</p>
							<p className="tp-marquee-vb__panel-hint">Modifica qui sotto: la anteprima sopra si aggiorna subito.</p>
							<div className="tp-marquee-vb__list">
								{childrenIds.length ? (
									<ChildModulesContainer ids={childrenIds} />
								) : (
									<p className="tp-marquee-vb__list-empty">
										Nessuna scritta ancora: sul sito scorrono le frasi qui sopra. Usa il pulsante + per aggiungerne una.
									</p>
								)}
							</div>
						</div>
					</>
				)}
			</div>
		</ModuleContainer>
	);
};
