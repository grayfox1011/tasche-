# Tasche Piene, tema figlio di Divi (1.2.1)

Il sito è fatto **solo di moduli Divi**: sezioni, righe, colonne, gruppi, testi, titoli, immagini,
pulsanti, elenchi con icone, accordion. Misure, spazi, colori, caratteri e disposizione sono nei
campi di ogni modulo, per computer, tablet e telefono, e si cambiano da Divi. Nessuna altezza è
fissata dal tema: una sezione è alta quanto quello che contiene.

Il tema aggiunge tre cose:

1. i due caratteri (Bowlby One e DM Sans), serviti dal sito e non da Google;
2. pochi effetti, ognuno legato a una classe scritta in *Avanzate > Attributi* del modulo;
3. la creazione del sito la prima volta: home, testata e piè di pagina (Theme Builder), Privacy
   Policy e Cookie Policy.

Dalla 1.0.0 il tema non ha più moduli suoi (Header sito, Footer sito, Striscia, Onda, Titolo
animato) né il foglio di stile che teneva ogni pezzo al posto del Figma.

La 1.1.0 parte dai testi e dalle foto che erano sul sito l'11 novembre 2026 (galleria in fondo
compresa) e riporta gli effetti della 0.8.3: il logo all'apertura della pagina, le card *Tasca o
vaschetta* che si aprono, la scritta diagonale che resta in vista, le card delle materie prime
che entrano ruotate e si accendono. Sono fatti sui moduli Divi: i contenuti restano in Divi.

La 1.1.1 corregge quattro cose viste sul sito con la 1.1.0, tutte nel tema (CSS e script): basta
caricare lo zip, le pagine non vengono toccate.

- Le onde tra le sezioni arrivano ai due bordi a ogni larghezza (si fermavano a 1440 px sui
  computer e a 390 px sui telefoni).
- Su telefono e tablet le card delle ricette non diventano più verdi al tocco (era il colore del
  passaggio del mouse, che sotto un dito restava, con i testi scuri).
- La scritta diagonale attraversa tutto lo schermo a ogni larghezza, come la striscia in alto.
- Le card del menù e i titoli dei suoi gruppi salgono e compaiono quando si arriva a loro, anche
  su telefono, come le righe del menù nella 0.8.3.

La 1.2.0 rifà da una pagina bianca la home, la testata e il piè di pagina, sezione per sezione,
sulle misure del Figma (il riquadro da 1512 px per i computer, quello da 390 px per i telefoni)
come le aveva realizzate la 0.8.3, sempre e solo con moduli Divi: non è servito nessun modulo su
misura. Ogni sezione è stata confrontata con il Figma e con la 0.8.3 a 1512 e a 390 px; il
confronto, le differenze rimaste e le prove sono in `VERIFICA-1.2.1.txt`. In breve:

- testata: su computer la barra crema (76 px, logo da 216 px); su tablet e telefono logo chiaro
  e panino sopra il video, senza barra, e le voci in un riquadro largo quanto lo schermo;
- menù su telefono come nel Figma: pagina crema, una scheda per piatto con le parole *Tasca* e
  *Piatto*, i tre disegni e i tre prezzi;
- materie prime: schede da 402 × 517 px sparse come sulla lavagna del Figma;
- dove siamo, orari e piè di pagina con le misure del Figma (foto 573 × 594, riquadri da 690 px,
  blocco verde con il nome a tutta larghezza);
- pulsanti con la freccia del Figma, foto che riempiono sempre la loro cornice.

Un sito che ha già una versione 1.x prende l'impaginazione nuova con *Rifai il sito da capo*
(vedi *Aggiornare*).

La 1.2.1 aggiunge la X: su tablet e telefono, mentre le voci del menù sono aperte, il panino
della testata diventa una X e torna alle tre linee quando si chiudono. I contenuti sono stati
riconfrontati il 12 novembre 2026 con il sito online, modulo per modulo (testi, collegamenti,
foto, galleria in fondo, pagine legali): sono gli stessi, non c'era niente da portare.

## Installare

Servono WordPress 6.5 o successivo, PHP 7.4 o successivo e il tema Divi 5 installato (provato con
Divi 5.15.1).

1. *Aspetto > Temi > Aggiungi > Carica tema*: caricare lo zip e attivarlo.
2. WordPress apre *Aspetto > Tasche Piene*, che crea il sito da sola e mostra a che punto è
   (circa mezzo minuto). Se la pagina non si apre, c'è un avviso con il pulsante *Crea il sito*.

