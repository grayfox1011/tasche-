export type WpNavItem = {
	title: string;
	url: string;
	target?: string;
};

export const getWpMenuItems = (location: string, fallback: WpNavItem[]): WpNavItem[] => {
	const menus = (window as Window & { tpChromeMenus?: Record<string, WpNavItem[]> }).tpChromeMenus;
	const items = menus?.[location];

	return items?.length ? items : fallback;
};
