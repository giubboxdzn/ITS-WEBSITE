# ITS Academy Roberto Rossellini — tema WordPress

Nuovo tema classico per [itsrossellini.it](https://itsrossellini.it), stesso stack del sito attuale: WordPress, PHP, CSS e JavaScript scritti a mano, senza framework né build.

Direzione visiva: **contenitori arrotondati, vetro e titoli leggeri**, sui riferimenti indicati dal committente: home su fondo blu notte con l'hero in una card arrotondata col video, menu a pillola, card in vetro, griglia di numeri e foto, card dei corsi a immagine, card di confronto con cosa include il biennio, blocco «Parti da Roma» con globo, FAQ ad accordion e footer a colonne con il logo in filigrana e l'interruttore «Animazioni»; pagine interne chiare con un alone azzurro. Solo i colori dell'istituto (blu, blu notte, ciano, blu elettrico e loro toni) e Raleway. Regole complete in [`DESIGN.md`](DESIGN.md), contesto di prodotto in [`PRODUCT.md`](PRODUCT.md).

## Struttura

```
wp-content/themes/its-rossellini/   il tema (da installare in produzione)
  front-page.php                    home (video, corsi, academy, percorso, news, partner)
  single-corso.php                  scheda corso
  single.php, single-evento.php, single-documento.php
  templates/                        modelli di pagina: Elenco corsi, Eventi, Bandi e documenti, Edizioni dei corsi
  template-parts/                   card corso, griglia corsi con filtro, riga datata, testata pagina
  inc/                              helper, icone SVG, query, Customizer
  assets/css/main.css               tutto lo stile (token in :root)
  assets/js/main.js                 header, video dell'hero, menu mobile, filtro corsi (miglioramento progressivo)
  assets/fonts/                     Raleway variabile, normale e corsivo (OFL), self-hosted
dev/                                ambiente locale (non va in produzione)
.claude/skills/                     skill per Claude Code: find-skills, impeccable, web-design-guidelines
```

## Sviluppo locale

Richiede PHP ≥ 8.1 con `pdo_sqlite`, git e Python 3. Nessun MySQL: WordPress gira su SQLite.

```bash
dev/setup.sh      # scarica WordPress 7.1.4 + SQLite, attiva il tema, importa i contenuti pubblici del sito
dev/serve.sh      # http://localhost:8080  (admin / admin)
```

Il server ascolta solo su questa macchina (`127.0.0.1`); `HOST=0.0.0.0 dev/serve.sh` per esporlo in rete, `PORT=8090` per cambiare porta.

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

`dev/seed/fetch.py` legge (solo GET) l'API REST e le pagine pubbliche di itsrossellini.it e salva tutto in `.wp/seed/` (ignorato da git). `dev/seed/import.php` lo importa nel database locale. Non scrive nulla sul sito live.

`dev/mu-plugins/its-dev-content-model.php` registra in locale gli stessi tipi di contenuto di Rossellini Core (`corso`, `evento`, `edizione_corso`, `documento` e relative tassonomie). In produzione non serve: li registra già il plugin.

## Messa in produzione

1. Installare la cartella `wp-content/themes/its-rossellini` come nuovo tema (slug diverso da `rossellini`, il tema attuale resta intatto per tornare indietro).
2. Tenere attivo **Rossellini Core**: il tema legge i suoi tipi di contenuto e tassonomie.
3. **Contenuti delle pagine.** Sul sito attuale quasi tutte le pagine hanno l'editor vuoto, perché il testo è scritto nel tema o nel plugin. Questo tema mostra il contenuto dell'editor, quindi prima di attivarlo i testi vanno inseriti nelle pagine (lo script di import locale mostra il risultato atteso).
4. Assegnare i modelli: *Elenco corsi* a `/offerta-formativa/corsi/`, *Eventi* a `/eventi/`, *Bandi e documenti* a `/bandi-albi/` e `/bandi/`, *Edizioni dei corsi* a `/offerta-formativa/corsi-in-svolgimento-e-conclusi/`; impostare `/news/` come pagina degli articoli.
5. Menu: posizioni *Menu principale*, *Menu footer*, *Note legali*.
6. Home: Customizer → *Home* → video dell'hero (MP4 muto); l'immagine in evidenza della pagina iniziale fa da copertina del video. Il contenuto della pagina (loghi con link) diventa la sezione «I nostri partner».
7. Customizer → *Contatti e sede* e *Dati pubblicati*: indirizzo, orari, telefono, email, durata, ore, posti, dato occupazionale e fonte.

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

