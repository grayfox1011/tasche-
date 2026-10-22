<?php
/**
 * Upload hardening for hosts/browsers that pass valid images without extensions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tp_child_upload_image_extension_for_mime( string $mime ): string {
	$map = [
		'image/jpeg' => 'jpg',
		'image/png'  => 'png',
		'image/gif'  => 'gif',
		'image/webp' => 'webp',
		'image/avif' => 'avif',
		'image/svg+xml' => 'svg',
	];

	return $map[ strtolower( $mime ) ] ?? '';
}

function tp_child_upload_detect_image_mime( string $path ): string {
	if ( function_exists( 'wp_get_image_mime' ) ) {
		$mime = wp_get_image_mime( $path );

		if ( is_string( $mime ) && '' !== $mime ) {
			return $mime;
		}
	}

	if ( function_exists( 'finfo_open' ) ) {
		$finfo = finfo_open( FILEINFO_MIME_TYPE );

		if ( $finfo ) {
			$mime = finfo_file( $finfo, $path );
			finfo_close( $finfo );

			if ( is_string( $mime ) && '' !== $mime ) {
				return $mime;
			}
		}
	}

	return '';
}

function tp_child_upload_restore_missing_image_extension( array $file ): array {
	$name = isset( $file['name'] ) ? (string) $file['name'] : '';

	if ( '' === $name || pathinfo( $name, PATHINFO_EXTENSION ) ) {
		return $file;
	}

	if ( ! empty( $file['error'] ) || empty( $file['tmp_name'] ) || ! is_readable( $file['tmp_name'] ) ) {
		return $file;
	}

	$mime = tp_child_upload_detect_image_mime( (string) $file['tmp_name'] );
	$extension = tp_child_upload_image_extension_for_mime( $mime );

	if ( '' === $extension ) {
		return $file;
	}

	$original_name = $name;
	$file['name'] = sanitize_file_name( $name . '.' . $extension );
	$file['type'] = $mime;

	if ( function_exists( 'tp_child_media_debug_log' ) ) {
		tp_child_media_debug_log(
			'restored_missing_image_extension',
			[
				'original_name' => $original_name,
				'new_name'      => $file['name'],
				'mime'          => $mime,
			]
		);
	}

	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'tp_child_upload_restore_missing_image_extension', 0 );

function tp_child_upload_relax_plupload_extension_filter( array $settings ): array {
	if ( ! is_admin() || ! current_user_can( 'upload_files' ) ) {
		return $settings;
	}

	if ( isset( $settings['filters']['mime_types'] ) ) {
		unset( $settings['filters']['mime_types'] );
	}

	return $settings;
}
add_filter( 'plupload_default_settings', 'tp_child_upload_relax_plupload_extension_filter', 20 );
