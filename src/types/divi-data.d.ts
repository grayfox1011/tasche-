declare module '@divi/data' {
	import type { Data } from '@divi/types';

	export const useSelect: Data.UseSelect;
}

declare module '@divi/global-data' {
	export const processGlobalVariableColor: ((value: string) => string) | undefined;
}
