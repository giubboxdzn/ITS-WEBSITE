# Accessibilità: obblighi, stato del tema, dichiarazione

> Non è una consulenza legale. Le norme sono state lette nel testo vigente il 17/11/2026 (fonti in fondo). Prima di pubblicare, la Fondazione deve far confermare a un legale o al proprio RTD se e come è soggetta agli obblighi.

## 1. Obblighi in sintesi

**Legge 9 gennaio 2004, n. 4 («Legge Stanca»).** Art. 3, comma 1 (testo vigente): si applica, oltre che alle pubbliche amministrazioni, «agli organismi di diritto pubblico […] nonché a tutti i soggetti che usufruiscono di contributi pubblici o agevolazioni per l'erogazione dei propri servizi tramite sistemi informativi o internet».

**Verifica nell'Indice PA (open data IPA, 17/11/2026).** La Fondazione ITS Academy Roberto Rossellini è registrata:
- codice IPA `8KY9MNU7`, codice fiscale 97616040586;
- tipologia e categoria «Gestori di Pubblici Servizi» (L37);
- sito istituzionale `https://itsrossellini.it/`;
- un Responsabile per la transizione al digitale è registrato con email istituzionale.

Quindi la dichiarazione si compila su **form.agid.gov.it**, con l'accesso dell'RTD, e il link che il sistema genera va nel footer.

Sul sito live, al 17/11/2026, non c'è nessun link a una dichiarazione di accessibilità, né nel footer né nella sitemap. Se ne esiste già una su form.agid.gov.it, l'RTD la trova nella sua area e basta collegarla dal Customizer.

Resta comunque da far confermare l'inquadramento a un legale o all'RTD.

Per i soggetti dell'art. 3, comma 1:

- **Conformità tecnica.** Requisiti di accessibilità delle linee guida AgID, cioè la norma UNI CEI EN 301 549, che rimanda alle WCAG 2.1 livello AA.
- **Dichiarazione di accessibilità (art. 3-quater).** Deve essere «particolareggiata, esaustiva e chiara», pubblicata sul sito e aggiornata ogni anno **entro il 23 settembre**. Deve contenere:
  - i contenuti non accessibili, con il motivo;
  - il **meccanismo di feedback**, con il suo link, con cui chiunque può segnalare problemi o chiedere i contenuti in forma accessibile;
  - il link alla **procedura di attuazione** presso il Difensore civico per il digitale, se la risposta non arriva entro 30 giorni o non è soddisfacente.
- **Dove si compila.** Gli enti presenti nell'Indice PA (IPA), come la Fondazione, compilano la dichiarazione su [form.agid.gov.it](https://form.agid.gov.it), con accesso del Responsabile per la transizione al digitale. Il sistema genera un link da mettere nel **footer del sito**.
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

Restano da verificare i contenuti inseriti dalla redazione (vedi punto 4). I PDF sono verificati al punto 5.

## 3. Bozza della dichiarazione (modello AgID)

Le parti tra [quadre] vanno compilate o verificate dalla Fondazione. Lo stato di conformità resta «parzialmente conforme» finché i PDF e i contenuti redazionali non sono stati verificati.

---

**Dichiarazione di accessibilità**

La Fondazione ITS Academy Roberto Rossellini si impegna a rendere il proprio sito web accessibile, conformemente alla legge 9 gennaio 2004, n. 4. La presente dichiarazione si applica a https://itsrossellini.it.

**Stato di conformità.** Questo sito web è **parzialmente conforme** ai requisiti previsti dalla norma UNI CEI EN 301 549 (WCAG 2.1 livello AA) per i motivi indicati sotto.

**Contenuti non accessibili.**

*a) Inosservanza della legge 4/2004:*
- 10 documenti PDF pubblicati in «Bandi e albi» non hanno tag di struttura: bandi e graduatorie delle prime edizioni e la procedura di selezione del 2024, elencati al punto 5. Uno screen reader ne legge il testo, ma senza titoli, tabelle né ordine di lettura. Saranno ripubblicati in formato accessibile entro [data]. Nel frattempo, su richiesta tramite il meccanismo di feedback, ciascun documento viene fornito in formato accessibile entro [N] giorni lavorativi.
- 16 documenti PDF hanno tag e lingua, ma non il titolo del documento (punto 5).
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

