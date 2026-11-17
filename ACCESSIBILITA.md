# Accessibilità: obblighi, stato del tema, dichiarazione

> Non è una consulenza legale. Le norme sono state lette nel testo vigente il 17/11/2026 (fonti in fondo). Prima di pubblicare, la Fondazione deve far confermare a un legale o al proprio RTD se e come è soggetta agli obblighi.

## 1. Obblighi in sintesi

**Legge 9 gennaio 2004, n. 4 («Legge Stanca»).** Art. 3, comma 1 (testo vigente): si applica, oltre che alle pubbliche amministrazioni, «agli organismi di diritto pubblico […] nonché a tutti i soggetti che usufruiscono di contributi pubblici o agevolazioni per l'erogazione dei propri servizi tramite sistemi informativi o internet».

Una Fondazione ITS Academy finanziata con FSE+, PNRR e fondi ministeriali ha con buona probabilità questi requisiti. Va confermato.

Per i soggetti dell'art. 3, comma 1:

- **Conformità tecnica.** Requisiti di accessibilità delle linee guida AgID, cioè la norma UNI CEI EN 301 549, che rimanda alle WCAG 2.1 livello AA.
- **Dichiarazione di accessibilità (art. 3-quater).** Deve essere «particolareggiata, esaustiva e chiara», pubblicata sul sito e aggiornata ogni anno **entro il 23 settembre**. Deve contenere:
  - i contenuti non accessibili, con il motivo;
  - il **meccanismo di feedback**, con il suo link, con cui chiunque può segnalare problemi o chiedere i contenuti in forma accessibile;
  - il link alla **procedura di attuazione** presso il Difensore civico per il digitale, se la risposta non arriva entro 30 giorni o non è soddisfacente.
- **Dove si compila.** Le amministrazioni presenti nell'Indice PA (IPA) compilano la dichiarazione su [form.agid.gov.it](https://form.agid.gov.it), con accesso del Responsabile per la transizione al digitale. Il sistema genera un link da mettere nel **footer del sito**. Se la Fondazione non è in IPA, la pubblica sul proprio sito seguendo lo stesso modello (art. 3-quater, comma 4).
- **Responsabilità (art. 9).** Per i soggetti del comma 1 la violazione rileva sulla responsabilità dirigenziale e disciplinare, ferme le responsabilità civili e penali. Le sanzioni fino al 5% del fatturato riguardano i privati del comma 1-bis.

**European Accessibility Act (D.Lgs. 82/2022, in vigore dal 28/06/2025).** Riguarda prodotti e servizi elencati, come commercio elettronico, banche, trasporti e comunicazioni elettroniche. Un sito informativo di formazione non rientra in quell'elenco, a meno che non venda servizi online (pagamenti, iscrizioni a pagamento). In quel caso va rivalutato.

## 2. Cosa garantisce il tema (verifica del 17/11/2026, ambiente locale)

**Verifiche automatiche**
- axe-core, regole WCAG 2.0, 2.1 e 2.2 A/AA: **0 violazioni** su 16 tipi di pagina, desktop e mobile. Inclusi menu mobile e selettore lingua aperti.
- Lighthouse: accessibilità 100 su tutte le pagine misurate.

**Verifiche sui pixel reali**
- Contrasto misurato sullo sfondo reale dietro ogni testo, anche su foto, video e vetro: tutto ≥ 4,5:1 (≥ 3:1 per il testo grande).

**Verifiche manuali**
- Tastiera:
  - link «Vai al contenuto» come primo elemento;
  - focus visibile su ogni elemento;
  - focus mai coperto dall'header fisso (2.4.11);
  - il menu mobile si chiude con Esc e riporta il focus al pulsante.
- Reflow a 320 px senza scorrimento orizzontale (1.4.10). Spaziatura del testo aumentata senza perdita di contenuto (1.4.12).
- Movimento:
  - interruttore «Animazioni» nel footer e rispetto di «riduci movimento» del sistema;
  - video della home senza audio, sempre fermabile;
  - nessun contenuto lampeggiante.
- Lingua della pagina dichiarata (it/en). Titoli animati letti da tastiera e screen reader come testo intero.

**Footer di ogni pagina**
- «Dichiarazione di accessibilità», dal Customizer → *Accessibilità*. Il campo accetta il link generato da AgID, oppure si crea una pagina con slug `dichiarazione-di-accessibilita`.
- «Segnala un problema di accessibilità»: email con oggetto e indirizzo della pagina già compilati. L'indirizzo si imposta nello stesso pannello; se è vuoto si usa l'email dei contatti.

**Fuori dal tema**

Restano da verificare i contenuti inseriti dalla redazione (vedi punto 4) e i PDF.

