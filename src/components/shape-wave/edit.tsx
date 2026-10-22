import React, { ReactElement } from 'react';
import { ModuleContainer } from '@divi/module';

import { ShapeWaveEditProps } from './types';
import { createModuleClassnames, createModuleScriptData, createModuleStyles } from '../../utils/module-boilerplate';
import { clampNumber, getString } from '../../utils/attrs';
import { resolveShapeColor, SHAPE_DEFINITIONS } from './shapes';

const MODULE_CLASSNAME = 'tp_shape_wave';

const ModuleStyles = createModuleStyles(['tpDesignContent']);
const moduleClassnames = createModuleClassnames(MODULE_CLASSNAME);
const ModuleScriptData = createModuleScriptData();

export const ShapeWaveEdit = (props: ShapeWaveEditProps): ReactElement => {
	const { attrs, elements, id, name } = props;

	const variantValue = getString(attrs.variant, 'choice-green-overlap');
	const variant = SHAPE_DEFINITIONS[variantValue] ? variantValue : 'choice-green-overlap';
	const shape = SHAPE_DEFINITIONS[variant];
	const fill = resolveShapeColor(getString(attrs.fillColor, 'auto'), shape.fill);
	const background = resolveShapeColor(getString(attrs.backgroundColor, 'auto'), shape.background);
	const width = clampNumber(getString(attrs.shapeWidth, String(shape.width)), shape.width, 120, 2400);
	const height = clampNumber(getString(attrs.shapeHeight, String(shape.height)), shape.height, 20, 700);
	const offsetY = clampNumber(getString(attrs.offsetY, '0'), 0, -400, 400);
	const alignValue = getString(attrs.align, 'center');
	const align = ['left', 'center', 'right'].includes(alignValue) ? alignValue : 'center';
	const flipX = getString(attrs.flipHorizontal, 'off') === 'on';
	const flipY = getString(attrs.flipVertical, 'off') === 'on';
	const clipId = `tp-shape-wave-clip-${String(id).replace(/[^A-Za-z0-9_-]/g, '-')}`;

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
			<div
				className={`tp-shape-wave tp-shape-wave--${variant} tp-shape-wave--align-${align}${flipX ? ' tp-shape-wave--flip-x' : ''}${flipY ? ' tp-shape-wave--flip-y' : ''}`}
				style={
					{
						'--tp-shape-wave-width': `${width}px`,
						'--tp-shape-wave-height': `${height}px`,
						'--tp-shape-wave-fill': fill,
						'--tp-shape-wave-bg': background,
						'--tp-shape-wave-offset-y': `${offsetY}px`,
					} as React.CSSProperties
				}
			>
				<svg className="tp-shape-wave__svg" viewBox={shape.viewBox} preserveAspectRatio="none" aria-hidden="true" focusable="false">
					{shape.clipPath ? (
						<defs>
							<clipPath id={clipId}>
								<path d={shape.clipPath} />
							</clipPath>
						</defs>
					) : null}
					{shape.paths
						? shape.paths.map((pathDef) => (
								<path
									key={pathDef.path}
									className={pathDef.className ?? 'tp-shape-wave__path'}
									d={pathDef.path}
									fill={resolveShapeColor(pathDef.fill, pathDef.fill)}
								/>
							))
						: (
							<path
								className="tp-shape-wave__path"
								d={shape.path ?? ''}
								fill="currentColor"
								clipPath={shape.clipPath ? `url(#${clipId})` : undefined}
							/>
						)}
				</svg>
			</div>
		</ModuleContainer>
	);
};
