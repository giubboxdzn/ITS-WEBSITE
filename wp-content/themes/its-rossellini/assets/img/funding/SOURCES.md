# Loghi dei finanziatori (footer)

File vettoriali ufficiali, nei colori originali. Sono stati solo ritagliati sul contenuto e ottimizzati con SVGO, senza modifiche al disegno.

| File | Logo | Fonte |
|---|---|---|
| `its-academy.svg` | Istituti Tecnici Superiori – It's my future (logo nazionale ITS) | INDIRE, Toolkit comunicazione, «Logo vettoriale» (`ITS_logo.pdf` convertito): https://www.indire.it/toolkit/logo-its/ |
| `coesione-italia-lazio.svg` | Coesione Italia 21-27 Lazio | Brand Book Coesione Italia 2.0 EU Lazio (p. 29), nel Media Kit FSE+ Lazio 2025: https://www.lazioeuropa.it/pr-fse/fse-comunicazione-e-identita-visiva/ |
| `regione-lazio.svg` | Regione Lazio | Stesso Brand Book (p. 42, «Blocco istituzionale e logo amministrazione») |
| `ue-cofinanziato.svg` | Emblema UE + «Cofinanziato dall'Unione europea» | Commissione europea, Download centre for visual elements, pacchetto `co-funded_it.zip`, versione orizzontale RGB POS (EPS convertito): https://ec.europa.eu/regional_policy/information-sources/logo-download-center_en |
| `repubblica-italiana.svg` | Emblema della Repubblica Italiana | Wikimedia Commons, `Emblem_of_Italy.svg` (pubblico dominio): https://commons.wikimedia.org/wiki/File:Emblem_of_Italy.svg |
| `futura-pnrr.svg` | Futura – PNRR Istruzione, Finanziato dall'UE NextGenerationEU, Ministero dell'Istruzione e del Merito | Ministero dell'Istruzione e del Merito, sito PNRR Istruzione: https://pnrr.istruzione.it/wp-content/uploads/2021/09/FUTURA-PNRR-2.svg |

Ordine e spaziatura del blocco FSE+ seguono il Brand Book:
- l'ordine è Coesione Italia, UE, Repubblica, Regione;
- i loghi sono equidistanti e separati da una linea grigia;
- da ogni lato della linea c'è almeno metà dell'altezza dell'emblema UE;
- l'emblema UE non è mai più piccolo degli altri loghi.

Il vecchio logo FSE+ «PR Lazio» della striscia precedente non ha una versione vettoriale ufficiale. Non fa più parte del blocco istituzionale: le note ai beneficiari FSE+ Lazio 2023–2025 e il Media Kit 2025 prevedono solo Coesione Italia, UE, Repubblica e Regione. Per aggiungere altri loghi basta mettere qui i file e inserirli in `its_funding_logos()` (`inc/helpers.php`) o tramite il filtro `its_funding_logos`.
