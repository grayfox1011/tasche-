<?php
/**
 * 0.7.0: the texts of taschepiene.it on the new design. The owner keeps the design of the Figma and takes the words
 * of the public site: titles, paragraphs, the dishes of the menu with their descriptions in Italian (where the Figma
 * has an English line) and their prices, the opening hours, the links of the header, the company data of the footer.
 *
 * - Hero: "Pensavi / fosse amore / e invece era / Polpetta", "Guarda il menù", "Street food · Gastronomia · Genova
 *   Centro Storico" under the title.
 * - Striscia: the seven phrases of the site.
 * - Introduzione and Alberto: the paragraphs and the title of the site.
 * - Tasca o piatto: "Tasca ripiena o vaschetta. / Mangi qui o dove vuoi tu!", "La vaschetta", the three lines and the
 *   text of each card.
 * - Menù: "Il nostro menù", the legend (Tasca di pane ripiena, Vaschetta Media / Grande), the nine dishes of the site
 *   (no Vitello tonnato; the salsiccia "in rotazione" in the row of the Proposta del mese), their descriptions and
 *   prices, no allergen numbers next to the dishes (the site has none: all its recipes are gluten free and the numbers
 *   of the Figma said otherwise), "I nostri contorni", "Verdure al forno", the drinks of the site in its order, the
 *   allergen list and the note of the site.
 * - Materie prime: the text of the site and its four cards (Le ormai celebri polpette, I ripieni liguri, Tutto senza
 *   glutine, Spumette a bicchiere) with the photos of the site, without the words in Genoese.
 * - Dove siamo: the text and the address of the site.
 * - Orari: the hours of the site (12-21, Saturday 12-22), the trippa on Tuesday and Friday.
 * - Header: La nostra idea, Il menù, Senza glutine, Domande (to Informazioni utili), Dove siamo.
 * - Footer: the company, VAT number and address of the site, Privacy Policy | Cookie Policy, "creato con ♥ da Mochi".
 * - Privacy Policy: the company of the site as data controller.
 *
 * As in 0.5.9 (inc/figma-059-migration.php, whose helpers this file uses), every change is made only where the content
 * is still the one the theme wrote: a text changed by hand in Divi stays. The same transform made the content of "Crea/
 * aggiorna home" (inc/seed/home-content.php), so a new site and an updated one are the same.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The block of $content at $index taken out, with the line breaks after it.
 */
function tp_child_t70_remove( string $content, array $block ): string {
	$end = $block['end'] + strspn( $content, "\n", $block['end'] );

	return substr_replace( $content, '', $block['start'], $end - $block['start'] );
}

/**
 * The children of block $parent with the given block name, in order.
 */
function tp_child_t70_children( array $blocks, int $parent, string $name ): array {
	$children = [];

	foreach ( $blocks as $index => $block ) {
		if ( $parent === $block['parent'] && $name === $block['name'] ) {
			$children[] = $index;
		}
	}

	return $children;
}

/**
 * A self-closing block of the theme: $attrs (objects) serialized as WordPress does.
 */
function tp_child_t70_block( string $name, stdClass $attrs ): string {
	return '<!-- wp:' . $name . ' ' . serialize_block_attributes( $attrs ) . ' /-->';
}

/**
 * The value of one attribute of the theme ({"innerContent":{"desktop":{"value": ...}}}).
 */
function tp_child_t70_value( $value ): stdClass {
	return (object) [ 'innerContent' => (object) [ 'desktop' => (object) [ 'value' => $value ] ] ];
}

/**
 * The texts of the home: [ class, the theme's text (or texts), the site's ].
 */
