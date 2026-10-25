<?php
/**
 * The one-time migrations run in one request at a time. Right after an update two requests can
 * arrive together (a visitor and WP-Cron, for instance): each would save the home from the content
 * it read, and the later save would drop what the other one changed while the flags say the work
 * is done. The first request that finds migrations to run for this theme version takes a lock and
 * runs them; a request that finds the lock taken skips them and serves the page as it is.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TP_CHILD_MIGRATION_LOCK', 'tp_child_migrations.lock' );

/**
 * Every one-time migration callback, by hook. A new migration goes in this list too.
 */
function tp_child_migration_callbacks(): array {
	return [
		'after_setup_theme' => [
			'tp_child_chrome_migrate_theme_builder_images',
			'tp_child_chrome_migrate_theme_builder_image_ids_v5',
			'tp_child_chrome_migrate_theme_builder_nav_attrs',
			'tp_child_migrate_materie_marquee_content',
			'tp_child_chrome_migrate_nav_menu_labels',
			'tp_child_nav_url_migrate',
			'tp_child_migrate_legal_pages',
			'tp_child_nav_menu_restore_titles',
			'tp_child_migrate_hero_anchor',
			'tp_child_migrate_design_cleanup',
		],
		'init'              => [
			'tp_child_migrate_menu_label_text_modules',
			'tp_child_migrate_menu_size_s',
			'tp_child_migrate_menu_proposal_row',
			'tp_child_migrate_menu_salsiccia_name',
			'tp_child_migrate_two_size_texts',
		],
	];
}

/**
 * Before the first migration, until a request has run them all for this version: take the lock
 * (and read the flags again, a request that held it may have just set them) or skip them.
 */
function tp_child_migrations_gate(): void {
	if ( TP_CHILD_VERSION === get_option( 'tp_child_migrations_version' ) ) {
		return;
	}

	if ( tp_child_lock( TP_CHILD_MIGRATION_LOCK ) ) {
		wp_cache_delete( 'alloptions', 'options' );
		wp_cache_delete( 'notoptions', 'options' );
		add_action( 'init', 'tp_child_migrations_done', PHP_INT_MAX );

		return;
	}

	foreach ( tp_child_migration_callbacks() as $hook => $callbacks ) {
		foreach ( $callbacks as $callback ) {
			$priority = has_action( $hook, $callback );

			if ( false !== $priority ) {
				remove_action( $hook, $callback, $priority );
			}
		}
	}
}
add_action( 'after_setup_theme', 'tp_child_migrations_gate', 2 );

/**
 * A lock, as WordPress takes it for its updates: a row of the options table that only one request
 * can insert. A lock older than ten minutes belongs to a request that died, and is taken over.
 * The migrations use it, and so does "Crea/aggiorna home" (inc/site-seeder.php).
 */
function tp_child_lock( string $name ): bool {
	global $wpdb;

	if ( $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO `$wpdb->options` ( `option_name`, `option_value`, `autoload` ) VALUES ( %s, %s, 'off' )", $name, (string) time() ) ) ) {
		return true;
	}

	$since = tp_child_lock_since( $name );

	if ( '' === $since || (int) $since > time() - 600 ) {
		return false;
	}

	// Only one of the requests that find the same stale lock gets it.
	return (bool) $wpdb->query( $wpdb->prepare( "UPDATE `$wpdb->options` SET `option_value` = %s WHERE `option_name` = %s AND `option_value` = %s", (string) time(), $name, $since ) );
}

/**
 * When the lock was taken, read from the table (the options cache may be older); empty if it is free.
 */
function tp_child_lock_since( string $name ): string {
	global $wpdb;

	return (string) $wpdb->get_var( $wpdb->prepare( "SELECT `option_value` FROM `$wpdb->options` WHERE `option_name` = %s", $name ) );
}

/**
 * Whether a request is working under the lock now: a lock older than ten minutes does not count.
 */
function tp_child_lock_held( string $name ): bool {
	$since = tp_child_lock_since( $name );

	return '' !== $since && (int) $since > time() - 600;
}

/**
 * After the last migration: no lock is needed again for this version, and this one goes.
 */
function tp_child_migrations_done(): void {
	update_option( 'tp_child_migrations_version', TP_CHILD_VERSION, true );
	delete_option( TP_CHILD_MIGRATION_LOCK );
}
