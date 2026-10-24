import type { CSSProperties } from 'react';
import { processGlobalVariableColor } from '@divi/global-data';

export const getVal = (attr: unknown, fallback: unknown = ''): unknown => {
	const value = (attr as any)?.innerContent?.desktop?.value ?? (attr as any)?.desktop?.value;

	return value === undefined || value === null || value === '' ? fallback : value;
};

export const getString = (attr: unknown, fallback = ''): string => {
	const value = getVal(attr, fallback);

	if (typeof value === 'object') {
		return fallback;
	}

	return String(value);
};

/**
 * A color field. Divi saves a global color as `$variable({...})$`, which becomes `var(--gcid-...)` on the
 * site; the builder passes the saved value as it is, so it is turned here with Divi's own function, as
 * tp_child_chrome_get_color() does on the site. A value it cannot resolve gives the fallback.
 */
export const getColor = (attr: unknown, fallback = ''): string => {
	let value = getString(attr, fallback);

	if (value.includes('$variable(') && typeof processGlobalVariableColor === 'function') {
		value = processGlobalVariableColor(value);
	}

	return value.includes('$variable(') ? fallback : value;
};

export const clampNumber = (value: string, fallback: number, min: number, max: number): number => {
	const parsed = parseFloat(value);
	const number = Number.isFinite(parsed) ? parsed : fallback;

	return Math.max(min, Math.min(max, number));
};

export const getBackgroundColor = (attrs: Record<string, unknown>): string => {
	const color = (attrs?.module as any)?.decoration?.background?.desktop?.value?.color ?? '';

	if (typeof color === 'string' && color !== '' && color !== 'transparent') {
		return color;
	}

	return '';
};

export const getUploadUrl = (uploadAttr: unknown, legacyUrl = '', fallback = ''): string => {
	const raw = (uploadAttr as any)?.innerContent?.desktop?.value ?? (uploadAttr as any)?.desktop?.value ?? '';

	if (typeof raw === 'string' && raw.trim()) {
		return raw.trim();
	}

	if (raw && typeof raw === 'object') {
		const fromObject = (raw as { src?: string; url?: string }).src || (raw as { src?: string; url?: string }).url || '';

		if (fromObject) {
			return String(fromObject);
		}
	}

	return getString(legacyUrl, '') || fallback;
};

export const buildStyleVars = (vars: Record<string, string>): CSSProperties | undefined => {
	const style: Record<string, string> = {};

	Object.entries(vars).forEach(([key, value]) => {
		if (value) {
			style[key] = value;
		}
	});

	return Object.keys(style).length ? (style as CSSProperties) : undefined;
};

export const collectLinkFields = (
	attrs: Record<string, unknown>,
	prefix: string,
	count: number,
	fallback: Array<{ title: string; url: string }>
): Array<{ title: string; url: string }> => {
	const items: Array<{ title: string; url: string }> = [];

	for (let index = 1; index <= count; index += 1) {
		const title = getString(attrs[`${prefix}${index}Text`], '').trim();
		const url = getString(attrs[`${prefix}${index}Link`], '').trim();

		if (title) {
			items.push({ title, url: url || '#' });
		}
	}

	return items.length ? items : fallback;
};
