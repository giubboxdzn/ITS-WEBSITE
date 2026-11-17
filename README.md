# ITS Academy Roberto Rossellini — tema WordPress

Nuovo tema classico per [itsrossellini.it](https://itsrossellini.it), stesso stack del sito attuale: WordPress, PHP, CSS e JavaScript scritti a mano, senza framework né build.

Direzione visiva: **contenitori arrotondati, vetro e titoli leggeri**, sui riferimenti indicati dal committente: home su fondo blu notte con il video dell'hero a tutta larghezza, menu a pillola, card in vetro, griglia di numeri e foto, card dei corsi a immagine, card di confronto con cosa include il biennio, blocco «Parti da Roma» con globo, FAQ ad accordion e footer a colonne con il logo in filigrana e l'interruttore «Animazioni»; pagine interne chiare con un alone azzurro. Solo i colori dell'istituto (blu, blu notte, ciano, blu elettrico e loro toni) e Raleway. Regole complete in [`DESIGN.md`](DESIGN.md), contesto di prodotto in [`PRODUCT.md`](PRODUCT.md). Accessibilità e obblighi di legge in [`ACCESSIBILITA.md`](ACCESSIBILITA.md).

## Struttura

```
wp-content/themes/its-rossellini/   il tema (da installare in produzione)
  front-page.php                    home (video, corsi, academy, percorso, news, partner)
  single-corso.php                  scheda corso
  single.php, single-evento.php, single-documento.php
  templates/                        modelli di pagina: Elenco corsi, Eventi, Bandi e documenti, Edizioni dei corsi
  template-parts/                   card corso, griglia corsi con filtro, riga datata, testata pagina
  inc/                              helper, icone SVG, bandiere e selettore lingua, query, Customizer
  languages/                        traduzioni del tema: its-rossellini.pot, en_GB.po/.mo/.l10n.php
  assets/css/main.css               tutto lo stile (token in :root)
  assets/js/main.js                 header, video dell'hero, menu mobile, filtro corsi, animazioni legate all'interruttore «Animazioni» (miglioramento progressivo)
  assets/fonts/                     Raleway variabile, normale e corsivo (OFL), self-hosted
dev/                                ambiente locale (non va in produzione); dev/i18n/build.py compila le traduzioni
.claude/skills/                     skill per Claude Code (versioni bloccate in skills-lock.json):
                                    design: impeccable, web-design-guidelines, ux-heuristics
                                    codice WordPress: wp-guard
                                    prestazioni e qualità: web-quality-audit, performance, core-web-vitals
                                    accessibilità: accessibility (WCAG 2.2), pdf-accessibility (PDF/UA), a11y-core
                                    legge: accessibilita-siti-app-l4-2004 e verifica-accessibilita-dichiarazione-agid (Legge Stanca, AgID),
                                      kb-legal-compliance-mapping e accessibility-statement (EAA, EN 301 549)
                                    sicurezza: performing-ssl-tls-security-assessment (TLS, con sslyze)
                                    ricerca di altre skill: find-skills
```

## Sviluppo locale

Richiede PHP ≥ 8.1 con `pdo_sqlite`, git e Python 3. Nessun MySQL: WordPress gira su SQLite.

```bash
dev/setup.sh      # scarica WordPress 7.1.4 + SQLite + Polylang, attiva tema e lingue, importa i contenuti pubblici IT ed EN
dev/serve.sh      # http://localhost:8080  (admin / admin)
```

`serve.sh` è raggiungibile anche dalla rete locale: stampa l'indirizzo da aprire sul telefono (stessa Wi-Fi), ad esempio `http://192.168.1.20:8080`. I link del sito seguono l'indirizzo con cui lo apri. `HOST=127.0.0.1 dev/serve.sh` lo limita a questo PC, `PORT=8090` cambia porta. Attenzione: in rete locale anche l'admin (admin / admin) è raggiungibile; usalo solo su reti fidate.

Su Windows, al primo avvio il firewall chiede se consentire PHP: spuntare **Reti private**. Se l'avviso è stato chiuso, da un prompt dei comandi *come amministratore*: `netsh advfirewall firewall add rule name="PHP dev server" dir=in action=allow protocol=TCP localport=8888 profile=private` (con la porta che usi), e verificare che la rete Wi-Fi sia impostata come *privata*.

### Windows (Git Bash)

1. **PHP**: da [windows.php.net/download](https://windows.php.net/download/) lo zip *VS17 x64 Non Thread Safe* (PHP 8.3 o 8.4), estratto in `C:\php`; aggiungere `C:\php` al PATH di Windows. Se manca `VCRUNTIME140.dll`, installare il Visual C++ Redistributable x64.
2. **php.ini** con le estensioni necessarie, da Git Bash:
   ```bash
   cd /c/php && cp php.ini-development php.ini
   sed -i -E 's|^;extension_dir = "ext"|extension_dir = "C:/php/ext"|; s/^;(extension=(curl|fileinfo|gd|mbstring|openssl|pdo_sqlite|sqlite3))\s*$/\1/' php.ini
   php -m | grep -iE 'pdo_sqlite|mbstring|gd'
   ```
3. **Python 3** da python.org, con l'opzione *Add python.exe to PATH*.
4. Riaprire Git Bash, poi `dev/setup.sh` e `dev/serve.sh` dalla cartella del repo.

Se `serve.sh` risponde *Failed to listen … autorizzazioni di accesso*, la porta è riservata da Hyper-V/WSL/Docker (`netsh interface ipv4 show excludedportrange protocol=tcp` mostra quali): usare un'altra porta, ad esempio `PORT=8888 dev/serve.sh`.

Su Windows il tema è collegato con una *junction* (non servono permessi di amministratore). Per ripartire da un database vuoto basta cancellare `.wp/db/` e rilanciare `dev/setup.sh`.

`dev/seed/fetch.py` legge (solo GET) l'API REST e le pagine pubbliche di itsrossellini.it, in italiano e in inglese, e salva tutto in `.wp/seed/` (ignorato da git). `dev/seed/import.php` lo importa nel database locale. Non scrive nulla sul sito live. Se `data.json` esiste già senza la parte inglese, `setup.sh` scarica solo quella (`fetch.py --en-only`, qualche minuto); se le pagine sono in un formato precedente, riscarica solo le pagine (`fetch.py --pages-only`, un paio di minuti). Le pagine interne del live diventano blocchi dell'editor con gli stili del tema (occhielli, numeri, card, invito finale), con lead (riassunto) e foto (immagine in evidenza).

### Qualità del codice

`phpcs.xml.dist` applica gli standard di WordPress al tema e agli script di `dev/`:
- WordPress-Extra, comprese le regole di sicurezza su escaping, sanificazione, nonce e SQL;
- compatibilità con PHP 8.1 e successivi;
- dominio di traduzione e prefisso `its_`.

Lo stato attuale è 0 errori e 0 avvisi. Le poche eccezioni sono motivate nel file stesso. Per rilanciarlo, con [WPCS 3](https://github.com/WordPress/WordPress-Coding-Standards) installato:

```bash
composer global require --dev wp-coding-standards/wpcs:^3.1 phpcompatibility/phpcompatibility-wp:"*"
phpcs          # verifica
phpcbf         # corregge da solo la formattazione
```

### Lingue (Polylang)

Come sul sito live: italiano predefinito senza prefisso, inglese su `/en/`. `setup.sh` prende **Polylang 3.9.2** da GitHub (`.wp/polylang`) e genera con `dev/polylang-autoload.php` i file che la release ufficiale include (autoload e liste dei moduli), senza Composer; `dev/languages.php` crea le lingue e le opzioni; l'import assegna la lingua a ogni contenuto, collega le traduzioni IT↔EN (dagli `hreflang` del sito live) e crea i menu inglesi.

Il selettore con le bandiere è nell'header (bandiera + codice) e nel footer (bandiera + nome, come nel riferimento): porta alla stessa pagina nell'altra lingua o, se manca la traduzione, alla home di quella lingua. I testi del tema sono tradotti in `languages/en_GB.po`; dopo averli modificati: `python3 -I dev/i18n/build.py` (aggiorna `.pot`, `.po` e compila `.mo` e `.l10n.php`).

`dev/mu-plugins/its-dev-content-model.php` registra in locale gli stessi tipi di contenuto di Rossellini Core (`corso`, `evento`, `edizione_corso`, `documento` e relative tassonomie). In produzione non serve: li registra già il plugin.

La copia locale non parla con internet (`WP_HTTP_BLOCK_EXTERNAL` nel `wp-config.php` generato): `dev/mu-plugins/its-dev-offline.php` risponde «servizio non disponibile» alle chiamate verso WordPress.org, così il controllo aggiornamenti si ferma in silenzio invece di stampare in admin il warning *could not establish a secure connection to WordPress.org*. `setup.sh` collega tutti i file di `dev/mu-plugins/`: dopo averne aggiunto uno basta rilanciarlo.

### Demo statica (AWS Amplify o altro hosting statico)

Per far vedere il sito senza un server PHP: con `dev/serve.sh` acceso, `python3 -I dev/export-static.py` salva tutte le pagine IT ed EN e i loro file in `.wp/static/` e crea `.wp/static.zip` (link relativi alla radice, quindi funziona su qualsiasi dominio). Le pagine restano `noindex`, perché sono una copia del sito live. Funzionano anche il filtro dei corsi e il selettore della lingua. Non funzionano la ricerca e i filtri dei documenti (`?q=`, `?tipo=`, `?biennio=`), che hanno bisogno di PHP, né l'admin.

Su AWS Amplify: *Create new app → Deploy without Git* e trascinare lo zip. Per la pagina 404, in *Hosting → Rewrites and redirects* aggiungere `/<*>` → `/404.html` con tipo `404 (Rewrite)`.

## Messa in produzione

1. Installare la cartella `wp-content/themes/its-rossellini` come nuovo tema (slug diverso da `rossellini`, il tema attuale resta intatto per tornare indietro). `style.css` dichiara `Update URI: false`: WordPress non proporrà mai come «aggiornamento» un tema di WordPress.org con lo stesso slug.
2. Tenere attivo **Rossellini Core**: il tema legge i suoi tipi di contenuto e tassonomie, e dà a Corsi, Eventi, Edizioni dei corsi e Documenti e bandi le proprie icone nel menu dell'admin (`inc/admin.php`, filtro `register_post_type_args`).
3. **Lingue**: con Polylang attivo il selettore con le bandiere compare da solo (italiano e inglese già configurati sul sito live). Le traduzioni dei testi del tema sono incluse (`en_GB`); indirizzo, orari, "come arrivare" e fonte del dato occupazionale, se modificati nel Customizer, si traducono in *Lingue → Traduzioni* (gruppo *ITS Rossellini*).
4. **Contenuti delle pagine.** Sul sito attuale quasi tutte le pagine hanno l'editor vuoto, perché il testo è scritto nel tema o nel plugin. Questo tema mostra il contenuto dell'editor, quindi prima di attivarlo i testi vanno inseriti nelle pagine (lo script di import locale mostra il risultato atteso). Le pagine interne si compongono con blocchi standard e tre stili del tema, da cui il layout sceglie da solo i componenti della home:
   - **Riassunto** della pagina → sottotitolo dell'hero; **immagine in evidenza** → foto dell'hero.
   - Paragrafo stile **Occhiello** prima di un Titolo H2 → etichetta della sezione; prima di un Titolo H3 → etichetta della card.
   - **Titolo H2** → nuova sezione (l'ancora HTML va nell'indice «In questa pagina»); **Titolo H3** + paragrafi → una card; un link nel titolo H3 rende cliccabile tutta la card. Titoli numerati (`01 · …`) → passi numerati; card che puntano tutte a corsi → card dei corsi con foto.
   - Elenco stile **Numeri**: una cifra per voce, la cifra in grassetto e poi l'etichetta (`**800** ore di tirocinio`).
   - Gruppo stile **Invito finale** (titolo, testo, pulsanti) → banda blu di chiusura; i suoi pulsanti sono anche le azioni dell'hero.
   Il layout a sezioni si attiva da solo su ogni pagina che usa questi stili o titoli H3 (`its_has_sections()` in `inc/sections.php`), anche su *Bandi e albi* sopra l'archivio dei documenti; le pagine scritte come testo semplice (note legali) e le FAQ restano come scritte. Ogni pagina interna finisce con la banda blu: l'*Invito finale* della pagina o, se manca, quello della home.
5. Assegnare i modelli: *Elenco corsi* a `/offerta-formativa/corsi/`, *Eventi* a `/eventi/`, *Bandi e documenti* a `/bandi-albi/` e `/bandi/`, *Edizioni dei corsi* a `/offerta-formativa/corsi-in-svolgimento-e-conclusi/`; impostare `/news/` come pagina degli articoli.
6. Menu: posizioni *Menu principale*, *Menu footer*, *Note legali*.
7. Home: Customizer → *Home* → video dell'hero (MP4 muto); l'immagine in evidenza della pagina iniziale fa da copertina del video. Il contenuto della pagina (loghi con link) diventa la sezione «I nostri partner». I loghi dei finanziatori nel footer sono file vettoriali ufficiali inclusi nel tema (`assets/img/funding/`, fonti in `SOURCES.md`); si cambiano con il filtro `its_funding_logos`.
8. Customizer → *Contatti e sede* e *Dati pubblicati*: indirizzo, orari, telefono, email, durata, ore, posti, dato occupazionale e fonte.
9. **Accessibilità (Legge 4/2004)**: pubblicare la dichiarazione di accessibilità su form.agid.gov.it (la Fondazione è nell'Indice PA, codice `8KY9MNU7`: accede il suo RTD) e impostare in Customizer → *Accessibilità* il suo link e l'email per le segnalazioni. Il footer di ogni pagina mostra «Dichiarazione di accessibilità» e «Segnala un problema di accessibilità». Obblighi, verifiche fatte, bozza della dichiarazione e regole per la redazione in [`ACCESSIBILITA.md`](ACCESSIBILITA.md); aggiornamento entro il 23 settembre di ogni anno.

### Campi personalizzati

Il tema legge date e file degli eventi/documenti da questi meta, nell'ordine:

| Dato | Chiavi provate |
|---|---|
| Data evento | `data_evento`, `data`, `event_date`, `data_inizio`, `start_date` |
| Orario / modalità / luogo | `ora_evento`, `orario` … / `modalita` … / `luogo`, `sede` … |
| File documento | `file_url`, `file`, `allegato`, `documento`, `pdf` (URL, ID allegato o array ACF) |
| Scadenza documento | `scadenza`, `data_scadenza`, `deadline` |

Se Rossellini Core usa nomi diversi, basta un filtro, senza toccare il tema:

```php
add_filter( 'its_rossellini_meta_keys', function ( $keys, $field ) {
	return 'event_date' === $field ? array( 'nome_campo_reale' ) : $keys;
}, 10, 2 );
```


## Sicurezza in produzione

**Nel tema.**
- Ogni dato in uscita è escapato.
- Gli unici input sono i filtri GET (`area`, `tipo`, `biennio`, `q`), tutti sanificati.
- Niente query SQL dirette, endpoint AJAX/REST o form che scrivono.
- Il JavaScript scrive solo testo (`textContent`).
- Il tema non stampa la versione di WordPress (pagine e feed) né il link di scoperta di XML-RPC.
- `Update URI: false` impedisce aggiornamenti da WordPress.org con lo stesso slug.
- Lo script inline in `<head>` passa da `wp_print_inline_script_tag()`, quindi accetta un nonce CSP via filtro.

**Sul server (fuori dal tema).** Controllo passivo del sito live del 17/11/2026, solo richieste in lettura.

Già a posto:
- elenco utenti via API bloccato (401);
- `?author=` → 404;
- `xmlrpc.php` → 403;
- niente `debug.log` e niente elenchi di cartelle;
- `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy` e `Permissions-Policy` presenti;
- cookie della lingua `Secure` + `SameSite=Lax`.

Da sistemare:
- manca `Strict-Transport-Security`;
- manca una Content Security Policy;
- `X-Powered-By` espone la versione di PHP;
- `readme.html` e `license.txt` sono pubblici;
- il meta `generator` espone la versione di Site Kit.

Il blocco da applicare è in [`server/htaccess-sicurezza.conf`](server/htaccess-sicurezza.conf): va incollato in cima al `.htaccess` della root di WordPress, fuori dai marcatori `# BEGIN WordPress`. Contiene:
- HSTS;
- la rimozione di `X-Powered-By`;
- la CSP in modalità *Report-Only*, escluse admin e login;
- il blocco di `readme.html` e `license.txt`.

Contiene solo ciò che manca sul live: gli altri header arrivano già dal server, e ridefinirli li manderebbe due volte. Il server locale applica lo stesso file (`dev/router.php`) e gira con `expose_php=0`, quindi i test locali usano gli header di produzione.

**Come applicarlo:**
- In `php.ini` impostare anche `expose_php = Off`. È un'impostazione di sistema: non si cambia da WordPress né da `.user.ini`, ma dal `php.ini` o dal pannello dell'hosting. Finché non è fatto, la riga `Header unset X-Powered-By` del blocco toglie comunque l'header.
- La CSP parte in modalità *Report-Only*: segnala nella console del browser senza bloccare nulla. Con il nuovo tema e WordPress l'abbiamo provata in modalità bloccante su tutti i tipi di pagina in locale, con zero violazioni. Dopo una o due settimane senza segnalazioni, anche con i plugin del sito (Yoast, Site Kit, moduli), si rinomina in `Content-Security-Policy` e si aggiunge `upgrade-insecure-requests`, che in modalità Report-Only il browser ignora e segnala come errore in console.
- HSTS è senza `includeSubDomains`: va aggiunto solo dopo aver verificato che ogni sottodominio risponda in HTTPS.

**Da verificare a parte:**
- il TLS, con SSL Labs (protocolli e cifrari: da qui non è stato possibile);
- la protezione dagli attacchi a forza bruta su `wp-login.php`, con limitazione dei tentativi o 2FA via plugin;
- che core e plugin siano sempre aggiornati.
