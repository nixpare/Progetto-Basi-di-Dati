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
    ('123456', 'utente3@unimi.it', 'password', 'Nome', 'Cognome'); -- ok

insert into uni.segretario values
    ('utente2@unimi.it', 'password', 'Nome', 'Cognome'); -- fail
insert into uni.docente values
    ('utente3@unimi.it', 'password', 'Nome', 'Cognome'); -- fail
insert into uni.studente values
    ('654321', 'utente1@unimi.it', 'password', 'Nome', 'Cognome'); -- fail