## 5. I PDF di «Bandi e albi» (verifica del 17/11/2026)

Sul live sono pubblicati 43 file distinti (le pagine italiane e inglesi condividono gli stessi file). Ogni PDF è stato controllato per:
- tag e albero di struttura (`Tagged`, `StructTreeRoot`): senza, uno screen reader legge il testo senza titoli, elenchi, tabelle né ordine di lettura;
- testo estraibile (nessuna scansione);
- lingua dichiarata;
- titolo del documento;
- font incorporati.

Esito:
- **16 accessibili** al controllo di base;
- **16 parziali**: hanno tag e lingua, manca solo il titolo del documento;
- **10 non accessibili**: testo senza tag;
- **1 modulo Word**.

Nessun file è una scansione. Un controllo completo PDF/UA (veraPDF, PAC) resta consigliato per i documenti nuovi.

**Non accessibili (10): da riesportare dal file originale.** Sono esportazioni senza tag:
- Google Docs prima del 2025 (*Skia/PDF m130*);
- «Microsoft Print to PDF».

Oggi sia Word sia Google Docs producono PDF con tag:
- in Word: *File → Esporta → PDF → Opzioni → «Tag della struttura del documento per l'accessibilità»*;
- in Google Docs: *File → Scarica → PDF*.

| Documento | File |
|---|---|
| Bando Creative Director of Photography 1ª edizione | `0991d3a10a690960-0991d3a10a6909608ea8b8b273c3d630ccf967316323eefda0793fcad0e9df7f.pdf` |
| Bando Digital Game Specialist 2ª edizione | `20ebc7740a818ec9-20ebc7740a818ec95b1c2b39fe871c8d11ac1d0bcaa72cf65deb604945c8b48e.pdf` |
| Bando Digital Sound Designer 3ª edizione | `244fb0e032fc0119-244fb0e032fc0119cb949ffabd7c8dcd9ece53898e2a3fe28e993a13f7c4d346.pdf` |
| Graduatoria Creative Director of Photography 1ª edizione | `314fc9f55179f5fb-314fc9f55179f5fba5b2d841210d26d61a39f0938e8a7982a76f613e991dafca.pdf` |
| Bando Cinema e TV Production 1ª edizione | `36a01a325916656c-36a01a325916656caef28d068a5737f6a36ddfc5db5272fdf05a307c8abafa59.pdf` |
| Procedura selezioni ITS Rossellini, revisione 5/2024 | `5cc4b6bec4a7f1c3-5cc4b6bec4a7f1c33b2afd81b34144e2e10b4d78d9f2336127f1e49553c4eee9.pdf` |
| Bando Cybersecurity Specialist 3ª edizione | `abf9f971623ddd22-abf9f971623ddd22cb07a4c0a191e1f20e76404fe4e7f2dcb6245db656a2fcde.pdf` |
| Graduatoria Visual Effects & 3D Artist 1ª edizione | `ae415afbcf4a9684-ae415afbcf4a96844f1333d3dbae38843a78e0a0955c3b349dfb9cf5fbf46eeb.pdf` |
| Bando Visual Effects & 3D Artist 1ª edizione | `bbddef35bbbd332e-bbddef35bbbd332e61c461c418f0057b94500ec3b7982eb6336e24e7fb7bd03b.pdf` |
| Graduatoria Digital Sound Designer 3ª edizione | `de8e9c221cd2a2dd-de8e9c221cd2a2ddc530b9a209aaad51101ca11702e9623295d32874174fe47f.pdf` |

**Parziali (16): manca il titolo del documento.** Nel file Word, *File → Informazioni → Proprietà → Titolo*, poi riesportare. Lo screen reader annuncia il titolo invece del nome del file.

