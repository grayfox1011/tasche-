"""The words and pictures of the site as first written (taken from the home of version 0.8.3).

After the import they live in WordPress and are changed from Divi.
"""

HERO_TITLE_HTML = 'Pensavi<br />fosse amore<br />e invece era<br />Polpetta'

HERO_BUTTON = 'Guarda il menù'

HERO_SUBLINE = 'Street food · Gastronomia · Genova Centro Storico'

STRIP = ('Fatto nel laboratorio, ogni mattina · Stoccafisso alla ligure · Trippe accomodate · Polpette al sugo · Le acciughe di Luciano · Anche senza '
 'glutine · Gastronomia o Street Food')

INTRO = {'image': {'src': 'images/intro/fig-intro-alberto-impasto.webp',
           'alt': 'Alberto prepara il ripieno nel laboratorio di Tasche Piene',
           'title': 'Alberto al lavoro'},
 'title': 'Tasche piene di qualcosa di buono',
 'html': '<p>Un’idea di Davide e Alberto, imprenditori genovesi che di stare con le mani in mano non sono capaci.</p>\n'
         '<p>Genova ha un centro storico che molte città si sognano. Ogni anno diventa più bella e più curata… ma un po’ meno sé stessa. I locali si '
         'moltiplicano, le insegne si assomigliano tutte, i menù cominciano sempre con «street food» e finiscono con «concept». Di quella musica lì, '
         'ne abbiamo le tasche piene.</p>\n'
         '<p>Ogni mattina nel nostro laboratorio cuociono le polpette nel sugo, lo stocco, la trippa, i ripieni liguri. Li mettiamo in tasca ancora '
         'caldi: il sugo inzuppa il pane e ogni morso è una scarpetta.</p>',
 'button': 'Le materie prime',
 'button_url': '#materie'}

ALBERTO = {'title': 'Cucinato ogni mattina nel nostro laboratorio, tutto senza glutine',
 'html': '<p>Da Tasche Piene è come una volta: profumo di sugo, cucina fresca ogni mattina e le acciughe di Luciano, il papà di Davide. Tutte le '
         'nostre ricette sono senza glutine: niente farina e niente pangrattato, nemmeno nel polpettone. Usiamo teglie chiuse e separate, ma la '
         'cucina è una sola. Il consiglio? Passate al banco, ne parliamo a voce!</p>',
 'photo_main': {'src': 'images/intro/fig-alberto-pentola.webp', 'alt': 'Il ripieno preparato nella pentola', 'title': ''},
 'photo_sub': {'src': 'images/dishes/flow-menu-pomodoro-mozzarella-pesto.webp', 'alt': 'Alberto con una teglia di pomodori', 'title': ''}}

SCELTA = {'title_html': 'Tasca ripiena o vaschetta.<br />Mangi qui o dove vuoi tu!',
 'cards': [{'image': {'src': 'images/dishes/flow-choice-tasca.webp', 'alt': 'La tasca ripiena', 'title': ''},
            'name': 'La tasca ripiena',
            'chips': ['Tra pinsa e panino', 'Il sugo inzuppa la mollica', 'È come fare la scarpetta'],
            'html': '<p>Anche senza glutine, dolci comprese: le scaldiamo in una vaschetta chiusa. La cucina è una sola, se hai la celiachia dillo '
                    'al banco.</p>'},
           {'image': {'src': 'images/dishes/flow-choice-piatto.webp', 'alt': 'La vaschetta', 'title': ''},
            'name': 'La vaschetta',
            'chips': ['Media da 200 g o grande da 400 g', 'Con un contorno incluso', 'Il pane croccante, se vuoi, a parte'],
            'html': '<p>Stesse ricette, nel piatto: tanto sugo, contorno e pane croccante per la scarpetta. Arriva anche a casa con Deliveroo e Just '
                    'Eat.</p>'}]}