### Aggiornare dalla 1.0.0, dalla 1.1.0 o dalla 1.1.1

1. *Aspetto > Temi > Aggiungi tema > Carica tema*, lo zip nuovo, *Installa ora*, poi *Sostituisci
   il tema installato con quello caricato*. Cambiano solo i file del tema: home, testata, piè di
   pagina e pagine legali restano come sono, con tutte le modifiche fatte in Divi. A vista cambia
   una cosa sola: tra due paragrafi dello stesso testo lo spazio diventa una riga intera.
2. Per avere l'impaginazione della 1.2.0: *Aspetto > Tasche Piene > Rifai il sito da capo*.
   **Riscrive home, testata, piè di pagina, Privacy Policy e Cookie Policy**: le modifiche fatte
   da Divi a queste pagine si perdono, testi e foto tornano quelli che erano sul sito il 12
   novembre 2026 (riletto quel giorno: uguali a quelli del tema). Chi ha cambiato qualcosa dopo
   se lo segni prima, e lo riscriva nella home nuova. La pagina va lasciata aperta finché dice *Il sito è pronto* (una decina di secondi).
3. Svuotare la cache del sito, se c'è un plugin di cache, e ricaricare la pagina (su telefono:
   chiuderla e riaprirla).

### Aggiornare un sito che ha una versione precedente (0.8.3 o più vecchia)

Fare prima una copia del database. Poi caricare lo zip come sopra e scegliere *Sostituisci*.
Alla prima pagina di amministrazione WordPress apre *Aspetto > Tasche Piene* e crea il sito nuovo:

- la home nuova prende l'indirizzo `/home/` e diventa la pagina iniziale;
- la home vecchia resta tra le bozze come *Home (versione precedente)*: era fatta con i moduli
  che il tema non ha più, quindi non si può riaprire com'era. La home nuova parte dai testi e
  dalle foto che erano sul sito l'11 novembre 2026: quello che viene cambiato sul sito dopo
  quella data non passa, va riscritto nella home nuova;
- testata e piè di pagina del Theme Builder vengono riscritti con moduli Divi;
- Privacy Policy e Cookie Policy create dal tema vengono riscritte; una pagina scritta a mano
  allo stesso indirizzo resta com'è;
- foto e video già nella libreria Media non vengono caricati due volte.

## Modificare il sito

- **Home**: *Pagine > Home > Modifica con Divi*.
- **Testata e piè di pagina**: *Divi > Theme Builder*, riquadro *Template Del Sito Web
  Predefinito*, la matita accanto a *Header Globale* o a *Footer Globale*.
- **Un testo**: doppio clic sul testo e si scrive. La sezione si allunga o si accorcia da sola.
- **Un piatto in più**: tasto destro sulla riga del piatto > *Duplica Gruppo*; la copia compare
  subito sotto, con lo stesso aspetto. Lo stesso per una scheda, un orario, una voce.
- **Una voce in più nella testata**: tasto destro su una voce > *Duplica Collegamento*. Una voce
  non si spezza mai su due righe: quando le voci sono più di quante la barra ne tiene, le ultime
  vanno su una seconda riga e la barra cresce. Per riaverle su una riga si riduce lo spazio tra le
  voci (gruppo *Voci del menù*, scheda *Design*, *Layout*, *Spazio Orizzontale*) o la misura del
  carattere delle voci.
- **Una foto**: clic sulla foto > *Contenuto > Immagine*. La foto che compare su un piatto con il
  mouse si vede passando sulla riga. Le foto ritagliate (introduzione, schede, piatti) riempiono
  la loro cornice qualunque sia la misura del file: forma e larghezza della cornice sono in
  *Design > Dimensionamento*.
- **Un orario**: ogni riga ha due nomi del giorno, quello intero (computer e tablet) e quello
  breve (telefono, modulo *Giorno (telefono)*, che si vede nella vista telefono del builder).
  Su telefono giorni e orari sono in maiuscolo; una riga senza numeri (*Chiusi*) resta com'è
  scritta.
- **Dove siamo**: foto, testi e pulsante stanno in una griglia (*Design > Layout* della
  colonna): su computer la foto prende la prima colonna, su tablet e telefono è una colonna sola
  nell'ordine in cui i pezzi sono scritti (testi, foto, pulsante).
