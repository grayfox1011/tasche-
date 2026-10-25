# Tasche Piene Divi Child

Child theme completo per la landing Tasche Piene su Divi 5.

Installazione su un nuovo WordPress:

- Installare prima il tema parent Divi.
- Installare e attivare questo child theme.
- Alla prima attivazione il tema crea la pagina `Home`, la imposta come home primaria, crea header/footer nel Divi Theme Builder e assegna i menu footer modificabili.
- Gli asset della landing sono inclusi nel child theme, ottimizzati in WebP/WebM e non dipendono dal vecchio sito locale.
- "Crea/aggiorna home" lavora in una richiesta alla volta (blocco `tp_child_seed_site.lock` nella tabella delle opzioni, lo stesso meccanismo delle operazioni una tantum): dopo il primo clic il pulsante si disattiva, e una seconda richiesta (da un'altra scheda o ricaricando la pagina) non crea pagine, menu o immagini doppie ma porta all'avviso "La home Tasche Piene e in preparazione". Un blocco lasciato da una richiesta interrotta scade dopo 10 minuti.

Aggiornamento di un sito esistente:

- Fare un backup del database.
- Sostituire il child theme da Aspetto > Temi > Aggiungi tema > Carica tema (WordPress chiede di sostituire il tema installato con quello caricato) e svuotare la cache di eventuali plugin di cache.
- La cache CSS di Divi si svuota da sola: caricando lo zip da wp-admin la svuota Divi; se i file vengono copiati via FTP, il tema la invalida alla prima visita in cui riscrive un layout. Svuotarla a mano resta innocuo.
- Alla prima richiesta il tema pulisce una sola volta le pagine della landing (vedi "Pulizia una tantum").
- Le operazioni una tantum (pulizia, misura S, salsiccia e quelle delle versioni precedenti) girano in una richiesta alla volta (`inc/migration-lock.php`): la prima richiesta dopo l'aggiornamento prende un blocco nella tabella delle opzioni (`tp_child_migrations.lock`) e le esegue tutte; le richieste che arrivano intanto mostrano la pagina com'e'. Un blocco lasciato da una richiesta interrotta scade dopo 10 minuti. Una nuova operazione una tantum va aggiunta all'elenco in quel file.

Contenuti del proprietario:

- Privacy e Cookie Policy vengono create dal tema solo se mancano. Una pagina eliminata viene ricreata senza toccare l'altra; riattivare il tema o usare "Crea/aggiorna home" non riscrive le pagine esistenti.
- I menu assegnati dal proprietario alle posizioni del tema restano assegnati anche se il tema viene riattivato.
- Aprire la Libreria media non reimporta le immagini del tema che il proprietario ha eliminato.
- Gli SVG si possono caricare solo con la capacita' unfiltered_html (amministratori ed editori su sito singolo).

Regole operative:

- Usare moduli nativi Divi quando il contenuto e semplice.
- Non modificare il tema Divi core.
- Non registrare override dei block name nativi `divi/*`.
- Mettere CSS e JS del progetto nel child theme.
- Tenere il runtime JS fuori dal Visual Builder.
- Creare custom module solo se i moduli nativi diventano fragili.

Modificare il design da Divi:

- Il foglio `assets/css/tasche-piene.css` non usa piu `!important` per i moduli: i valori impostati nei controlli Design di Divi (testo, spaziature, sfondi, dimensioni) prevalgono sul tema, anche in anteprima nel Visual Builder.
- Ogni regola dei moduli e scritta come `.tp-child-theme :where(...)` e pesa quanto una sola classe. Le regole sono ordinate come nel foglio originale, quindi l'aspetto della pagina non cambia.
- La tipografia dei paragrafi dei moduli Testo e impostata su `.et_pb_text_inner`, l'elemento che Divi modifica con i controlli Testo.
- `!important` resta solo per la cornice della pagina (header/footer di default, wrapper, blocchi di scroll), per gli stati gestiti da JavaScript (preloader, animazioni, video hero), per i font dei pulsanti (Divi li imposta con `!important`) e per il padding orizzontale in hover dei tre pulsanti (Divi lo blocca con un selettore con ID quando l'icona e disattivata).
- Per aggiungere CSS: una regola nuova in fondo al file con lo stesso prefisso vince sulle precedenti; in alternativa usare i campi CSS personalizzati di Divi.
- Testi a due misure (dalla 0.5.3): nella legenda Tasca/Piatto la parola grande e' un titolo H4 e il resto il testo del modulo (nel Piatto una misura per paragrafo); negli orari il giorno e' un H4, il giorno abbreviato dei telefoni un H5 e l'orario il testo del modulo. Ogni parte si cambia da sola, in Design > Testo dei titoli (H4, H5) e in Design > Testo; i valori del tema sono solo il punto di partenza. Il modulo di lunedi'-mercoledi' ha la sua misura H4 di 20 px (prima la dava il tema a quel giorno).
- Allergeni (dalla 0.5.3): ogni piatto ha dopo il nome un modulo Testo "Allergeni" (classe `tp-menu-allergeni`) con i numeri; colore, carattere e misura si cambiano dal suo Design > Testo. Sul sito `tp-layout.js` lo mette in fondo al nome, in alto all'altezza delle maiuscole come l'apice di prima, anche quando il nome va a capo; nel Visual Builder sta in alto a destra del nome (nei browser che non sanno ancorarlo, subito dopo il nome). Senza il modulo valgono ancora i numeri scritti tra parentesi quadre in fondo al nome, per esempio `[1,7]`, e per i piatti di oggi la tabella dello script.
- Prezzi delle bevande sui telefoni: la misura e' sul testo del modulo, quindi quella impostata in Divi per i telefoni prevale.

Cosa resta fisso:

- La sezione Tasca/Piatto viene ricostruita dal tema: dai moduli Divi si modificano testi e immagini, non il design. La ricostruzione vale per la sezione dovunque sia, in qualunque pagina, anche da sola su una pagina nuova (`tp_child_choice_section_bounds()` in `functions.php` la trova dall'inizio alla fine, qualunque cosa ci sia prima o dopo).
- Le scritte "Di venerdi'" e "Di giovedi'" (sotto i 981 px) e il nome corto sui telefoni valgono per il quinto e il sesto piatto del menu principale, oggi trippa e stoccafisso: spostando i piatti in Divi restano su quelle posizioni. Nel Visual Builder compaiono sulle stesse card del sito.
- La figura sopra il riquadro Piatto sale al suo posto quando il menu entra nello schermo (`tp-menu.js`); nel Visual Builder, dove lo script non gira, e' gia' al suo posto.
- Sui telefoni (sotto i 768 px) la card del menu segue il Figma: scritte Tasca e Piatto inclinate, icone, lettere accanto alle ciotole, prezzi in una riga. Con il prezzo S la card ha quattro colonne (tavola per iPhone 17), senza ne ha tre (tavola "mobilw"). Sono regole CSS in fondo al foglio, uguali su tutti i browser: i valori impostati in Divi su scritte e prezzi prevalgono.
- Sui tablet (da 768 a 980 px) la sezione menu segue la tavola "iPad Pro 11" - 1" (834 px): foto, card, contorni, dolci e bevande a tutta larghezza, titolo a sinistra, card larghe e basse con Tasca e Piatto a destra delle icone e frecce curve (`menu-arrow-green-tablet.svg`, `menu-arrow-bordeaux-tablet.svg`). Misure e distanze sono quelle del Figma a 834 px e crescono con lo schermo; i nomi dei piatti stanno su una riga e uno piu' lungo va a capo dentro la card. Da 981 px in su (anche iPad in orizzontale) vale il layout desktop.
- I titoli "Siamo nei caruggi..." e "Il nostro menù", da 981 px in su, diventano piu' piccoli con la loro colonna quando la parola piu' lunga non ci starebbe a 64 px, invece di spezzarla a meta' (regole in fondo al foglio, con `@container`; i browser senza container query restano a 64 px; una dimensione impostata in Divi prevale). La parola piu' lunga e' misurata sul testo scritto in Divi (`assets/js/tp-layout.js`, variabile `--tp-title-fit`): un titolo diverso si riduce quanto serve a lui. Nel Visual Builder, dove lo script non gira, vale la misura delle parole di oggi. Nella sezione Dove siamo, sotto i 1090 px la mappa riduce il margine sinistro (da 107 px fino a 0) perche' la colonna del testo, larga almeno quanto il pulsante Mappe (277,52 px), resti dentro la pagina.
- Sul computer i titoli si animano riga per riga (`assets/js/tp-split-text.js`): ogni riga nasconde le lettere mentre salgono e a fine animazione le mostra intere, accenti compresi. Quando uno dei due titoli va a capo e una riga dopo la prima ha un accento sopra una maiuscola (oggi la U di "menù", tra 981 e 1330 px circa), le righe hanno il 10% di spazio in piu', perche' l'accento non tocchi la riga sopra: lo decide `tp-layout.js` sul testo vero (classe `tp-title-accent-room`). Nel Visual Builder vale per il titolo di oggi del menu. Le righe sono tagliate dove il testo va a capo nel momento in cui il titolo comincia ad animarsi: un titolo gia' visibile mentre la pagina carica (per esempio in cima a una pagina nuova) resta nascosto finche' non e' arrivato il suo carattere, al massimo 3 secondi, cosi' non viene tagliato sul carattere di riserva; subito prima del taglio `tp-layout.js` rifa' le misure (evento `tp:split-text:lines`). Le misure valgono anche se arrivano durante l'animazione, quando parte delle lettere e' ancora nascosta.
- Misura S del piatto: in ogni riga del menu principale un modulo Testo con classe `tp-menu-label tp-menu-label-s` (la lettera) e uno con classe `tp-menu-price tp-menu-price-value-s` (il prezzo), prima della M. Su desktop, se almeno un piatto ha la S, i prezzi stanno in colonne fisse (Tasca, S, M, L): un piatto senza S lascia vuota la sua cella. Per togliere la S a un piatto basta eliminare i suoi due moduli. I browser senza il selettore `:has()` (Safari prima del 15.4, Firefox prima del 121) non mostrano la S e tengono la card a tre colonne.
- L'allineamento del testo di alcuni moduli (prezzi, card Materie, link "Torna alla home") resta quello del tema.
- Footer sul computer (da 981 px): nella home resta fisso sotto la pagina, che ci scorre sopra e lo scopre alla fine; le pagine legali e ogni altra pagina (una pagina nuova fatta in Divi, articoli, archivi, ricerca, 404) lo hanno in fondo, dopo il contenuto, senza le figure. Sotto i 981 px il footer sta in fondo su tutte le pagine: le pagine legali hanno la loro disposizione, le altre quella della home.
- Ricreare una pagina: una pagina nuova con il contenuto della home (per esempio clonata in Divi) e' identica alla home; ogni sezione della home copiata su una pagina nuova, anche da sola, e' identica a quella della home. La cornice della home (preloader, footer che si scopre alla fine) vale per le pagine con la sezione hero.
- Nelle pagine senza la hero (tutte tranne la home) il contenuto comincia sotto l'header: lo spazio e' la variabile `--tp-header-space` (86 px sul computer; posizione piu' altezza del pulsante del menu tra 768 e 980 px; 86,7 px sui telefoni). Non ha peso: una pagina puo' cambiarla nel suo CSS personalizzato, per esempio `body { --tp-header-space: 0; }`. Sui telefoni, in quelle pagine, logo e pulsante del menu sono bordeaux come da 768 px in su (nella home restano crema sopra il video).

Moduli custom:

- I moduli `tp/*` hanno gruppi Design propri (testo della striscia, logo, link, copyright...). I valori impostati vincono sempre sul tema: Divi li stampa con `!important` dentro `@layer tp-authored`.
- Gli stili dei moduli custom sono in `assets/css/tasche-piene.css`; `npm run build` compila solo `scripts/bundle.js` per il Visual Builder.
- I colori di Header e Footer (sfondo e scritte) accettano i colori globali di Divi, anche con una variazione (piu' chiaro, piu' scuro, trasparente): il tema li risolve con le funzioni di Divi sul sito (`tp_child_chrome_get_color()` in `inc/theme-chrome-helpers.php`) e nell'anteprima del Visual Builder (`getColor()` in `src/utils/attrs.ts`, con `divi.globalData`). Un valore che Divi non sa risolvere lascia il colore del tema.
- Ogni modulo PHP estende `modules/ModuleBase.php`, che registra il modulo e ne stampa classi, stili e dati script: la classe del modulo dichiara solo la cartella del `module.json`, la classe CSS e i gruppi Design, e tiene il proprio render.
- Il "CSS personalizzato" della scheda Avanzate funziona su tutti i moduli `tp/*`, sul sito (`ModuleBase::module_styles()`) e nell'anteprima del Visual Builder (`createModuleStyles()` in `src/utils/module-boilerplate.tsx`), come nei moduli di Divi.
- Nell'anteprima del Visual Builder i campi con contenuto dinamico (data, titolo del sito...) mostrano il valore, come sul sito, e non il codice `$variable(...)`: `useDynamicText()` e `DynamicText` in `src/utils/dynamic.tsx`, con le funzioni di Divi.
- I segnaposto di Header e Footer e le figure di ripiego del footer nel Visual Builder usano il percorso vero del tema, ricavato da dove si trova `scripts/bundle.js` (`src/utils/asset-base.ts`): giusto anche con WordPress in una sottocartella o con la cartella del tema rinominata.
- Modulo Onda (TP Shape Wave): una fascia nel "Colore sfondo" con l'onda nel "Colore wave", larga e alta quanto "Larghezza disegno" e "Altezza disegno" (vuoti: la misura della forma scelta), spostata di "Offset verticale", allineata e specchiata come impostato. Nella forma a due colori il "Colore wave" vale per lo strato davanti. L'onda non si deforma mai: in una colonna piu' stretta la fascia la taglia ai lati, secondo l'allineamento, e la pagina non scorre di lato.
- Striscia scorrevole: svuotando "Simbolo tra le scritte" le scritte restano separate dallo stesso spazio, senza simbolo. La variante Materie scorre senza salti con qualunque "Lunghezza nastro". Ogni Scritta porta la classe del suo modulo, quindi le sue opzioni arrivano alla scritta sul sito e nell'anteprima: sfondo (colore, sfumatura, immagine), spaziature, bordo, ombra, filtri, trasformazioni, transizioni, overflow, CSS personalizzato, "Disattiva su" (la scritta sparisce con il suo separatore) e Visualizzazione condizionale (la scritta non compare; se nessuna Scritta compare, la striscia non c'e'). Le altre opzioni (animazione, posizione, scorrimento, link...) non sono offerte perche' dentro una striscia che si ripete non possono funzionare; valori salvati prima per quelle opzioni restano ignorati. Una striscia senza Scritte mostra cinque frasi predefinite (Materie: una), sul sito e nell'anteprima del Visual Builder.

Script del sito pubblico:

- `assets/js/tasche-piene.js`: header e menu, pulsanti, orari e lo stato attivo comune a orari, card Tasca/Piatto e Materie. Su iPhone e iPad tiene l'header visibile anche se il preloader si blocca; se il preloader sta ancora girando, aspetta che finisca, cosi' l'animazione d'apertura si vede intera come su Android e sul computer.
- `assets/js/tp-layout.js`: scale delle sezioni a 1512 px e della card del menu, misura dei due titoli che si riducono con la colonna, video della hero su mobile (usa il video impostato in Divi per quel breakpoint; se in Divi non c'e' video, non ne mostra), ordine mobile della sezione Alberto, allergeni. Viene stampato prima di jQuery, dove stavano gli script inline, perche' deve girare prima degli script di Divi.

Misura S una tantum (`inc/menu-size-s-migration.php`):

- Alla prima richiesta dopo l'aggiornamento alla 0.4.5 la home riceve la misura S del Figma (tavola "statico per copy"): lettera e prezzo S in ogni piatto del menu principale (la parmigiana non ha la S), prezzi M e L del Figma e legenda "S - 150g", "M - 200g", "L - 400g".
- Un prezzo cambiato a mano in Divi resta com'e', e cosi' un piatto rinominato. Le revisioni di WordPress conservano la versione precedente della home.
- Alla prima richiesta dopo l'aggiornamento alla 0.4.6, con un secondo passaggio una tantum, la riga "in rotazione" della salsiccia diventa una riga normale come nel Figma desktop: etichette Tasca e Piatto, S 9,00, M 13,50 e L 18,00; il prezzo Tasca e i testi restano quelli salvati. Se la riga e' gia' stata cambiata a mano (altro piatto o moduli aggiunti) non viene toccata.
- Alla prima richiesta dopo l'aggiornamento alla 0.4.7, con un terzo passaggio una tantum, la salsiccia prende il nome del Figma: "Salsiccia, alloro e pomodori confit" invece di "peperoni confit". Un nome cambiato a mano resta com'e'.
- Alla prima richiesta dopo l'aggiornamento alla 0.5.3 la home passa ai testi a due misure modificabili (`inc/two-size-text-migration.php`): legenda e orari con titoli e testo al posto dei due span, e un modulo Allergeni per ogni piatto che ha i numeri (scritti nel nome tra parentesi quadre, che lasciano il nome, o presi dalla tabella dello script). Un modulo cambiato a mano, che non ha piu' i due span, resta com'e'. L'aspetto della pagina non cambia; WordPress tiene la versione di prima tra le revisioni.
- Tutti i passaggi salvano la home cosi' com'e', anche quando la prima richiesta arriva da un visitatore non collegato.

Pulizia una tantum (`inc/design-cleanup-migration.php`):

- Fino alla 0.3.x il tema sovrascriveva con `!important` padding, altezza minima e colore di sfondo delle sezioni e padding e larghezze delle righe con classe `tp-...`: i valori salvati in Divi non si vedevano.
- Alla prima esecuzione della 0.4.0 questi valori desktop vengono rimossi dalle pagine della landing, cosi la pagina resta identica e i campi ripartono dai valori del tema. Le revisioni di WordPress conservano la versione precedente.
- I valori modificati a mano con il candidato 0.3.1 (registrati in `tpDesignOverrides`) vengono mantenuti.

Classi iniziali per la hero:

- `tp-hero-section` sulla Section Divi con background video nativo.
- `tp-hero-content` sul Row/Group che contiene testi.
- `tp-hero-title` sul modulo Heading.
- `tp-hero-subline` sul modulo Text.

Font:

- Il CSS del child theme registra `TP Bowlby One` e `TP DM Sans` da `assets/fonts/`.
- Il CSS usa `--et_global_heading_font` / `--et_global_body_font` quando Divi li espone, con fallback ai font inclusi nel tema.
- Google Fonts e disattivato nelle opzioni Divi per evitare richieste esterne.
