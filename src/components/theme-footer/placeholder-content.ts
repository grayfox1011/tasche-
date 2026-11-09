import { ThemeFooterAttrs } from './types';

import { ASSET_BASE } from '../../utils/asset-base';

export const placeholderContent: ThemeFooterAttrs = {
	// Its characters are its Personaggi (0.6.8): none when the last one is removed.
	characterItems: {
		innerContent: {
			desktop: {
				value: 'on',
			},
		},
	},
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
