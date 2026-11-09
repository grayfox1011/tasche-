<?php
/**
 * 0.6.1: the photos the theme puts in the Media Library get their alternative text, and the same words as title in
 * place of the one made from the file name. Divi's Image module copies both from the photo chosen in the library,
 * so a section made anew in Divi gets the same texts as the home instead of none (and of a title such as
 * "Fig Intro Alberto Impasto" that the browser shows on the photo). Only an empty alternative text and a title
 * still made from the file name are changed: what is written in the library stays.
 * The texts are those of the home; the photos that only appear on hover in the menu, and the drawings of the
 * footer, are decorative and keep none.
 * 0.6.6: the two photos that have a title of their own in the home ("Alberto al lavoro", "Tasche Piene nei caruggi
 * di Genova") get it in the library too, so a section made anew gets it as well; the theme no longer shows a
 * title that only repeats the alternative text (tp_child_image_alt_title() in functions.php).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Alternative text of the theme's photos, by file name.
 */
function tp_child_media_alt_texts(): array {
	return [
		'fig-intro-alberto-impasto.webp'          => 'Alberto prepara il ripieno nel laboratorio di Tasche Piene',
		'fig-alberto-pentola.webp'                => 'Il ripieno preparato nella pentola',
		'flow-menu-pomodoro-mozzarella-pesto.webp' => 'Alberto con una teglia di pomodori',
		'flow-choice-tasca.webp'                  => 'La tasca ripiena',
		'flow-choice-piatto.webp'                 => 'La vaschetta',
		'fig-menu-feature.webp'                   => 'Piatto di ripieni alla genovese',
		'flow-menu-tiramisu.webp'                 => 'Tiramisù in tasca',
		'e0d905c6.webp'                           => 'Acciughe liguri appena pescate',
		'83b65fe4.webp'                           => 'Olive taggiasche',
		'5277ad78.webp'                           => 'Stracciatella di bufala',
		'79537578.webp'                           => 'Pollo alla ligure',
		'9d821c0e.webp'                           => 'Pomodori dei fornitori scelti',
		'fig-intro-caruggi.webp'                  => 'Tasche Piene davanti ai leoni di San Lorenzo, a Genova',
		'logo-footer.webp'                        => 'Tasche Piene',
		// 0.8.0: the photos of the 0.6.9 and of the 0.7.0, with the texts of the home.
		'just-eat.png'                            => 'Ordina Tasche Piene su Just Eat, consegna a domicilio a Genova centro',
		'deliveroo.png'                           => 'Ordina Tasche Piene su Deliveroo, consegna a domicilio a Genova centro',
		'polpette.webp'                           => 'Polpette pronte per il sugo',
		'ripieni.webp'                            => 'Una zucchina svuotata per i ripieni',
		'melanzane.webp'                          => 'Melanzane alla parmigiana',
		'spuma.webp'                              => 'Una bottiglia di spuma',
	];
}

/**
 * 0.6.6: the title of the photos that have one of their own in the home, by file name (the others take their
 * alternative text).
 */
function tp_child_media_title_texts(): array {
	return [
		'fig-intro-alberto-impasto.webp' => 'Alberto al lavoro',
		'fig-intro-caruggi.webp'         => 'Tasche Piene nei caruggi di Genova',
		'just-eat.png'                   => 'Tasche Piene su Just Eat',
		'deliveroo.png'                  => 'Tasche Piene su Deliveroo',
	];
}

/**
 * Alternative text for a file of the theme ("assets/images/intro/fig-alberto-pentola.webp"), empty if it has none.
 */
function tp_child_media_alt_for_asset( string $relative_path ): string {
	return tp_child_media_alt_texts()[ basename( $relative_path ) ] ?? '';
}

/**
 * Gives a photo the theme imported its alternative text, when the library has none for it, and the same words
 * as title while it still has the one made from the file name ("Fig Intro Alberto Impasto"): Divi copies the
 * title too, and the browser shows it when the mouse stops on the photo. A photo with a title of its own in the
 * home (tp_child_media_title_texts()) gets that one, also in place of the alternative text the 0.6.1 gave it.
 */
