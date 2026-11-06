import { getString } from './attrs';

/**
 * What the strip and its Scritte need to know of the strip's attributes, in the builder as on the site
 * (modules/Marquee/MarqueeTrait/RenderCallbackTrait.php): the variant, whether it is the strip of Materie prime,
 * and the separator.
 */
type MarqueeLikeAttrs = {
	variant?: unknown;
	separator?: { innerContent?: { desktop?: { value?: unknown } } } & Record<string, unknown>;
	module?: { advanced?: { htmlAttributes?: { desktop?: { value?: { class?: unknown } } } } } & Record<string, unknown>;
};

/** Divi may hand a parent's attributes as an immutable object: a plain one either way. */
export const toPlainAttrs = (attrs: unknown): MarqueeLikeAttrs => {
	if (attrs && typeof (attrs as { toJS?: unknown }).toJS === 'function') {
		return (attrs as { toJS: () => MarqueeLikeAttrs }).toJS();
	}

	return (attrs ?? {}) as MarqueeLikeAttrs;
};

export const getMarqueeVariant = (attrs: MarqueeLikeAttrs): string => {
	const value = getString(attrs?.variant, 'green');

	return ['green', 'burgundy', 'cream', 'materie'].includes(value) ? value : 'green';
};

/** The strip of Materie prime: the "materie" variant, or the class the home gives it. */
export const isMaterieMarquee = (attrs: MarqueeLikeAttrs): boolean =>
	getMarqueeVariant(attrs) === 'materie' ||
	getString(attrs?.module?.advanced?.htmlAttributes?.desktop?.value?.class, '').includes('tp-materie-marquee');

/** Emptied on purpose: no symbol, the space between the phrases stays. Otherwise "•" for Materie, "·" for the others. */
export const getSeparator = (attrs: MarqueeLikeAttrs): string => {
	const raw = attrs?.separator?.innerContent?.desktop?.value;

	if (typeof raw === 'string' && raw.trim() === '') {
		return '';
	}

	return getString(attrs?.separator, getMarqueeVariant(attrs) === 'materie' ? '•' : '·');
};
