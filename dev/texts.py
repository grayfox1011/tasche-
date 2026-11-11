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

# The legal pages. {{url:...}} are the addresses of the pages, {{date}} the day the page is created.
LEGAL = {'privacy': {'slug': 'privacy-policy',
             'title': 'Privacy Policy',
             'html': '<p><strong>Ultimo aggiornamento:</strong> {{date}}</p>\n'
                     '<p>La presente informativa descrive come Tasche Piene tratta i dati personali in relazione al sito web {{url:home}}.</p>\n'
                     '<h2>1. Titolare del trattamento</h2>\n'
                     '<p><strong>Barrakuda S.r.l.</strong> (Tasche Piene)<br> Via Carducci 5/10, 16121 Genova<br> P. IVA e C.F. 02789540990 </p>\n'
                     '<h2>2. Tipologia di sito</h2>\n'
                     '<p>Il sito ha finalità informativa e promozionale: presenta l’attività gastronomica, il menu e le informazioni utili per '
                     'raggiungerci in negozio.</p>\n'
                     '<p><strong>Non sono presenti moduli di contatto, form di iscrizione, newsletter o raccolta diretta di dati tramite e-mail sul '
                     'sito.</strong></p>\n'
                     '<h2>3. Dati trattati</h2>\n'
                     '<p>Potrebbero essere trattati, in modo limitato e per il solo funzionamento tecnico del sito:</p>\n'
                     '<ul><li>dati di navigazione e log tecnici (es. indirizzo IP, data e ora della richiesta, user agent del '
                     'browser);</li><li>cookie e strumenti tecnici descritti nella <a href="{{url:cookie}}">Cookie Policy</a>.</li></ul>\n'
                     '<p>Non effettuiamo profilazione commerciale né invio di comunicazioni promozionali tramite il sito.</p>\n'
                     '<h2>4. Finalità e base giuridica</h2>\n'
                     '<ul><li><strong>Funzionamento e sicurezza del sito</strong> — legittimo interesse del titolare e necessità tecnica (art. 6, '
                     'par. 1, lett. f GDPR).</li><li><strong>Cookie tecnici</strong> — vedi Cookie Policy; per i cookie non strettamente necessari, '
                     'ove presenti, il consenso viene richiesto separatamente.</li></ul>\n'
                     '<h2>5. Conservazione</h2>\n'
                     '<p>I dati tecnici di log sono conservati per il tempo strettamente necessario alla sicurezza e alla manutenzione del sito, '
                     'secondo i termini del provider di hosting.</p>\n'
                     '<h2>6. Comunicazione e trasferimenti</h2>\n'
                     '<p>I dati possono essere trattati da fornitori tecnici (hosting, manutenzione sito) nominati responsabili del trattamento o '
                     'autonomi titolari, solo per erogare il servizio.</p>\n'
                     '<h2>7. Diritti dell’interessato</h2>\n'
                     '<p>In qualità di interessato puoi esercitare i diritti previsti dagli artt. 15-22 GDPR (accesso, rettifica, cancellazione, '
                     'limitazione, opposizione, portabilità ove applicabile) rivolgendoti al titolare presso la sede indicata sopra.</p>\n'
                     '<p>Hai inoltre diritto di proporre reclamo all’Autorità Garante per la protezione dei dati personali (<a '
                     'href="https://www.garanteprivacy.it" rel="noopener noreferrer" target="_blank">garanteprivacy.it</a>).</p>\n'
                     '<h2>8. Modifiche</h2>\n'
                     '<p>Il titolare può aggiornare la presente informativa; la versione aggiornata sarà pubblicata su questa pagina.</p>',
             'back': '<p><a href="{{url:home}}">Torna alla home</a> · <a href="{{url:cookie}}">Cookie Policy</a></p>'},
 'cookie': {'slug': 'cookie-policy',
            'title': 'Cookie Policy',
            'html': '<p><strong>Ultimo aggiornamento:</strong> {{date}}</p>\n'
                    '<p>Questa Cookie Policy spiega come Tasche Piene utilizza cookie e tecnologie simili sul sito {{url:home}}.</p>\n'
                    '<h2>1. Cosa sono i cookie</h2>\n'
                    '<p>I cookie sono piccoli file di testo che i siti possono salvare sul dispositivo dell’utente per memorizzare preferenze o '
                    'informazioni tecniche.</p>\n'
                    '<h2>2. Cookie utilizzati da questo sito</h2>\n'
                    '<p>Il sito utilizza principalmente <strong>cookie tecnici</strong>, necessari al corretto funzionamento della piattaforma '
                    'WordPress e alla visualizzazione delle pagine.</p>\n'
                    '<p>Tra questi possono rientrare, a seconda della configurazione del server:</p>\n'
                    '<ul><li>cookie di sessione o tecnici di WordPress;</li><li>cookie legati alla sicurezza e alla cache del '
                    'sito;</li><li>preferenze salvate dal builder o dal tema per la corretta resa grafica.</li></ul>\n'
                    '<p><strong>Non utilizziamo cookie di profilazione o pubblicitari</strong> e non raccogliamo dati tramite form o newsletter sul '
                    'sito.</p>\n'
                    '<p>Se in futuro venissero attivati strumenti di analisi o marketing (es. Google Analytics, pixel social), questa informativa '
                    'sarà aggiornata e, ove richiesto, verrà richiesto il consenso tramite apposito banner.</p>\n'
                    '<h2>3. Cookie di terze parti</h2>\n'
                    '<p>Il sito può includere link esterni (es. mappe o profili social). Cliccando su tali link si accede a siti di terze parti con '
                    'informative autonome.</p>\n'
                    '<h2>4. Come gestire i cookie dal browser</h2>\n'
                    '<p>Puoi configurare il browser per bloccare o cancellare i cookie. La disabilitazione dei cookie tecnici potrebbe compromettere '
                    'alcune funzioni del sito.</p>\n'
                    '<h2>5. Titolare e privacy</h2>\n'
                    '<p>Per il trattamento dei dati personali collegato all’uso del sito, vedi la <a href="{{url:privacy}}">Privacy Policy</a>.</p>',
            'back': '<p><a href="{{url:home}}">Torna alla home</a> · <a href="{{url:privacy}}">Privacy Policy</a></p>'}}

# Header and footer.
NAV = [('La nostra idea', '#chi-siamo'),
 ('Il menù', '#menu'),
 ('Senza glutine', '#materie'),
 ('Domande', '#faq'),
 ('Dove siamo', '#location')]

FOOTER = {'left': [('MENÙ', '#menu'), ('DOVE SIAMO', '#location'), ('SU DI NOI', '#chi-siamo')],
 'right': [('TORNA SÙ', '#home', 'off'), ('FACEBOOK', 'https://facebook.com', 'on'), ('INSTAGRAM', 'https://instagram.com', 'on')],
 'lines': ['Tasche Piene è un progetto di Barrakuda S.r.l.',
           'P.IVA e C.F.: 02789540990 | Via Carducci 5/10, 16121 - Genova',
           '<a href="{{url:privacy}}">Privacy Policy</a> | <a href="{{url:cookie}}">Cookie Policy</a>',
           '<a href="https://www.mochidesign.it" target="_blank" rel="noopener">creato con ♥ da Mochi</a>']}