function tp_child_t70_simple_texts(): array {
	return [
		[ 'tp-hero-button', 'Scopri il menù', 'Guarda il menù' ],
		[ 'tp-hero-subline', 'Ne abbiamo le tasche piene<br>degli “assaggini”', 'Street food · Gastronomia · Genova Centro Storico' ],
		[
			'tp-intro-copy',
			"<p>Genova ha un centro storico che molte città si sognano. Ogni anno diventa più bella e più curata… ma un po’ meno sé stessa. I locali si moltiplicano, le insegne si assomigliano tutte, i menù cominciano sempre con «street food» e finiscono con «concept». Di quella musica lì, ne abbiamo le tasche piene.</p>\n<p>Alberto ha la testa tra gli ingredienti da che si ricorda. Al mercato conosce il nome dei produttori, si ferma a parlare, torna con qualcosa di bello e ci costruisce un piatto.</p>\n<p>Tasche Piene è una gastronomia. Di quelle vere: banco, vaschette, cucinato pronto, odore di sugo. Tutto fatto nel nostro laboratorio, ogni mattina. Non deleghiamo, non compriamo cose pronte da riscaldare. Se c’è lo stoccafisso il giovedì, è perché qualcuno l’ha messo sul fuoco il giorno prima.</p>",
			"<p>Un’idea di Davide e Alberto, imprenditori genovesi che di stare con le mani in mano non sono capaci.</p>\n<p>Genova ha un centro storico che molte città si sognano. Ogni anno diventa più bella e più curata… ma un po’ meno sé stessa. I locali si moltiplicano, le insegne si assomigliano tutte, i menù cominciano sempre con «street food» e finiscono con «concept». Di quella musica lì, ne abbiamo le tasche piene.</p>\n<p>Ogni mattina nel nostro laboratorio cuociono le polpette nel sugo, lo stocco, la trippa, i ripieni liguri. Li mettiamo in tasca ancora caldi: il sugo inzuppa il pane e ogni morso è una scarpetta.</p>",
		],
		[ 'tp-alberto-title', "Una gastronomia, com'era una volta.", 'Cucinato ogni mattina nel nostro laboratorio, tutto senza glutine' ],
		[
			'tp-alberto-copy',
			"<p>Tasche Piene è una gastronomia. Di quelle vere: banco, vaschette, cucinato pronto, odore di sugo. Tutto fatto nel nostro laboratorio, ogni mattina. Non deleghiamo, non compriamo cose pronte da riscaldare. Se c’è lo stoccafisso il giovedì, è perché qualcuno l’ha messo sul fuoco il giorno prima.</p>\n<p>Le acciughe sott’olio le facciamo con la ricetta di Luciano, il papà di Davide. Luciano amava cucinare: certe ricette, quando sono fatte bene, non hanno bisogno di essere cambiate.</p>\n<p>Davide viene dall’informatica. Alberto lavora nello shipping. Nessuno dei due sa cucinare. Ed è proprio per questo che tengono così tanto che in cucina le cose siano fatte come si deve: loro non potrebbero rimediare.</p>",
			'<p>Da Tasche Piene è come una volta: profumo di sugo, cucina fresca ogni mattina e le acciughe di Luciano, il papà di Davide. Tutte le nostre ricette sono senza glutine: niente farina e niente pangrattato, nemmeno nel polpettone. Usiamo teglie chiuse e separate, ma la cucina è una sola. Il consiglio? Passate al banco, ne parliamo a voce!</p>',
		],
		[ 'tp-scelta-titolo', '<h2>Tasca o piatto<br />Mangi qui o a casa<br />Sta a te</h2>', '<h2>Tasca ripiena o vaschetta.<br />Mangi qui o dove vuoi tu!</h2>' ],
		[ 'tp-menu-title', '<h2>Il nostro menu’</h2>', '<h2>Il nostro menù</h2>' ],
		[ 'tp-menu-legend-tasca', '<h4>TASCA</h4><p>Il panino</p>', '<h4>TASCA</h4><p>Di pane ripiena</p>' ],
		[ 'tp-menu-legend-piatto', '<h4>PIATTO</h4><p>Regular - 200g</p><p>Large - 400g</p>', '<h4>VASCHETTA</h4><p>Media - 200g</p><p>Grande - 400g</p>' ],
		[
			'tp-menu-note',
			"<p>Allergeni</p>\n<p>1 glutine • 2 latte • 3 uova • 4 arachidi • 5 frutta a guscio • 5 pesce<br>6 crostacei • 7 molluschi • 8 soia • 9 sesamo • 10 sedano • 11 senape</p>\n<p>Senza glutine disponibile su richiesta <br>(pane confezionato singolo)</p>\n<p>Nota scarsità max 10–12 porzioni per stocco e trippa. Quando finisce puoi venire prima la settimana dopo.</p>",
			"<p>Allergeni</p>\n<p>1 glutine • 2 crostacei • 3 uova • 4 pesce • 5 arachidi • 6 soia • 7 latte • 8 frutta a guscio • 9 sedano • 10 senape • 11 sesamo • 12 anidride solforosa e solfiti • 13 lupini • 14 molluschi</p>\n<p>Tutte le ricette sono senza glutine. Il pane senza glutine è confezionato singolarmente: chiedilo al banco.</p>",
		],
		[
			'tp-materie-desc',
			"<p>Ogni cosa che vedi nel banco è il risultato di una scelta precisa. Non compriamo pronto, non riscaldiamo per rivendere: se è nel banco, qualcuno l'ha cucinato quella mattina. Polpette, stocco, trippa, ripieni liguri ricette che si cucinano da quando Genova è Genova. Noi non le abbiamo reinventate, le abbiamo solo messe in tasca.</p>\n<p>Siamo a Piazza San Lorenzo, centro storico vero. Un posto senza età, senza fretta, senza liste d'attesa per chi lavora qui vicino, per chi abita nel quartiere, per chi capita nei caruggi per la prima volta.<br>5,50 € per un pasto vero: né un euro di meno, né uno di più del necessario.</p>",
			"<p>Ogni cosa che vedi nel banco è il risultato di una scelta precisa. Non compriamo pronto, non riscaldiamo per rivendere: se è nel banco, qualcuno l'ha cucinato quella mattina. Polpette, stocco, trippa, ripieni liguri: ricette che si cucinano da quando Genova è Genova. Noi non le abbiamo reinventate, le abbiamo solo messe in tasca.</p>\n<p>Siamo in piazza San Lorenzo, centro storico vero. Un posto senza età, senza fretta, senza liste d'attesa per chi lavora qui vicino, per chi abita nel quartiere, per chi capita nei caruggi per la prima volta.<br>5,50 € per un pasto vero: né un euro di meno, né uno di più del necessario.</p>",
		],
		[ 'tp-location-text', '<p>Siamo in Piazza San Lorenzo a due passi dalla Cattedrale.<br>Ci trovi anche su Google Maps, ma lasciando il cellulare in tasca e venendo a piedi si capisce meglio</p>', '<p>Siamo in Piazza San Lorenzo a due passi dalla Cattedrale.</p>' ],
		[ 'tp-location-address', '<p>Via Di Scurreria 42R</p>', '<p>Via di Scurreria 42/R</p>' ],
		[ 'tp-menu-sectionhead-title', 'Contorni', 'I nostri contorni' ],
		[ 'tp-menu-sectionhead-subtitle', '<p>Cosa vuoi vicino?</p>', '<p>Cosa ci vuoi vicino?</p>' ],
	];
}

/**
 * The cards of Tasca o piatto: card class => [ [ class, the theme's text, the site's ]... ].
 */
function tp_child_t70_choice_texts(): array {
	return [
		'tp-scelta-scheda--tasca'  => [
			[ 'tp-scelta-chip', '<ul><li>Il format originale</li><li>60g di pane</li><li>100 g di ripieno</li></ul>', '<ul><li>Tra pinsa e panino</li><li>Il sugo inzuppa la mollica</li><li>È come fare la scarpetta</li></ul>' ],
			[ 'tp-scelta-descrizione', '<p>Un panino piccolo, si mangia in mano e si porta ovunque. Tante persone ne prendono due: lo diciamo subito così non ci sono sorprese.</p>', '<p>Anche senza glutine, dolci comprese: le scaldiamo in una vaschetta chiusa. La cucina è una sola, se hai la celiachia dillo al banco.</p>' ],
		],
		'tp-scelta-scheda--piatto' => [
			[ 'tp-scelta-nome', 'Il piatto', 'La vaschetta' ],
			[ 'tp-scelta-chip', '<ul><li>3 dimensioni S-M-L</li><li>Pane a parte</li><li>Include un contorno</li></ul>', '<ul><li>Media da 200 g o grande da 400 g</li><li>Con un contorno incluso</li><li>Il pane croccante, se vuoi, a parte</li></ul>' ],
			[ 'tp-scelta-descrizione', '<p>Stesso cibo della tasca, ma al piatto. Ripieno in vaschetta, pane a parte in un sacchettino, così resta croccante. Include un contorno a scelta.</p>', '<p>Stesse ricette, nel piatto: tanto sugo, contorno e pane croccante per la scarpetta. Arriva anche a casa con Deliveroo e Just Eat.</p>' ],
		],
	];
}

