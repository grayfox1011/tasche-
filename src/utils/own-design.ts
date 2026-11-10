/**
 * 0.8.0: on which devices a module has a background of its own (Design > Sfondo), as inc/own-design-080.php
 * says on the site with the classes tp-own-bg-d, tp-own-bg-t and tp-own-bg-p: the Striscia scorrevole lets
 * that background through its strip (see "0.8.0" in tasche-piene-figma.css).
 */

const DEVICES: Array<[string, string]> = [
	['desktop', 'd'],
	['tablet', 't'],
	['phone', 'p'],
];

/** Whether a saved value says something (a colour, a size, a list of styles). */
const hasValue = (value: unknown): boolean => {
	if (Array.isArray(value)) {
		return value.some(hasValue);
	}

	if (value && typeof value === 'object') {
		return Object.values(value as Record<string, unknown>).some(hasValue);
	}

	if (typeof value === 'string' || typeof value === 'number') {
		return String(value).trim() !== '';
	}

	return value === true;
};

/**
 * With the theme's device isolation (inc/breakpoint-isolation.php) a value is of its device only; without it the
 * value of the computer is also of tablet and phone, and the tablet's of the phone.
 */
const isIsolated = (): boolean => (window as any)?.tpChildDevices?.isolated !== false;

/** The classes tp-own-bg-* of a module, from its attributes. */
export const ownBackgroundClassnames = (attrs: any): string[] => {
	const background = attrs?.module?.decoration?.background;

	if (!background || typeof background !== 'object') {
		return [];
	}

	const isolated = isIsolated();
	const classes: string[] = [];
	let above = false;

	DEVICES.forEach(([device, suffix]) => {
		const value = background?.[device]?.value;
		const own =
			!!value &&
			typeof value === 'object' &&
			(hasValue(value.color) || value?.gradient?.enabled === 'on' || hasValue(value?.image?.url));

		if (own || (!isolated && above)) {
			classes.push(`tp-own-bg-${suffix}`);
		}

		above = above || own;
	});

	return classes;
};
