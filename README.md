# Tasche Piene Divi Child

Child theme completo per la landing Tasche Piene su Divi 5.

Installazione su un nuovo WordPress:

- Installare prima il tema parent Divi.
- Installare e attivare questo child theme.
- Alla prima attivazione il tema crea la pagina `Home`, la imposta come home primaria, crea header/footer nel Divi Theme Builder e assegna i menu footer modificabili.
- Gli asset della landing sono inclusi nel child theme, ottimizzati in WebP/WebM e non dipendono dal vecchio sito locale.

Regole operative:

- Usare moduli nativi Divi quando il contenuto e semplice.
- Non modificare il tema Divi core.
- Non registrare override dei block name nativi `divi/*`.
- Mettere CSS e JS del progetto nel child theme.
- Tenere il runtime JS fuori dal Visual Builder.
- Creare custom module solo se i moduli nativi diventano fragili.

Classi iniziali per la hero:

- `tp-hero-section` sulla Section Divi con background video nativo.
- `tp-hero-content` sul Row/Group che contiene testi.
- `tp-hero-title` sul modulo Heading.
- `tp-hero-subline` sul modulo Text.

Font:

- Il CSS del child theme registra `TP Bowlby One` e `TP DM Sans` da `assets/fonts/`.
- Il CSS usa `--et_global_heading_font` / `--et_global_body_font` quando Divi li espone, con fallback ai font inclusi nel tema.
- Google Fonts e disattivato nelle opzioni Divi per evitare richieste esterne.