/**
 * The dishes of the menu, by the theme's name: [ the site's name, the theme's allergens (they go), the theme's English
 * line, the site's description, the theme's Tasca / M / L, the site's Tasca / M / L ].
 */
function tp_child_t70_dishes(): array {
	return [
		'MELANZANE ALLA PARMIGIANA'                  => [ 'MELANZANA ALLA PARMIGIANA', '7,8', 'baked layers of eggplant, tomato and cheese', 'Strati di melanzana, sugo e formaggio filante', [ '6,00€', '7,00€', '10,00€' ], [ '5,50€', '6,00€', '9,00€' ] ],
		'RIPIENI ALLA GENOVESE'                      => [ 'RIPIENI LIGURI DI STAGIONE', '1,3,7,12', 'genovese-style stuffed vegetables', 'Zucchina, cipolla, peperone. Cambia con la stagione', [ '6,00€', '7,00€', '10,00€' ], [ '5,50€', '6,00€', '9,00€' ] ],
		'POLPETTE AL SUGO'                           => [ 'POLPETTE AL SUGO', '1,3,7', 'meatballs in tomato sauce', 'Tre polpette morbide, circa 100 g, affogate nel sugo', [ '6,00€', '7,00€', '10,00€' ], [ '6,50€', '7,50€', '10,50€' ] ],
		'POLLO ALLA LIGURE'                          => [ 'POLLO ALLA LIGURE, OLIVE TAGGIASCHE, PINOLI', '8,12', 'ligurian-style chicken with olives and pine nuts', 'Pollo in umido, sughetto di olive e pinoli', [ '6,00€', '7,00€', '10,00€' ], [ '6,50€', '7,50€', '10,50€' ] ],
		'TRIPPA IN UMIDO ALLA GENOVESE (di Venerdì)' => [ 'TRIPPA - MARTEDÌ E VENERDÌ', '8,9,12', 'traditional slow-cooked beef tripe stew', 'Quella genovese, con le trippe della Tripperia La Casana. Max 10-12 porzioni', [ '6,50€', '7,50€', '10,50€' ], [ '7,50€', '7,50€', '10,50€' ] ],
		'STOCCAFISSO ACCOMODATO (di Giovedì)'        => [ 'STOCCAFISSO - TUTTI I GIORNI', '4,8,9,12', 'ligurian-style stockfish stew (thursday)', 'Ricetta tradizionale ligure. Max 10-12 porzioni', [ '6,50€', '7,50€', '10,50€' ], [ '7,50€', '7,50€', '10,50€' ] ],
		'POMODORO, MOZZARELLA, PESTO'                => [ 'CAPRESE, PESTO GENOVESE, BUFALA', '7,8', 'confit tomato, mozzarella, basil pesto', 'Pomodoro fresco, stracciatella e pesto', [ '5,50€', '6,00€', '9,00€' ], [ '5,50€', '6,00€', '9,00€' ] ],
		'ACCIUGHE SOTT’OLIO, STRACCIATELLA'          => [ 'LE ACCIUGHE DI LUCIANO + STRACCIATELLA', '4,7', 'anchovies in oil with stracciatella cheese', 'Olive, capperi e ricetta di Luciano', [ '5,50€', '6,00€', '9,00€' ], [ '6,50€', '7,50€', '10,50€' ] ],
	];
}

/**
 * The index of the row of table $table whose name is exactly $name (as typed), or null.
 */
function tp_child_t70_row( array $blocks, string $table, string $name ): ?int {
	foreach ( tp_child_f59_rows( $blocks, $table ) as $row ) {
		if ( trim( tp_child_f59_row_name( $blocks, $row ) ) === $name ) {
			return $row;
		}
	}

	return null;
}

/**
 * The text of the first block with $class inside $row ('' when there is none).
 */
function tp_child_t70_text_in( array $blocks, int $row, string $class ): string {
	$found = tp_child_f59_find( $blocks, $class, $row );

	return $found ? tp_child_f59_text( $blocks[ $found[0] ] ) : '';
}

/**
 * One dish of the menu: prices, description, allergens out, name; each where it is still the theme's.
 */
function tp_child_t70_dish( string $content, string $old, array $dish ): string {
	list( $name, $allergens, $meta_from, $meta, $prices_from, $prices ) = $dish;

	$blocks = tp_child_f59_blocks( $content );
	$row    = tp_child_t70_row( $blocks, 'tp-menu-table--main', $old );

	if ( null === $row ) {
		return $content;
	}

	// From the end of the row to its start, so the offsets of the blocks still to change stay right.
	$steps = [
		[ 'tp-menu-price-value-l', "<p>{$prices_from[2]}</p>", "<p>{$prices[2]}</p>" ],
		[ 'tp-menu-price-value-m', "<p>{$prices_from[1]}</p>", "<p>{$prices[1]}</p>" ],
		[ 'tp-menu-price-value-tasca', "<p>{$prices_from[0]}</p>", "<p>{$prices[0]}</p>" ],
		[ 'tp-menu-meta', "<span>{$meta_from}</span>", "<span>{$meta}</span>" ],
	];

	foreach ( $steps as list( $class, $from, $to ) ) {
		foreach ( array_reverse( tp_child_f59_find( $blocks, $class, $row ) ) as $index ) {
			$content = tp_child_f59_swap_text( $content, $blocks, $index, $from, $to );
		}
	}

	// The allergen numbers of the Figma go: the site has none next to its dishes.
	$blocks = tp_child_f59_blocks( $content );
	$row    = tp_child_t70_row( $blocks, 'tp-menu-table--main', $old );

	if ( null === $row ) {
		return $content;
	}

	foreach ( array_reverse( tp_child_f59_find( $blocks, 'tp-menu-allergeni', $row ) ) as $index ) {
		if ( "<p>{$allergens}</p>" === tp_child_f59_text( $blocks[ $index ] ) ) {
			$content = tp_child_t70_remove( $content, $blocks[ $index ] );
		}
	}

	// The name last: the row is found by its old name until here.
	$blocks = tp_child_f59_blocks( $content );
	$row    = tp_child_t70_row( $blocks, 'tp-menu-table--main', $old );

	if ( null !== $row ) {
		foreach ( tp_child_f59_find( $blocks, 'tp-menu-name', $row ) as $index ) {
			$content = tp_child_f59_swap_text( $content, $blocks, $index, $old, $name );
		}
	}

	return $content;
}

