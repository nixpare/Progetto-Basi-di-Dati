# Progetto Basi di Dati
## Modello ER

### Gerarchia Utente e Ristrutturazione
Nel primo modello ER è stata introdotta la gerarchia **Utente** (totale ed esclusiva)
tra le entità **Studente**, **Docente** e **Segretario** per il fatto che tutti
hanno in comune gli attributi mail, password, nome e cognome.
Come identificatore primario della gerarchia quindi è stata scelta la `mail`,
per definizione univoca, a differenza della coppia `(nome, cognome)`.

Però nella ristrutturazione della gerarchia, ottenuta eliminando il padre
e facendo ereditare gli attributi di esso a tutti i figli, per aderire
meglio alle specifiche fornite, per l'entità **Studente** è stata cambiata
la chiave primaria, da `mail` a `matricola`, tenendo sempre presente che
la mail deve essere univoca.

### Vincoli Extra-schema
#### Relazione *`sostiene`*
Questa relazione può essere usata sia per sapere a quali appelli un utente
si sia iscritto, sia per poi sapere, dopo la correzione, quale voto abbia
preso in quell'appello, questo grazie all'attributo facoltativo `voto`:
 + se voto non è impostato nella relazione, significa che lo studente si
  è iscritto all'appello ma non ha ancora sostenuto l'esame (la data non è 
  ancora arrivata) oppure che il docente non ha ancora inserito il voto (esame
  ancora in fase di correzione)
 + se il voto è presente invece vuol dire che l'esame è stato sostenuto e 
  corretto: quindi si può direttamente anche capire se l'esito sia positivo
  o negativo guardando il suo valore

#### Corso di Laurea e Insegnamento
Nell'identità **Corso di Laurea**, il campo `tipo` può avere solo due valori,
*triennale* o *magistrale*, per indicarle la tipologia.

Di conseguenza, il campo `anno` degli **Insegnamenti** possono avere come range
di valori, rispettivamente, $\{1, 2, 3\}$ e $\{1, 2\}$.

#### Entità **Appello**
Il campo `tipo` di Appello può assumere i valori *scritto* e *orale*

---

## Progettazione Logica $\rightarrow$ Traduzione
**segretario**(<u>email</u>, password, nome, cognome)

**docente**(<u>email</u>, password, nome, cognome)

**corso_di_laurea**(<u>nome</u>, tipo)

**studente**(<u>matricola</u>, email, password, nome, cognome, *corso*) \
corso -> corso_di_laurea(nome)

**insegnamento**(<u>codice, *corso*</u>, anno, descrizione, *responsabile*) \
corso -> corso_di_laurea(nome) \
responsabile -> docente(email)

**propedeuticità**(<u>*codice_insegnamento*, *corso_insegnamento*, *codice_propedeutico*, *corso_propedeutico*</u>) \
(codice_insegnamento, corso_insegnamento) -> insegnamento(codice, corso) \
(codice_propedeutico, corso_propedeutico) -> insegnamento(codice, corso)

**appello**(<u>data, *insegnamento*, *corso*</u>, tipo) \
(insegnamento, corso) -> insegnamento(codice, corso)

**sostiene**(<u>*studente*, *data*, *insegnamento*, *corso*</u>, *voto) \
studente -> studente(matricola) \
(data, insegnamento, corso) -> appello(data, insegnamento, corso)

# Funzionalità

## Login

Il login, che è anche la pagina iniziale dell'applicazione web (`/index.php`),
è costituito da un unico form in cui si può selezionare il tipo di utente per
l'accesso e quindi poi le credenziali. Tutti il sito è tematizzato in base all'utente
con cui si effettua l'autenticazione.

Inoltre nella pagina iniziale è presente anche un link accessibile da chiunque per
poter vedere tutti i corsi e i relativi insegnamenti.

## Studente

Una volta effettuato il login come studente, sarà possibile vedere tutte le proprie
informazioni personali e modificarle alcune (password, telefono e indirizzo) tramite
un bottone posto accando che trasforma il campo desiderato in un form per inserire il
nuovo valore, con la possibilità di annullare la modifica prima di averla inviata.

Sotto sarà possibile vedere a quale corso di studi si è iscritti e, divisi per anno, tutti
gli esami del nostro corso. Ogni insegnamento elencato avrà un link rapido per andare a vedere
gli appelli di quello specifico insegnamento, mentre sopra vi sono due link, uno per andare a tutti
gli appelli, l'altro per andare a consultare la propria carriera.

### Appelli

La sezione degli appelli serve sia per vedere gli appelli a cui si è iscritti ma che non sono ancora
avvenuti oppure valutati, sia per iscriversi a dei nuovi appelli.

Il filtro che ha effetto su tutta la pagina e filtra in base a una corrispondenza anche parziale sia
con il nome dell'insegnamento che con il codice
Se la data dell'appello non è ancora passata, è possibile disiscriversi con un bottone.

### Carriera

La sezione carriera permette di visualizzare sia la carriera valida in un'unica tabella, sia di
vedere lo storico di tutti gli esami sostenuti, con i relativi voti.

