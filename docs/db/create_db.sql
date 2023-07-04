create schema uni;

set search_path to uni;

create table corso_laurea (
    nome varchar(50) primary key,
    tipo varchar(10) not null check (tipo = 'triennale' or tipo = 'magistrale')
);

-- dominio email per verificare approssimativamente
-- che l'input sia simile a una mail e non un testo normale
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
-- sulla cancellazione del corso si applica la policy di NO ACTION perchè se
-- alla fine della transazione questo studente dovrebbe trovarsi ancora nel
-- DB senza un corso valido, allora la transazione deve essere bloccata

-- funzione trigger per segretario, studente e docente per verficare
-- che la mail sia univoca tra tutti
create or replace function check_email_unique_i_u_f()
    returns trigger
language plpgsql as $$
    begin
        NEW.email := trim(lower(NEW.email));

        if (trim(lower(OLD.email)) = NEW.email) then
            return NEW;
        end if;

        -- check for segretario
        perform * from uni.segretario where trim(lower(email)) = NEW.email;
        if (FOUND) then
            raise 'Email "%" già usata da un segretario', NEW.email;
            -- return NULL;
        end if;

        -- check for docente
        perform * from uni.docente where trim(lower(email)) = NEW.email;
        if (FOUND) then
            raise 'Email "%" già usata da un docente', NEW.email;
            -- return NULL;
        end if;

        -- check for studente
        perform * from uni.studente where trim(lower(email)) = NEW.email;
        if (FOUND) then
            raise 'Email "%" già usata da uno studente', NEW.email;
            -- return NULL;
        end if;

        return NEW;
    end;
$$;

-- creo i trigger con la funzione sopra
create or replace trigger check_email_unique_i_u_t
    before insert or update on uni.segretario
    for each row execute function check_email_unique_i_u_f();

create or replace trigger check_email_unique_i_u_t
    before insert or update on uni.docente
    for each row execute function check_email_unique_i_u_f();

create or replace trigger check_email_unique_i_u_t
    before insert or update on uni.studente
    for each row execute function check_email_unique_i_u_f();

--- create funzione e trigger che controllino che il corso di studente abbia almeno un insegnamento

create or replace function studente_corso_laurea_i_u_f () 
	returns trigger
language plpgsql as $$
	begin
		perform * from uni.insegnamento
		where insegnamento.corso = NEW.corso;

		if not FOUND then
			raise 'Il corso % non ha insegnamenti associati', NEW.corso;
		end if;

		return NEW;
	end;
$$;

create or replace trigger studente_corso_laurea_i_u_t
    before insert or update on uni.studente
    for each row execute function studente_corso_laurea_i_u_f();

create table insegnamento (
    codice varchar(10),
    corso varchar(50) references corso_laurea(nome) on update cascade,
    anno int not null check ( anno = 1 or anno = 2 or anno = 3 ),
    nome varchar(50) not null,
    descrizione text not null,
    responsabile email not null references docente(email) on update cascade,
    primary key (codice, corso)
);
-- un insegnamento non può non avere un corso, quindi default policy on delete
-- un insegnamento non può non avere un docente che tiene il corso, quindi default policy on delete

-- creo la funzione e il trigger che controllano il valore di anno
create or replace function check_anno_insegnamento_i_u_f()
    returns trigger
language plpgsql as $$
    declare
        tipo_corso uni.corso_laurea.tipo%type;
    begin
        select tipo into tipo_corso from uni.corso_laurea where nome = NEW.corso;
        if (tipo_corso = 'magistrale' and NEW.anno = 3) then
            raise 'L''insegnamento "%" per il corso "%" (magistrale) non può essere al terzo anno', NEW.codice, NEW.corso;
        end if;

        return NEW;
    end;
$$;

create or replace trigger check_anno_insegnamento_i_u_t
    before insert or update on uni.insegnamento
    for each row execute function uni.check_anno_insegnamento_i_u_f();