/**
 * The menu: dishes, Vitello tonnato out, the salsiccia in the row of the Proposta del mese, contorni, dolci, bevande.
 */
function tp_child_t70_menu( string $content ): string {
	foreach ( tp_child_t70_dishes() as $old => $dish ) {
		$content = tp_child_t70_dish( $content, $old, $dish );
	}

	// Vitello tonnato is not on the site: the row goes when it is still the theme's.
	$blocks = tp_child_f59_blocks( $content );
	$row    = tp_child_t70_row( $blocks, 'tp-menu-table--main', 'VITELLO TONNATO' );

	if ( null !== $row
		&& '<span>sliced veal with creamy tuna dressing</span>' === tp_child_t70_text_in( $blocks, $row, 'tp-menu-meta' )
		&& '<p>6,50€</p>' === tp_child_t70_text_in( $blocks, $row, 'tp-menu-price-value-tasca' )
		&& '<p>7,50€</p>' === tp_child_t70_text_in( $blocks, $row, 'tp-menu-price-value-m' )
		&& '<p>10,50€</p>' === tp_child_t70_text_in( $blocks, $row, 'tp-menu-price-value-l' ) ) {
		$content = tp_child_t70_remove( $content, $blocks[ $row ] );
	}

	// The salsiccia of the site, "in rotazione", in the row of the Proposta del mese.
	$blocks = tp_child_f59_blocks( $content );
	$row    = tp_child_t70_row( $blocks, 'tp-menu-table--main', 'PROPOSTA DEL MESE' );

	if ( null !== $row ) {
		$steps = [
			[ 'tp-menu-price-value-tasca', '<p>6-8€</p>', '<p>7,50€</p>' ],
			[ 'tp-menu-meta', '<span>monthly special</span>', '<span>IN ROTAZIONE · PULLED PORK, CAVOLO VIOLA E MAYO SPICY</span>' ],
			[ 'tp-menu-name', 'PROPOSTA DEL MESE', 'SALSICCIA, ALLORO E PEPERONI CONFIT' ],
		];

		foreach ( $steps as list( $class, $from, $to ) ) {
			foreach ( array_reverse( tp_child_f59_find( $blocks, $class, $row ) ) as $index ) {
				$content = tp_child_f59_swap_text( $content, $blocks, $index, $from, $to );
			}
		}
	}

	// Contorni and dolci: the site's names, no English line under them.
	$rows = [
		[ 'tp-menu-table--sides', 'PATATE AL FORNO', 'PATATE AL FORNO', 'ROASTED POTATOES' ],
		[ 'tp-menu-table--sides', 'VERDURE DEL GIORNO', 'VERDURE AL FORNO', 'VEGETABLE OF THE DAY' ],
		[ 'tp-menu-table--desserts', 'TIRAMISÙ IN TASCA', 'TIRAMISÙ IN TASCA', 'FILLED WITH FRESH TIRAMISÙ CREAM' ],
	];

	foreach ( $rows as list( $table, $old, $name, $meta ) ) {
		$blocks = tp_child_f59_blocks( $content );
		$row    = tp_child_t70_row( $blocks, $table, $old );

		if ( null === $row ) {
			continue;
		}

		foreach ( array_reverse( tp_child_f59_find( $blocks, 'tp-menu-meta', $row ) ) as $index ) {
			if ( "<span>{$meta}</span>" === tp_child_f59_text( $blocks[ $index ] ) ) {
				$content = tp_child_t70_remove( $content, $blocks[ $index ] );
			}
		}

		if ( $old !== $name ) {
			$blocks = tp_child_f59_blocks( $content );
			$row    = tp_child_t70_row( $blocks, $table, $old );

			foreach ( null !== $row ? tp_child_f59_find( $blocks, 'tp-menu-name', $row ) : [] as $index ) {
				$content = tp_child_f59_swap_text( $content, $blocks, $index, $old, $name );
			}
		}
	}

	// Bevande: the site's names, then its order (Acqua, Bevande, Spumetta).
	foreach ( [ 'BIBITE' => 'BEVANDE', 'SPUMETTA (A BICCHIERE)' => 'SPUMETTA (AL BICCHIERE)' ] as $old => $name ) {
		$blocks = tp_child_f59_blocks( $content );
		$row    = tp_child_t70_row( $blocks, 'tp-menu-table--drinks', $old );

		foreach ( null !== $row ? tp_child_f59_find( $blocks, 'tp-menu-name', $row ) : [] as $index ) {
			$content = tp_child_f59_swap_text( $content, $blocks, $index, $old, $name );
		}
	}

	return tp_child_t70_drinks_order( $content );
}

/**
 * The drinks in the site's order: Acqua first, when the three rows are still Bevande, Spumetta, Acqua.
 */
function tp_child_t70_drinks_order( string $content ): string {
	$blocks = tp_child_f59_blocks( $content );
	$rows   = tp_child_f59_rows( $blocks, 'tp-menu-table--drinks' );
	$names  = array_map(
		static function ( int $row ) use ( $blocks ): string {
			return trim( tp_child_f59_row_name( $blocks, $row ) );
		},
		$rows
	);

	if ( [ 'BEVANDE', 'SPUMETTA (AL BICCHIERE)', 'ACQUA' ] !== $names ) {
		return $content;
	}

	list( $bevande, $spumetta, $acqua ) = array_map(
		static function ( int $row ) use ( $blocks ): array {
			return $blocks[ $row ];
		},
		$rows
	);

	$part = static function ( int $from, int $to ) use ( $content ): string {
		return substr( $content, $from, $to - $from );
	};

	// The same text, the rows in the new order and the blank lines between them where they were.
	$reordered = $part( $acqua['start'], $acqua['end'] ) . $part( $bevande['end'], $spumetta['start'] )
		. $part( $bevande['start'], $bevande['end'] ) . $part( $spumetta['end'], $acqua['start'] )
		. $part( $spumetta['start'], $spumetta['end'] );

	return substr_replace( $content, $reordered, $bevande['start'], $acqua['end'] - $bevande['start'] );
}

