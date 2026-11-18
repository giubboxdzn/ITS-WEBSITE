=== ITS Rossellini ===
Requires at least: 6.9
Tested up to: 7.1
Requires PHP: 8.1
Stable tag: 1.3.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Tema classico per itsrossellini.it: il sito attuale modernizzato, con palette istituzionale, Raleway, video e foto reali.

== Descrizione ==

Tema per ITS Academy Roberto Rossellini. CSS e JavaScript scritti a mano, senza framework né build: il pacchetto è completo così com'è. Font, icone e loghi sono inclusi, il tema non chiama servizi esterni e non va compilato né serve scaricare librerie.

Documentazione completa (sviluppo, design, accessibilità) nel repository del progetto: README.md, DESIGN.md, PRODUCT.md, ACCESSIBILITA.md.

== Requisiti ==

* WordPress 6.9 o successivo, PHP 8.1 o successivo. WordPress rifiuta il caricamento se non sono soddisfatti.
* Plugin necessari:
  * Rossellini Core: registra corsi, eventi, edizioni dei corsi e documenti. Non è su WordPress.org: è già installato sul sito attuale; per un'installazione nuova si copia la sua cartella da wp-content/plugins del sito attuale.
  * Polylang: italiano e inglese. Si installa da WordPress.org.
* Plugin già presenti sul sito attuale e compatibili: Yoast SEO, Site Kit by Google, Cookie Notice, WP Consent API.

Se manca un plugin necessario, dopo l'attivazione l'admin mostra un avviso con il pulsante per installarlo o attivarlo.

== Installazione ==

1. Fare una copia di sicurezza completa del sito (file e database).
2. Aspetto → Temi → Aggiungi nuovo tema → Carica tema → scegliere lo zip del tema (its-rossellini-<versione>.zip) → Installa ora.
3. Non attivarlo subito: prima inserire i testi nelle pagine e controllare con "Anteprima dal vivo" (vedi sotto).
4. Attivare il tema. Se compare l'avviso dei plugin, usare i pulsanti "Installa" o "Attiva".

== Dopo l'installazione ==

1. Pagine: il tema mostra il contenuto dell'editor. Sul sito attuale molte pagine hanno l'editor vuoto, perché i testi erano scritti nel tema precedente: vanno inseriti prima dell'attivazione.
2. Modelli di pagina: "Elenco corsi" a /offerta-formativa/corsi/, "Eventi" a /eventi/, "Bandi e documenti" a /bandi-albi/ e /bandi/, "Edizioni dei corsi" a /offerta-formativa/corsi-in-svolgimento-e-conclusi/. Impostazioni → Lettura: /news/ come pagina degli articoli.
3. Aspetto → Menu: posizioni "Menu principale", "Menu footer", "Note legali".
4. Aspetto → Personalizza:
   * Home: video dell'hero (MP4 senza audio);
   * Contatti e sede, Dati pubblicati: indirizzo, orari, telefono, email, numeri dei bandi, superficie del teatro di posa;
   * Accessibilità: link alla dichiarazione di accessibilità (form.agid.gov.it) ed email per le segnalazioni.
5. Lingue → Traduzioni, gruppo "ITS Rossellini": traduzioni in inglese di indirizzo, orari, "come arrivare" e fonte del dato occupazionale, se modificati.
6. Svuotare la cache del sito, se ne usa una.

== Tornare indietro ==

Aspetto → Temi → attivare il tema precedente. I contenuti non cambiano.

== Aggiornare ==

Caricare lo zip della nuova versione da Aspetto → Temi → Aggiungi nuovo tema → Carica tema e scegliere "Sostituisci quello attuale con quello caricato". CSS e JavaScript cambiano versione da soli, i browser li riscaricano.

== Changelog ==

= 1.3.0 - 18/11/2026 =
* Superficie del teatro di posa nel Customizer (Dati pubblicati), come gli altri numeri: nessun dato è più scritto nel codice. Lasciata vuota, la cifra non si mostra.

= 1.2.0 - 18/11/2026 =
* Animazioni allo scroll reversibili: risalendo, ciò che scende nell'ultimo quarto dello schermo esce con la stessa animazione dell'entrata al contrario, e riparte scendendo; i numeri tornano a zero e ricontano.
* Avviso nell'admin per i plugin necessari (Rossellini Core, Polylang), con il pulsante per installarli o attivarli.
* readme.txt con requisiti, installazione e aggiornamento.

= 1.1.0 - 16-17/11/2026 =
* Nuovo design sui riferimenti del committente: pannelli arrotondati, vetro, titoli leggeri, palette istituzionale.
* Italiano e inglese con Polylang, ricerca, pagine interne a sezioni, footer con i loghi ufficiali dei finanziatori.
* Accessibilità (WCAG 2.1 AA), sicurezza, prestazioni, standard di codice WordPress.

== Crediti ==

* Raleway, SIL Open Font License 1.1 (assets/fonts/OFL.txt).
* Loghi dei finanziatori: file ufficiali dei rispettivi enti, fonti in assets/img/funding/SOURCES.md.