function tp_child_media_fill_alt( int $attachment_id, string $relative_path ): void {
	$alt = tp_child_media_alt_for_asset( $relative_path );

	if ( '' === $alt ) {
		return;
	}

	if ( '' === trim( (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) ) {
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $alt );
	}

	if ( ! function_exists( 'tp_child_seed_media_title_from_filename' ) ) {
		return;
	}

	$title    = (string) get_post_field( 'post_title', $attachment_id );
	$file     = (string) get_attached_file( $attachment_id );
	$fallback = [ tp_child_seed_media_title_from_filename( basename( $relative_path ) ) ];

	if ( '' !== $file ) {
		$fallback[] = tp_child_seed_media_title_from_filename( basename( $file ) );
	}

	$own = tp_child_media_title_texts()[ basename( $relative_path ) ] ?? '';

	if ( '' !== $own && $title === $alt ) {
		$fallback[] = $alt;
	}

	if ( in_array( $title, $fallback, true ) ) {
		wp_update_post(
			[
				'ID'         => $attachment_id,
				'post_title' => '' !== $own ? $own : $alt,
			]
		);
	}
}

/**
 * Once after the update: the photos already in the library.
 */
function tp_child_fill_media_alt_061(): void {
	$flag = 'tp_child_media_alt_061_v1';

	if ( get_option( $flag ) ) {
		return;
	}

	$ids = get_posts(
		[
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_key'       => '_tp_child_seed_asset_path',
			'no_found_rows'  => true,
		]
	);

	foreach ( $ids as $id ) {
		tp_child_media_fill_alt( (int) $id, (string) get_post_meta( (int) $id, '_tp_child_seed_asset_path', true ) );
	}

	update_option( $flag, TP_CHILD_VERSION, true );
}

add_action( 'init', 'tp_child_fill_media_alt_061', 45 );

/**
 * 0.6.6, once after the update: the two photos with a title of their own get it in the library (only where the
 * title is still the alternative text the 0.6.1 gave it, or the one made from the file name).
 */
function tp_child_fill_media_title_066(): void {
	$flag = 'tp_child_media_title_066_v1';

	if ( get_option( $flag ) ) {
		return;
	}

	$ids = get_posts(
		[
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_key'       => '_tp_child_seed_asset_path',
			'no_found_rows'  => true,
		]
	);

	foreach ( $ids as $id ) {
		$path = (string) get_post_meta( (int) $id, '_tp_child_seed_asset_path', true );

		if ( isset( tp_child_media_title_texts()[ basename( $path ) ] ) ) {
			tp_child_media_fill_alt( (int) $id, $path );
		}
	}

	update_option( $flag, TP_CHILD_VERSION, true );
}

add_action( 'init', 'tp_child_fill_media_title_066', 46 );

/**
 * 0.8.0, once after the update: the photos the 0.6.9 and the 0.7.0 added (the two buttons of the delivery, the four
 * of Materie prime) had no alternative text in the library and their file name as title, so a section made anew
 * with them had a photo without text and "Polpette" under the mouse. They get the texts of the home, as the others
 * (only an empty alternative text and a title still made from the file name). The photo of the vaschetta keeps
 * "Il piatto" of the Figma in the library only if nobody wrote another text: it becomes "La vaschetta", as in the
 * home since the 0.7.0.
 */
function tp_child_fill_media_alt_080(): void {
	$flag = 'tp_child_media_alt_080_v1';

	if ( get_option( $flag ) ) {
		return;
	}

	$new = [ 'just-eat.png', 'deliveroo.png', 'polpette.webp', 'ripieni.webp', 'melanzane.webp', 'spuma.webp' ];
	$ids = get_posts(
		[
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_key'       => '_tp_child_seed_asset_path',
			'no_found_rows'  => true,
		]
	);

	foreach ( $ids as $id ) {
		$id   = (int) $id;
		$path = (string) get_post_meta( $id, '_tp_child_seed_asset_path', true );
		$file = basename( $path );

		if ( in_array( $file, $new, true ) ) {
			tp_child_media_fill_alt( $id, $path );
		} elseif ( 'flow-choice-piatto.webp' === $file ) {
			if ( 'Il piatto' === trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) ) ) {
				update_post_meta( $id, '_wp_attachment_image_alt', 'La vaschetta' );
			}

			if ( 'Il piatto' === (string) get_post_field( 'post_title', $id ) ) {
				wp_update_post(
					[
						'ID'         => $id,
						'post_title' => 'La vaschetta',
					]
				);
			}
		}
	}

	update_option( $flag, TP_CHILD_VERSION, true );
}

add_action( 'init', 'tp_child_fill_media_alt_080', 47 );
