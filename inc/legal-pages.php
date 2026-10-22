<?php
/**
 * Privacy Policy and Cookie Policy pages for Tasche Piene.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Legal page slugs managed by the child theme.
 */
function tp_child_legal_page_slugs(): array {
	return [
		'privacy' => 'privacy-policy',
		'cookie'  => 'cookie-policy',
	];
}

/**
 * Business details used in legal copy.
 */
function tp_child_legal_business_details(): array {
	return [
		'brand'    => 'Tasche Piene',
		'address'  => 'Via di Scurreria 42/R, 16124 Genova (GE)',
		'vat'      => '03942309300',
		'site_url' => home_url( '/' ),
	];
}

/**
 * Build a Divi text module for legal pages.
 */
function tp_child_legal_text_module( string $class_name, string $html ): string {
	return tp_child_seed_block_single(
		'divi/text',
		[
			'module'         => [
				'advanced' => [
					'htmlAttributes' => [
						'desktop' => [
							'value' => [
								'class' => $class_name,
							],
						],
					],
				],
			],
			'content'        => [
				'innerContent' => [
					'desktop' => [
						'value' => $html,
					],
				],
			],
			'builderVersion' => TP_CHILD_SEED_BUILDER_VERSION,
		]
	);
}

/**
 * Build a Divi heading module for legal pages.
 */
function tp_child_legal_heading_module( string $title ): string {
	return tp_child_seed_block_single(
		'divi/heading',
		[
			'module'         => [
				'advanced' => [
					'htmlAttributes' => [
						'desktop' => [
							'value' => [
								'class' => 'tp-legal-title',
							],
						],
					],
				],
			],
			'title'          => [
				'innerContent' => [
					'desktop' => [
						'value' => $title,
					],
				],
				'decoration'   => [
					'font' => [
						'font' => [
							'desktop' => [
								'value' => [
									'headingLevel' => 'h1',
								],
							],
						],
					],
				],
			],
			'builderVersion' => TP_CHILD_SEED_BUILDER_VERSION,
		]
	);
}

/**
 * Logo block for legal pages.
 */
function tp_child_legal_logo_module(): string {
	return tp_child_seed_block_single(
		'divi/image',
		[
			'module'         => [
				'advanced' => [
					'htmlAttributes' => [
						'desktop' => [
							'value' => [
								'class' => 'tp-legal-logo',
							],
						],
					],
				],
			],
			'image'          => [
				'innerContent' => [
					'desktop' => [
						'value' => [
							'src' => '{{TP_CHILD_URL}}assets/svg/logo-tasche-piene-nav.svg',
							'alt' => 'Tasche Piene',
						],
					],
				],
			],
			'builderVersion' => TP_CHILD_SEED_BUILDER_VERSION,
		]
	);
}

/**
 * Privacy policy HTML body.
 */
function tp_child_legal_privacy_body_html(): string {
	$details = tp_child_legal_business_details();
	$cookie  = esc_url( tp_child_legal_page_url( 'cookie' ) );
	$updated = esc_html( gmdate( 'd/m/Y' ) );

	ob_start();
	?>
	<p><strong>Ultimo aggiornamento:</strong> <?php echo $updated; ?></p>
	<p>La presente informativa descrive come <?php echo esc_html( $details['brand'] ); ?> tratta i dati personali in relazione al sito web <?php echo esc_html( $details['site_url'] ); ?>.</p>

	<h2>1. Titolare del trattamento</h2>
	<p>
		<strong><?php echo esc_html( $details['brand'] ); ?></strong><br>
		<?php echo esc_html( $details['address'] ); ?><br>
		P. IVA <?php echo esc_html( $details['vat'] ); ?>
	</p>

	<h2>2. Tipologia di sito</h2>
	<p>Il sito ha finalità informativa e promozionale: presenta l&apos;attività gastronomica, il menu e le informazioni utili per raggiungerci in negozio.</p>
	<p><strong>Non sono presenti moduli di contatto, form di iscrizione, newsletter o raccolta diretta di dati tramite e-mail sul sito.</strong></p>

	<h2>3. Dati trattati</h2>
	<p>Potrebbero essere trattati, in modo limitato e per il solo funzionamento tecnico del sito:</p>
	<ul>
		<li>dati di navigazione e log tecnici (es. indirizzo IP, data e ora della richiesta, user agent del browser);</li>
		<li>cookie e strumenti tecnici descritti nella <a href="<?php echo $cookie; ?>">Cookie Policy</a>.</li>
	</ul>
	<p>Non effettuiamo profilazione commerciale né invio di comunicazioni promozionali tramite il sito.</p>

	<h2>4. Finalità e base giuridica</h2>
	<ul>
		<li><strong>Funzionamento e sicurezza del sito</strong> — legittimo interesse del titolare e necessità tecnica (art. 6, par. 1, lett. f GDPR).</li>
		<li><strong>Cookie tecnici</strong> — vedi Cookie Policy; per i cookie non strettamente necessari, ove presenti, il consenso viene richiesto separatamente.</li>
	</ul>

	<h2>5. Conservazione</h2>
	<p>I dati tecnici di log sono conservati per il tempo strettamente necessario alla sicurezza e alla manutenzione del sito, secondo i termini del provider di hosting.</p>

	<h2>6. Comunicazione e trasferimenti</h2>
	<p>I dati possono essere trattati da fornitori tecnici (hosting, manutenzione sito) nominati responsabili del trattamento o autonomi titolari, solo per erogare il servizio.</p>

	<h2>7. Diritti dell&apos;interessato</h2>
	<p>In qualità di interessato puoi esercitare i diritti previsti dagli artt. 15-22 GDPR (accesso, rettifica, cancellazione, limitazione, opposizione, portabilità ove applicabile) rivolgendoti al titolare presso la sede indicata sopra.</p>
	<p>Hai inoltre diritto di proporre reclamo all&apos;Autorità Garante per la protezione dei dati personali (<a href="https://www.garanteprivacy.it" rel="noopener noreferrer" target="_blank">garanteprivacy.it</a>).</p>

	<h2>8. Modifiche</h2>
	<p>Il titolare può aggiornare la presente informativa; la versione aggiornata sarà pubblicata su questa pagina.</p>
	<?php
	return trim( (string) ob_get_clean() );
}

