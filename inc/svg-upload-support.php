<?php
/**
 * Allow trusted editors to upload SVG assets (logo header/footer, ecc.).
 *
 * An SVG can carry scripts that run on the site's own domain, and the regex clean-up
 * below cannot catch every form, so SVG uploads are limited to users who may already
 * publish unfiltered HTML (administrators and editors on a single site).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register SVG mime types for the Media Library and Divi upload fields.
 */
function tp_child_allow_svg_upload_mimes( array $mimes ): array {
	if ( ! current_user_can( 'unfiltered_html' ) ) {
		return $mimes;
	}

	$mimes['svg']  = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';

	return $mimes;
}
add_filter( 'upload_mimes', 'tp_child_allow_svg_upload_mimes', 20 );

/**
 * WordPress sometimes mis-detects SVG file extensions.
 */
function tp_child_fix_svg_filetype( array $data, string $file, string $filename, $mimes ): array {
	if ( ! current_user_can( 'unfiltered_html' ) ) {
		return $data;
	}

	$extension = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );

	if ( in_array( $extension, [ 'svg', 'svgz' ], true ) ) {
		$data['ext']  = $extension;
		$data['type'] = 'image/svg+xml';
	}

	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'tp_child_fix_svg_filetype', 10, 4 );

/**
 * Strip the most common SVG XSS vectors before the file is stored.
 */
function tp_child_sanitize_svg_upload( array $file ): array {
	if ( ! empty( $file['error'] ) || empty( $file['tmp_name'] ) || ! is_readable( $file['tmp_name'] ) ) {
		return $file;
	}

	$extension = strtolower( pathinfo( (string) ( $file['name'] ?? '' ), PATHINFO_EXTENSION ) );
	$mime      = strtolower( (string) ( $file['type'] ?? '' ) );

	if ( 'svg' !== $extension && 'svgz' !== $extension && 'image/svg+xml' !== $mime ) {
		return $file;
	}

	$contents = file_get_contents( (string) $file['tmp_name'] );

	if ( ! is_string( $contents ) || '' === $contents ) {
		$file['error'] = __( 'Il file SVG non è valido o è vuoto.', 'tasche-piene-divi-child' );

		return $file;
	}

	if ( false === stripos( $contents, '<svg' ) ) {
		$file['error'] = __( 'Il file caricato non sembra un SVG valido.', 'tasche-piene-divi-child' );

		return $file;
	}

	$sanitized = preg_replace( '/<script\b[^>]*>.*?<\/script>/is', '', $contents );
	$sanitized = preg_replace( '/<foreignObject\b[^>]*>.*?<\/foreignObject>/is', '', (string) $sanitized );
	$sanitized = preg_replace( '/on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', (string) $sanitized );
	$sanitized = preg_replace( '/javascript:/i', '', (string) $sanitized );

	if ( ! is_string( $sanitized ) || '' === $sanitized || false === stripos( $sanitized, '<svg' ) ) {
		$file['error'] = __( 'Il file SVG contiene markup non consentito.', 'tasche-piene-divi-child' );

		return $file;
	}

	file_put_contents( (string) $file['tmp_name'], $sanitized );
	$file['type'] = 'image/svg+xml';

	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'tp_child_sanitize_svg_upload', 5 );
add_filter( 'wp_handle_sideload_prefilter', 'tp_child_sanitize_svg_upload', 5 );

/**
 * Show SVG previews in the Media Library grid/list and Divi media modal.
 */
function tp_child_prepare_svg_attachment_for_js( array $response, WP_Post $attachment, $meta ): array {
	if ( empty( $response['url'] ) || 'image' !== ( $response['type'] ?? '' ) || 'svg+xml' !== ( $response['subtype'] ?? '' ) ) {
		return $response;
	}

	$response['icon']  = $response['url'];
	$response['thumb'] = $response['url'];
	$response['sizes'] = [
		'full' => [
			'url'         => $response['url'],
			'width'       => (int) ( $response['width'] ?? 0 ),
			'height'      => (int) ( $response['height'] ?? 0 ),
			'orientation' => 'portrait',
		],
	];
	$response['image'] = [
		'src'    => $response['url'],
		'width'  => (int) ( $response['width'] ?? 0 ),
		'height' => (int) ( $response['height'] ?? 0 ),
	];

	return $response;
}
add_filter( 'wp_prepare_attachment_for_js', 'tp_child_prepare_svg_attachment_for_js', 10, 3 );