-- creo la funzione e il trigger per l'inserimento o aggiornamento su insegnamento
-- per constrollare che il docente responsabile non abbia più di 3 insegnamenti
create or replace function check_insegnamenti_docente_i_u_f()
    returns trigger
language plpgsql as $$
    declare
        n int := 0;
        doc uni.docente%rowtype;
    begin
        select count(*) into n
        from uni.insegnamento
        where responsabile = NEW.responsabile;

        if (n >= 3) then
            select * into doc from uni.docente where email = NEW.responsabile;
            raise 'Il docente "% %" ha già 3 insegnamenti assegnati', doc.nome, doc.cognome;
        end if;

        return NEW;
    end;
$$;

create or replace trigger check_insegnamenti_docente_i_u_t
    before insert or update on uni.insegnamento
    for each row execute function check_insegnamenti_docente_i_u_f();

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

-- creo una funzione e un trigger che controlli che non ci siano
-- altri appelli dello stesso anno del corso
create or replace function check_appelli_anno_i_u_f()
    returns trigger
language plpgsql as $$
    declare
        anno_appello uni.insegnamento.anno%type;
    begin
        select anno into anno_appello from uni.insegnamento
        where codice = NEW.insegnamento and corso = NEW.corso;

        perform * from
            uni.appello
            join
            uni.insegnamento on appello.insegnamento = insegnamento.codice and appello.corso = insegnamento.corso
        where appello.corso = NEW.corso and insegnamento.anno = anno_appello and appello.data = NEW.data;

        if (FOUND) then
            raise 'Un appello del corso "%" (anno %) è già registrato in data %', NEW.corso, anno_appello, NEW.data;
            -- return NULL;
        end if;

        return NEW;
    end;
$$;

create or replace trigger check_appelli_anno_i_u_t
    before insert or update on uni.appello
    for each row execute function check_appelli_anno_i_u_f();

create table sostiene (
    studente char(6),
    data date,
    insegnamento varchar(10),
    corso varchar(50),
    voto int check ( voto is null or (voto >= 0 and voto <= 30) ),
    primary key (studente, data, insegnamento, corso),
    foreign key (data, insegnamento, corso) references appello on update cascade
);

-- creo una funzione e un trigger che controlli che uno studente
-- si iscriva solo ed esclusivamente ad un insegnamento a cui è
-- iscritto
create or replace function check_studente_corso_appello_i_u_f()
    returns trigger
language plpgsql as $$
    declare
        corso_studente uni.studente.corso%type;
    begin
        select corso into corso_studente from uni.studente where matricola = NEW.studente;

        if (corso_studente <> NEW.corso) then
            raise 'Lo studente "%" non può iscriversi ad un appello del corso "%"', NEW.studente, NEW.corso;
        end if;

        return NEW;
    end;
$$;

create or replace trigger check_studente_corso_appello_i_u_t
    before insert or update on uni.sostiene
    for each row execute function check_studente_corso_appello_i_u_f();

create or replace function get_carriera_completa(matricola uni.studente.matricola%type)
    returns table (
        data uni.sostiene.data%type,
        insegnamento uni.sostiene.insegnamento%type,
        nome uni.insegnamento.nome%type,
        anno uni.insegnamento.anno%type,
        tipo uni.appello.tipo%type,
        voto uni.sostiene.voto%type
    )
language plpgsql as $$
    begin
        return query (
            select sostiene.data, sostiene.insegnamento, insegnamento.nome, insegnamento.anno, appello.tipo, sostiene.voto from
                uni.studente
                join
                uni.sostiene on studente.matricola = sostiene.studente and studente.corso = sostiene.corso
                join
                uni.appello on sostiene.data = appello.data and sostiene.insegnamento = appello.insegnamento and sostiene.corso = appello.corso
                join
                uni.insegnamento on sostiene.insegnamento = insegnamento.codice and sostiene.corso = insegnamento.corso
            where sostiene.voto is not null
            order by insegnamento.anno, insegnamento.nome, sostiene.data
        );
    end;
