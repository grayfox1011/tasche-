/**
 * The theme's assets folder, from where this bundle (scripts/bundle.js) was loaded: right also with WordPress
 * in a subfolder or the theme folder renamed. Root-relative, like the path it replaces, so that images placed
 * by the placeholders survive a change of domain.
 */
const bundleSrc = (document.currentScript as HTMLScriptElement | null)?.src || '';

export const ASSET_BASE = bundleSrc
	? new URL('../assets', bundleSrc).pathname.replace(/\/$/, '')
	: '/wp-content/themes/tasche-piene-divi-child/assets';