/**
 * The cards of Materie prime: card class => [ the theme's title, the site's, the theme's text, the site's, the
 * theme's photo, the site's photo (a theme asset), its alt, the theme's word in Genoese (it goes) ]. Cards 2 and 3
 * go: the site has four.
 */
function tp_child_t70_materie_cards(): array {
	return [
		1 => [ '<h3>Le acciughe<br>di Luciano</h3>', '<h3>Le ormai celebri polpette</h3>', '<p>Le facciamo noi, con la ricetta del papà di Davide. Acciughe liguri, sott’olio, preparate nel laboratorio.</p>', "<p>Ricetta del papà di Davide, con l'amido al posto del pane: morbide, nel sugo da scarpetta.</p>", 'e0d905c6.webp', 'assets/images/materie-prime/polpette.webp', 'Polpette pronte per il sugo', 'Anciûe' ],
		4 => [ '<h3>Di stagione<br>quando possibile</h3>', '<h3>I ripieni liguri</h3>', '<p>I ripieni vegetali cambiano con quello che c’è in stagione. Non lo chiamiamo km zero, lo facciamo e basta.</p>', '<p>Verdure di stagione svuotate, riempite e passate in forno fino alla crosticina.</p>', 'fig-menu-feature.webp', 'assets/images/materie-prime/ripieni.webp', 'Una zucchina svuotata per i ripieni', 'de stagion' ],
		5 => [ '<h3>Pollo alla ligure</h3>', '<h3>Tutto senza glutine</h3>', "<p>Pollo doc, da produttori locali.<br>Costa un po' di più ma si sente.</p>", '<p>Niente farina né pangrattato, nemmeno nel polpettone. Hai la celiachia? Chiedi al banco.</p>', '79537578.webp', 'assets/images/materie-prime/melanzane.webp', 'Melanzane alla parmigiana', 'Pöllo' ],
		6 => [ '<h3>Fornitori scelti</h3>', '<h3>Spumette a bicchiere</h3>', '<p>Produttori vicini, piccoli, che fanno le cose per bene. Slow Food è un punto di riferimento, non un bollino.</p>', '<p>Come al banco del bar sotto casa. Con una tasca di polpette fa 7,40 €.</p>', '9d821c0e.webp', 'assets/images/materie-prime/spuma.webp', 'Una bottiglia di spuma', 'km 0' ],
	];
}

function tp_child_t70_materie_removed_cards(): array {
	return [
		2 => [ '<h3>Olive taggiasce<br>e pesto</h3>', '<p>Olive della Riviera. Il pesto lo facciamo noi con le basi giuste: basilico genovese, pinoli, parmigiano.</p>' ],
		3 => [ '<h3>Stracciatella<br>di bufala</h3>', '<p>Non mozzarella qualsiasi ma stracciatella, fresca. Con le acciughe di Luciano diventa una cosa seria.</p>' ],
	];
}

function tp_child_t70_materie( string $content ): string {
	// Cards 2 and 3 go, when title and text are still the theme's.
	foreach ( array_reverse( tp_child_t70_materie_removed_cards(), true ) as $number => list( $title, $body ) ) {
		$blocks = tp_child_f59_blocks( $content );

		foreach ( array_reverse( tp_child_f59_find( $blocks, 'tp-materie-card--' . $number ) ) as $card ) {
			if ( $title === tp_child_t70_text_in( $blocks, $card, 'tp-materie-card-title' ) && $body === tp_child_t70_text_in( $blocks, $card, 'tp-materie-card-body' ) ) {
				$content = tp_child_t70_remove( $content, $blocks[ $card ] );
				$blocks  = tp_child_f59_blocks( $content );
			}
		}
	}

	foreach ( tp_child_t70_materie_cards() as $number => list( $title_from, $title, $body_from, $body, $photo_from, $photo, $alt, $word ) ) {
		$blocks = tp_child_f59_blocks( $content );
		$cards  = tp_child_f59_find( $blocks, 'tp-materie-card--' . $number );

		if ( ! $cards ) {
			continue;
		}

		$card = $cards[0];

		// Only a card still the theme's changes: its title tells.
		if ( $title_from !== tp_child_t70_text_in( $blocks, $card, 'tp-materie-card-title' ) ) {
			continue;
		}

		// From the end of the card to its start.
		foreach ( array_reverse( tp_child_f59_find( $blocks, 'tp-materie-chip', $card ) ) as $index ) {
			if ( in_array( tp_child_f59_text( $blocks[ $index ] ), [ $word, "<p>{$word}</p>" ], true ) ) {
				$content = tp_child_t70_remove( $content, $blocks[ $index ] );
			}
		}

		$steps = [
			[ 'tp-materie-card-body', $body_from, $body ],
			[ 'tp-materie-card-title', $title_from, $title ],
		];

		foreach ( $steps as list( $class, $from, $to ) ) {
			$blocks = tp_child_f59_blocks( $content );
			$card   = tp_child_f59_find( $blocks, 'tp-materie-card--' . $number )[0];

			foreach ( tp_child_f59_find( $blocks, $class, $card ) as $index ) {
				$content = tp_child_f59_swap_text( $content, $blocks, $index, $from, $to );
			}
		}

		$blocks = tp_child_f59_blocks( $content );
		$card   = tp_child_f59_find( $blocks, 'tp-materie-card--' . $number )[0];

		foreach ( tp_child_f59_find( $blocks, 'tp-materie-card-image', $card ) as $index ) {
			$content = tp_child_f59_swap_image( $content, $blocks, $index, $photo_from, $photo, $alt );
		}
	}

	return $content;
}

/**
 * The opening hours of the site: [ day, short day, hours ] for each row, the colours alternating from green.
 */
function tp_child_t70_hours(): array {
	return [
		[ 'Lunedì', 'Lun', '12:00-21:00' ],
		[ 'Martedì', 'Mar', '12:00-21:00 c’è la trippa' ],
		[ 'Mercoledì-Giovedì', 'Mer-Gio', '12:00-21:00' ],
		[ 'Venerdì', 'Ven', '12:00-21:00 c’è la trippa' ],
		[ 'Sabato', 'Sab', '12:00-22:00' ],
		[ 'Domenica', 'Dom', 'Chiusi' ],
	];
}

