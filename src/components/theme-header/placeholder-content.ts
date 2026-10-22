import { ThemeHeaderAttrs } from './types';

const ASSET_BASE = '/wp-content/themes/tasche-piene-divi-child/assets';

export const placeholderContent: ThemeHeaderAttrs = {
	logoImage: {
		innerContent: {
			desktop: {
				value: {
					src: `${ASSET_BASE}/svg/logo-tasche-piene-nav.svg`,
				},
			},
		},
	},
};