- **Le schede delle materie prime**: su computer il posto di ogni scheda è nei suoi margini
  sinistro e superiore (*Design > Spaziatura*); una scheda duplicata prende quelli della scheda
  copiata e va spostata da lì.
- **Un pulsante**: i pulsanti del tema hanno la classe `tp-freccia`, che mette la freccia del
  Figma al posto dell'icona di Divi. Un pulsante nuovo ottenuto duplicandone uno ce l'ha già.
- **Il logo all'apertura**: è la prima sezione della home, *Apertura*. Nel builder è una sezione
  come le altre (colore di fondo e immagine del logo si cambiano lì); sul sito diventa lo schermo
  che si vede per primo. Per toglierlo si elimina la sezione, o la sua classe `tp-apertura`.
- **Le card Tasca o vaschetta**: nel builder la descrizione sta sotto le etichette e si scrive
  lì; sul sito compare sopra la foto quando la card si apre.
- **La galleria in fondo**: è il modulo *Galleria* di Divi. *Contenuto > Immagini* per
  aggiungere, togliere o riordinare le foto; quante per riga in *Design > Layout*.
- **Colori, caratteri, misure, spazi**: scheda *Design* del modulo. L'icona del dispositivo
  accanto a un campo dà un valore diverso a tablet e telefono.
- **Pezzi che esistono solo su un dispositivo** (*Avanzate > Visibilità*): nella testata il
  logo scuro (computer), il logo chiaro e il panino (tablet e telefono); la legenda del menù e
  le descrizioni dei piatti (solo computer); parole, disegni e foto in cima al menù con i suoi
  tre puntini (solo tablet e telefono); i nomi brevi dei giorni e l'onda dopo le due schede
  (solo telefono); la scritta obliqua e l'onda prima delle materie prime (non su telefono). Nel
  builder si vedono nella vista del loro dispositivo.

Due preferenze del builder che il tema imposta la prima volta, dove nessuno le aveva scelte:

- i moduli spenti per un dispositivo non vengono disegnati nella vista di quel dispositivo, come
  sul sito (preferenza `hide_disabled_modules`);
- la barretta di icone che Divi mostra al passaggio del mouse (sposta, impostazioni, duplica,
  elimina) parte spenta. Sui pezzi piccoli di questo sito, un prezzo, un giorno, una voce, copre
  il testo: non si riesce a cliccarlo e un clic può finire su "elimina". Senza barretta: doppio
  clic per scrivere, un clic per le impostazioni, tasto destro per duplicare o eliminare. Si
  riaccende dalla barra laterale del builder: *Icone azione al passaggio del mouse* (e *Icone
  azione del genitore al passaggio del mouse* per quelle delle scatole intorno). Il "+" per
  aggiungere un modulo resta sempre: i pezzi piccoli sono fatti in modo che non cada sul testo.

## Gli effetti del tema

Sono in `assets/css/tp.css` e `assets/js/tp.js`. Si attivano scrivendo la classe in
*Avanzate > Attributi* di un modulo; tolta la classe l'effetto sparisce e il contenuto resta.
Nel Visual Builder gli effetti stanno fermi, per poter scrivere.

