/**
 * A field of a Voce as saved, or the default of its module.json when Divi did not save it (a field never
 * changed); a field emptied by hand stays empty. Same rule as tp_child_header_nav_item_value() on the site.
 */
export const navItemValue = (attr: unknown, fallback: string): string => {
	const holder = attr as { innerContent?: { desktop?: { value?: unknown } }; desktop?: { value?: unknown } } | undefined;
	const value = holder?.innerContent?.desktop?.value ?? holder?.desktop?.value;

	return typeof value === 'string' ? value.trim() : fallback;
};

export const NAV_ITEM_DEFAULTS = { text: 'Nuova voce', link: '#home', target: 'off' };

/** The links of the theme's header: text and link, as tp_child_header_default_nav_items() on the site. */
export const HEADER_NAV_DEFAULT_ITEMS = [
	{ title: 'La nostra idea', url: '#chi-siamo' },
	{ title: 'Il menù', url: '#menu' },
	{ title: 'Senza glutine', url: '#materie' },
	{ title: 'Domande', url: '#faq' },
	{ title: 'Dove siamo', url: '#location' },
];

/** A text attribute as Divi saves it. */
export const navItemField = (value: string): Record<string, unknown> => ({ innerContent: { desktop: { value } } });

/**
 * The Voci of a Header sito added in the builder (Divi adds a module's "template" children with it). Without
 * them a new Header sito showed the links of the WordPress menu but had no Voce in Elementi: nothing to edit,
 * and the first Voce added replaced them all (0.8.0).
 */
export const headerNavTemplate = (): Array<[string, Record<string, unknown>]> =>
	HEADER_NAV_DEFAULT_ITEMS.map(({ title, url }) => [
		'tp/header-nav-item',
		{
			text: navItemField(title),
			link: navItemField(url),
			module: { meta: { adminLabel: { desktop: { value: `Voce: ${title}` } } } },
		},
	]);
