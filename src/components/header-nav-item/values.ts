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
