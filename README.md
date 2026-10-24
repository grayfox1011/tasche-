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
- Gli apici degli allergeni nei nomi del menu sono aggiunti via JavaScript e mantengono il loro stile.
- Orari e legenda del menu usano due span con dimensioni diverse: il colore si modifica, le due dimensioni restano distinte.
- Su iPhone e iPad in verticale i prezzi del menu sono posizionati via JavaScript; da 981 px in su (iPad in orizzontale) vale il layout desktop.
- L'allineamento del testo di alcuni moduli (prezzi, card Materie, link "Torna alla home") resta quello del tema.

Moduli custom:

- I moduli `tp/*` hanno gruppi Design propri (testo della striscia, logo, link, copyright...). I valori impostati vincono sempre sul tema: Divi li stampa con `!important` dentro `@layer tp-authored`.
- Gli stili dei moduli custom sono in `assets/css/tasche-piene.css`; `npm run build` compila solo `scripts/bundle.js` per il Visual Builder.
- Ogni modulo PHP estende `modules/ModuleBase.php`, che registra il modulo e ne stampa classi, stili e dati script: la classe del modulo dichiara solo la cartella del `module.json`, la classe CSS e i gruppi Design, e tiene il proprio render.

Script del sito pubblico:

- `assets/js/tasche-piene.js`: header e menu, pulsanti, orari, prezzi su iOS e lo stato attivo comune a orari, card Tasca/Piatto e Materie.
- `assets/js/tp-layout.js`: scale delle sezioni a 1512 px e della card del menu, video della hero su mobile (usa il video impostato in Divi per quel breakpoint; se in Divi non c'e' video, non ne mostra), ordine mobile della sezione Alberto, allergeni. Viene stampato prima di jQuery, dove stavano gli script inline, perche' deve girare prima degli script di Divi.

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
