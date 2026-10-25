declare module '@divi/data' {
	import type { Data } from '@divi/types';

	export const useSelect: Data.UseSelect;
}

declare module '@divi/global-data' {
	export const processGlobalVariableColor: ((value: string) => string) | undefined;
}

declare module '@divi/dynamic-data' {
	export const useDynamicData:
		| ((value: string) => { isDynamic: boolean; isLoading: boolean; isError: boolean; resolvedValue: unknown })
		| undefined;
}

declare module '@divi/module-utils' {
	export const getModuleOrderClassName: ((moduleId: string) => string) | undefined;
}