/**
 * Cookie policy HTML body.
 */
function tp_child_legal_cookie_body_html(): string {
	$details = tp_child_legal_business_details();
	$privacy = esc_url( tp_child_legal_page_url( 'privacy' ) );
	$updated = esc_html( gmdate( 'd/m/Y' ) );

	ob_start();
	?>
	<p><strong>Ultimo aggiornamento:</strong> <?php echo $updated; ?></p>
	<p>Questa Cookie Policy spiega come <?php echo esc_html( $details['brand'] ); ?> utilizza cookie e tecnologie simili sul sito <?php echo esc_html( $details['site_url'] ); ?>.</p>

	<h2>1. Cosa sono i cookie</h2>
	<p>I cookie sono piccoli file di testo che i siti possono salvare sul dispositivo dell&apos;utente per memorizzare preferenze o informazioni tecniche.</p>

	<h2>2. Cookie utilizzati da questo sito</h2>
	<p>Il sito utilizza principalmente <strong>cookie tecnici</strong>, necessari al corretto funzionamento della piattaforma WordPress e alla visualizzazione delle pagine.</p>
	<p>Tra questi possono rientrare, a seconda della configurazione del server:</p>
	<ul>
		<li>cookie di sessione o tecnici di WordPress;</li>
		<li>cookie legati alla sicurezza e alla cache del sito;</li>
		<li>preferenze salvate dal builder o dal tema per la corretta resa grafica.</li>
	</ul>
	<p><strong>Non utilizziamo cookie di profilazione o pubblicitari</strong> e non raccogliamo dati tramite form o newsletter sul sito.</p>
	<p>Se in futuro venissero attivati strumenti di analisi o marketing (es. Google Analytics, pixel social), questa informativa sarà aggiornata e, ove richiesto, verrà richiesto il consenso tramite apposito banner.</p>

	<h2>3. Cookie di terze parti</h2>
	<p>Il sito può includere link esterni (es. mappe o profili social). Cliccando su tali link si accede a siti di terze parti con informative autonome.</p>

	<h2>4. Come gestire i cookie dal browser</h2>
	<p>Puoi configurare il browser per bloccare o cancellare i cookie. La disabilitazione dei cookie tecnici potrebbe compromettere alcune funzioni del sito.</p>

	<h2>5. Titolare e privacy</h2>
	<p>Per il trattamento dei dati personali collegato all&apos;uso del sito, vedi la <a href="<?php echo $privacy; ?>">Privacy Policy</a>.</p>
	<?php
	return trim( (string) ob_get_clean() );
}

/**
 * Footer note with cross-links.
 */
function tp_child_legal_footer_nav_html( string $type ): string {
	$home    = esc_url( home_url( '/' ) );
	$privacy = esc_url( tp_child_legal_page_url( 'privacy' ) );
	$cookie  = esc_url( tp_child_legal_page_url( 'cookie' ) );

	if ( 'privacy' === $type ) {
		return sprintf(
			'<p class="tp-legal-back"><a href="%1$s">Torna alla home</a> · <a href="%2$s">Cookie Policy</a></p>',
			$home,
			$cookie
		);
	}

	return sprintf(
		'<p class="tp-legal-back"><a href="%1$s">Torna alla home</a> · <a href="%2$s">Privacy Policy</a></p>',
		$home,
		$privacy
	);
}