MENU = {'title': 'Il nostro menù',
 'legend': [{'name': 'TASCA', 'lines': ['Di pane ripiena']}, {'name': 'VASCHETTA', 'lines': ['Media - 200g', 'Grande - 400g']}],
 'feature': [{'src': 'images/dishes/fig-menu-feature.webp', 'alt': 'Piatto di ripieni alla genovese', 'title': ''},
             {'src': 'images/dishes/flow-menu-tiramisu.webp', 'alt': 'Tiramisù in tasca', 'title': ''}],
 'dishes': [{'name': 'MELANZANA ALLA PARMIGIANA',
             'prices': ['5,50€', '6,00€', '9,00€'],
             'meta': 'Strati di melanzana, sugo e formaggio filante',
             'image': 'images/dishes/flow-menu-melanzane.webp'},
            {'name': 'RIPIENI LIGURI DI STAGIONE',
             'prices': ['5,50€', '6,00€', '9,00€'],
             'meta': 'Zucchina, cipolla, peperone. Cambia con la stagione',
             'image': 'images/dishes/flow-menu-ripieni.webp'},
            {'name': 'POLPETTE AL SUGO',
             'prices': ['6,50€', '7,50€', '10,50€'],
             'meta': 'Tre polpette morbide, circa 100 g, affogate nel sugo',
             'image': 'images/dishes/flow-menu-polpette.webp'},
            {'name': 'POLLO ALLA LIGURE, OLIVE TAGGIASCHE, PINOLI',
             'prices': ['6,50€', '7,50€', '10,50€'],
             'meta': 'Pollo in umido, sughetto di olive e pinoli',
             'image': 'images/dishes/flow-menu-pollo-ligure.webp'},
            {'name': 'TRIPPA - MARTEDÌ E VENERDÌ',
             'prices': ['7,50€', '7,50€', '10,50€'],
             'meta': 'Quella genovese, con le trippe della Tripperia La Casana. Max 10-12 porzioni',
             'image': 'images/dishes/flow-menu-trippa.webp'},
            {'name': 'STOCCAFISSO - TUTTI I GIORNI',
             'prices': ['7,50€', '7,50€', '10,50€'],
             'meta': 'Ricetta tradizionale ligure. Max 10-12 porzioni',
             'image': 'images/dishes/flow-menu-stoccafisso.webp'},
            {'name': 'CAPRESE, PESTO GENOVESE, BUFALA',
             'prices': ['5,50€', '6,00€', '9,00€'],
             'meta': 'Pomodoro fresco, stracciatella e pesto',
             'image': 'images/dishes/flow-menu-pomodoro-mozzarella-pesto.webp'},
            {'name': 'LE ACCIUGHE DI LUCIANO + STRACCIATELLA',
             'prices': ['6,50€', '7,50€', '10,50€'],
             'meta': 'Olive, capperi e ricetta di Luciano',
             'image': 'images/dishes/flow-menu-acciughe-stracciatella.webp'},
            {'name': 'SALSICCIA, ALLORO E PEPERONI CONFIT',
             'prices': ['7,50€'],
             'meta': 'IN ROTAZIONE · PULLED PORK, CAVOLO VIOLA E MAYO SPICY',
             'image': 'images/dishes/flow-menu-proposta-mese.webp'}],
 'groups': [{'title': 'I nostri contorni',
             'subtitle': 'Cosa ci vuoi vicino?',
             'rows': [{'name': 'PATATE AL FORNO', 'prices': ['1,50€'], 'image': 'images/dishes/flow-menu-patate.webp'},
                      {'name': 'VERDURE AL FORNO', 'prices': ['1,50€'], 'image': 'images/dishes/flow-menu-verdure.webp'}]},
            {'title': 'I nostri dolci',
             'subtitle': 'Finiscila bene',
             'rows': [{'name': 'TIRAMISÙ IN TASCA', 'prices': ['4,50€'], 'image': 'images/dishes/flow-menu-tiramisu.webp'},
                      {'name': 'Strudel di mele', 'prices': ['4,50€']}]},
            {'title': 'Da bere',
             'subtitle': 'Hai sete?',
             'rows': [{'name': 'ACQUA', 'prices': ['1,00€']},
                      {'name': 'BEVANDE', 'prices': ['2,50€']},
                      {'name': 'SPUMETTA (AL BICCHIERE)', 'prices': ['0,90€']}]}],
 'note': '<p>Allergeni</p>\n'
         '<p>1 glutine • 2 crostacei • 3 uova • 4 pesce • 5 arachidi • 6 soia • 7 latte • 8 frutta a guscio • 9 sedano • 10 senape • 11 sesamo • 12 '
         'anidride solforosa e solfiti • 13 lupini • 14 molluschi</p>\n'
         '<p>Tutte le ricette sono senza glutine. Il pane senza glutine è confezionato singolarmente: chiedilo al banco.</p>',
 'delivery_text': 'Ti portiamo la vaschetta a casa o in ufficio:',
 'delivery': [{'src': 'images/delivery/just-eat.png',
               'alt': 'Ordina Tasche Piene su Just Eat, consegna a domicilio a Genova centro',
               'title': 'Tasche Piene su Just Eat',
               'url': 'https://www.justeat.it/restaurants-tasche-piene-genova/menu'},
              {'src': 'images/delivery/deliveroo.png',
               'alt': 'Ordina Tasche Piene su Deliveroo, consegna a domicilio a Genova centro',
               'title': 'Tasche Piene su Deliveroo',
               'url': 'https://deliveroo.it/it/menu/genova/genova-centro/tasche-piene-genova-centro'}]}

