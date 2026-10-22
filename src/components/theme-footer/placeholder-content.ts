import { ThemeFooterAttrs } from './types';

const ASSET_BASE = '/wp-content/themes/tasche-piene-divi-child/assets';

export const placeholderContent: ThemeFooterAttrs = {
	logoImage: {
		innerContent: {
			desktop: {
				value: {
					src: `${ASSET_BASE}/images/logo-footer.webp`,
				},
			},
		},
	},
};
