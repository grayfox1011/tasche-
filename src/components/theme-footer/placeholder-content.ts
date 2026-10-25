import { ThemeFooterAttrs } from './types';

import { ASSET_BASE } from '../../utils/asset-base';

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