$$;

-- inseriti per il test
insert into uni.corso_laurea values
    ('Corso di Test', 'triennale');
insert into uni.corso_laurea values
    ('Corso di Test 2', 'magistrale');

insert into uni.segretario values
    ('segretario@test.it', 'password', 'Nome Segretario', 'Cognome Segretario');
insert into uni.docente values
    ('docente@test.it', 'password', 'Nome Docente', 'Cognome Docente');
insert into uni.docente values
    ('docente2@test.it', 'password', 'Nome Docente 2', 'Cognome Docente 2');
insert into uni.studente values
    ('000000', 'studente@test.it', 'password',
    'Nome Studente', 'Cognome Studente', '+39 3891234567', 'Viale dei Test 0, Città Testata',
    'Corso di Test');

insert into uni.insegnamento values
    ('INS-TEST01', 'Corso di Test', 1, 'Insegnamento di Test 1 Triennale', 'Descrizione Insegnamento di Test 1 Triennale', 'docente@test.it');
insert into uni.insegnamento values
    ('INS-TEST02', 'Corso di Test', 2, 'Insegnamento di Test 2', 'Descrizione Insegnamento di Test 2', 'docente@test.it');
insert into uni.insegnamento values
    ('INS-TEST01', 'Corso di Test 2', 1, 'Insegnamento di Test 1 Magistrale', 'Descrizione Insegnamento di Test 1 Magistrale', 'docente@test.it');
insert into uni.insegnamento values
    ('INS-TEST00', 'Corso di Test', 1, 'Insegnamento di Test 0', 'Descrizione Insegnamento di Test 0', 'docente2@test.it');
insert into uni.insegnamento values
    ('INS-TEST04', 'Corso di Test', 2, 'Insegnamento di Test 4', 'Descrizione Insegnamento di Test 4', 'docente2@test.it');

insert into uni.appello values
    ('2023-05-30', 'INS-TEST02', 'Corso di Test', 'orale');
insert into uni.appello values
    ('2023-06-15', 'INS-TEST01', 'Corso di Test', 'scritto');
insert into uni.appello values
    ('2023-06-15', 'INS-TEST02', 'Corso di Test', 'scritto');
insert into uni.appello values
    ('2023-06-15', 'INS-TEST01', 'Corso di Test 2', 'orale');
insert into uni.appello values
    ('2023-02-17', 'INS-TEST00', 'Corso di Test', 'scritto');
insert into uni.appello values
    ('2023-02-24', 'INS-TEST00', 'Corso di Test', 'orale');
insert into uni.appello values
    ('2023-06-30', 'INS-TEST00', 'Corso di Test', 'scritto');
insert into uni.appello values
    ('2023-01-17', 'INS-TEST04', 'Corso di Test', 'orale');
insert into uni.appello values
    ('2023-01-28', 'INS-TEST04', 'Corso di Test', 'orale');
insert into uni.appello values
    ('2023-06-27', 'INS-TEST04', 'Corso di Test', 'orale');

insert into uni.sostiene values
    ('000000', '2023-01-17', 'INS-TEST04', 'Corso di Test', 30);
insert into uni.sostiene values
    ('000000', '2023-01-28', 'INS-TEST04', 'Corso di Test', 15);
insert into uni.sostiene values
    ('000000', '2023-02-17', 'INS-TEST00', 'Corso di Test', 27);
insert into uni.sostiene values
    ('000000', '2023-02-24', 'INS-TEST00', 'Corso di Test', 24);
insert into uni.sostiene values
    ('000000', '2023-05-30', 'INS-TEST02', 'Corso di Test', NULL);
insert into uni.sostiene values
    ('000000', '2023-06-15', 'INS-TEST01', 'Corso di Test', NULL);
