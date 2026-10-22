import React, { ReactElement, useMemo } from 'react';
import { useSelect } from '@divi/data';
import { ChildModulesContainer, ModuleContainer } from '@divi/module';

import { MarqueeEditProps } from './types';
import { createModuleClassnames, createModuleScriptData, createModuleStyles } from '../../utils/module-boilerplate';
import { clampNumber, getString } from '../../utils/attrs';

const MODULE_CLASSNAME = 'tp_marquee';
const EMPTY_PREVIEW = 'Modifica la scritta a scorrimento qui sotto';
const MATERIE_EMPTY_PREVIEW = 'Scritta obliqua a scorrimento';

const ModuleStyles = createModuleStyles(['tpDesignText', 'tpDesignStrip']);
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

	const storePhrases = useSelect(
		(select) => {
			if (!childrenIds.length) {
				return [] as string[];
			}

			const getAttrs = select('divi/edit-post').getModuleAttrs;

			return childrenIds
				.map((childId) => getPhrase(getAttrs(childId) as Parameters<typeof getPhrase>[0]))
				.filter(Boolean);
		},
		[childrenKey]
	);

	const phrases = childrenIds.length ? storePhrases : fallbackPhrases;

	const isEmpty = phrases.length === 0;
	const variantValue = getString(attrs.variant, 'green');
	const variant = ['green', 'burgundy', 'cream', 'materie'].includes(variantValue) ? variantValue : 'green';
	const isMaterieLayout = variant === 'materie' || getModuleClass(attrs).includes('tp-materie-marquee');
	const duration = clampNumber(getString(attrs.speed, isMaterieLayout ? '10' : '28'), isMaterieLayout ? 10 : 28, 8, 120);
	const repeatCount = Math.max(
		2,
		Math.min(8, Math.round(clampNumber(getString(attrs.repeatCount, isMaterieLayout ? '4' : '2'), isMaterieLayout ? 4 : 2, 2, 8)))
	);
	const direction = getString(attrs.direction, 'left') === 'right' ? 'right' : 'left';
	const separator = getString(attrs.separator, '·');
	const pauseOnHover = getString(attrs.pauseOnHover, isMaterieLayout ? 'off' : 'on') === 'on';
	const loopItems = isEmpty ? [EMPTY_PREVIEW] : Array.from({ length: repeatCount }).flatMap(() => phrases);
	const previewItems = isMaterieLayout
		? isEmpty
			? [MATERIE_EMPTY_PREVIEW]
			: phrases.slice(0, 1)
		: loopItems;
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
										<span className="tp-marquee__phrase tp-marquee__phrase--placeholder">{MATERIE_EMPTY_PREVIEW}</span>
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
								aria-label={isEmpty ? EMPTY_PREVIEW : phrases.join(separator ? ` ${separator} ` : ' ')}
							>
								<div className="tp-marquee__track">
									{previewItems.map((phrase, index) => (
										<React.Fragment key={`${phrase}-${index}`}>
											<span
												className={`tp-marquee__phrase${isEmpty ? ' tp-marquee__phrase--placeholder' : ''}`}
												aria-hidden="true"
											>
												{phrase}
											</span>
											{!isEmpty && separator ? (
												<span className="tp-marquee__separator" aria-hidden="true">
													{separator}
												</span>
											) : null}
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
									<p className="tp-marquee-vb__list-empty">Nessuna scritta ancora. Usa il pulsante + per aggiungerne una.</p>
								)}
							</div>
						</div>
					</>
				)}
			</div>
		</ModuleContainer>
	);
};