/**
 * Assemble a full legal page layout.
 */
function tp_child_legal_page_content( string $type ): string {
	$section_class = 'tp-legal-page tp-legal-page--' . sanitize_key( $type );
	$title         = 'privacy' === $type ? 'Privacy Policy' : 'Cookie Policy';
	$body          = 'privacy' === $type ? tp_child_legal_privacy_body_html() : tp_child_legal_cookie_body_html();

	$modules  = tp_child_legal_heading_module( $title );
	$modules .= tp_child_legal_text_module( 'tp-legal-body', $body );
	$modules .= tp_child_legal_text_module( 'tp-legal-back-wrap', tp_child_legal_footer_nav_html( $type ) );

	$content = tp_child_seed_block_open( 'divi/placeholder' );
	$content .= tp_child_seed_block_open( 'divi/section', tp_child_legal_section_attrs( $section_class, $title ) );
	$content .= tp_child_seed_block_open( 'divi/row', tp_child_seed_row_attrs() );
	$content .= tp_child_seed_block_open( 'divi/column', tp_child_seed_column_attrs() );
	$content .= $modules;
	$content .= tp_child_seed_block_close( 'divi/column' );
	$content .= tp_child_seed_block_close( 'divi/row' );
	$content .= tp_child_seed_block_close( 'divi/section' );
	$content .= tp_child_seed_block_close( 'divi/placeholder' );

	return str_replace( '{{TP_CHILD_URL}}', esc_url_raw( TP_CHILD_URL ), $content );
}

/**
 * Section attrs with cream background for legal pages.
 */
function tp_child_legal_section_attrs( string $class_name, string $label ): array {
	$attrs = tp_child_seed_section_attrs( $class_name, $label );
	$attrs['module']['decoration']['background']['desktop']['value']['color'] = '#fef5e9';

	return $attrs;
}

/**
 * URL for a managed legal page.
 */
function tp_child_legal_page_url( string $type ): string {
	$slugs = tp_child_legal_page_slugs();
	$slug  = $slugs[ $type ] ?? '';

	if ( '' === $slug ) {
		return home_url( '/' );
	}

	$page = get_page_by_path( $slug, OBJECT, 'page' );

	return $page instanceof WP_Post ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
}

/**
 * Create or update legal pages.
 */
function tp_child_seed_legal_pages(): array {
	$pages = [
		'privacy' => [
			'slug'  => tp_child_legal_page_slugs()['privacy'],
			'title' => 'Privacy Policy',
		],
		'cookie'  => [
			'slug'  => tp_child_legal_page_slugs()['cookie'],
			'title' => 'Cookie Policy',
		],
	];

	$ids = [];

	foreach ( $pages as $type => $config ) {
		$content = tp_child_legal_page_content( $type );
		$post_id = tp_child_seed_upsert_post( 'page', $config['slug'], $config['title'], $content );

		if ( $post_id ) {
			tp_child_seed_apply_divi_meta( $post_id, $config['title'], 'page', 'off', false );
			tp_child_seed_clear_divi_cache_meta( $post_id );
			$ids[ $type ] = $post_id;
		}
	}

	if ( ! empty( $ids['privacy'] ) ) {
		update_option( 'wp_page_for_privacy_policy', (int) $ids['privacy'] );
	}

	tp_child_update_footer_legal_links( $ids );
	flush_rewrite_rules( false );

	return $ids;
}

/**
 * Point footer copyright lines to legal pages.
 */
function tp_child_update_footer_legal_links( array $ids = [] ): void {
	if ( empty( $ids['privacy'] ) ) {
		$privacy = get_page_by_path( tp_child_legal_page_slugs()['privacy'], OBJECT, 'page' );
		$ids['privacy'] = $privacy instanceof WP_Post ? (int) $privacy->ID : 0;
	}

	if ( empty( $ids['cookie'] ) ) {
		$cookie = get_page_by_path( tp_child_legal_page_slugs()['cookie'], OBJECT, 'page' );
		$ids['cookie'] = $cookie instanceof WP_Post ? (int) $cookie->ID : 0;
	}

	if ( empty( $ids['privacy'] ) && empty( $ids['cookie'] ) ) {
		return;
	}

	$privacy_url = ! empty( $ids['privacy'] ) ? get_permalink( (int) $ids['privacy'] ) : '';
	$cookie_url  = ! empty( $ids['cookie'] ) ? get_permalink( (int) $ids['cookie'] ) : '';

	$posts = get_posts(
		[
			'post_type'      => 'et_footer_layout',
			'post_status'    => [ 'publish', 'draft', 'private' ],
			'posts_per_page' => -1,
			'fields'         => 'ids',
		]
	);

	foreach ( $posts as $post_id ) {
		$content = (string) get_post_field( 'post_content', $post_id );

		if ( '' === $content || false === strpos( $content, 'tp/theme-footer' ) ) {
			continue;
		}

		if ( ! function_exists( 'parse_blocks' ) || ! function_exists( 'serialize_blocks' ) ) {
			continue;
		}

		$blocks          = parse_blocks( $content );
		$updated_blocks  = tp_child_legal_patch_footer_blocks( $blocks, $privacy_url, $cookie_url );
		$updated_content = serialize_blocks( $updated_blocks );

		if ( $updated_content === $content ) {
			continue;
		}

		wp_update_post(
			[
				'ID'           => (int) $post_id,
				'post_content' => wp_slash( $updated_content ),
			]
		);
	}
}

