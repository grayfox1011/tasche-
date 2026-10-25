import { ShapeDefinition } from './types';

export const SHAPE_COLORS: Record<string, string> = {
	transparent: 'transparent',
	green: '#054726',
	burgundy: '#560b22',
	cream: '#fff5e9',
	sand: '#f8ebdb',
};

export const SHAPE_DEFINITIONS: Record<string, ShapeDefinition> = {
	'choice-layered-desktop': {
		viewBox: '0 0 1811.7627 428.3477',
		paths: [
			{
				className: 'tp-shape-wave__path tp-shape-wave__path--green',
				path: 'M999.447 217.19C1300.56 148.011 1608.33 129.375 1724.58 128.705L1811.76 178.981L1711.82 428.348L0 428.348L0 281.543L0 0C207.686 101.221 698.337 286.369 999.447 217.19Z',
				fill: 'green',
			},
			{
				// The front layer takes "Colore wave" (burgundy, the shape's own fill, by default).
				className: 'tp-shape-wave__path tp-shape-wave__path--burgundy',
				path: 'M846.006 243.417C479.085 140.469 222.372 125.73 139.881 131.229L149.78 414.999L1664.32 414.999L1664.32 332.508C1544.43 345.707 1212.93 346.366 846.006 243.417Z',
				fill: 'auto',
			},
		],
		fill: 'burgundy',
		background: 'transparent',
		width: 1811.7627,
		height: 428.3477,
	},
	'choice-green-overlap': {
		viewBox: '0 0 1811.7627 428.3477',
		path: 'M999.447 217.19C1300.56 148.011 1608.33 129.375 1724.58 128.705L1811.76 178.981L1711.82 428.348L0 428.348L0 281.543L0 0C207.686 101.221 698.337 286.369 999.447 217.19Z',
		fill: 'green',
		background: 'transparent',
		width: 1811.7627,
		height: 428.3477,
	},
	'choice-cream-cover': {
		viewBox: '0 0 1524.4385 240.1285',
		path: 'M0 0H1524.4385V226.27C1404.55 239.469 1073.05 240.128 706.125 137.179C339.204 34.2306 82.4911 19.4918 0 24.991Z',
		fill: 'cream',
		background: 'transparent',
		width: 1524.44,
		height: 240.13,
	},
	'choice-burgundy-top': {
		viewBox: '0 0 472 114',
		path: 'M218.646 60.4652C105.115 28.6112 25.6842 24.0507 0.160177 25.7523L3.22312 113.555H471.846V88.0312C434.75 92.115 332.178 92.3192 218.646 60.4652Z',
		fill: 'burgundy',
		background: 'cream',
		width: 471.49,
		height: 113.54,
	},
	'choice-cream-bottom': {
		viewBox: '0 0 525 99',
		path: 'M243.182 38.9778C116.818 3.5233 28.409 -1.55258 0 0.341285L3.40914 98.0687H525V69.6596C483.712 74.205 369.546 74.4322 243.182 38.9778Z',
		fill: 'cream',
		background: 'burgundy',
		width: 524.8,
		height: 98.03,
	},
};

export const resolveShapeColor = (color: string, fallbackToken: string): string => {
	if (color !== 'auto' && SHAPE_COLORS[color]) {
		return SHAPE_COLORS[color];
	}

	return SHAPE_COLORS[fallbackToken] ?? SHAPE_COLORS.green;
};
