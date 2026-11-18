<p align="center">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="docs/img/logo-bianco.svg">
    <img src="docs/img/logo-blu.svg" alt="ITS Academy Roberto Rossellini" width="440">
  </picture>
</p>

# ITS Academy Roberto Rossellini — tema WordPress

Nuovo tema classico per [itsrossellini.it](https://itsrossellini.it), stesso stack del sito attuale: WordPress, PHP, CSS e JavaScript scritti a mano, senza framework né build.

Direzione visiva: **contenitori arrotondati, vetro e titoli leggeri**, sui riferimenti indicati dal committente: home su fondo blu notte con il video dell'hero a tutta larghezza, menu a pillola, card in vetro, griglia di numeri e foto, card dei corsi a immagine, card di confronto con cosa include il biennio, blocco «Parti da Roma» con globo, FAQ ad accordion e footer a colonne con il logo in filigrana e l'interruttore «Animazioni»; pagine interne chiare con un alone azzurro. Solo i colori dell'istituto (blu, blu notte, ciano, blu elettrico e loro toni) e Raleway. Regole complete in [`DESIGN.md`](DESIGN.md), contesto di prodotto in [`PRODUCT.md`](PRODUCT.md). Accessibilità e obblighi di legge in [`ACCESSIBILITA.md`](ACCESSIBILITA.md).

| | |
|---|---|
| Tema | ITS Rossellini 1.3.0, cartella e slug `its-rossellini` |
| WordPress | 6.9 o successivo, provato fino alla 7.1 |
| PHP | 8.1 o successivo (sul live: PHP 8.4, Apache) |
| Plugin necessari | **Rossellini Core** (tipi di contenuto), **Polylang** (italiano e inglese) |
| Plugin del live compatibili | Yoast SEO, Site Kit by Google, Cookie Notice, WP Consent API |
| Pacchetto di produzione | `python3 -I dev/package.py` → `dist/its-rossellini-<versione>.zip` (vedi [Messa in produzione](#messa-in-produzione)) |
| Build | nessuna: si modificano direttamente i file CSS, JS e PHP |
| Licenza | GPL-2.0-or-later; font Raleway con licenza OFL |

## Indice

- [Documentazione del progetto](#documentazione-del-progetto)
- [Avvio con un doppio clic](#avvio-con-un-doppio-clic)
- [Primo avvio, per chi riceve il progetto](#primo-avvio-per-chi-riceve-il-progetto)
- [Struttura](#struttura)
- [Sviluppo locale](#sviluppo-locale)
- [Come funziona il tema](#come-funziona-il-tema)
- [Lavorare con git](#lavorare-con-git)
- [Messa in produzione](#messa-in-produzione)
- [Sicurezza in produzione](#sicurezza-in-produzione)
- [Problemi comuni](#problemi-comuni)
- [Punti aperti](#punti-aperti)
- [Crediti e licenze](#crediti-e-licenze)

## Documentazione del progetto

| File | Cosa contiene | Quando leggerlo |
|---|---|---|
| `README.md` | Installazione, sviluppo, messa in produzione, sicurezza | Sempre, per primo |
| [`DESIGN.md`](DESIGN.md) | Sistema visivo: colori, tipografia, layout, componenti, animazioni | Prima di toccare grafica, CSS o animazioni |
| [`PRODUCT.md`](PRODUCT.md) | Utenti, obiettivi, vincoli del marchio, decisioni prese | Prima di aggiungere pagine o funzioni |
| [`ACCESSIBILITA.md`](ACCESSIBILITA.md) | Legge Stanca, stato del tema, bozza della dichiarazione, regole per la redazione, PDF | Prima della messa online e ogni anno entro il 23 settembre |
| [`server/htaccess-sicurezza.conf`](server/htaccess-sicurezza.conf) | Header di sicurezza da applicare al server | Alla messa in produzione |
| [`wp-content/themes/its-rossellini/assets/img/funding/SOURCES.md`](wp-content/themes/its-rossellini/assets/img/funding/SOURCES.md) | Fonti ufficiali dei loghi dei finanziatori | Se cambiano i loghi nel footer |
| [`phpcs.xml.dist`](phpcs.xml.dist) | Regole di qualità del codice PHP | Prima di ogni commit |

## Avvio con un doppio clic

Per vedere il sito in locale senza usare il terminale, nella cartella del progetto:

| Sistema | File | Cosa fa la prima volta |
|---|---|---|
| Windows 10 e 11 | `avvia-windows.cmd` | Se mancano, propone di installare Git per Windows e Python con winget. Scarica PHP 8.4 dentro `.wp\php`, senza installarlo nel sistema, e lo verifica con l'impronta pubblicata da php.net |
| macOS | `avvia-mac.command` | Se mancano PHP, Python o git, propone di installarli con Homebrew (o gli strumenti di Xcode per git) |
| Linux | `avvia-linux.sh` | Se mancano, propone di installare i pacchetti con `apt-get` (Debian, Ubuntu); su altre distribuzioni dice quali servono |

Poi, su tutti i sistemi:
1. la prima volta installa WordPress e scarica i contenuti del sito, in qualche minuto (`dev/setup.sh`);
2. avvia il server su una porta libera, a partire dalla 8080;
3. apre il browser sul sito. L'admin è su `/wp-admin`, con utente `admin` e password `admin`, valide solo in locale.

Dalla seconda volta il sito parte in pochi secondi. Se è già acceso, il file apre solo il browser. Per fermarlo basta chiudere la finestra.

Nulla viene installato senza conferma. Il sito risponde solo da questo PC. Per provarlo dal telefono sulla stessa Wi-Fi, da terminale: `avvia-windows.cmd --rete` (oppure `./avvia-mac.command --rete`, `./avvia-linux.sh --rete`). Con `--aggiorna` riscarica prima i contenuti dal sito live.

Al primo doppio clic il sistema può chiedere conferma:
- **Windows**, se il progetto è stato scaricato come zip da GitHub: *Windows ha protetto il PC* → *Ulteriori informazioni* → *Esegui comunque*;
- **macOS**: tasto destro sul file → *Apri* → *Apri*.

Sono script leggibili, non programmi `.exe` compilati: si possono controllare prima di eseguirli, non vanno ricompilati a ogni modifica e l'antivirus non li blocca come eseguibili sconosciuti. La logica comune è in `dev/avvia.sh`, la preparazione di Windows in `dev/windows/avvia.ps1`.

## Primo avvio, per chi riceve il progetto

1. **Accesso al codice.** Chiedere l'invito come collaboratore del repository GitHub, poi:
   ```bash
   git clone https://github.com/giubboxdzn/ITS-WEBSITE.git
   cd ITS-WEBSITE
   ```
2. **Requisiti.** PHP 8.1 o successivo con `pdo_sqlite`, git e Python 3 (su Windows vedi [sotto](#windows-git-bash)). Non serve MySQL.
3. **Installazione locale**: doppio clic sul file del proprio sistema ([sopra](#avvio-con-un-doppio-clic)), oppure da terminale (la prima volta qualche minuto: scarica WordPress e i contenuti pubblici del sito):
   ```bash
   dev/setup.sh
   dev/serve.sh
   ```
4. Aprire **http://localhost:8080**. Per l'admin: **http://localhost:8080/wp-admin** con utente `admin` e password `admin`, valide solo in locale.
5. **Dove si lavora.** Solo in `wp-content/themes/its-rossellini/`. WordPress legge il tema direttamente dal repository, quindi una modifica salvata si vede ricaricando la pagina, senza riavviare nulla. CSS e JavaScript hanno come versione la data del file: il browser scarica sempre quelli nuovi.
6. **Prima di ogni commit**: `phpcs` (vedi [Qualità del codice](#qualità-del-codice)) e i test, `python3 -I dev/test/run.py` (vedi [Test](#test)). Per le modifiche grafiche, anche un'occhiata alle pagine toccate, in italiano e in inglese, da desktop e da telefono.

## Struttura

```
wp-content/themes/its-rossellini/   il tema (da installare in produzione)
  style.css                         intestazione del tema (nome, versione, requisiti); lo stile è in assets/css/main.css
  readme.txt                        dentro lo zip: requisiti, plugin, installazione, aggiornamento, changelog
  functions.php                     avvio: supporti, menu, formati immagine, CSS e JS
  front-page.php                    home (video, corsi, academy, percorso, news, partner)
  page.php                          pagina interna: a sezioni se usa gli stili del tema, altrimenti testo
  single-corso.php                  scheda corso
  single.php                        articolo (news)
  single-evento.php                 evento
  single-documento.php              documento o bando
  index.php                         elenco news, archivi e risultati di ricerca
  404.php, searchform.php           pagina non trovata, modulo di ricerca
  header.php, footer.php            barra in alto (menu, ricerca, lingue) e footer (colonne, finanziatori, interruttore «Animazioni»)
  templates/                        modelli di pagina: Elenco corsi, Eventi, Bandi e documenti, Edizioni dei corsi
  template-parts/                   card corso, griglia corsi con filtro, card news, riga datata, testata pagina,
                                    sezioni delle pagine interne, FAQ, banda finale blu, pagine figlie
  inc/
    helpers.php                     funzioni di supporto: URL delle pagine, meta dei contenuti, loghi dei finanziatori
    content.php                     query condivise: eventi, documenti con filtri, corso scelto per la preiscrizione
    sections.php                    legge il contenuto dell'editor come sezioni e card; stili dei blocchi del tema
    prose.php                       pagine di testo (articoli, eventi, documenti, note legali, FAQ)
    customizer.php                  impostazioni del Customizer (contatti, dati pubblicati, video, accessibilità)
    icons.php                       icone SVG, bandiere e selettore della lingua
    admin.php                       icone del menu admin per Corsi, Eventi, Edizioni e Documenti
    requirements.php                avviso nell'admin se manca un plugin necessario, con «Installa» o «Attiva»
  languages/                        traduzioni del tema: its-rossellini.pot, en_GB.po/.mo/.l10n.php
  assets/css/main.css               tutto lo stile (token in :root)
  assets/js/main.js                 header, video dell'hero, menu mobile, filtro corsi, animazioni legate all'interruttore «Animazioni» (miglioramento progressivo)
  assets/css/editor.css             stile dell'editor a blocchi
  assets/fonts/                     Raleway variabile, normale e corsivo (OFL), self-hosted
  assets/img/                       logo, loghi dei finanziatori (funding/, con le fonti in SOURCES.md)
avvia-windows.cmd, avvia-mac.command, avvia-linux.sh
                                    avvio del sito in locale con un doppio clic (vedi «Avvio con un doppio clic»)
dev/                                ambiente locale (non va in produzione); dev/i18n/build.py compila le traduzioni;
                                    dev/package.py crea il pacchetto di produzione; dev/testi-pagine.php i testi per il live;
                                    dev/test/ i test nel browser
dist/                               creata dagli script, ignorata da git: lo zip del tema (dev/package.py)
                                    e i testi delle pagine per il live (dev/testi-pagine.php)
server/htaccess-sicurezza.conf      header di sicurezza per il server di produzione
docs/img/                           logo per questo README (copie del logo del tema, in blu e in bianco)
.claude/skills/                     skill per Claude Code (versioni bloccate in skills-lock.json):
                                    design: impeccable, web-design-guidelines, ux-heuristics
                                    codice WordPress: wp-guard
                                    prestazioni e qualità: web-quality-audit, performance, core-web-vitals
                                    accessibilità: accessibility (WCAG 2.2), pdf-accessibility (PDF/UA), a11y-core
                                    legge: accessibilita-siti-app-l4-2004 e verifica-accessibilita-dichiarazione-agid (Legge Stanca, AgID),
                                      kb-legal-compliance-mapping e accessibility-statement (EAA, EN 301 549)
                                    sicurezza: performing-ssl-tls-security-assessment (TLS, con sslyze)
                                    ricerca di altre skill: find-skills
.impeccable/                        configurazione dello strumento di design Impeccable
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

### Windows con WSL (Ubuntu)

In WSL (`bash` da PowerShell) si è in Linux: PHP e Python vanno installati dentro Ubuntu (`sudo apt install php-cli php-sqlite3 php-mbstring php-gd php-curl python3`). La via provata su Windows resta Git Bash. Con WSL:

- **git chiede utente e password.** Il git di WSL non vede le credenziali salvate in Windows, e GitHub non accetta la password dell'account. Si usa il git di Windows (`git.exe pull`) oppure si collega una volta il gestore credenziali di Windows:
  ```bash
  git config --global credential.helper "/mnt/c/Program\ Files/Git/mingw64/bin/git-credential-manager.exe"
  ```
- **Niente `sudo` per git o per gli script**: root ha una configurazione sua e lascerebbe file di sua proprietà.

### Cosa fanno gli script di `dev/`

| Script | Cosa fa |
|---|---|
| `avvia.sh` | Avvio in un passo: controlla i requisiti (e propone di installarli), la prima volta lancia `setup.sh`, avvia `serve.sh` su una porta libera e apre il browser. `--rete`, `--aggiorna` |
| `windows/avvia.ps1` | Solo Windows: prepara Git Bash, PHP portatile in `.wp\php` e Python, poi lancia `avvia.sh`. Usato da `avvia-windows.cmd` |
| `setup.sh` | Installa WordPress, SQLite e Polylang in `.wp/`, crea `wp-config.php`, collega tema e mu-plugin, importa i contenuti. Si può rilanciare: rifà solo ciò che manca. `--no-seed` salta l'import |
| `serve.sh` | Avvia il server PHP integrato (porta 8080, raggiungibile in rete locale) |
| `router.php` | Instrada le richieste del server locale e applica gli stessi header di `server/htaccess-sicurezza.conf` |
| `install.php`, `languages.php` | Installano WordPress, attivano il tema, creano le lingue di Polylang |
| `polylang-autoload.php` | Genera i file di autoload che mancano nel sorgente GitHub di Polylang |
| `seed/fetch.py`, `seed/import.php` | Scaricano (solo GET) i contenuti pubblici del sito live e li importano nel database locale |
| `mu-plugins/its-dev-content-model.php` | Registra in locale i tipi di contenuto di Rossellini Core |
| `mu-plugins/its-dev-offline.php` | Ferma in silenzio le chiamate a WordPress.org, perché la copia locale non va su internet |
| `i18n/build.py` | Aggiorna `.pot` e `.po` e compila `.mo` e `.l10n.php` |
| `export-static.py` | Esporta il sito locale in HTML statico per una demo |
| `testi-pagine.php` | Prepara in `dist/testi-pagine/` i testi da inserire nelle pagine del live (vedi [Messa in produzione](#passaggi), passaggio 4) |
| `test/run.py` | Test nel browser (vedi [Test](#test)) |
| `package.py` | Controlla il tema e crea lo zip di produzione in `dist/` (vedi [Messa in produzione](#messa-in-produzione)) |

### Contenuti di prova

`dev/seed/fetch.py` legge (solo GET) l'API REST e le pagine pubbliche di itsrossellini.it, in italiano e in inglese, e salva tutto in `.wp/seed/` (ignorato da git). `dev/seed/import.php` lo importa nel database locale. Non scrive nulla sul sito live. Se `data.json` esiste già senza la parte inglese, `setup.sh` scarica solo quella (`fetch.py --en-only`, qualche minuto); se le pagine sono in un formato precedente, riscarica solo le pagine (`fetch.py --pages-only`, un paio di minuti). Le pagine interne del live diventano blocchi dell'editor con gli stili del tema (occhielli, numeri, card, invito finale), con lead (riassunto) e foto (immagine in evidenza).

La cartella `.wp/` (WordPress, database, foto scaricate) non è nel repository: ognuno la ricrea con `dev/setup.sh`.

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

Per il JavaScript non c'è un linter configurato: almeno `node --check wp-content/themes/its-rossellini/assets/js/main.js` per la sintassi, e la console del browser senza errori (i test lo controllano su ogni pagina).

### Test

Test in un browser vero (Chromium, con Playwright per Python) sul WordPress locale:

```bash
python3 -I dev/test/run.py --installa   # una volta sola: crea .wp/venv con Playwright e Chromium (circa 150 MB)
python3 -I dev/test/run.py              # tutti i test, circa un minuto e mezzo
python3 -I dev/test/run.py animazioni   # solo alcune serie: pagine, animazioni, admin, pacchetto
```

Su Windows al posto di `python3` si usa `py` (oppure `python`), da Git Bash o dal prompt dei comandi.

Il runner avvia da solo un server PHP su una porta libera e lo spegne alla fine; con `--url http://localhost:8080` usa invece un sito locale già acceso. Serve il WordPress locale (`dev/setup.sh` o un file `avvia-*`, una volta). Nulla viene inviato al sito live.

| Serie | Cosa controlla |
|---|---|
| `pagine` | Ogni tipo di pagina, in italiano e in inglese, trovata nei menu e nei contenuti: home, pagine del menu, scheda corso, evento, documento, articolo, ricerca, 404. Per ciascuna: stato della risposta, nessun errore PHP o JavaScript, un solo h1, lingua, titolo, immagini con `alt`, «Vai al contenuto» come primo Tab, nessuno scorrimento laterale a 320 px. In più: i link di header e footer non portano a pagine rotte |
| `animazioni` | Le animazioni allo scroll come descritte in [`DESIGN.md`](DESIGN.md#motion): entrata scendendo, uscita in vista risalendo con le stesse durate, numeri che salgono e tornano a zero, logo del footer, niente nascosto a scroll fermo; con «riduci movimento» il sito è statico |
| `admin` | L'avviso dei plugin: assente con tutto attivo; con Polylang disattivato compare con «Attiva», e il pulsante lo riattiva. Polylang viene comunque riattivato alla fine, nel database locale |
| `pacchetto` | Se il tema non ha modifiche non committate, crea lo zip con `dev/package.py` e ne controlla il contenuto |

Alla fine: ✓ se tutto è passato, altrimenti l'elenco dei controlli falliti e le schermate in `.wp/test-results/`.

### Lingue (Polylang)

Come sul sito live: italiano predefinito senza prefisso, inglese su `/en/`. `setup.sh` prende **Polylang 3.9.2** da GitHub (`.wp/polylang`) e genera con `dev/polylang-autoload.php` i file che la release ufficiale include (autoload e liste dei moduli), senza Composer; `dev/languages.php` crea le lingue e le opzioni; l'import assegna la lingua a ogni contenuto, collega le traduzioni IT↔EN (dagli `hreflang` del sito live) e crea i menu inglesi.

Il selettore con le bandiere è nell'header (bandiera + codice) e nel footer (bandiera + nome, come nel riferimento): porta alla stessa pagina nell'altra lingua o, se manca la traduzione, alla home di quella lingua. I testi del tema sono tradotti in `languages/en_GB.po`; dopo averli modificati: `python3 -I dev/i18n/build.py` (aggiorna `.pot`, `.po` e compila `.mo` e `.l10n.php`).

**Aggiungere un testo al tema:** scriverlo in italiano dentro `__()` o `esc_html_e()` con dominio `its-rossellini`, lanciare `dev/i18n/build.py`, scrivere la traduzione nella nuova voce di `languages/en_GB.po`, rilanciare `build.py` e committare tutti i file di `languages/`.

`dev/mu-plugins/its-dev-content-model.php` registra in locale gli stessi tipi di contenuto di Rossellini Core (`corso`, `evento`, `edizione_corso`, `documento` e relative tassonomie). In produzione non serve: li registra già il plugin.

La copia locale non parla con internet (`WP_HTTP_BLOCK_EXTERNAL` nel `wp-config.php` generato): `dev/mu-plugins/its-dev-offline.php` risponde «servizio non disponibile» alle chiamate verso WordPress.org, così il controllo aggiornamenti si ferma in silenzio invece di stampare in admin il warning *could not establish a secure connection to WordPress.org*. `setup.sh` collega tutti i file di `dev/mu-plugins/`: dopo averne aggiunto uno basta rilanciarlo.

### Demo statica (AWS Amplify o altro hosting statico)

Per far vedere il sito senza un server PHP: con `dev/serve.sh` acceso, `python3 -I dev/export-static.py` salva tutte le pagine IT ed EN e i loro file in `.wp/static/` e crea `.wp/static.zip` (link relativi alla radice, quindi funziona su qualsiasi dominio). Le pagine restano `noindex`, perché sono una copia del sito live. Funzionano anche il filtro dei corsi e il selettore della lingua. Non funzionano la ricerca e i filtri dei documenti (`?q=`, `?tipo=`, `?biennio=`), che hanno bisogno di PHP, né l'admin.

Su AWS Amplify: *Create new app → Deploy without Git* e trascinare lo zip. Per la pagina 404, in *Hosting → Rewrites and redirects* aggiungere `/<*>` → `/404.html` con tipo `404 (Rewrite)`.

Lo zip va rigenerato e ricaricato dopo ogni modifica che si vuole mostrare nella demo.

## Come funziona il tema

### Impostazioni nel Customizer

*Aspetto → Personalizza*. I testi segnati con † si traducono in *Lingue → Traduzioni*, gruppo *ITS Rossellini*.

| Sezione | Campo | Valore iniziale |
|---|---|---|
| Home | Video della home (MP4, senza audio) | nessuno; la copertina è l'immagine in evidenza della pagina iniziale |
| Contatti e sede | Indirizzo † | Via Tiburtina 691, Roma |
| | Come arrivare † | Metro B Pietralata o Monti Tiburtini |
| | Orari † | Lun–Ven 9:30–13:30 / 14:30–18:30 |
| | Telefono | 06 5576493 |
| | Email | info@itsrossellini.it |
| | Instagram, X (URL) | profili ufficiali |
| Dati pubblicati | Durata (anni), ore in aula e laboratorio, ore di tirocinio, posti per corso | 2, 1.000, 800+, 28 |
| | Teatro di posa, superficie (vuoto: la cifra non si mostra) | 360 m² |
| | Diplomati occupati entro 12 mesi | 87% |
| | Fonte del dato occupazionale † | Dato pubblicato da ITS Rossellini |
| Accessibilità | Dichiarazione di accessibilità (URL) | vuoto: pagina con slug `dichiarazione-di-accessibilita`, se esiste |
| | Email per le segnalazioni | vuoto: l'email dei contatti |

I numeri vanno aggiornati qui quando cambiano i bandi: nessun dato è scritto nel codice.

### Parametri negli indirizzi

| Parametro | Dove | Effetto |
|---|---|---|
| `?area=` | Elenco corsi | Filtra per area tecnologica; funziona anche senza JavaScript |
| `?tipo=`, `?biennio=`, `?q=` | Bandi e documenti | Filtri e ricerca nei documenti |
| `?corso=` | Preiscrizione (`/iscrizioni/`) | Mostra il corso scelto dalla sua scheda e lo aggiunge all'oggetto delle email |

Tutti i valori sono sanificati; nessuno scrive dati.

### Filtri per estendere il tema

Si usano da un plugin o da un mu-plugin, senza modificare il tema.

| Filtro | Serve a |
|---|---|
| `its_rossellini_meta_keys` | Cambiare i nomi dei campi personalizzati letti per date, luoghi e file (vedi [Campi personalizzati](#campi-personalizzati)) |
| `its_funding_logos` | Cambiare i loghi dei finanziatori nel footer |
| `its_has_sections` | Forzare o escludere il layout a sezioni su una pagina (`$editorial`, `$post_id`) |
| `wp_inline_script_attributes` (core) | Aggiungere il nonce CSP allo script inline in `<head>` |

### Animazioni

Tutto il movimento è in `assets/js/main.js` (modulo `Motion`) e nella sezione *Scroll reveals* di `assets/css/main.css`. Le regole di design sono in [`DESIGN.md`](DESIGN.md#motion).

- Esiste solo con l'interruttore «Animazioni» acceso (footer). Senza una scelta del visitatore decide l'impostazione «riduci movimento» del sistema; cambiare l'interruttore ricarica la pagina dall'alto.
- Scendendo, titoli, card, righe e numeri entrano a gruppi scaglionati. Risalendo, ciò che scende nell'ultimo quarto dello schermo esce con la stessa animazione al contrario; quando lo scroll si ferma, ciò che è sullo schermo rientra.
- Senza JavaScript non è nascosto nulla.

Valori da regolare in `main.js`:

| Costante | Valore | Significato |
|---|---|---|
| `STEP` | 80 ms | Ritardo tra un elemento e il successivo in un gruppo (al massimo 8 passi) |
| `ENTER` | 0.08 | Scendendo, gli elementi entrano sopra l'ultimo 8% dello schermo |
| `EXIT` | 0.25 | Risalendo, gli elementi escono quando scendono nell'ultimo 25% |
| soglia di direzione | 24 px | Spostamento minimo per cambiare direzione (evita sfarfallii) |
| pausa | 400 ms | Dopo quanto, a scroll fermo, ciò che è sullo schermo rientra |

Le durate (0,8 s per la dissolvenza, 1 s per lo spostamento, ecc.) sono nel CSS e valgono sia in entrata sia in uscita.

## Lavorare con git

- Il branch principale del repository è `ccr-bb43ad8e-sn1kna`. Consigliato rinominarlo in `main` (*GitHub → Settings → Branches*). Dopo, in ogni copia locale:
  ```bash
  git branch -m ccr-bb43ad8e-sn1kna main
  git fetch origin
  git branch -u origin/main main
  git remote set-head origin -a
  ```
- Per ogni modifica un branch dedicato (`git switch -c nome-modifica`), commit piccoli con un titolo che dica cosa cambia, poi push e pull request.
- Prima di iniziare a lavorare: `git pull`. Con `dev/serve.sh` acceso non serve riavviare nulla.
- Gli script `.sh` restano con fine riga LF anche su Windows (`.gitattributes`): non convertirli, altrimenti Git Bash non li esegue.
- Non vanno nel repository: `.wp/` (WordPress locale), `.claude/settings.local.json`, password o chiavi di qualsiasi tipo.

## Messa in produzione

### Prima di iniziare

- **Copia di sicurezza** completa del sito (file e database), per esempio con JetBackup dal pannello dell'hosting.
- Se l'hosting offre un **ambiente di prova** (staging), fare tutti i passaggi prima lì.
- Scegliere un orario con poco traffico, lontano dalle scadenze dei bandi.

### Plugin e dipendenze

Il tema **non ha dipendenze da scaricare**: niente Composer, npm o build. Font, icone e loghi sono dentro lo zip, e il tema non chiama servizi esterni. Le uniche dipendenze sono plugin di WordPress:

| Plugin | Serve? | Da dove | Senza |
|---|---|---|---|
| **Rossellini Core** | Sì | Già installato sul sito attuale. Non è su WordPress.org: per un sito nuovo si copia la sua cartella da `wp-content/plugins` del sito attuale (FTP o file manager dell'hosting) | Corsi, eventi, edizioni e documenti non si vedono |
| **Polylang** | Sì, per l'inglese | Già installato sul sito attuale; altrimenti *Plugin → Aggiungi nuovo* o il pulsante nell'avviso del tema | Il sito resta solo in italiano, senza selettore della lingua |
| Yoast SEO, Site Kit by Google, Cookie Notice, WP Consent API | No | Già sul sito attuale | Il tema funziona lo stesso |

WordPress controlla da solo i requisiti scritti in `style.css` (WordPress 6.9 e PHP 8.1 o successivi) e rifiuta lo zip se non sono soddisfatti. Per i plugin ci pensa il tema: dopo l'attivazione, se Rossellini Core o Polylang mancano o sono disattivati, l'admin mostra un avviso con il pulsante **Installa** (Polylang, da WordPress.org) o **Attiva**. Per Rossellini Core, se non è installato, l'avviso spiega da dove copiarlo. Nessun plugin viene installato senza un clic. L'avviso è in `inc/requirements.php`.

**Sito nuovo o di prova.** Il modo più semplice è clonare il sito attuale (ambiente di prova dell'hosting, o ripristino di una copia JetBackup su un altro dominio): così arrivano anche i plugin, le lingue di Polylang, i contenuti e le impostazioni. Poi si carica lo zip del tema. Partendo da un WordPress vuoto, l'ordine è: Rossellini Core → Polylang (lingue: italiano predefinito, inglese su `/en/`) → tema → plugin facoltativi.

### Preparare il pacchetto

Dalla cartella del repository, dopo aver fatto commit di tutte le modifiche:

```bash
python3 -I dev/package.py
```

Crea `dist/its-rossellini-<versione>.zip` (oggi `its-rossellini-1.3.0.zip`, circa 1,3 MB) e accanto il file `.sha256` con l'impronta, utile per verificare di caricare proprio quello. Lo zip esce dall'ultimo commit, quindi contiene solo i file del tema nel repository: niente `dev/`, documentazione o file locali. Prima di crearlo lo script controlla che:
- il tema non abbia modifiche non committate (non finirebbero nello zip);
- la versione sia la stessa in `style.css` e in `readme.txt` (*Stable tag*), con la sua voce nel changelog;
- le traduzioni siano compilate e aggiornate;
- i file PHP e `main.js` non abbiano errori di sintassi.

Poi verifica lo zip stesso: una sola cartella `its-rossellini/`, i file obbligatori presenti (`style.css`, `functions.php`, `index.php`, `screenshot.png`, `readme.txt`), nessun file da non pubblicare. Se qualcosa non va, si ferma e dice cosa sistemare.

Dentro lo zip c'è `readme.txt`, con requisiti, plugin, installazione, passaggi dopo l'attivazione, aggiornamento e changelog: chi riceve solo lo zip ha le istruzioni essenziali.

Senza Python, lo stesso zip (senza i controlli) si ottiene con:

```bash
git archive --format=zip --prefix=its-rossellini/ -o its-rossellini.zip HEAD:wp-content/themes/its-rossellini
```

### Passaggi

1. Installare il tema: *Aspetto → Temi → Aggiungi nuovo tema → Carica tema*, con `dist/its-rossellini-<versione>.zip`. Lo slug è diverso da `rossellini`: il tema attuale resta intatto per tornare indietro. `style.css` dichiara `Update URI: false`: WordPress non proporrà mai come «aggiornamento» un tema di WordPress.org con lo stesso slug.
2. Tenere attivi **Rossellini Core** e **Polylang** (vedi [Plugin e dipendenze](#plugin-e-dipendenze)). Rossellini Core: il tema legge i suoi tipi di contenuto e tassonomie, e dà a Corsi, Eventi, Edizioni dei corsi e Documenti e bandi le proprie icone nel menu dell'admin (`inc/admin.php`, filtro `register_post_type_args`).
3. **Lingue**: con Polylang attivo il selettore con le bandiere compare da solo (italiano e inglese già configurati sul sito live). Le traduzioni dei testi del tema sono incluse (`en_GB`); indirizzo, orari, "come arrivare" e fonte del dato occupazionale, se modificati nel Customizer, si traducono in *Lingue → Traduzioni* (gruppo *ITS Rossellini*).
4. **Contenuti delle pagine.** Sul sito attuale quasi tutte le pagine hanno l'editor vuoto, perché il testo è scritto nel tema o nel plugin. Questo tema mostra il contenuto dell'editor, quindi prima di attivarlo i testi vanno inseriti nelle pagine (lo script di import locale mostra il risultato atteso). Le pagine interne si compongono con blocchi standard e tre stili del tema, da cui il layout sceglie da solo i componenti della home:
   - **Riassunto** della pagina → sottotitolo dell'hero; **immagine in evidenza** → foto dell'hero.
   - Paragrafo stile **Occhiello** prima di un Titolo H2 → etichetta della sezione; prima di un Titolo H3 → etichetta della card.
   - **Titolo H2** → nuova sezione (l'ancora HTML va nell'indice «In questa pagina»); **Titolo H3** + paragrafi → una card; un link nel titolo H3 rende cliccabile tutta la card. Titoli numerati (`01 · …`) → passi numerati; card che puntano tutte a corsi → card dei corsi con foto.
   - Elenco stile **Numeri**: una cifra per voce, la cifra in grassetto e poi l'etichetta (`**800** ore di tirocinio`).
   - Gruppo stile **Invito finale** (titolo, testo, pulsanti) → banda blu di chiusura; i suoi pulsanti sono anche le azioni dell'hero.
   Il layout a sezioni si attiva da solo su ogni pagina che usa questi stili o titoli H3 (`its_has_sections()` in `inc/sections.php`), anche su *Bandi e albi* sopra l'archivio dei documenti; le pagine scritte come testo semplice (note legali) e le FAQ restano come scritte. Ogni pagina interna finisce con la banda blu: l'*Invito finale* della pagina o, se manca, quello della home.
   **I testi sono già pronti nel sito locale.** Per averli tutti insieme:
   ```bash
   php dev/testi-pagine.php        # crea dist/testi-pagine/index.html
   ```
   Si apre nel browser. Mostra tutte le pagine, italiane e inglesi, ognuna con:
   - il link diretto all'editor di quella pagina sul live;
   - il codice dei blocchi, il riassunto e il nome dell'immagine in evidenza, ciascuno con il pulsante «Copia»;
   - il modello da assegnare, se ne serve uno;
   - una spunta «Fatto», che resta salvata in quel browser.

   Le pagine sono divise in «Da inserire», «Già sul live» (il testo c'è già: basta controllarlo) e «Solo il modello» (mostrano un elenco). Se una pagina usa file della libreria media (loghi dei partner, PDF), la scheda li elenca, da controllare dopo l'incollo. Nulla viene inviato al live: si incolla a mano, pagina per pagina.
5. Assegnare i modelli: *Elenco corsi* a `/offerta-formativa/corsi/`, *Eventi* a `/eventi/`, *Bandi e documenti* a `/bandi-albi/` e `/bandi/`, *Edizioni dei corsi* a `/offerta-formativa/corsi-in-svolgimento-e-conclusi/`; impostare `/news/` come pagina degli articoli.
6. Menu: posizioni *Menu principale*, *Menu footer*, *Note legali*.
7. Home: Customizer → *Home* → video dell'hero (MP4 muto); l'immagine in evidenza della pagina iniziale fa da copertina del video. Il contenuto della pagina (loghi con link) diventa la sezione «I nostri partner». I loghi dei finanziatori nel footer sono file vettoriali ufficiali inclusi nel tema (`assets/img/funding/`, fonti in `SOURCES.md`); si cambiano con il filtro `its_funding_logos`.
8. Customizer → *Contatti e sede* e *Dati pubblicati*: indirizzo, orari, telefono, email, durata, ore, posti, dato occupazionale e fonte.
9. **Accessibilità (Legge 4/2004)**: pubblicare la dichiarazione di accessibilità su form.agid.gov.it (la Fondazione è nell'Indice PA, codice `8KY9MNU7`: accede il suo RTD) e impostare in Customizer → *Accessibilità* il suo link e l'email per le segnalazioni. Il footer di ogni pagina mostra «Dichiarazione di accessibilità» e «Segnala un problema di accessibilità». Obblighi, verifiche fatte, bozza della dichiarazione e regole per la redazione in [`ACCESSIBILITA.md`](ACCESSIBILITA.md); aggiornamento entro il 23 settembre di ogni anno.
10. **Anteprima prima di attivare**: *Aspetto → Temi → ITS Rossellini → Anteprima dal vivo* mostra il sito con il nuovo tema senza cambiarlo per i visitatori. Quando tutto torna, *Attiva*.
11. **Server**: applicare `server/htaccess-sicurezza.conf` (vedi [Sicurezza in produzione](#sicurezza-in-produzione)).

### Controlli dopo l'attivazione

- [ ] Home, elenco corsi, una scheda corso, news, un articolo, eventi, bandi e albi, un documento, FAQ, preiscrizione, contatti, ricerca e una pagina inesistente (404).
- [ ] Le stesse pagine in inglese (`/en/`) e il selettore della lingua.
- [ ] Da telefono: menu, ricerca, filtro corsi, video dell'hero.
- [ ] Interruttore «Animazioni» acceso e spento.
- [ ] Link «Dichiarazione di accessibilità» e «Segnala un problema» nel footer.
- [ ] Yoast: titoli e descrizioni delle pagine ancora presenti (sorgente della pagina).
- [ ] Svuotare la cache, se il sito ne usa una (plugin o hosting).
- [ ] Console del browser senza errori.

### Tornare indietro

*Aspetto → Temi → attivare il tema precedente (`rossellini`)*. I contenuti non cambiano. Unica differenza: i testi inseriti nelle pagine al passaggio 4 restano nell'editor; il vecchio tema li ignora.

### Aggiornare il tema in seguito

1. Aumentare la versione in `style.css` (`Version`) e in `readme.txt` (`Stable tag`), per esempio 1.2.0 → 1.3.0, e aggiungere la voce nel changelog di `readme.txt`. Numeri: il terzo per le correzioni, il secondo per le novità, il primo per i cambiamenti che richiedono interventi sui contenuti.
2. Commit, poi `python3 -I dev/package.py`.
3. Caricare il nuovo zip in *Aspetto → Temi → Aggiungi nuovo tema → Carica tema*: WordPress propone *Sostituisci quello attuale con quello caricato*.
4. Svuotare la cache del sito, se presente. CSS e JS cambiano versione da soli, quindi i browser li riscaricano.

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
- Gli unici input sono i parametri GET (`area`, `tipo`, `biennio`, `q`, `corso`), tutti sanificati.
- Niente query SQL dirette, endpoint AJAX/REST o form che scrivono.
- Il JavaScript scrive solo testo (`textContent`).
- Il tema non stampa la versione di WordPress (pagine e feed) né il link di scoperta di XML-RPC.
- `Update URI: false` impedisce aggiornamenti da WordPress.org con lo stesso slug.
- Lo script inline in `<head>` passa da `wp_print_inline_script_tag()`, quindi accetta un nonce CSP via filtro.
- Il tema non aggiunge richieste a servizi esterni: font, icone e loghi sono nel tema.

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

### Dominio, DNS e posta

Verifica passiva del 18/11/2026: registri pubblici dei certificati HTTPS dal 2017, record DNS, registri degli indirizzi IP, link nei contenuti del sito. Niente scansione di porte o di indirizzi.

**Cosa gira sul dominio.** Nessun'altra piattaforma oltre a queste: i certificati emessi dal 2017 coprono solo questi nomi, e non ci sono certificati jolly (`*.itsrossellini.it`).

| Indirizzo | Cos'è | Dove |
|---|---|---|
| `itsrossellini.it`, `www` | Il sito WordPress | Keliweb, server `web116.keliweb.com` |
| `iscrizioni.itsrossellini.it` | Portale iscrizioni, Laravel con pannello Backpack (certificato dal 13/05/2026) | Linode/Akamai |
| Posta `@itsrossellini.it` | Google Workspace (MX di Google, firma DKIM `google._domainkey` attiva) | Google |
| DNS | Zona del dominio | Register.it |

La vecchia installazione `/its2024/` non c'è più (404).

**Da correggere nel DNS** (pannello Register.it):

| Record | Oggi | Da fare |
|---|---|---|
| SPF (`TXT` su `itsrossellini.it`) | `v=spf1 include:spf.webapps.net ip4:185.221.174.14 ~all`: autorizza solo i server Keliweb, non Google, che invece spedisce la posta | Aggiungere `include:_spf.google.com`. L'IP `185.221.174.14` è un altro server Keliweb (`web74`): toglierlo se non spedisce più. Un solo record SPF, entro 10 ricerche DNS |
| DMARC (`TXT` su `_dmarc.itsrossellini.it`) | Assente: chiunque può fingersi `@itsrossellini.it` con meno filtri | Aggiungere `v=DMARC1; p=none; rua=mailto:<casella per i report>; fo=1`; dopo 2–4 settimane di report puliti passare a `p=quarantine` |
| `pop.itsrossellini.it` | CNAME verso `mail.register.it`, la vecchia posta Register.it | Togliere, se nessuno la usa più |
| `_autodiscover._tcp` (`SRV`) | Verso `ms-it.securemail.pro`, posta Register.it: Outlook può configurarsi lì invece che su Google | Togliere |
| `ftp.itsrossellini.it` | Punta al server del sito | Togliere se l'FTP non si usa; altrimenti usare SFTP |
| `MS=…` (`TXT`) | Verifica del dominio per Microsoft 365 | Togliere, se l'istituto non usa Microsoft 365 |
| CAA | Assente (facoltativo) | `0 issue "letsencrypt.org"`: solo Let's Encrypt, che oggi emette tutti i certificati del dominio, può emetterne di nuovi |

**Portale iscrizioni** (a cura di chi lo gestisce):
- il link «Prendi visione dell'informativa» porta a un PDF che non esiste più (404), mentre la presa visione è obbligatoria;
- la pagina di accesso dell'amministrazione (`/admin/login`) è pubblica: limitarla per indirizzo IP o aggiungere la verifica in due passaggi;
- quando non ci sono corsi aperti, il modulo resta compilabile con l'elenco dei corsi vuoto e nessun messaggio;
- i font arrivano dai server di Google (dati di navigazione a terzi): meglio ospitarli sul portale.

**Per ripetere la verifica:**
- certificati: [crt.sh](https://crt.sh/?q=%25.itsrossellini.it);
- record: `dig +short TXT itsrossellini.it`, `dig +short TXT _dmarc.itsrossellini.it`, `dig +short MX itsrossellini.it`, oppure Google Admin Toolbox *Check MX*;
- dopo una modifica i record si aggiornano in qualche ora.

## Problemi comuni

| Problema | Soluzione |
|---|---|
| `setup.sh`: *Serve PHP >= 8.1 con l'estensione pdo_sqlite* | Attivare `pdo_sqlite` e `sqlite3` in `php.ini` (Windows: passo 2 sopra; Ubuntu: `sudo apt install php-sqlite3`) |
| `serve.sh`: *Address already in use* o *Failed to listen* | Porta occupata o riservata: `PORT=8090 dev/serve.sh` |
| Dal telefono il sito non si apre | Stessa Wi-Fi, rete impostata come *privata*, regola del firewall per PHP (vedi sopra) |
| `git pull` chiede utente e password | Si è in WSL: vedi [Windows con WSL](#windows-con-wsl-ubuntu) |
| git: *detected dubious ownership* | `git config --global --add safe.directory "$(pwd)"` dalla cartella del repo |
| `dev/setup.sh`: *bad interpreter* o `\r` negli errori | Lo script è stato convertito a fine riga CRLF: `git checkout -- dev/setup.sh dev/serve.sh` (scarta eventuali modifiche locali a quei due file) |
| Le modifiche al CSS o al JS non si vedono | Ricaricare con Ctrl+F5; controllare di aver salvato nel tema del repository |
| Le animazioni non partono | Interruttore «Animazioni» spento, oppure «riduci movimento» attivo nel sistema operativo |
| Link che puntano a `localhost` aprendo il sito da un altro dispositivo | Aprire il sito con l'indirizzo di rete stampato da `serve.sh`, non con `localhost` |
| Si vuole ripartire da zero | Cancellare `.wp/db/` e rilanciare `dev/setup.sh` |
| `avvia-windows.cmd` si chiude subito o dice che PowerShell è bloccato | Aprirlo da un prompt dei comandi per leggere il messaggio. Se l'organizzazione blocca gli script PowerShell, seguire l'installazione a mano ([Windows (Git Bash)](#windows-git-bash)) |
| `avvia-windows.cmd`: *VCRUNTIME140.dll non trovato* | Accettare l'installazione del Visual C++ Redistributable che il file propone, oppure scaricarlo da Microsoft (vc_redist.x64.exe) |
| Il doppio clic su `avvia-linux.sh` apre l'editor | Dipende dal file manager: lanciarlo da terminale con `./avvia-linux.sh` |
| In locale, nella console dell'admin: *Unexpected token '<'* | Il Polylang locale viene dal sorgente GitHub, che non ha `js/build/admin.min.js`. Non riguarda il tema né la produzione, dove Polylang arriva completo da WordPress.org |
| `package.py`: *modifiche non committate* | Fare commit delle modifiche al tema, oppure annullarle, e rilanciare |
| `dev/test/run.py`: *Playwright non è installato* | `python3 -I dev/test/run.py --installa`, una volta sola |
| `dev/test/run.py`: un controllo fallisce | Leggere il motivo sotto la ✗ e guardare la schermata in `.wp/test-results/`; il log del server dei test è in `.wp/test-results/server.log` |
| `package.py`: *Traduzioni non aggiornate* | `python3 -I dev/i18n/build.py`, tradurre le nuove voci in `en_GB.po`, rilanciare `build.py`, commit |
| Caricando lo zip: *Il pacchetto non può essere installato* o *Stile mancante* | Lo zip non ha la cartella `its-rossellini/` al primo livello: usare quello creato da `package.py` |
| Dopo l'attivazione compare l'avviso «servono questi plugin» | Usare i pulsanti dell'avviso; per Rossellini Core vedi [Plugin e dipendenze](#plugin-e-dipendenze) |

## Punti aperti

- **Rossellini Core**: il plugin del live che registra i tipi di contenuto. Il suo sorgente non è disponibile: il tema si basa sui nomi visti sul live (`corso`, `evento`, `edizione_corso`, `documento`). Se cambiano, si adegua con i filtri, senza toccare il tema.
- **Testi delle pagine**: vanno inseriti nell'editor prima dell'attivazione (passaggio 4).
- **Portale iscrizioni**: le domande si inviano su [iscrizioni.itsrossellini.it](https://iscrizioni.itsrossellini.it/), un'applicazione separata (Laravel, su un altro server), non WordPress. Il tema non la integra (decisione del 18/11/2026): i pulsanti «Preiscriviti» portano alla pagina `/iscrizioni/`. Da sistemare, a cura di chi gestisce il portale e la redazione (verifica del 18/11/2026):
  - nel modulo del portale il link «Prendi visione dell'informativa» porta a un PDF che non esiste più (404), mentre la presa visione è obbligatoria;
  - il testo della pagina `/iscrizioni/` del live dice «senza rimandarti a un portale esterno», ma le domande si inviano proprio dal portale;
  - altri punti del portale in [Dominio, DNS e posta](#dominio-dns-e-posta).
- **Accessibilità**: dichiarazione da pubblicare e PDF da riesportare ([`ACCESSIBILITA.md`](ACCESSIBILITA.md)).
- **Server**: header di sicurezza da applicare e TLS da verificare ([sopra](#sicurezza-in-produzione)).
- **DNS e posta**: SPF senza Google, DMARC assente, record di vecchi servizi da togliere ([Dominio, DNS e posta](#dominio-dns-e-posta)).
- **Privacy**: il sito rimanda a Google Forms e Google Docs (prenotazioni degli open day), Eventbrite (eventi) e Google Drive (foto). L'informativa li deve citare: oggi nomina solo il «Modulo Google».

## Crediti e licenze

- Tema: GPL-2.0-or-later, come WordPress.
- Font: [Raleway](https://github.com/impallari/Raleway), SIL Open Font License (`assets/fonts/OFL.txt`).
- Logo dell'istituto: file del sito attuale, usato così com'è (`assets/img/logo.svg`; in `docs/img/` le copie colorate per questo README).
- Loghi dei finanziatori: file ufficiali dei rispettivi enti, fonti in [`SOURCES.md`](wp-content/themes/its-rossellini/assets/img/funding/SOURCES.md).