/**
 * Patch tp/theme-footer attrs with legal links.
 */
function tp_child_legal_patch_footer_blocks( array $blocks, string $privacy_url, string $cookie_url ): array {
	foreach ( $blocks as $index => $block ) {
		if ( ! is_array( $block ) ) {
			continue;
		}

		if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
			$blocks[ $index ]['innerBlocks'] = tp_child_legal_patch_footer_blocks( $block['innerBlocks'], $privacy_url, $cookie_url );
		}

		if ( 'tp/theme-footer' !== ( $block['blockName'] ?? '' ) || empty( $block['attrs'] ) || ! is_array( $block['attrs'] ) ) {
			continue;
		}

		$attrs = $block['attrs'];

		if ( $privacy_url ) {
			$attrs['copyrightLine3'] = [
				'innerContent' => [
					'desktop' => [
						'value' => 'Privacy',
					],
				],
			];
			$attrs['copyrightLink3'] = [
				'innerContent' => [
					'desktop' => [
						'value' => $privacy_url,
					],
				],
			];
		}

		if ( $cookie_url ) {
			$attrs['copyrightLine4'] = [
				'innerContent' => [
					'desktop' => [
						'value' => 'Cookie',
					],
				],
			];
			$attrs['copyrightLink4'] = [
				'innerContent' => [
					'desktop' => [
						'value' => $cookie_url,
					],
				],
			];
		}

		$attrs['copyrightLine5'] = [
			'innerContent' => [
				'desktop' => [
					'value' => 'Designed By Mochi',
				],
			],
		];
		$attrs['copyrightLink5'] = [
			'innerContent' => [
				'desktop' => [
					'value' => '',
				],
			],
		];

		$blocks[ $index ]['attrs'] = $attrs;
	}

	return $blocks;
}

/**
 * Whether the current request is a managed legal page.
 */
function tp_child_is_legal_page(): bool {
	if ( is_admin() || ( function_exists( 'tp_child_is_visual_builder' ) && tp_child_is_visual_builder() ) ) {
		return false;
	}

	if ( ! is_page() ) {
		return false;
	}

	return is_page( array_values( tp_child_legal_page_slugs() ) );
}

/**
 * Body class for legal document layout (no fixed footer reveal).
 */
function tp_child_legal_page_body_class( array $classes ): array {
	if ( tp_child_is_legal_page() ) {
		$classes[] = 'tp-legal-document';
	}

	return $classes;
}

add_filter( 'body_class', 'tp_child_legal_page_body_class', 20 );

/**
 * Whether both managed legal pages exist.
 */
function tp_child_legal_pages_exist(): bool {
	foreach ( tp_child_legal_page_slugs() as $slug ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );

		if ( ! $page instanceof WP_Post ) {
			return false;
		}
	}

	return true;
}

/**
 * Create legal pages when missing; refresh footer links when present.
 */
function tp_child_ensure_legal_pages(): array {
	if ( tp_child_legal_pages_exist() ) {
		tp_child_update_footer_legal_links();

		return [];
	}

	return tp_child_seed_legal_pages();
}

/**
 * Bootstrap / repair legal pages on theme load (idempotent).
 */
function tp_child_migrate_legal_pages(): void {
	if ( tp_child_legal_pages_exist() ) {
		if ( ! get_option( 'tp_child_legal_pages_v2' ) ) {
			tp_child_update_footer_legal_links();
			update_option( 'tp_child_legal_pages_v1', '1', false );
			update_option( 'tp_child_legal_pages_v2', '1', false );
		}

		return;
	}

	tp_child_ensure_legal_pages();
	update_option( 'tp_child_legal_pages_v1', '1', false );
	update_option( 'tp_child_legal_pages_v2', '1', false );

	if ( function_exists( 'et_core_clear_cache' ) ) {
		et_core_clear_cache();
	}
}
add_action( 'after_setup_theme', 'tp_child_migrate_legal_pages', 24 );

