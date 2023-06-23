create schema uni;

set search_path to uni;

create table corso_laurea (
    nome varchar(50) primary key,
    tipo varchar(10) not null check (tipo = 'triennale' or tipo = 'magistrale')
);

create domain email as varchar(100) check ( value like '%@%.%' );

create table segretario (
    email email primary key,
    password varchar(24) not null,
    nome varchar(20) not null,
    cognome varchar(20) not null
);

create table docente (
    email email primary key,
    password varchar(24) not null,
    nome varchar(20) not null,
    cognome varchar(20) not null
);

create table studente (
    matricola char(6) primary key,
    email email not null unique,
    password varchar(24) not null,
    nome varchar(20) not null,
    cognome varchar(20) not null,
    tel varchar(15) not null,
    indirizzo varchar(100) not null,
    corso varchar(50) not null references corso_laurea(nome) on update cascade
);

create table insegnamento (
    codice varchar(10),
    corso varchar(50) references corso_laurea(nome) on update cascade,
    anno int not null check ( anno = 1 or anno = 2 or anno = 3 ),
    nome varchar(50) not null,
    descrizione text not null,
    responsabile email not null references docente(email) on update cascade,
    primary key (codice, corso)
);

create table propedeuticità (
    codice_insegnamento varchar(10),
    corso_insegnamento varchar(50),
    codice_propedeutico varchar(10),
    corso_propedeutico varchar(50),
    primary key (codice_insegnamento, corso_insegnamento, codice_propedeutico, corso_propedeutico),
    foreign key (codice_insegnamento, corso_insegnamento) references insegnamento(codice, corso)
                                                            on update cascade on delete cascade,
    foreign key (codice_propedeutico, corso_propedeutico) references insegnamento(codice, corso)
                                                            on update cascade on delete cascade
);

create table appello (
    data date,
    insegnamento varchar(10),
    corso varchar(50),
    tipo varchar(7) not null check (tipo = 'scritto' or tipo = 'orale'),
    primary key (data, insegnamento, corso),
    foreign key (insegnamento, corso) references insegnamento(codice, corso) on update cascade
);

create table sostiene (
    studente char(6),
    data date,
    insegnamento varchar(10),
    corso varchar(50),
    voto int check ( voto is null or (voto >= 0 and voto <= 30) ),
    primary key (studente, data, insegnamento, corso),
    foreign key (data, insegnamento, corso) references appello on update cascade
);
