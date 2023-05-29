-- inseriti per il test
insert into uni.corso_laurea values
    ('Corso di Test', 'triennale');
insert into uni.corso_laurea values
    ('Corso di Test 2', 'magistrale');
insert into uni.segretario values
    ('segretario@test.it', 'password', 'Nome Segretario', 'Cognome Segretario');
insert into uni.docente values
    ('docente@test.it', 'password', 'Nome Docente', 'Cognome Docente');
insert into uni.studente values
    ('000000', 'studente@test.it', 'password', 'Nome Studente', 'Cognome Studente', 'Corso di Test');

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
    ('123456', 'utente3@unimi.it', 'password', 'Nome', 'Cognome', 'Corso di Test'); -- ok

insert into uni.segretario values
    ('utente2@unimi.it', 'password', 'Nome', 'Cognome'); -- fail
insert into uni.docente values
    ('utente3@unimi.it', 'password', 'Nome', 'Cognome'); -- fail
insert into uni.studente values
    ('654321', 'utente1@unimi.it', 'password', 'Nome', 'Cognome', 'Corso di Test'); -- fail

delete from uni.segretario where email = 'utente1@unimi.it';
delete from uni.docente where email = 'utente2@unimi.it';
delete from uni.studente where email = 'utente3@unimi.it';

-- test insegnamento e docente non può avere più di tre corsi
insert into uni.insegnamento values
    ('TEST-00', 'Corso di Test', '2023', 'Descrizione di Test numero 00', 'docente@test.it'); -- ok
insert into uni.insegnamento values
    ('TEST-00', 'Corso di Test 2', '2022', 'Descrizione di Test 2 numero 00', 'docente@test.it'); -- ok
insert into uni.insegnamento values
    ('TEST-00', 'Corso di Test', '2021', 'Descrizione di Test numero 00', 'docente@test.it'); -- fail
insert into uni.insegnamento values
    ('TEST-01', 'Corso di Test', '2023', 'Descrizione di Test numero 01', 'docente@test.it'); -- ok
insert into uni.insegnamento values
    ('TEST-02', 'Corso di Test', '2023', 'Descrizione di Test numero 02', 'docente@test.it'); -- fail

delete from uni.insegnamento where codice like 'TEST%';