MATERIE = {'title': 'Non sono principi astratti, sono scelte.',
 'html': '<p>Ogni cosa che vedi nel banco è il risultato di una scelta precisa. Non compriamo pronto, non riscaldiamo per rivendere: se è nel banco, '
         "qualcuno l'ha cucinato quella mattina. Polpette, stocco, trippa, ripieni liguri: ricette che si cucinano da quando Genova è Genova. Noi "
         'non le abbiamo reinventate, le abbiamo solo messe in tasca.</p>\n'
         "<p>Siamo in piazza San Lorenzo, centro storico vero. Un posto senza età, senza fretta, senza liste d'attesa per chi lavora qui vicino, per "
         'chi abita nel quartiere, per chi capita nei caruggi per la prima volta.<br>5,50 € per un pasto vero: né un euro di meno, né uno di più del '
         'necessario.</p>',
 'marquee': 'Le cose che finiscono in tasca',
 'cards': [{'image': {'src': 'images/materie-prime/polpette.webp', 'alt': 'Polpette pronte per il sugo', 'title': ''},
            'title': 'Le ormai celebri polpette',
            'text': "Ricetta del papà di Davide, con l'amido al posto del pane: morbide, nel sugo da scarpetta."},
           {'image': {'src': 'images/materie-prime/ripieni.webp', 'alt': 'Una zucchina svuotata per i ripieni', 'title': ''},
            'title': 'I ripieni liguri',
            'text': 'Verdure di stagione svuotate, riempite e passate in forno fino alla crosticina.'},
           {'image': {'src': 'images/materie-prime/melanzane.webp', 'alt': 'Melanzane alla parmigiana', 'title': ''},
            'title': 'Tutto senza glutine',
            'text': 'Niente farina né pangrattato, nemmeno nel polpettone. Hai la celiachia? Chiedi al banco.'},
           {'image': {'src': 'images/materie-prime/spuma.webp', 'alt': 'Una bottiglia di spuma', 'title': ''},
            'title': 'Spumette a bicchiere',
            'text': 'Come al banco del bar sotto casa. Con una tasca di polpette fa 7,40 €.'}]}

LOCATION = {'image': {'src': 'images/intro/fig-intro-caruggi.webp',
           'alt': 'Tasche Piene davanti ai leoni di San Lorenzo, a Genova',
           'title': 'Tasche Piene nei caruggi di Genova'},
 'title': 'Siamo nei caruggi. Non troppo nascosti.',
 'text': 'Siamo in Piazza San Lorenzo a due passi dalla Cattedrale.',
 'address': 'Via di Scurreria 42/R',
 'button': 'Mappe',
 'button_url': 'https://www.google.com/maps/search/?api=1&query=Via%20di%20Scurreria%2042%2FR%2C%20Genova'}

HOURS = {'title': 'Quando siamo aperti?',
 'rows': [{'day': 'Lunedì', 'short': 'Lun', 'hours': '12:00-21:00'},
          {'day': 'Martedì', 'short': 'Mar', 'hours': '12:00-21:00 c’è la trippa'},
          {'day': 'Mercoledì-Giovedì', 'short': 'Mer-Gio', 'hours': '12:00-21:00'},
          {'day': 'Venerdì', 'short': 'Ven', 'hours': '12:00-21:00 c’è la trippa'},
          {'day': 'Sabato', 'short': 'Sab', 'hours': '12:00-22:00'},
          {'day': 'Domenica', 'short': 'Dom', 'hours': 'Chiusi'}]}

