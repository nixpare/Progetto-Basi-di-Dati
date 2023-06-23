-- Inserimento dati nella tabella corso_laurea
INSERT INTO corso_laurea (nome, tipo) VALUES
    ('Informatica', 'triennale'),
    ('Matematica', 'triennale'),
    ('Fisica', 'magistrale'),
    ('Chimica', 'magistrale'),
    ('Biologia', 'triennale');

-- Inserimento dati nella tabella segretario
INSERT INTO segretario (email, password, nome, cognome) VALUES
    ('segretario1@example.com', 'password1', 'Mario', 'Rossi'),
    ('segretario2@example.com', 'password2', 'Giulia', 'Bianchi'),
    ('segretario3@example.com', 'password3', 'Luca', 'Verdi'),
    ('segretario4@example.com', 'password4', 'Laura', 'Neri'),
    ('segretario5@example.com', 'password5', 'Sara', 'Gialli');

-- Inserimento dati nella tabella docente
INSERT INTO docente (email, password, nome, cognome) VALUES
    ('docente1@example.com', 'password1', 'Antonio', 'Russo'),
    ('docente2@example.com', 'password2', 'Francesca', 'Marroni'),
    ('docente3@example.com', 'password3', 'Giovanni', 'Verdi'),
    ('docente4@example.com', 'password4', 'Simona', 'Rossi'),
    ('docente5@example.com', 'password5', 'Marco', 'Bianchi');

-- Inserimento dati nella tabella studente
INSERT INTO studente (matricola, email, password, nome, cognome, tel, indirizzo, corso) VALUES
    ('000001', 'studente1@example.com', 'password1', 'Luca', 'Gialli', '123456789', 'Via Roma 1', 'Informatica'),
    ('000002', 'studente2@example.com', 'password2', 'Laura', 'Marroni', '987654321', 'Via Venezia 2', 'Matematica'),
    ('000003', 'studente3@example.com', 'password3', 'Giuseppe', 'Verdi', '555555555', 'Via Milano 3', 'Fisica'),
    ('000004', 'studente4@example.com', 'password4', 'Maria', 'Rossi', '777777777', 'Via Napoli 4', 'Chimica'),
    ('000005', 'studente5@example.com', 'password5', 'Simone', 'Bianchi', '999999999', 'Via Firenze 5', 'Biologia');

-- Inserimento dati nella tabella insegnamento
INSERT INTO insegnamento (codice, corso, anno, nome, descrizione, responsabile) VALUES
    ('INF101', 'Informatica', 1, 'Programmazione', 'Corso introduttivo alla programmazione', 'docente1@example.com'),
    ('MAT201', 'Matematica', 2, 'Calcolo II', 'Corso avanzato di calcolo differenziale e integrale', 'docente2@example.com'),
    ('FIS301', 'Fisica', 3, 'Meccanica Quantistica', 'Corso avanzato sulla meccanica quantistica', 'docente3@example.com'),
    ('CHI401', 'Chimica', 4, 'Chimica Organica', 'Corso avanzato di chimica organica', 'docente4@example.com'),
    ('BIO501', 'Biologia', 5, 'Genetica', 'Corso avanzato di genetica', 'docente5@example.com');

-- Inserimento dati nella tabella propedeuticità
INSERT INTO propedeuticità (codice_insegnamento, corso_insegnamento, codice_propedeutico, corso_propedeutico) VALUES
    ('INF101', 'Informatica', 'MAT201', 'Matematica'),
    ('MAT201', 'Matematica', 'FIS301', 'Fisica'),
    ('FIS301', 'Fisica', 'CHI401', 'Chimica'),
    ('CHI401', 'Chimica', 'BIO501', 'Biologia'),
    ('BIO501', 'Biologia', 'INF101', 'Informatica');

-- Inserimento dati nella tabella appello
INSERT INTO appello (data, insegnamento, corso, tipo) VALUES
    ('2023-06-01', 'INF101', 'Informatica', 'scritto'),
    ('2023-06-02', 'MAT201', 'Matematica', 'scritto'),
    ('2023-06-03', 'FIS301', 'Fisica', 'orale'),
    ('2023-06-04', 'CHI401', 'Chimica', 'orale'),
    ('2023-06-05', 'BIO501', 'Biologia', 'scritto');

-- Inserimento dati nella tabella sostiene
INSERT INTO sostiene (studente, data, insegnamento, corso, voto) VALUES
    ('000001', '2023-06-01', 'INF101', 'Informatica', 28),
    ('000002', '2023-06-01', 'INF101', 'Informatica', 25),
    ('000003', '2023-06-02', 'MAT201', 'Matematica', 30),
    ('000004', '2023-06-02', 'MAT201', 'Matematica', 27),
    ('000005', '2023-06-03', 'FIS301', 'Fisica', NULL);