| Documento | File |
|---|---|
| Avviso di istituzione dell’Albo professionisti e collaboratori | `01_Avviso_Albo_Professionisti.pdf` |
| Regolamento dell’Albo professionisti e collaboratori | `02_Regolamento_Albo_Professionisti.pdf` |
| Elenco delle aree professionali | `03_Elenco_Aree_Professionali.pdf` |
| Graduatoria Digital Game Specialist 2ª edizione | `1340b1e615f0fd71-1340b1e615f0fd715759a112a43cfd921f02aaa8230f986228663f8f5a649190.pdf` |
| Bando Cybersecurity Specialist 4ª edizione | `25ce82231beb9e73-25ce82231beb9e73407353f54b8196314c66613ef73f0c65dc2a08062b088dcc.pdf` |
| Bando Videomaker 15ª edizione | `381338e3f5be45e5-381338e3f5be45e53b4b42798d26ecd8295b92d838054caff88e4646b41e64a9.pdf` |
| Bando Visual Effects & 3D Artist 2ª edizione | `414f8d433d192cac-414f8d433d192cac9f65e2a9c3683892a514ef03ef21f65f2b5092063c21affc.pdf` |
| Bando Videomaker 14ª edizione | `503826c944865e36-503826c944865e362d009cfa3ec72e9667c248838ae879c7171dc2a44c5a5a68.pdf` |
| Bando Cinema e TV Production 2ª edizione | `7abd5037d3cbbe11-7abd5037d3cbbe11bd9ad26830f2b0b97f37c7e98ba3f7e4d413d8c240ee3f02.pdf` |
| Admission call — 2025-2027 | `8c17b9ca686b23fe-8c17b9ca686b23fe0360f401a21fa31f494b99ae43d8bbdda5e099aee9bb636c.pdf` |
| Bando Creative Director of Photography 2ª edizione | `90a0c73846dec668-90a0c73846dec66884fb888a04c74152af9277ee95294d58bcbdbc9f5da259db.pdf` |
| Informativa privacy dell’Albo professionisti | `Informativa_Privacy_Albo_Professionisti_Rossellini.pdf` |
| Graduatoria Cybersecurity Specialist 3ª edizione | `b310915968fc8311-b310915968fc8311a6dd807820c366c1608f4482f0bc496fdfcfb615c41ca646.pdf` |
| Graduatoria Cinema e TV Production 1ª edizione | `d828f4d349409b8d-d828f4d349409b8da01605e83502a914638c0b78d9b00d337580ac49d4c608e0.pdf` |
| Graduatoria Videomaker 14ª edizione | `f4ac4dfaad668421-f4ac4dfaad668421ed044e2f4d3b2366ade1cec2f61be2fc87e98dc367a7a7da.pdf` |
| Bando Digital Sound Designer 4ª edizione | `fee6c5be548a036f-fee6c5be548a036fcd78fb179c574d8bcea4cddc8673e12b3167a6f3a4fc5d5a.pdf` |

**Modulo Word (1):** «Istanza di iscrizione». Va bene come modulo compilabile se usa titoli, campi con etichetta e niente tabelle per impaginare. Affiancargli il PDF accessibile se è solo da leggere.

Per i documenti già usati in procedure concluse (bandi e graduatorie di edizioni passate), la riesportazione può essere sostituita dall'impegno a fornirli in formato accessibile su richiesta, indicandoli nella dichiarazione.

## Fonti

- Legge 4/2004, testo vigente: [art. 3](https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:legge:2004-01-09;4~art3), [art. 3-quater](https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:legge:2004-01-09;4~art3quater), [art. 9](https://www.normattiva.it/uri-res/N2Ls?urn:nir:stato:legge:2004-01-09;4~art9) (Normattiva).
- AgID, [Dichiarazione di accessibilità: pubblicazione o aggiornamento entro il 23 settembre](https://www.agid.gov.it/it/notizie/dichiarazione-di-accessibilita-pubblicazione-o-aggiornamento-entro-il-23-settembre) (16/09/2026).
- AgID, [Linee guida sull'accessibilità per i soggetti privati](https://www.agid.gov.it/it/design-servizi/accessibilita/linee-guida-accessibilita-privati).
- AgID, [Procedura di attuazione](https://www.agid.gov.it/it/design-servizi/accessibilita/procedura-attuazione) e [Difensore civico per il digitale](https://www.agid.gov.it/it/agenzia/difensore-civico-il-digitale).
- Indice PA, open data: [enti](https://indicepa.gov.it/ipa-dati/dataset/enti), [categorie](https://indicepa.gov.it/ipa-dati/dataset/categorie-enti), [responsabili della transizione al digitale](https://indicepa.gov.it/ipa-dati/dataset/responsabili-della-transizione-al-digitale).
- AgID, [Guida alla compilazione delle dichiarazioni di accessibilità](https://form.agid.gov.it/assets/documents/Guida-alla-compilazione-delle-Dichiarazioni-di-accessibilit%C3%A0.pdf).