FAQ = {'title': 'Informazioni utili',
 'items': [{'question': "Che cos'è una tasca ripiena?",
            'html': '<p>È un pane croccante, a metà tra una pinsa e un panino, riempito con una delle ricette del banco e scaldato al momento. Si '
                    'mangia con le mani, anche camminando per i caruggi. Il ripieno è lo stesso che trovi nella vaschetta: polpette al sugo, '
                    'melanzane alla parmigiana, ripieni liguri, pollo alla ligure. Da 5,50 €.</p>'},
           {'question': "Tasca o vaschetta: che differenza c'è?",
            'html': '<p>La ricetta è la stessa, cambia come la mangi. La tasca è il pane ripieno. La vaschetta è il piatto: media da 200 g o grande '
                    'da 400 g, con un contorno incluso e, se vuoi, il pane croccante a parte. La vaschetta è anche quella che arriva a casa con '
                    'Deliveroo e Just Eat.</p>'},
           {'question': 'Siete senza glutine?',
            'html': '<p>Tutte le ricette del banco sono preparate senza glutine. Non usiamo farina né pangrattato, nemmeno nel polpettone: le '
                    "polpette, per esempio, le leghiamo con l'amido. Il pane della tasca senza glutine è confezionato singolarmente e lo scaldiamo "
                    'in forno sigillato nella stagnola. La cucina però è una sola e non abbiamo una certificazione: se hai la celiachia dillo al '
                    'banco, così ne parliamo.</p>'},
           {'question': 'Cosa trovo nel banco e cosa cambia?',
            'html': '<p>Ogni mattina cuciniamo nel nostro laboratorio quello che vedi nel banco: polpette al sugo, melanzane alla parmigiana, pollo '
                    'alla ligure con olive taggiasche e pinoli, le acciughe di Luciano con la stracciatella, la caprese con il pesto. I ripieni di '
                    'verdura cambiano con la stagione. Quando una cosa finisce, è finita: non riscaldiamo per rivendere.</p>'},
           {'question': "Quando c'è la trippa e quando lo stoccafisso?",
            'html': '<p>Lo stoccafisso alla ligure c’è tutti i giorni. La trippa alla genovese il martedì e il venerdì: le trippe arrivano dalla '
                    'Tripperia La Casana, bottega storica del centro. Ne prepariamo 10-12 porzioni, quindi conviene passare presto. Tutti e due si '
                    'possono avere in tasca o in vaschetta.</p>'},
           {'question': 'Posso mangiare da voi o solo da asporto?',
            'html': '<p>Tutte e due le cose. Puoi mangiare da noi, portare via la tasca e mangiarla dove vuoi tu, oppure farti consegnare la '
                    'vaschetta a casa o in ufficio.</p>'},
           {'question': 'Fate consegne a domicilio?',
            'html': '<p>Sì, con <a href="https://deliveroo.it/it/menu/genova/genova-centro/tasche-piene-genova-centro" target="_blank" '
                    'rel="noopener">Deliveroo</a> e <a href="https://www.justeat.it/restaurants-tasche-piene-genova/menu" target="_blank" '
                    'rel="noopener">Just Eat</a>, negli orari di apertura. Dal banco invece si ordina e si porta via senza prenotare.</p>'},
           {'question': 'Dove siete?',
            'html': '<p>In via di Scurreria 42/R, su piazza San Lorenzo, a due passi dalla Cattedrale. Siamo nei caruggi, ma non troppo nascosti. <a '
                    'href="https://www.google.com/maps/search/?api=1&query=Tasche+Piene+Via+di+Scurreria+42+Genova" target="_blank" '
                    'rel="noopener">Apri la mappa</a> oppure chiamaci al <a href="tel:+390104075541">010 407 5541</a>.</p>'},
           {'question': 'Quali sono gli orari?',
            'html': '<p>lunedì 12–21<br>martedì 12–21<br>mercoledì 12–21<br>giovedì 12–21<br>venerdì 12–21<br>sabato 12–22</p>'},
           {'question': 'Quanto costa un pranzo?',
            'html': '<p>Una tasca costa da 5,50 a 7,50 €. Una vaschetta media da 6,00 €, grande da 9,00 €, con il contorno incluso. Patate o verdure '
                    'al forno a parte 1,50 €, un dolce 4,50 €, la spumetta al bicchiere 0,90 €.</p>'},
           {'question': "Ho un'allergia: come faccio?",
            'html': "<p>Chiedi al banco: sappiamo cosa c'è in ogni ricetta perché la facciamo noi. La lista degli allergeni è esposta in negozio e "
                    'sul menù qui sopra.</p>'}]}