Sul fondo di questa sezione è presente il form per la rinuncia agli studi, il quale per editare
incidenti richiede che sia data la conferma dell'azione prima di procedere. Una volta che uno studente
viene rimosso, questo verrà spostato in una tabella a parte in cui verranno trattenute solo alcune informazioni
originali, quali `Nome, Cognome, Email, Matricola`, mentre in un'altra tabella verranno conservati gli esami dati,
con riferimento al giorno dello svolgimento, il codice dell'insegnamento, il corso di appartenenza e il voto.

## Docente

Una volta effettuato il login come docente, sarà possibile vedere tutte le proprie
informazioni personali e modificare la password. Inoltre subito sotto è possibile
vedere di quali insegnamenti si è responsabile e quindi andare agli appelli dei propri
insegnamenti.

### Appelli

In questa sezione sarà possibile:
- creare nuovi appelli per i propri insegnamenti
- cancellare gli appelli che non sono ancora avvenuti
- accedere al registro voti per gli appelli già avvenuti
  per dare i voti agli studenti che li hanno sostenuti

Come in precedenza, è possibile usare il filtro per avere visione solo di una selezioni di tali.

## Segreteria

Una volta effettuato il login come docente, sarà possibile vedere tutte le proprie
informazioni personali e modificare la password. Inoltre subito sotto è possibile
accdere alle varie funzionalità tramite 4 link.

### Gestione Studenti

Nella sezione studenti è possibile vedere tutti gli studenti iscritti e rimossi in due tabelle separate,
con dei link per poi andare a gestire il singolo utente. Possono essere anche filtrati per cognome o matricola.

Inoltre è possibile creare nuovi studenti associandoli da subito a un corso di laurea.

Per gli studenti iscritti è possibile modificare tutti i cambi, fatta eccezione della matricola; inoltre è possibile
vedere separatamente la carriera valida e quella completa, come nell'area dedicata agli studenti, e ritirarlo dagli
studi.

Invece per gli studenti disiscritti è possibile vedere i dati e la carriera completa salvata.

### Gestione Docenti

Come nella sezione studenti, è presente l'elenco di tutti i docenti filtrabili per cognome e la possibilità di
creare di nuovi. Al momento della creazione non è richiesto che il docente venga subito associato ad un insegnamento.

Nella gestione del singolo account è possibile modificare ogni singola informazione, inclusa l'email, e rimuovere, se
il database lo permette, l'acount interamente. Questo è possibile solo se il docente non è associato ad alcun insegnamento.

### Gestione Corsi di Laurea

Identico alle pagine precedenti, è possibile vedere i corsi di laurea e crearne di nuovi. Come per i docenti,
un corso di laurea appena creato non ha insegnamenti associati, ma non è possibile iscrivere uno studente in un
corso di laurea senza insegnamenti; questo è dovuto per semplificare l'interfaccia grafica e renderla meno complessa
e più intuitiva.

Di ogni corso può esserne cambiato il nome, rimossi degli insegnamenti ed eliminato l'intero corso, se le dipendenze
interne al database lo permettono.

### Gestione Insegnamenti

Identico alle pagine precedenti, è possibile vedere gli insegnamenti e crearne di nuovi; è presente un filtro
che agisce su codice dell'insegnamento, nome o corso di appartenenza. Quando si crea un insegnamento, è richiesto
specificare in quale corso di laurea inserirlo, in che anno (il controllo della correttezza dell'anno verrà effettuata
a posteriori dal database), e il docente responsabile da associargli, tra quelli che possono avere ancora insegnamenti.

Di ogni insegnamento può essere cambiato ogni informazione, inoltre si possono gestire le propedeuticità, sia aggiungerle
che rimuoverle, rispettando le regole:
- Gli insegnamenti devono appartenere allo stesso corso di laurea
- L'insegnamento propedeutico deve essere antecedente
- Non è possibile avere una propedeuticità ciclica

# Utilizzo del progetto

Il progetto è interamente sviluppato utilizzando esclusivamente le seguenti tecnologie:
- PostgreSQL per il database
- HTML e CSS per la struttura delle pagine e la grafica
- PHP per lo scripting lato server
- Javascript (vanilla) per effettuare qualche form "dinamico" e programmazione sull'interfaccia
  grafica

Il processo PHP installato sul server deve avere disponibili e installate le librerie di
PostgreSQL, inoltre dovrebbe avere come configurazione di default disabilitati gli errori a schermo,
in quando tutti gli errori possibili proveniente dalle query SQL sono poi catturati e mostrati
sull'interfaccia grafica.

Tutto il materiale utile al web server (per esempio Apache o NginX) è presente nella cartella `public`,
quindi o spostare il contenuto nella cartella di default usata dai web server per servire le richieste,
oppure cambiare la configurazione di base per puntare a quella cartella.

Tutte le query verso il databse proveniente da PHP sono configurate per collegarsi a un DB in `localhost`,
usando come username `bdlab` e password `bdlab`, e il nome del database è `project`. Però tutti questi
campi possono essere modificati semplicemente cambiano i dati nella funzione `db_connect()` presente dentro
la cartella `public` all'indirizzo `assets/php/db.php`. L'unica necessità, come impostato dal dump del database,
è che lo schema sia esattamente `uni`.
