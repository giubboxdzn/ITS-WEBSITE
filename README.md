# ITS Academy Roberto Rossellini — tema WordPress

Nuovo tema classico per [itsrossellini.it](https://itsrossellini.it), stesso stack del sito attuale: WordPress, PHP, CSS e JavaScript scritti a mano, senza framework né build.

Direzione visiva: **contenitori arrotondati, vetro e titoli leggeri**, sui riferimenti indicati dal committente: home su fondo blu notte con il video dell'hero a tutta larghezza, menu a pillola, card in vetro, griglia di numeri e foto, card dei corsi a immagine, card di confronto con cosa include il biennio, blocco «Parti da Roma» con globo, FAQ ad accordion e footer a colonne con il logo in filigrana e l'interruttore «Animazioni»; pagine interne chiare con un alone azzurro. Solo i colori dell'istituto (blu, blu notte, ciano, blu elettrico e loro toni) e Raleway. Regole complete in [`DESIGN.md`](DESIGN.md), contesto di prodotto in [`PRODUCT.md`](PRODUCT.md).

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
.claude/skills/                     skill per Claude Code: find-skills, impeccable, web-design-guidelines
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

`dev/seed/fetch.py` legge (solo GET) l'API REST e le pagine pubbliche di itsrossellini.it, in italiano e in inglese, e salva tutto in `.wp/seed/` (ignorato da git). `dev/seed/import.php` lo importa nel database locale. Non scrive nulla sul sito live. Se `data.json` esiste già senza la parte inglese, `setup.sh` scarica solo quella (`fetch.py --en-only`, qualche minuto); se le pagine sono nel formato precedente, riscarica solo le pagine (`fetch.py --pages-only`, un paio di minuti). Le pagine interne del live diventano blocchi dell'editor con gli stili del tema (occhielli, numeri, card, invito finale), con lead (riassunto) e foto (immagine in evidenza).

### Lingue (Polylang)

Come sul sito live: italiano predefinito senza prefisso, inglese su `/en/`. `setup.sh` prende **Polylang 3.9.2** da GitHub (`.wp/polylang`) e genera con `dev/polylang-autoload.php` i file che la release ufficiale include (autoload e liste dei moduli), senza Composer; `dev/languages.php` crea le lingue e le opzioni; l'import assegna la lingua a ogni contenuto, collega le traduzioni IT↔EN (dagli `hreflang` del sito live) e crea i menu inglesi.

Il selettore con le bandiere è nell'header (bandiera + codice) e nel footer (bandiera + nome, come nel riferimento): porta alla stessa pagina nell'altra lingua o, se manca la traduzione, alla home di quella lingua. I testi del tema sono tradotti in `languages/en_GB.po`; dopo averli modificati: `python3 -I dev/i18n/build.py` (aggiorna `.pot`, `.po` e compila `.mo` e `.l10n.php`).

`dev/mu-plugins/its-dev-content-model.php` registra in locale gli stessi tipi di contenuto di Rossellini Core (`corso`, `evento`, `edizione_corso`, `documento` e relative tassonomie). In produzione non serve: li registra già il plugin.

## Messa in produzione

1. Installare la cartella `wp-content/themes/its-rossellini` come nuovo tema (slug diverso da `rossellini`, il tema attuale resta intatto per tornare indietro).
2. Tenere attivo **Rossellini Core**: il tema legge i suoi tipi di contenuto e tassonomie.
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
7. Home: Customizer → *Home* → video dell'hero (MP4 muto); l'immagine in evidenza della pagina iniziale fa da copertina del video. Il contenuto della pagina (loghi con link) diventa la sezione «I nostri partner».
8. Customizer → *Contatti e sede* e *Dati pubblicati*: indirizzo, orari, telefono, email, durata, ore, posti, dato occupazionale e fonte.

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

