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