## 3. Bozza della dichiarazione (modello AgID)

Le parti tra [quadre] vanno compilate o verificate dalla Fondazione. Lo stato di conformità resta «parzialmente conforme» finché i PDF e i contenuti redazionali non sono stati verificati.

---

**Dichiarazione di accessibilità**

La Fondazione ITS Academy Roberto Rossellini si impegna a rendere il proprio sito web accessibile, conformemente alla legge 9 gennaio 2004, n. 4. La presente dichiarazione si applica a https://itsrossellini.it.

**Stato di conformità.** Questo sito web è **parzialmente conforme** ai requisiti previsti dalla norma UNI CEI EN 301 549 (WCAG 2.1 livello AA) per i motivi indicati sotto.

**Contenuti non accessibili.**

*a) Inosservanza della legge 4/2004:*
- [DA VERIFICARE] Alcuni documenti PDF pubblicati in «Bandi e albi» potrebbero essere scansioni o non avere struttura (titoli, testo selezionabile, ordine di lettura). Su richiesta, tramite il meccanismo di feedback, il documento viene fornito in formato accessibile entro [N] giorni lavorativi.
- [DA VERIFICARE] Alcune immagini nelle news meno recenti potrebbero non avere un testo alternativo.

*b) Onere sproporzionato:* [nessuno / indicare].

*c) Contenuti non soggetti alla legge:* [es. documenti d'ufficio pubblicati prima del 23 settembre 2018 non necessari ai processi amministrativi in corso].

**Redazione della dichiarazione.** Dichiarazione redatta il [data], con un'**autovalutazione** effettuata dalla Fondazione con il supporto del fornitore del sito:
- verifiche automatiche (axe-core, Lighthouse);
- verifiche manuali (tastiera, contrasto, reflow, screen reader).

Ultimo riesame: [data].

**Meccanismo di feedback e contatti.** Per segnalare problemi di accessibilità o chiedere un contenuto in formato accessibile: [email per le segnalazioni], o il link «Segnala un problema di accessibilità» presente in fondo a ogni pagina. La Fondazione risponde entro 30 giorni.

**Procedura di attuazione.** In caso di risposta insoddisfacente o di mancata risposta entro 30 giorni, è possibile fare ricorso al Difensore civico per il digitale di AgID: https://www.agid.gov.it/it/design-servizi/accessibilita/procedura-attuazione

**Informazioni sul sito.**
- Data di pubblicazione: [data di messa online del nuovo tema].
- Test di usabilità con persone con disabilità: [sì/no].
- CMS: WordPress, tema «ITS Rossellini».

---

## 4. Regole per chi inserisce contenuti

Il tema rende accessibile la struttura, ma i contenuti li scrive la redazione.

- **Immagini.**
  - Testo alternativo che dica cosa mostra l'immagine e perché è lì.
  - Lasciare vuoto solo se l'immagine è decorativa.
  - Mai il nome del file.
- **Titoli.** Usare i blocchi Titolo in ordine (H2, poi H3), non il grassetto. Il tema ci costruisce sezioni e indice.
- **Link.** Il testo deve dire dove porta («Bando 2026–2028 (PDF, 300 KB)»), mai «clicca qui».
- **PDF.** Esportare da Word o InDesign come PDF accessibile (con tag), non scansionare. Ogni bando o graduatoria deve avere almeno un riassunto in pagina.
- **Video.** Con parlato: sottotitoli. Solo musica: nessun obbligo.
- **Tabelle.** Solo per dati, con riga di intestazione. Mai per impaginare.

## Fonti

- Legge 4/2004, testo vigente: [art. 3](https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:legge:2004-01-09;4~art3), [art. 3-quater](https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:legge:2004-01-09;4~art3quater), [art. 9](https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:legge:2004-01-09;4~art9) (Normattiva).
- AgID, [Dichiarazione di accessibilità: pubblicazione o aggiornamento entro il 23 settembre](https://www.agid.gov.it/it/notizie/dichiarazione-di-accessibilita-pubblicazione-o-aggiornamento-entro-il-23-settembre) (16/09/2026).
- AgID, [Linee guida sull'accessibilità per i soggetti privati](https://www.agid.gov.it/it/design-servizi/accessibilita/linee-guida-accessibilita-privati).
- AgID, [Procedura di attuazione](https://www.agid.gov.it/it/design-servizi/accessibilita/procedura-attuazione) e [Difensore civico per il digitale](https://www.agid.gov.it/it/agenzia/difensore-civico-il-digitale).
- AgID, [Guida alla compilazione delle dichiarazioni di accessibilità](https://form.agid.gov.it/assets/documents/Guida-alla-compilazione-delle-Dichiarazioni-di-accessibilit%C3%A0.pdf).
