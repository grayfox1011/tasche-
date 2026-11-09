import { ASSET_BASE } from '../../utils/asset-base';

/** The places of the theme's CSS ("Posto"): the couple's, the pigeons', the man's. */
export const FOOTER_CHARACTER_SLOTS = ['four', 'one', 'three'];

/** The place chosen in "Posto" (the third, the free one, when the value is not one of the theme's). */
export const footerCharacterSlot = (value: string): string => (FOOTER_CHARACTER_SLOTS.includes(value) ? value : 'three');

const characterAttrs = (label: string, slot: string, src: string, hidden: string[] = []): Record<string, unknown> => {
	const disabledOn = Object.fromEntries(hidden.map((device) => [device, { value: 'on' }]));

	return {
		slot: { innerContent: { desktop: { value: slot } } },
		image: { innerContent: { desktop: { value: { src, id: 0 } } } },
		module: {
			meta: { adminLabel: { desktop: { value: label } } },
			...(hidden.length ? { decoration: { disabledOn } } : {}),
		},
	};
};

/**
 * The Personaggi of a Footer sito added in the builder (Divi adds a module's "template" children with it), as
 * the theme puts them in its footer (tp_child_footer_character_blocks()): the couple, the pigeons, the man of
 * the phones.
 */
export const footerCharacterTemplate = (): Array<[string, Record<string, unknown>]> => [
	['tp/footer-character', characterAttrs('Personaggio: coppia', 'four', `${ASSET_BASE}/images/footer/personaggi-4.webp`)],
	['tp/footer-character', characterAttrs('Personaggio: piccioni', 'one', `${ASSET_BASE}/images/footer/personaggi-1.webp`)],
	['tp/footer-character', characterAttrs('Personaggio: uomo seduto (solo telefono)', 'three', `${ASSET_BASE}/svg/footer-character.svg`, ['desktop', 'tablet'])],
];
