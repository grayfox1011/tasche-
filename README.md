# Tasche Piene Divi Child

Child theme completo per la landing Tasche Piene su Divi 5.

Installazione su un nuovo WordPress:

- Installare prima il tema parent Divi.
- Installare e attivare questo child theme.
- Alla prima attivazione il tema crea la pagina `Home`, la imposta come home primaria, crea header/footer nel Divi Theme Builder e assegna i menu footer modificabili.
- Gli asset della landing sono inclusi nel child theme, ottimizzati in WebP/WebM e non dipendono dal vecchio sito locale.

Aggiornamento di un sito esistente:

- Fare un backup del database.
- Sostituire il child theme da Aspetto > Temi > Aggiungi tema > Carica tema (WordPress chiede di sostituire il tema installato con quello caricato) e svuotare la cache di eventuali plugin di cache.
- La cache CSS di Divi si svuota da sola: caricando lo zip da wp-admin la svuota Divi; se i file vengono copiati via FTP, il tema la invalida alla prima visita in cui riscrive un layout. Svuotarla a mano resta innocuo.
- Alla prima richiesta il tema pulisce una sola volta le pagine della landing (vedi "Pulizia una tantum").

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

Cosa resta fisso:

- La sezione Tasca/Piatto viene ricostruita dal tema: dai moduli Divi si modificano testi e immagini, non il design.
- Gli apici degli allergeni nei nomi del menu sono aggiunti via JavaScript e mantengono il loro stile. I numeri di un piatto si scrivono in Divi tra parentesi quadre in fondo al nome, per esempio `[1,7]`: sul sito diventano l'apice.
- Orari e legenda del menu usano due span con dimensioni diverse: il colore si modifica, le due dimensioni restano distinte.
- La figura sopra il riquadro Piatto sale al suo posto quando il menu entra nello schermo (`tp-menu.js`); nel Visual Builder, dove lo script non gira, e' gia' al suo posto.
- Sui telefoni (sotto i 768 px) la card del menu segue il Figma: scritte Tasca e Piatto inclinate, icone, lettere accanto alle ciotole, prezzi in una riga. Con il prezzo S la card ha quattro colonne (tavola per iPhone 17), senza ne ha tre (tavola "mobilw"). Sono regole CSS in fondo al foglio, uguali su tutti i browser: i valori impostati in Divi su scritte e prezzi prevalgono.
- Sui tablet (da 768 a 980 px) la sezione menu segue la tavola "iPad Pro 11" - 1" (834 px): foto, card, contorni, dolci e bevande a tutta larghezza, titolo a sinistra, card larghe e basse con Tasca e Piatto a destra delle icone e frecce curve (`menu-arrow-green-tablet.svg`, `menu-arrow-bordeaux-tablet.svg`). Misure e distanze sono quelle del Figma a 834 px e crescono con lo schermo; i nomi dei piatti stanno su una riga. Da 981 px in su (anche iPad in orizzontale) vale il layout desktop.
- Misura S del piatto: in ogni riga del menu principale un modulo Testo con classe `tp-menu-label tp-menu-label-s` (la lettera) e uno con classe `tp-menu-price tp-menu-price-value-s` (il prezzo), prima della M. Su desktop, se almeno un piatto ha la S, i prezzi stanno in colonne fisse (Tasca, S, M, L): un piatto senza S lascia vuota la sua cella. Per togliere la S a un piatto basta eliminare i suoi due moduli. I browser senza il selettore `:has()` (Safari prima del 15.4, Firefox prima del 121) non mostrano la S e tengono la card a tre colonne.
- L'allineamento del testo di alcuni moduli (prezzi, card Materie, link "Torna alla home") resta quello del tema.

Moduli custom:

- I moduli `tp/*` hanno gruppi Design propri (testo della striscia, logo, link, copyright...). I valori impostati vincono sempre sul tema: Divi li stampa con `!important` dentro `@layer tp-authored`.
- Gli stili dei moduli custom sono in `assets/css/tasche-piene.css`; `npm run build` compila solo `scripts/bundle.js` per il Visual Builder.
- Ogni modulo PHP estende `modules/ModuleBase.php`, che registra il modulo e ne stampa classi, stili e dati script: la classe del modulo dichiara solo la cartella del `module.json`, la classe CSS e i gruppi Design, e tiene il proprio render.

Script del sito pubblico:

- `assets/js/tasche-piene.js`: header e menu, pulsanti, orari e lo stato attivo comune a orari, card Tasca/Piatto e Materie.
- `assets/js/tp-layout.js`: scale delle sezioni a 1512 px e della card del menu, video della hero su mobile (usa il video impostato in Divi per quel breakpoint; se in Divi non c'e' video, non ne mostra), ordine mobile della sezione Alberto, allergeni. Viene stampato prima di jQuery, dove stavano gli script inline, perche' deve girare prima degli script di Divi.

Misura S una tantum (`inc/menu-size-s-migration.php`):

- Alla prima richiesta dopo l'aggiornamento alla 0.4.5 la home riceve la misura S del Figma (tavola "statico per copy"): lettera e prezzo S in ogni piatto del menu principale (la parmigiana non ha la S), prezzi M e L del Figma e legenda "S - 150g", "M - 200g", "L - 400g".
- Un prezzo cambiato a mano in Divi resta com'e', e cosi' un piatto rinominato. Le revisioni di WordPress conservano la versione precedente della home.
- Alla prima richiesta dopo l'aggiornamento alla 0.4.6, con un secondo passaggio una tantum, la riga "in rotazione" della salsiccia diventa una riga normale come nel Figma desktop: etichette Tasca e Piatto, S 9,00, M 13,50 e L 18,00; il prezzo Tasca e i testi restano quelli salvati. Se la riga e' gia' stata cambiata a mano (altro piatto o moduli aggiunti) non viene toccata.
- Entrambi i passaggi salvano la home cosi' com'e', anche quando la prima richiesta arriva da un visitatore non collegato.

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
