-- tests per il tipo di corso di laurea
insert into uni.corso_laurea values ('Informatica', 'triennale'); -- ok
insert into uni.corso_laurea values ('Informatica musicale', 'trienale'); -- fail
insert into uni.corso_laurea values ('Informatica musicale', 'Triennale'); -- fail
insert into uni.corso_laurea values ('Informatica musicale', 'magistrale'); -- ok

delete from uni.corso_laurea where nome = 'Informatica' or nome = 'Informatica musicale';

-- test per l'inserimento degli utenti (email)
insert into uni.segretario values
    ('segr.01@unimi.it', 'password', 'Nome', 'Cognome'); -- ok
delete from uni.segretario where email = 'segr.01@unimi.it';

insert into uni.segretario values
    ('segr.01@unimi_it', 'password', 'Nome', 'Cognome'); -- fail

insert into uni.segretario values
    ('segr.01.unimi.it', 'password', 'Nome', 'Cognome'); -- fail

insert into uni.segretario values
    ('segr01@unimi.it', 'password', 'Nome', 'Cognome'); -- ok
delete from uni.segretario where email = 'segr01@unimi.it';

-- test univocità tra mail
insert into uni.segretario values
    ('utente1@unimi.it', 'password', 'Nome', 'Cognome'); -- ok
insert into uni.docente values
    ('utente2@unimi.it', 'password', 'Nome', 'Cognome'); -- ok
insert into uni.studente values
    ('123456', 'utente3@unimi.it', 'password',
    'Nome', 'Cognome', '+39 3891234567', 'Viale dei Test 0, Città Testata',
    'Corso di Test'); -- ok

insert into uni.segretario values
    ('utente2@unimi.it', 'password', 'Nome', 'Cognome'); -- fail
insert into uni.docente values
    ('utente3@unimi.it', 'password', 'Nome', 'Cognome'); -- fail
insert into uni.studente values
    ('654321', 'utente1@unimi.it', 'password',
    'Nome', 'Cognome', '+39 3891234567', 'Viale dei Test 0, Città Testata',
    'Corso di Test'); -- fail

delete from uni.segretario where email = 'utente1@unimi.it';
delete from uni.docente where email = 'utente2@unimi.it';
delete from uni.studente where email = 'utente3@unimi.it';

-- test insegnamento -> anno
insert into uni.insegnamento values
    ('TEST-00', 'Corso di Test', 1, 'Insegnamento di Test 0', 'Descrizione di Test numero 00', 'docente@test.it'); -- ok
delete from uni.insegnamento where codice = 'TEST-00';

insert into uni.insegnamento values
    ('TEST-00', 'Corso di Test', 2, 'Insegnamento di Test 00', 'Descrizione di Test numero 00', 'docente@test.it'); -- ok
delete from uni.insegnamento where codice = 'TEST-00';

insert into uni.insegnamento values
    ('TEST-00', 'Corso di Test', 3, 'Insegnamento di Test 00', 'Descrizione di Test numero 00', 'docente@test.it'); -- ok
delete from uni.insegnamento where codice = 'TEST-00';

insert into uni.insegnamento values
    ('TEST-00', 'Corso di Test', 0, 'Insegnamento di Test 00', 'Descrizione di Test numero 00', 'docente@test.it'); -- fail

insert into uni.insegnamento values
    ('TEST-00', 'Corso di Test', 4, 'Insegnamento di Test 00', 'Descrizione di Test numero 00', 'docente@test.it'); -- fail

insert into uni.insegnamento values
    ('TEST-00', 'Corso di Test 2', 1, 'Insegnamento di Test 2 00', 'Descrizione di Test 2 numero 00', 'docente@test.it'); -- ok
delete from uni.insegnamento where codice = 'TEST-00';

insert into uni.insegnamento values
    ('TEST-00', 'Corso di Test 2', 2, 'Insegnamento di Test 2 00', 'Descrizione di Test 2 numero 00', 'docente@test.it'); -- ok
delete from uni.insegnamento where codice = 'TEST-00';

insert into uni.insegnamento values
    ('TEST-00', 'Corso di Test 2', 0, 'Insegnamento di Test 2 00', 'Descrizione di Test 2 numero 00', 'docente@test.it'); -- fail
insert into uni.insegnamento values
    ('TEST-00', 'Corso di Test 2', 3, 'Insegnamento di Test 2 00', 'Descrizione di Test 2 numero 00', 'docente@test.it'); -- fail

-- test docente non può avere più di tre corsi
insert into uni.insegnamento values
    ('TEST-00', 'Corso di Test', 1, 'Insegnamento di Test 00', 'Descrizione di Test numero 00', 'docente@test.it'); -- ok
insert into uni.insegnamento values
    ('TEST-00', 'Corso di Test 2', 1, 'Insegnamento di Test 2 00', 'Descrizione di Test 2 numero 00', 'docente@test.it'); -- ok
insert into uni.insegnamento values
    ('TEST-00', 'Corso di Test', 1, 'Insegnamento di Test 00', 'Descrizione di Test numero 00', 'docente@test.it'); -- fail
insert into uni.insegnamento values
    ('TEST-01', 'Corso di Test', 1, 'Insegnamento di Test 01', 'Descrizione di Test numero 01', 'docente@test.it'); -- ok
insert into uni.insegnamento values
    ('TEST-02', 'Corso di Test', 1, 'Insegnamento di Test 02', 'Descrizione di Test numero 02', 'docente@test.it'); -- fail

delete from uni.insegnamento where codice like 'TEST%';

-- test
insert into uni.sostiene values ('000000', '2023-06-15', 'INS-TEST01', 'Corso di Test', NULL); -- ok
insert into uni.sostiene values ('000000', '2023-06-15', 'INS-TEST02', 'Corso di Test', NULL); -- ok
insert into uni.sostiene values ('000000', '2023-06-15', 'INS-TEST01', 'Corso di Test 2', NULL); -- fail

delete from uni.sostiene where studente = '000000';