/**
 * The days of the theme (the Figma's): they all become the site's, when they are all still the theme's.
 */
function tp_child_t70_opening_hours( string $content ): string {
	$theme = [
		'<h4>Lunedì-Martedì-Mercoledì</h4><h5>Lun-Mer</h5><p>12:00-15:30</p>',
		'<h4>Giovedì</h4><h5>Gio</h5><p>12:00-15:30 c’è lo stocco</p>',
		'<h4>Venerdì</h4><h5>Ven</h5><p>12:00-15:30 c’è la trippa</p>',
		'<h4>Sabato</h4><h5>Sab</h5><p>12:00-16:00</p>',
		'<h4>Domenica</h4><h5>Dom</h5><p>Chiusi</p>',
	];

	$blocks = tp_child_f59_blocks( $content );

	foreach ( array_reverse( tp_child_f59_find( $blocks, 'tp-hours-card' ) ) as $card ) {
		$items = array_values(
			array_filter(
				tp_child_t70_children( $blocks, $card, 'divi/text' ),
				static function ( int $index ) use ( $blocks ): bool {
					return tp_child_f59_has_class( $blocks[ $index ], 'tp-hours-item' );
				}
			)
		);

		$texts = array_map(
			static function ( int $index ) use ( $blocks ): string {
				return tp_child_f59_text( $blocks[ $index ] );
			},
			$items
		);

		if ( $texts !== $theme ) {
			continue;
		}

		$version = (string) ( tp_child_f59_get( $blocks[ $items[0] ]['attrs'], 'builderVersion' ) ?? '5.2.0' );
		$rows    = [];

		foreach ( tp_child_t70_hours() as $n => list( $day, $short, $hours ) ) {
			$colour = 0 === $n % 2 ? 'green' : 'burgundy';
			$rows[] = tp_child_t70_block(
				'divi/text',
				(object) [
					'module'         => (object) [
						'advanced' => (object) [ 'htmlAttributes' => (object) [ 'desktop' => (object) [ 'value' => (object) [ 'class' => 'tp-hours-item tp-hours-item--' . $colour ] ] ] ],
						'meta'     => (object) [ 'adminLabel' => (object) [ 'desktop' => (object) [ 'value' => 'Giorno: ' . $day ] ] ],
					],
					'content'        => tp_child_t70_value( "<h4>{$day}</h4><h5>{$short}</h5><p>{$hours}</p>" ),
					'builderVersion' => $version,
				]
			);
		}

		$first   = $blocks[ $items[0] ];
		$last    = $blocks[ end( $items ) ];
		$content = substr_replace( $content, implode( "\n\n", $rows ), $first['start'], $last['end'] - $first['start'] );
	}

	return $content;
}

/**
 * The phrases of the strip under the hero: the theme's five become the site's seven.
 */
function tp_child_t70_marquee( string $content ): string {
	$theme = [ 'Stocco ogni giovedì', 'Trippa ogni venerdì', 'Polpette al sugo', 'Acciughe di Luciano', 'Tasche piene di cucinato' ];
	$site  = [ 'Fatto nel laboratorio, ogni mattina', 'Stoccafisso alla ligure', 'Trippe accomodate', 'Polpette al sugo', 'Le acciughe di Luciano', 'Anche senza glutine', 'Gastronomia o Street Food' ];

	$blocks = tp_child_f59_blocks( $content );

	foreach ( array_reverse( tp_child_f59_find( $blocks, 'tp-marquee-module' ) ) as $marquee ) {
		$items   = tp_child_t70_children( $blocks, $marquee, 'tp/marquee-item' );
		$phrases = array_map(
			static function ( int $index ) use ( $blocks ): string {
				return tp_child_f59_text( $blocks[ $index ] );
			},
			$items
		);

		if ( $phrases !== $theme ) {
			continue;
		}

		$version = (string) ( tp_child_f59_get( $blocks[ $items[0] ]['attrs'], 'builderVersion' ) ?? '5.2.0' );
		$new     = [];

		foreach ( $site as $phrase ) {
			$new[] = tp_child_t70_block(
				'tp/marquee-item',
				(object) [
					'phrase'         => tp_child_t70_value( $phrase ),
					'builderVersion' => $version,
					'module'         => (object) [ 'meta' => (object) [ 'adminLabel' => (object) [ 'desktop' => (object) [ 'value' => 'Scritta: ' . $phrase ] ] ] ],
				]
			);
		}

		$first   = $blocks[ $items[0] ];
		$last    = $blocks[ end( $items ) ];
		$content = substr_replace( $content, implode( "\n\n", $new ), $first['start'], $last['end'] - $first['start'] );
	}

	return $content;
}

/**
 * The title of the hero, line by line, when its four lines are still the theme's.
 */
function tp_child_t70_hero_title( string $content ): string {
	$theme = [ 'lineOne' => 'La gastronomia', 'lineTwo' => 'di quartiere,', 'lineThree' => 'nel centro storico', 'lineFour' => 'di Genova' ];
	$site  = [ 'lineOne' => 'Pensavi', 'lineTwo' => 'fosse amore', 'lineThree' => 'e invece era', 'lineFour' => 'Polpetta' ];

	$blocks = tp_child_f59_blocks( $content );

	foreach ( array_reverse( tp_child_f59_find( $blocks, 'tp-hero-title' ) ) as $index ) {
		$block = $blocks[ $index ];

		foreach ( $theme as $key => $text ) {
			if ( $text !== tp_child_f59_get( $block['attrs'], $key . '.innerContent.desktop.value' ) ) {
				continue 2;
			}
		}

		foreach ( $site as $key => $text ) {
			tp_child_f59_set( $block['attrs'], $key . '.innerContent.desktop.value', $text );
		}

		$content = tp_child_f59_save( $content, $block );
	}

	return $content;
}

/**
 * The home content with the texts of taschepiene.it where it still has the theme's.
 */
