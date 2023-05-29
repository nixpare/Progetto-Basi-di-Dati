create schema uni;

set search_path to uni;

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
    matricola varchar(6) primary key,
    email email not null unique,
    password varchar(24) not null,
    nome varchar(20) not null,
    cognome varchar(20) not null
);

-- funzione trigger per segretario, studente e docente per verficare
-- che la mail sia univoca tra tutti
create or replace function check_email_unique_i_u_f()
    returns trigger
language plpgsql as $$
    begin
        NEW.email := trim(lower(NEW.email));

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
