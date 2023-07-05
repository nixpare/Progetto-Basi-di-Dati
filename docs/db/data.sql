-- corso_laurea
INSERT INTO corso_laurea (nome, tipo) VALUES
    ('Informatica', 'triennale'),
    ('Matematica', 'magistrale'),
    ('Fisica', 'triennale'),
    ('Chimica', 'magistrale'),
    ('Economia', 'triennale');

-- segretario
INSERT INTO segretario (email, password, nome, cognome) VALUES
    ('segretario1@example.com', 'password1', 'John', 'Doe'),
    ('segretario2@example.com', 'password2', 'Jane', 'Smith');

-- docente
INSERT INTO docente (email, password, nome, cognome) VALUES
    ('docente1@example.com', 'password1', 'Michael', 'Johnson'),
    ('docente2@example.com', 'password2', 'Sarah', 'Wilson');

-- studente
INSERT INTO studente (matricola, email, password, nome, cognome, tel, indirizzo, corso) VALUES
    ('000001', 'studente1@example.com', 'password1', 'Robert', 'Brown', '123456789', '123 Main St', 'Informatica'),
    ('000002', 'studente2@example.com', 'password2', 'Emily', 'Davis', '987654321', '456 Elm St', 'Matematica'),
    ('000003', 'studente3@example.com', 'password3', 'David', 'Miller', '555555555', '789 Oak St', 'Informatica');

-- insegnamento
INSERT INTO insegnamento (codice, corso, anno, nome, descrizione, responsabile) VALUES
    ('INS001', 'Informatica', 1, 'Programming Fundamentals', 'Introduction to programming concepts', 'docente1@example.com'),
	('INS002', 'Informatica', 3, 'Database', 'Manage databases', 'docente1@example.com'),
    ('INS002', 'Matematica', 2, 'Advanced Calculus', 'Higher-level calculus topics', 'docente2@example.com');

-- propedeuticità
INSERT INTO propedeuticità (codice_insegnamento, corso_insegnamento, codice_propedeutico, corso_propedeutico) VALUES
    ('INS002', 'Informatica', 'INS001', 'Informatica');

-- appello
INSERT INTO appello (data, insegnamento, corso, tipo) VALUES
    ('2023-07-10', 'INS001', 'Informatica', 'scritto'),
    ('2023-07-15', 'INS002', 'Matematica', 'orale');

-- sostiene
INSERT INTO sostiene (studente, data, insegnamento, corso, voto) VALUES
    ('000001', '2023-07-10', 'INS001', 'Informatica', 28),
    ('000002', '2023-07-15', 'INS002', 'Matematica', NULL);

-- studente_rimosso
INSERT INTO studente_rimosso (matricola, email, nome, cognome) VALUES
    ('000004', 'studente4@example.com', 'Laura', 'Wilson'),
    ('000005', 'studente5@example.com', 'Mark', 'Taylor');

-- carriera_rimossa
INSERT INTO carriera_rimossa (studente, data, insegnamento, corso, voto) VALUES
    ('000004', '2023-06-30', 'INS001', 'Informatica', 25),
    ('000005', '2023-06-30', 'INS002', 'Matematica', 22);

-- corso_laurea
INSERT INTO corso_laurea (nome, tipo) VALUES
    ('Elettronica', 'triennale'),
    ('Architettura', 'magistrale'),
    ('Storia dell arte', 'triennale'),
    ('Biologia', 'magistrale'),
    ('Scienze politiche', 'triennale');

-- docente
INSERT INTO docente (email, password, nome, cognome) VALUES
    ('docente3@example.com', 'password3', 'Emma', 'Wilson'),
    ('docente4@example.com', 'password4', 'Matthew', 'Davis');

-- studente
INSERT INTO studente (matricola, email, password, nome, cognome, tel, indirizzo, corso) VALUES
    ('000004', 'studente4@example.com', 'password4', 'Sophia', 'Taylor', '111111111', '321 Maple St', 'Elettronica')

-- insegnamento
INSERT INTO insegnamento (codice, corso, anno, nome, descrizione, responsabile) VALUES
    ('INS003', 'Elettronica', 1, 'Digital Electronics', 'Introduction to digital circuits', 'docente3@example.com'),
    ('INS004', 'Architettura', 2, 'Urban Planning', 'Study of urban design and planning principles', 'docente4@example.com');

-- propedeuticità
INSERT INTO propedeuticità (codice_insegnamento, corso_insegnamento, codice_propedeutico, corso_propedeutico) VALUES
    ('INS003', 'Elettronica', 'INS001', 'Informatica'),
    ('INS004', 'Architettura', 'INS002', 'Matematica');

-- appello
INSERT INTO appello (data, insegnamento, corso, tipo) VALUES
    ('2023-07-20', 'INS003', 'Elettronica', 'scritto'),
    ('2023-07-25', 'INS004', 'Architettura', 'orale');

-- sostiene
INSERT INTO sostiene (studente, data, insegnamento, corso, voto) VALUES
    ('000004', '2023-07-20', 'INS003', 'Elettronica', 26)

-- studente_rimosso
INSERT INTO studente_rimosso (matricola, email, nome, cognome) VALUES
    ('000007', 'studente7@example.com', 'Ava', 'Clark'),
    ('000008', 'studente8@example.com', 'James', 'Johnson');

-- carriera_rimossa
INSERT INTO carriera_rimossa (studente, data, insegnamento, corso, voto) VALUES
    ('000007', '2023-06-30', 'INS001', 'Informatica', 22),
    ('000008', '2023-06-30', 'INS002', 'Matematica', 20);