| Classe | Su quale modulo | Cosa fa |
| --- | --- | --- |
| `tp-scorre` | Testo | La scritta scorre da destra a sinistra senza interruzione. Attributo `data-durata` (i secondi che il testo impiega a passare una volta) oppure `data-velocita` (pixel al secondo, 70 se mancano tutti e due); `data-verso="destra"` per l'altro verso. |
| `tp-lettere` | Titolo, o Testo con un titolo dentro | Il titolo entra una lettera dopo l'altra la prima volta che si vede (GSAP). |
| `tp-piatto` | Gruppo (la riga di un piatto) | Sale e compare la prima volta che arriva sullo schermo, anche su telefono. Su computer, con il mouse sopra, mostra i moduli al suo interno che hanno `tp-al-passaggio` (foto, descrizione). |
| `tp-al-passaggio` | Modulo dentro un `tp-piatto` | Vedi sopra. Dove non c'è il mouse (tablet, telefono) resta sempre visibile. |
| `tp-entra` | Qualunque modulo | Sale e compare la prima volta che arriva sullo schermo, come le card del menù (ce l'hanno i titoli *I nostri contorni*, *I nostri dolci*, *Da bere*). Quelli che entrano insieme si seguono uno dopo l'altro. |
| `tp-video-telefono` | Sezione con video di sfondo | Il video parte anche su telefono e tablet (un video semplice prende il posto di quello di Divi, che lì non parte; se il telefono lo blocca, resta l'immagine di sfondo). |
| `tp-testata` | Sezione della testata | Le pagine diverse dalla home iniziano sotto la testata; su telefono un tocco su una voce chiude il menù; una pagina aperta su un'àncora (`…/#menu`, da un link di un'altra pagina) mostra la sezione sotto la testata e non dietro. |
| `tp-fascia` | Sezione della testata, insieme a `tp-testata` | Su tablet e telefono la testata non ha una barra sua: logo chiaro e panino stanno sopra il video della home. Nelle altre pagine, che iniziano su fondo chiaro, la sezione prende una fascia bordeaux. |
| `tp-panino` | Immagine (il panino della testata) | È il pulsante che `tp-testata` preme per richiudere il menù dopo un tocco su una voce; si usa anche da tastiera (Invio o Spazio). Mentre le voci sono aperte diventa una X: l'immagine delle tre linee sfuma e due linee crema si incrociano al suo posto (lo script scrive `aria-expanded` sul panino, il CSS disegna la X). Se il panino è un'icona di Divi, come nelle versioni 1.0 e 1.1, compare il segno *chiudi* di Divi. |
| `tp-apertura` | Sezione (la prima della home) | Sul sito è lo schermo che apre la pagina: il logo sale in due righe, poi lo schermo si alza e si vede la pagina. Nel builder, senza JavaScript o con "meno movimento" chiesto al dispositivo, non c'è nessuno schermo. |
| `tp-scheda` | Colonna o gruppo (una card) | Con il mouse sopra (un tocco su telefono) la foto con `tp-scheda-foto` riempie la card e si scurisce, nome ed etichette salgono e compare quello che ha `tp-scheda-nascosto`. La card è alta quanto foto, nome ed etichette. |
| `tp-scheda-foto`, `tp-scheda-nascosto` | Moduli dentro una `tp-scheda` | Vedi sopra. |
| `tp-resta` | Modulo (la scritta diagonale) | Su computer resta in vista, al 39% dell'altezza dello schermo, mentre la sua sezione scorre; attributo `data-posizione` per un'altra altezza (in percentuale). Insieme a `tp-scorre` sullo stesso Testo: sul sito la scritta attraversa tutto lo schermo, e un po' di più perché è inclinata; larghezza e margini sinistro e destro di quel modulo li dà il tema, il resto (margine sopra, inclinazione, carattere, colore) si cambia in Divi. |
| `tp-foto` | Immagine | La foto riempie la sua cornice (larghezza e forma scelte in *Design > Dimensionamento*) anche quando il file è più piccolo della cornice. Divi da solo non ingrandisce mai una foto, se non con *Forza larghezza intera*. |
| `tp-freccia` | Pulsante | Al posto dell'icona scelta in Divi il pulsante mostra la freccia del Figma: quella di DM Sans, in grassetto, girata in alto a destra e staccata dal testo. L'icona deve essere accesa (*Design > Pulsante*): dove è spenta, su telefono per alcuni pulsanti, la freccia non c'è. |
| `tp-carte` | Gruppo di card | Ogni card entra dal basso, un po' ruotata, quando arriva sullo schermo (ferme su telefono). Con il mouse sopra una card i testi diventano chiari e, su computer, la card cresce un poco; fondo e bordo al passaggio sono i valori *hover* della card in Divi. Sugli schermi che si toccano (telefono, tablet) le card non reagiscono al dito: lì il "passaggio del mouse" resterebbe acceso dopo il tocco. |

Tutto il resto è Divi: il panino della testata apre e chiude le voci con le *Interazioni* di
Divi (*Avanzate > Interazioni* dell'immagine), le domande sono un modulo Accordion, la galleria è
il modulo Galleria, il video dell'hero è lo sfondo video della sezione, la testata resta in alto
con *Posizione: fissa* (su tablet e telefono *assoluta*: sale con la pagina).

Una regola vale per tutti i moduli Testo: tra due paragrafi c'è una riga vuota, come nel Figma
(Divi lascia un po' meno).

Le onde tra le sezioni sono immagini (`assets/svg/onda-*.svg`): per cambiarne i colori serve un
file nuovo. Sono SVG con *Forza larghezza intera*: Divi da solo lascia un SVG alla larghezza del
disegno, il tema fa arrivare ai bordi quelli che si chiamano `onda-…` (un'onda in PNG, WebP o
JPG arriva ai bordi con la sola opzione di Divi).

Quello che entra allo scorrimento (`tp-piatto`, `tp-entra`, `tp-carte`) aspetta invisibile solo
finché è sotto lo schermo: senza JavaScript, con "meno movimento" chiesto al dispositivo, in
stampa e nel builder è sempre visibile.

## Caratteri

Nei campi di Divi i caratteri si chiamano *Bowlby One* e *DM Sans*, come su Google Fonts. I file
sono nel tema (`assets/fonts`) e `inc/fonts.php` toglie quei due nomi dalla richiesta che Divi
farebbe a Google. Un altro carattere di Google scelto in Divi viene caricato da Google come
sempre.

## File

```
style.css            intestazione del tema
functions.php        caricamento di caratteri, effetti e script
inc/fonts.php        caratteri serviti dal sito
inc/svg-upload.php   caricamento di file SVG per chi amministra
inc/setup-site.php   creazione del sito e pagina Aspetto > Tasche Piene
content/*.html       le pagine come blocchi Divi 5, copiate in WordPress alla creazione
assets/              caratteri, immagini, video, tp.css, tp.js, GSAP
dev/                 come sono state scritte le pagine (non entra nello zip)
```

`{{media:percorso}}` in `content/` indica un file di `assets/`: alla creazione va nella libreria
Media e nella pagina finisce il suo indirizzo (`{{id:percorso}}`: il suo numero, per la galleria).
Una foto già caricata a mano nella libreria, con lo stesso nome e lo stesso contenuto, viene
usata così com'è e non caricata una seconda volta.

## Sviluppo

Le pagine in `content/` sono scritte da `dev/build.py` (Python 3, nessuna dipendenza):
`dev/divi.py` produce i blocchi Divi, `dev/texts.py` ha testi e immagini. Le misure sono quelle
del Figma: `vw(96)` in `dev/build.py` vuol dire 96 px sul riquadro da 1512 px, in proporzione
sugli altri schermi. `dev/drawings.py` ha scritto i tre disegni delle porzioni per il telefono
(`assets/svg/menu-telefono-*.svg`) dalle frecce del Figma; serve solo a rifarli e vuole
la libreria Python `fontTools`.

```
python3 dev/build.py       riscrive content/*.html
python3 dev/make-zip.py    crea lo zip da caricare in WordPress
```

Dopo la creazione le pagine vivono in WordPress: `content/` serve solo a un sito nuovo o a
*Rifai il sito da capo* (in *Aspetto > Tasche Piene*), che riporta home, testata, piè di pagina e
pagine legali allo stato iniziale.

Nel Visual Builder Divi disegna i suoi strumenti sopra il pezzo sotto il mouse: una barretta in
alto (24 px) e un "+" largo 40 px al centro, tra 42 e 22 px sopra il fondo del pezzo. Un testo
corto in una scatola alta una sessantina di pixel finisce proprio sotto il "+" e non si riesce a
cliccare. Due modi per evitarlo, usati in `dev/build.py`:

- il pezzo tiene dentro di sé lo spazio della riga, più sotto che sopra, così il "+" resta sotto
  il testo: i prezzi (`price()`), che per questo non stanno in gruppi a loro misura, e giorno e
  orario di ogni riga degli orari (lì un margine inferiore negativo toglie dall'altezza della riga
  lo spazio in più, e la riga resta alta come prima);
- il pezzo è alto solo quanto il suo testo e lo spazio sta nella scatola intorno, così il "+"
  resta sopra il testo: le voci della testata (lo spazio è nel padding della riga), i nomi dei
  piatti.

"Non restringere" e "riempi" (flex-shrink, flex-grow) in Divi sono la scelta *Dimensionamento >
Dimensione* (*Riduci Per Adattare*, *Espandi Per Riempire*, *Personalizzato*) e si salvano nel
campo `size`: i soli numeri `flexGrow` e `flexShrink` non vengono letti. `sizing()` in
`dev/divi.py` li scrive nel modo giusto.