function tp_child_t70_home_transform( string $content ): string {
	if ( false === strpos( $content, 'tp-menu-section' ) && false === strpos( $content, 'tp-hero-section' ) ) {
		return $content;
	}

	$content = tp_child_t70_hero_title( $content );

	foreach ( tp_child_t70_simple_texts() as list( $class, $from, $to ) ) {
		$blocks = tp_child_f59_blocks( $content );

		foreach ( array_reverse( tp_child_f59_find( $blocks, $class ) ) as $index ) {
			$content = tp_child_f59_swap_text( $content, $blocks, $index, $from, $to );
		}
	}

	$content = tp_child_t70_marquee( $content );

	// Tasca o piatto.
	foreach ( tp_child_t70_choice_texts() as $card_class => $texts ) {
		foreach ( $texts as list( $class, $from, $to ) ) {
			$blocks = tp_child_f59_blocks( $content );

			foreach ( tp_child_f59_find( $blocks, $card_class ) as $card ) {
				foreach ( array_reverse( tp_child_f59_find( $blocks, $class, $card ) ) as $index ) {
					$content = tp_child_f59_swap_text( $content, $blocks, $index, $from, $to );
				}
			}
		}
	}

	// The alt of the photo of the second card follows its name.
	$blocks = tp_child_f59_blocks( $content );

	foreach ( array_reverse( tp_child_f59_find( $blocks, 'tp-scelta-scheda--piatto' ) ) as $card ) {
		foreach ( array_reverse( tp_child_f59_find( $blocks, 'tp-scelta-foto', $card ) ) as $index ) {
			$block = $blocks[ $index ];

			if ( 'Il piatto' === tp_child_f59_get( $block['attrs'], 'image.innerContent.desktop.value.alt' ) ) {
				tp_child_f59_set( $block['attrs'], 'image.innerContent.desktop.value.alt', 'La vaschetta' );
				$content = tp_child_f59_save( $content, $block );
			}
		}
	}

	$content = tp_child_t70_menu( $content );
	$content = tp_child_t70_materie( $content );
	$content = tp_child_t70_opening_hours( $content );

	// The names in the builder follow the new texts ("Piatto: Melanzana alla parmigiana", "Card: I ripieni liguri"...).
	return function_exists( 'tp_child_wireframe_labels_transform' ) ? tp_child_wireframe_labels_transform( $content ) : $content;
}

/**
 * The links of the menu at the top in the Figma and on the site: [ text, link ].
 */
function tp_child_t70_header_links(): array {
	return [
		[ [ 'Chi siamo', '#chi-siamo' ], [ 'Menù', '#menu' ], [ 'Ingredienti', '#materie' ], [ 'Come mangi', '#come-mangi' ], [ 'Dove trovarci', '#location' ] ],
		[ [ 'La nostra idea', '#chi-siamo' ], [ 'Il menù', '#menu' ], [ 'Senza glutine', '#materie' ], [ 'Domande', '#faq' ], [ 'Dove siamo', '#location' ] ],
	];
}

/**
 * A header layout with the site's links, where its Voci are still the theme's five.
 */
function tp_child_t70_header_transform( string $content ): string {
	if ( false === strpos( $content, 'wp:tp/theme-header' ) ) {
		return $content;
	}

	list( $from_links, $to_links ) = tp_child_t70_header_links();

	$blocks = tp_child_f59_blocks( $content );

	foreach ( array_reverse( array_keys( $blocks ) ) as $index ) {
		if ( 'tp/theme-header' !== $blocks[ $index ]['name'] ) {
			continue;
		}

		$items = tp_child_t70_children( $blocks, $index, 'tp/header-nav-item' );
		$links = array_map(
			static function ( int $i ) use ( $blocks ): array {
				$attrs = json_decode( wp_json_encode( $blocks[ $i ]['attrs'] ), true );

				return [ tp_child_nav_item_value( $attrs, 'text', 'Nuova voce' ), tp_child_nav_item_value( $attrs, 'link', '#home' ) ];
			},
			$items
		);

		if ( $links !== $from_links ) {
			continue;
		}

		foreach ( array_reverse( $items, true ) as $n => $i ) {
			$block = $blocks[ $i ];
			tp_child_f59_set( $block['attrs'], 'text.innerContent.desktop.value', $to_links[ $n ][0] );
			tp_child_f59_set( $block['attrs'], 'link.innerContent.desktop.value', $to_links[ $n ][1] );

			if ( 'Voce: ' . tp_child_wireframe_plain( $from_links[ $n ][0] ) === tp_child_f59_get( $block['attrs'], 'module.meta.adminLabel.desktop.value' ) ) {
				tp_child_f59_set( $block['attrs'], 'module.meta.adminLabel.desktop.value', 'Voce: ' . tp_child_wireframe_plain( $to_links[ $n ][0] ) );
			}

			$content = tp_child_f59_save( $content, $block );
		}
	}

	return $content;
}

/**
 * The WordPress menu of the header (used by a Header sito without Voci): the site's links where it still has the
 * theme's five.
 */
function tp_child_t70_update_wp_header_menu(): void {
	$locations = get_nav_menu_locations();
	$menu_id   = (int) ( $locations['tp_main_nav'] ?? 0 );
	$items     = $menu_id ? wp_get_nav_menu_items( $menu_id ) : [];

	if ( ! is_array( $items ) || 5 !== count( $items ) ) {
		return;
	}

	list( $from_links, $to_links ) = tp_child_t70_header_links();

	foreach ( array_values( $items ) as $n => $item ) {
		if ( $from_links[ $n ][0] !== $item->title || $from_links[ $n ][1] !== $item->url ) {
			return;
		}
	}

	foreach ( array_values( $items ) as $n => $item ) {
		wp_update_nav_menu_item(
			$menu_id,
			(int) $item->ID,
			[
				'menu-item-title'     => $to_links[ $n ][0],
				'menu-item-url'       => $to_links[ $n ][1],
				'menu-item-type'      => 'custom',
				'menu-item-status'    => 'publish',
				'menu-item-position'  => (int) $item->menu_order,
				'menu-item-parent-id' => (int) $item->menu_item_parent,
			]
		);
	}
}

/**
 * The lines under the footer: key => [ the theme's text, the site's ].
 */
