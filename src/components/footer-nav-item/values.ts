import { navItemField } from '../header-nav-item/values';

/** The links of the two columns of the theme's footer, as tp_child_footer_default_nav_items() on the site. */
export const FOOTER_NAV_DEFAULT_LEFT = [
	{ title: 'MENÙ', url: '#menu' },
	{ title: 'DOVE SIAMO', url: '#location' },
	{ title: 'SU DI NOI', url: '#chi-siamo' },
];

export const FOOTER_NAV_DEFAULT_RIGHT = [
	{ title: 'TORNA SÙ', url: '#home' },
	{ title: 'FACEBOOK', url: 'https://facebook.com', target: '_blank' },
	{ title: 'INSTAGRAM', url: 'https://instagram.com', target: '_blank' },
];

/** "MENÙ" as the name of its Voce: "Menù". */
const labelText = (title: string): string => {
	const lower = title.toLocaleLowerCase('it');

	return lower.charAt(0).toLocaleUpperCase('it') + lower.slice(1);
};

const voce = (column: 'left' | 'right', item: { title: string; url: string; target?: string }): [string, Record<string, unknown>] => [
	'tp/footer-nav-item',
	{
		column: navItemField(column),
		text: navItemField(item.title),
		link: navItemField(item.url),
		...(item.target === '_blank' ? { target: navItemField('on') } : {}),
		module: { meta: { adminLabel: { desktop: { value: `${column === 'right' ? 'Destra' : 'Sinistra'}: ${labelText(item.title)}` } } } },
	},
];

/**
 * The Voci of a Footer sito added in the builder (Divi adds a module's "template" children with it), as the
 * theme puts them in its footer: without them a new Footer sito showed the links of the WordPress menus but
 * had no Voce in Elementi (0.8.0).
 */
export const footerNavTemplate = (): Array<[string, Record<string, unknown>]> => [
	...FOOTER_NAV_DEFAULT_LEFT.map((item) => voce('left', item)),
	...FOOTER_NAV_DEFAULT_RIGHT.map((item) => voce('right', item)),
];
