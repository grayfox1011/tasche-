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
	// The two waves of the Tasca/Piatto section on the computer and the tablet: they keep their proportions
	// in a box of another size (xMinYMin meet), as the drawing of the section did before the modules.
	'choice-green-wave': {
		viewBox: '0 0 1811.7627 428.3477',
		path: 'M999.447 217.19C1300.56 148.011 1608.33 129.375 1724.58 128.705L1811.76 178.981L1711.82 428.348L0 428.348L0 281.543L0 0C207.686 101.221 698.337 286.369 999.447 217.19Z',
		fill: 'green',
		background: 'transparent',
		width: 1811.7627,
		height: 428.3477,
		aspect: 'xMinYMin meet',
	},
	'choice-burgundy-wave': {
		viewBox: '0 0 1524.4385 284.7612',
		path: 'M706.125 113.179C339.204 10.2306 82.4911 -4.50822 0 0.990986L9.89911 284.761L1524.44 284.761L1524.44 202.27C1404.55 215.469 1073.05 216.128 706.125 113.179Z',
		fill: 'burgundy',
		background: 'transparent',
		width: 1524.4385,
		height: 284.7612,
		aspect: 'xMinYMin meet',
	},
	// Under the menu (0.5.9): the burgundy of the board ends in this wave (the Tasca/Piatto one, upside down).
	'materie-burgundy-wave': {
		viewBox: '0 0 1524.4385 284.7612',
		path: 'M706.125 113.179C339.204 10.2306 82.4911 -4.50822 0 0.990986L9.89911 284.761L1524.44 284.761L1524.44 202.27C1404.55 215.469 1073.05 216.128 706.125 113.179Z',
		fill: 'burgundy',
		background: 'transparent',
		width: 1524.4385,
		height: 284.7612,
	},
	// Under the menu (0.5.9): the green wave behind the burgundy one (Figma: Vector 61, drawn upside down).
	'materie-green-wave': {
		viewBox: '0 0 1708.0251 202.0957',
		path: 'M942.22 71.903C1181.679 -9.978 1505.012 -7.982 1625.83 17.346L1708.025 48.344L1613.803 202.096L0 202.096L0 111.581L6.779 0C202.574 62.41 716.828 148.974 942.22 71.903Z',
		fill: 'green',
		background: 'transparent',
		width: 1708.0251,
		height: 202.0957,
	},
};

/**
 * Empty or "auto": the shape's own colour; a name of the old list (green, burgundy...): the theme's colour;
 * any other colour (Divi's colour picker, since 0.6.7) as it is. As get_color_value() on the site.
 */
export const resolveShapeColor = (color: string, fallbackToken: string): string => {
	const value = color.trim();

	if (value !== '' && value !== 'auto' && !SHAPE_COLORS[value] && /^(#[0-9a-f]{3,8}|(rgb|rgba|hsl|hsla)\([0-9.,%\s/+-]+\)|var\(--[a-z0-9_-]+(,\s*#[0-9a-f]{3,8})?\)|[a-z]{3,20})$/i.test(value)) {
		return value;
	}

	if (value !== 'auto' && SHAPE_COLORS[value]) {
		return SHAPE_COLORS[value];
	}

	return SHAPE_COLORS[fallbackToken] ?? SHAPE_COLORS.green;
};
