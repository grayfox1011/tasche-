# Tasche Piene, tema figlio di Divi (1.0.0)

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

## Installare

Servono WordPress 6.5 o successivo, PHP 7.4 o successivo e il tema Divi 5 installato (provato con
Divi 5.15.1).

1. *Aspetto > Temi > Aggiungi > Carica tema*: caricare lo zip e attivarlo.
2. WordPress apre *Aspetto > Tasche Piene*, che crea il sito da sola e mostra a che punto è
   (circa mezzo minuto). Se la pagina non si apre, c'è un avviso con il pulsante *Crea il sito*.

### Aggiornare un sito che ha una versione precedente (0.8.3 o più vecchia)

Fare prima una copia del database. Poi caricare lo zip come sopra e scegliere *Sostituisci*.
Alla prima pagina di amministrazione WordPress apre *Aspetto > Tasche Piene* e crea il sito nuovo:

- la home nuova prende l'indirizzo `/home/` e diventa la pagina iniziale;
- la home vecchia resta tra le bozze come *Home (versione precedente)*: era fatta con i moduli
  che il tema non ha più, quindi non si può riaprire com'era. I testi cambiati a mano nella
  vecchia home non passano nella nuova, che parte dai testi del tema;
- testata e piè di pagina del Theme Builder vengono riscritti con moduli Divi;
- Privacy Policy e Cookie Policy create dal tema vengono riscritte; una pagina scritta a mano
  allo stesso indirizzo resta com'è;
- foto e video già nella libreria Media non vengono caricati due volte.

## Modificare il sito

- **Home**: *Pagine > Home > Modifica con Divi*.
- **Testata e piè di pagina**: *Divi > Theme Builder*, modello *Tasche Piene: tutto il sito*
  (oppure un clic sulla testata o sul piè di pagina mentre si modifica una pagina).
- **Un testo**: doppio clic sul testo e si scrive. La sezione si allunga o si accorcia da sola.
- **Un piatto in più**: tasto destro sulla riga del piatto > *Duplica Gruppo*; la copia compare
  subito sotto, con lo stesso aspetto. Lo stesso per una scheda, un orario, una voce.
- **Una foto**: clic sulla foto > *Contenuto > Immagine*. La foto che compare su un piatto con il
  mouse si vede passando sulla riga.
- **Colori, caratteri, misure, spazi**: scheda *Design* del modulo. L'icona del dispositivo
  accanto a un campo dà un valore diverso a tablet e telefono.
- **Pezzi che esistono solo su un dispositivo** (*Avanzate > Visibilità*): il panino della
  testata, la legenda del menù (solo computer), disegni e nomi sopra i prezzi e la foto in cima
  al menù (solo tablet e telefono), le due versioni di ogni onda. Nel builder si vedono nella
  vista del loro dispositivo.

Due preferenze del builder che il tema imposta la prima volta, dove nessuno le aveva scelte:

- i moduli spenti per un dispositivo non vengono disegnati nella vista di quel dispositivo, come
  sul sito (preferenza `hide_disabled_modules`);
- le icone che Divi mostra al passaggio del mouse (impostazioni, duplica, elimina, "+") partono
  spente. Sui pezzi piccoli di questo sito, un prezzo, un giorno, una voce, coprono il testo: non
  si riesce a cliccarlo e un clic può finire su "elimina". Senza icone: doppio clic per scrivere,
  un clic per le impostazioni, tasto destro per duplicare o eliminare. Si riaccendono dalla barra
  laterale del builder: *Icone azione al passaggio del mouse* (e *Icone azione del genitore al
  passaggio del mouse* per quelle delle scatole intorno).

## Gli effetti del tema

Sono in `assets/css/tp.css` e `assets/js/tp.js`. Si attivano scrivendo la classe in
*Avanzate > Attributi* di un modulo; tolta la classe l'effetto sparisce e il contenuto resta.
Nel Visual Builder gli effetti stanno fermi, per poter scrivere.

| Classe | Su quale modulo | Cosa fa |
| --- | --- | --- |
| `tp-scorre` | Testo | La scritta scorre da destra a sinistra senza interruzione. Attributo `data-velocita` (pixel al secondo, 70 se manca); `data-verso="destra"` per l'altro verso. |
| `tp-lettere` | Titolo, o Testo con un titolo dentro | Il titolo entra una lettera dopo l'altra la prima volta che si vede (GSAP). |
| `tp-piatto` | Gruppo (la riga di un piatto) | Su computer, con il mouse sopra, mostra i moduli al suo interno che hanno `tp-al-passaggio` (foto, descrizione). |
| `tp-al-passaggio` | Modulo dentro un `tp-piatto` | Vedi sopra. Dove non c'è il mouse (tablet, telefono) resta sempre visibile. |
| `tp-video-telefono` | Sezione con video di sfondo | Il video parte anche su telefono e tablet (un video semplice prende il posto di quello di Divi, che lì non parte; se il telefono lo blocca, resta l'immagine di sfondo). |
| `tp-testata` | Sezione della testata | Le pagine diverse dalla home iniziano sotto la testata; su telefono un tocco su una voce chiude il menù. |

Tutto il resto è Divi: il panino della testata apre e chiude le voci con le *Interazioni* di
Divi (*Avanzate > Interazioni* dell'icona), le domande sono un modulo Accordion, il video
dell'hero è lo sfondo video della sezione, la testata resta in alto con *Posizione: fissa*.

Le onde tra le sezioni sono immagini (`assets/svg/onda-*.svg`): per cambiarne i colori serve un
file nuovo.

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
Media e nella pagina finisce il suo indirizzo.

## Sviluppo

Le pagine in `content/` sono scritte da `dev/build.py` (Python 3, nessuna dipendenza):
`dev/divi.py` produce i blocchi Divi, `dev/texts.py` ha testi e immagini.

```
python3 dev/build.py       riscrive content/*.html
python3 dev/make-zip.py    crea lo zip da caricare in WordPress
```

Dopo la creazione le pagine vivono in WordPress: `content/` serve solo a un sito nuovo o a
*Rifai il sito da capo* (in *Aspetto > Tasche Piene*), che riporta home, testata, piè di pagina e
pagine legali allo stato iniziale.

Nel Visual Builder Divi disegna i suoi strumenti sopra il pezzo sotto il mouse: una barretta in
alto e un "+" poco sopra il fondo. Un testo corto che riempie da solo la sua scatola finisce
sotto e non si riesce a cliccare. Per questo i prezzi tengono dentro di sé lo spazio della riga
(più sotto che sopra) e non stanno in gruppi a loro misura: vedi `price()` in `dev/build.py`.
