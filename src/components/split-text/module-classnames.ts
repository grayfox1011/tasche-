import { elementClassnames } from '@divi/module';

export const moduleClassnames = (args: any): void => {
	const { classnamesInstance, attrs } = args;

	classnamesInstance.add('tp_split_text', true);
	classnamesInstance.add(
		elementClassnames({
			attrs: {
				...(attrs?.module?.decoration ?? {}),
				link: attrs?.module?.advanced?.link ?? {},
			},
		})
	);
};