function tp_child_t70_footer_lines(): array {
	return [
		'copyrightLine1' => [ 'Copyright 2026, Tasche piene tutti i diritti riservati', 'Tasche Piene è un progetto di Barrakuda S.r.l.' ],
		'copyrightLine2' => [ 'P.IVA 03942309300', 'P.IVA e C.F.: 02789540990 | Via Carducci 5/10, 16121 - Genova' ],
		'copyrightLine3' => [ 'Privacy', 'Privacy Policy' ],
		'copyrightLine4' => [ 'Cookie', 'Cookie Policy' ],
		'copyrightLine5' => [ 'Designed By Mochi', 'creato con ♥ da Mochi' ],
	];
}

/**
 * A footer layout with the site's company data, where the lines are still the theme's (a line Divi never saved
 * shows the theme's default, which is the site's from 0.7.0).
 */
function tp_child_t70_footer_transform( string $content ): string {
	if ( false === strpos( $content, 'wp:tp/theme-footer' ) ) {
		return $content;
	}

	$blocks = tp_child_f59_blocks( $content );

	foreach ( array_reverse( array_keys( $blocks ) ) as $index ) {
		$block = $blocks[ $index ];

		if ( 'tp/theme-footer' !== $block['name'] ) {
			continue;
		}

		$changed = false;

		foreach ( tp_child_t70_footer_lines() as $key => list( $from, $to ) ) {
			if ( $from === tp_child_f59_get( $block['attrs'], $key . '.innerContent.desktop.value' ) ) {
				tp_child_f59_set( $block['attrs'], $key . '.innerContent.desktop.value', $to );
				$changed = true;
			}
		}

		// The credit of the site links to the studio, as there.
		$credit = tp_child_f59_get( $block['attrs'], 'copyrightLine5.innerContent.desktop.value' );
		$link   = tp_child_f59_get( $block['attrs'], 'copyrightLink5.innerContent.desktop.value' );

		if ( ( null === $credit || 'creato con ♥ da Mochi' === $credit ) && ( null === $link || '' === $link ) ) {
			tp_child_f59_set( $block['attrs'], 'copyrightLink5.innerContent.desktop.value', 'https://www.mochidesign.it' );
			$changed = true;
		}

		if ( $changed ) {
			$content = tp_child_f59_save( $content, $block );
		}
	}

	return $content;
}

/**
 * The Privacy Policy the theme wrote: the company of the site as data controller, where the page still has the
 * theme's lines.
 */
function tp_child_t70_privacy_transform( string $content ): string {
	if ( false === strpos( $content, '03942309300' ) ) {
		return $content;
	}

	$details = tp_child_legal_business_details();
	$blocks  = tp_child_f59_blocks( $content );

	foreach ( array_reverse( array_keys( $blocks ) ) as $index ) {
		$block = $blocks[ $index ];

		if ( 'divi/text' !== $block['name'] ) {
			continue;
		}

		$html = tp_child_f59_text( $block );
		$new  = preg_replace(
			'#<strong>Tasche Piene</strong><br>(\s*)Via di Scurreria 42/R, 16124 Genova \(GE\)<br>(\s*)P\. IVA 03942309300#u',
			'<strong>' . $details['company'] . '</strong> (' . $details['brand'] . ')<br>$1' . $details['address'] . '<br>$2P. IVA e C.F. ' . $details['vat'],
			$html,
			1
		);

		if ( is_string( $new ) && $new !== $html ) {
			tp_child_f59_set( $block['attrs'], 'content.innerContent.desktop.value', $new );
			$content = tp_child_f59_save( $content, $block );
		}
	}

	return $content;
}

/**
 * $post's content through $transform, saved verbatim when it changes.
 */
function tp_child_t70_update_post( int $post_id, callable $transform ): bool {
	$content = (string) get_post_field( 'post_content', $post_id );
	$updated = '' !== $content ? $transform( $content ) : $content;

	if ( $updated === $content ) {
		return false;
	}

	// The block JSON must be saved verbatim, whoever triggers the request.
	kses_remove_filters();
	wp_update_post(
		[
			'ID'           => $post_id,
			'post_content' => wp_slash( $updated ),
		]
	);
	kses_init();

	if ( function_exists( 'tp_child_seed_clear_divi_cache_meta' ) ) {
		tp_child_seed_clear_divi_cache_meta( $post_id );
	}

	return true;
}

/**
 * Once: the home (the front page and the page the theme made as home), the header and footer layouts of the Theme
 * Builder, the WordPress menu of the header, the Privacy Policy.
 */
function tp_child_migrate_live_texts_070(): void {
	$flag = 'tp_child_live_texts_070_v1';

	if ( get_option( $flag ) ) {
		return;
	}

	$changed = false;
	$homes   = [];

	if ( 'page' === get_option( 'show_on_front' ) ) {
		$homes[] = (int) get_option( 'page_on_front' );
	}

	$made = get_page_by_path( 'tasche-piene-hero', OBJECT, 'page' );

	if ( $made instanceof WP_Post ) {
		$homes[] = (int) $made->ID;
	}

	foreach ( array_unique( array_filter( $homes ) ) as $id ) {
		$post = get_post( $id );

		if ( $post instanceof WP_Post && ! in_array( $post->post_status, [ 'trash', 'auto-draft' ], true ) ) {
			$changed = tp_child_t70_update_post( $id, 'tp_child_t70_home_transform' ) || $changed;
		}
	}

	foreach ( [ 'et_header_layout' => 'tp_child_t70_header_transform', 'et_footer_layout' => 'tp_child_t70_footer_transform' ] as $type => $transform ) {
		$layouts = get_posts(
			[
				'post_type'        => $type,
				'post_status'      => 'any',
				'numberposts'      => -1,
				'suppress_filters' => true,
				'fields'           => 'ids',
			]
		);

		foreach ( $layouts as $layout_id ) {
			$changed = tp_child_t70_update_post( (int) $layout_id, $transform ) || $changed;
		}
	}

	$privacy = get_page_by_path( tp_child_legal_page_slugs()['privacy'], OBJECT, 'page' );

	if ( $privacy instanceof WP_Post ) {
		tp_child_t70_update_post( (int) $privacy->ID, 'tp_child_t70_privacy_transform' );
	}

	tp_child_t70_update_wp_header_menu();

	if ( $changed && function_exists( 'tp_child_clear_divi_static_css' ) ) {
		tp_child_clear_divi_static_css();
	}

	update_option( $flag, TP_CHILD_VERSION, true );
}
add_action( 'init', 'tp_child_migrate_live_texts_070', 53 );
