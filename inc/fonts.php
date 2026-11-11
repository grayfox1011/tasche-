<?php
/**
 * The two fonts of the site, Bowlby One and DM Sans, come from the theme (assets/fonts) and not
 * from Google: no visitor's address goes to Google for them.
 *
 * In Divi the fonts are chosen by their names, as any Google font, so the font fields show them
 * and work as usual. Divi would then ask Google for their files: here those two names are taken
 * out of Divi's request. Any other Google font chosen in Divi keeps loading as Divi does it.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The fonts served by the theme, as Google Fonts names them.
 *
 * @return string[]
 */
function tp_local_fonts(): array {
	return [ 'Bowlby One', 'DM Sans' ];
}

/**
 * A Google Fonts address without the theme's fonts; an empty string when nothing else is asked.
 * Both forms are read: css?family=A:400|B:700 and css2?family=A:wght@400&family=B.
 */
function tp_google_fonts_url_without_local( string $url ): string {
	if ( false === strpos( $url, 'fonts.googleapis.com' ) ) {
		return $url;
	}

	$parts = wp_parse_url( html_entity_decode( $url ) );
	if ( empty( $parts['query'] ) ) {
		return $url;
	}

	$local    = array_map( 'strtolower', tp_local_fonts() );
	$is_local = static function ( string $family ) use ( $local ): bool {
		$name = strtolower( trim( explode( ':', str_replace( '+', ' ', urldecode( $family ) ) )[0] ) );

		return in_array( $name, $local, true );
	};

	$pairs    = [];
	$families = 0;

	foreach ( explode( '&', $parts['query'] ) as $pair ) {
		if ( 0 !== strpos( $pair, 'family=' ) ) {
			$pairs[] = $pair;
			continue;
		}

		// css?family=A:400|B:700 holds several fonts in one value; css2 has one value per font.
		$value = substr( $pair, 7 );
		$bar   = false !== stripos( $value, '%7C' ) ? '%7C' : '|';
		$kept  = array_filter(
			preg_split( '/\||%7C/i', $value ),
			static fn( $family ) => '' !== $family && ! $is_local( $family )
		);

		if ( $kept ) {
			$pairs[]   = 'family=' . implode( $bar, $kept );
			$families += count( $kept );
		}
	}

	if ( ! $families ) {
		return '';
	}

	return 'https://fonts.googleapis.com' . ( $parts['path'] ?? '/css' ) . '?' . implode( '&', $pairs );
}

/**
 * Divi's Google Fonts stylesheets, printed as links.
 *
 * @param string|false $src    The stylesheet's address.
 * @param string       $handle Its name in WordPress.
 * @return string|false
 */
function tp_filter_google_fonts_stylesheet( $src, $handle ) {
	if ( ! is_string( $src ) || false === strpos( $src, 'fonts.googleapis.com' ) ) {
		return $src;
	}

	$filtered = tp_google_fonts_url_without_local( $src );

	return '' === $filtered ? false : $filtered;
}
add_filter( 'style_loader_src', 'tp_filter_google_fonts_stylesheet', 20, 2 );

/**
 * The same request when Divi fetches the fonts' CSS itself to print it in the page.
 */
add_filter( 'et_builder_google_fonts_get_url', 'tp_google_fonts_url_without_local', 20 );

/**
 * Divi lists its fonts for the pickers from Google's catalogue: the two fonts are there already.
 * Nothing to add; the files are declared in assets/css/tp.css under the same names.
 */
