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
| Tema | ITS Rossellini 1.1.0, cartella e slug `its-rossellini` |
| WordPress | 6.9 o successivo, provato fino alla 7.1 |
| PHP | 8.1 o successivo (sul live: PHP 8.4, Apache) |
| Plugin necessari | **Rossellini Core** (tipi di contenuto), **Polylang** (italiano e inglese) |
| Plugin del live compatibili | Yoast SEO, Site Kit by Google, Cookie Notice |
| Build | nessuna: si modificano direttamente i file CSS, JS e PHP |
| Licenza | GPL-2.0-or-later; font Raleway con licenza OFL |

## Indice

- [Documentazione del progetto](#documentazione-del-progetto)
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

## Primo avvio, per chi riceve il progetto

1. **Accesso al codice.** Chiedere l'invito come collaboratore del repository GitHub, poi:
   ```bash
   git clone https://github.com/giubboxdzn/ITS-WEBSITE.git
   cd ITS-WEBSITE
   ```
2. **Requisiti.** PHP 8.1 o successivo con `pdo_sqlite`, git e Python 3 (su Windows vedi [sotto](#windows-git-bash)). Non serve MySQL.
3. **Installazione locale** (la prima volta qualche minuto: scarica WordPress e i contenuti pubblici del sito):
   ```bash
   dev/setup.sh
   dev/serve.sh
   ```
4. Aprire **http://localhost:8080**. Per l'admin: **http://localhost:8080/wp-admin** con utente `admin` e password `admin`, valide solo in locale.
5. **Dove si lavora.** Solo in `wp-content/themes/its-rossellini/`. WordPress legge il tema direttamente dal repository, quindi una modifica salvata si vede ricaricando la pagina, senza riavviare nulla. CSS e JavaScript hanno come versione la data del file: il browser scarica sempre quelli nuovi.
6. **Prima di ogni commit**: `phpcs` (vedi [Qualità del codice](#qualità-del-codice)) e una prova in locale delle pagine toccate, in italiano e in inglese, da desktop e da telefono.

## Struttura

```
wp-content/themes/its-rossellini/   il tema (da installare in produzione)
  style.css                         intestazione del tema (nome, versione, requisiti); lo stile è in assets/css/main.css
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
  languages/                        traduzioni del tema: its-rossellini.pot, en_GB.po/.mo/.l10n.php
  assets/css/main.css               tutto lo stile (token in :root)
  assets/js/main.js                 header, video dell'hero, menu mobile, filtro corsi, animazioni legate all'interruttore «Animazioni» (miglioramento progressivo)
  assets/css/editor.css             stile dell'editor a blocchi
  assets/fonts/                     Raleway variabile, normale e corsivo (OFL), self-hosted
  assets/img/                       logo, loghi dei finanziatori (funding/, con le fonti in SOURCES.md)
dev/                                ambiente locale (non va in produzione); dev/i18n/build.py compila le traduzioni
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

Per il JavaScript non c'è un linter configurato: almeno `node --check wp-content/themes/its-rossellini/assets/js/main.js` per la sintassi, e la console del browser senza errori.

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
| | Diplomati occupati entro 12 mesi | 87% |
| | Fonte del dato occupazionale † | Dato pubblicato da ITS Rossellini |
| Accessibilità | Dichiarazione di accessibilità (URL) | vuoto: pagina con slug `dichiarazione-di-accessibilita`, se esiste |
| | Email per le segnalazioni | vuoto: l'email dei contatti |

I numeri vanno aggiornati qui quando cambiano i bandi. L'unico dato scritto nel codice è la superficie del teatro di posa (360 m², in `front-page.php`).

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

### Preparare lo zip del tema

Dalla cartella del repository:

```bash
git archive --format=zip --prefix=its-rossellini/ -o its-rossellini.zip HEAD:wp-content/themes/its-rossellini
```

Lo zip contiene solo il tema, senza `dev/` né documentazione. Da Windows, senza riga di comando: tasto destro su `wp-content/themes/its-rossellini` → *Comprimi in file ZIP*. Lo zip deve contenere la cartella `its-rossellini/`, non i file sciolti.

### Passaggi

1. Installare la cartella `wp-content/themes/its-rossellini` come nuovo tema: *Aspetto → Temi → Aggiungi nuovo tema → Carica tema*, con lo zip qui sopra. Lo slug è diverso da `rossellini`: il tema attuale resta intatto per tornare indietro. `style.css` dichiara `Update URI: false`: WordPress non proporrà mai come «aggiornamento» un tema di WordPress.org con lo stesso slug.
2. Tenere attivo **Rossellini Core**: il tema legge i suoi tipi di contenuto e tassonomie, e dà a Corsi, Eventi, Edizioni dei corsi e Documenti e bandi le proprie icone nel menu dell'admin (`inc/admin.php`, filtro `register_post_type_args`).
3. **Lingue**: con Polylang attivo il selettore con le bandiere compare da solo (italiano e inglese già configurati sul sito live). Le traduzioni dei testi del tema sono incluse (`en_GB`); indirizzo, orari, "come arrivare" e fonte del dato occupazionale, se modificati nel Customizer, si traducono in *Lingue → Traduzioni* (gruppo *ITS Rossellini*).
4. **Contenuti delle pagine.** Sul sito attuale quasi tutte le pagine hanno l'editor vuoto, perché il testo è scritto nel tema o nel plugin. Questo tema mostra il contenuto dell'editor, quindi prima di attivarlo i testi vanno inseriti nelle pagine (lo script di import locale mostra il risultato atteso). Le pagine interne si compongono con blocchi standard e tre stili del tema, da cui il layout sceglie da solo i componenti della home:
   - **Riassunto** della pagina → sottotitolo dell'hero; **immagine in evidenza** → foto dell'hero.
   - Paragrafo stile **Occhiello** prima di un Titolo H2 → etichetta della sezione; prima di un Titolo H3 → etichetta della card.
   - **Titolo H2** → nuova sezione (l'ancora HTML va nell'indice «In questa pagina»); **Titolo H3** + paragrafi → una card; un link nel titolo H3 rende cliccabile tutta la card. Titoli numerati (`01 · …`) → passi numerati; card che puntano tutte a corsi → card dei corsi con foto.
   - Elenco stile **Numeri**: una cifra per voce, la cifra in grassetto e poi l'etichetta (`**800** ore di tirocinio`).
   - Gruppo stile **Invito finale** (titolo, testo, pulsanti) → banda blu di chiusura; i suoi pulsanti sono anche le azioni dell'hero.
   Il layout a sezioni si attiva da solo su ogni pagina che usa questi stili o titoli H3 (`its_has_sections()` in `inc/sections.php`), anche su *Bandi e albi* sopra l'archivio dei documenti; le pagine scritte come testo semplice (note legali) e le FAQ restano come scritte. Ogni pagina interna finisce con la banda blu: l'*Invito finale* della pagina o, se manca, quello della home.
   Per copiare i testi già pronti: in locale aprire la pagina in *Pagine → Modifica*, menu ⋮ → *Editor del codice*, copiare tutto e incollarlo nella stessa pagina del sito vero.
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

1. Aumentare `Version` in `style.css` (per esempio 1.1.0 → 1.2.0).
2. Ricreare lo zip e caricarlo in *Aspetto → Temi → Aggiungi nuovo tema → Carica tema*: WordPress propone *Sostituisci quello attuale con quello caricato*.
3. Svuotare la cache del sito, se presente. CSS e JS cambiano versione da soli, quindi i browser li riscaricano.

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

## Punti aperti

- **Rossellini Core**: il plugin del live che registra i tipi di contenuto. Il suo sorgente non è disponibile: il tema si basa sui nomi visti sul live (`corso`, `evento`, `edizione_corso`, `documento`). Se cambiano, si adegua con i filtri, senza toccare il tema.
- **Testi delle pagine**: vanno inseriti nell'editor prima dell'attivazione (passaggio 4).
- **Portale iscrizioni**: le domande si inviano su [iscrizioni.itsrossellini.it](https://iscrizioni.itsrossellini.it/), un'applicazione separata (Laravel, su un altro server), non WordPress. Il tema non la integra (decisione del 18/11/2026): i pulsanti «Preiscriviti» portano alla pagina `/iscrizioni/`. Da sistemare, a cura di chi gestisce il portale e la redazione (verifica del 18/11/2026):
  - nel modulo del portale il link «Prendi visione dell'informativa» porta a un PDF che non esiste più (404), mentre la presa visione è obbligatoria;
  - il testo della pagina `/iscrizioni/` del live dice «senza rimandarti a un portale esterno», ma le domande si inviano proprio dal portale.
- **Accessibilità**: dichiarazione da pubblicare e PDF da riesportare ([`ACCESSIBILITA.md`](ACCESSIBILITA.md)).
- **Server**: header di sicurezza da applicare e TLS da verificare ([sopra](#sicurezza-in-produzione)).

## Crediti e licenze

- Tema: GPL-2.0-or-later, come WordPress.
- Font: [Raleway](https://github.com/impallari/Raleway), SIL Open Font License (`assets/fonts/OFL.txt`).
- Logo dell'istituto: file del sito attuale, usato così com'è (`assets/img/logo.svg`; in `docs/img/` le copie colorate per questo README).
- Loghi dei finanziatori: file ufficiali dei rispettivi enti, fonti in [`SOURCES.md`](wp-content/themes/its-rossellini/assets/img/funding/SOURCES.md).
