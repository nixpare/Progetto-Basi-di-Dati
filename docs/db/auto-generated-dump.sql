--
-- PostgreSQL database dump
--

-- Dumped from database version 15.3
-- Dumped by pg_dump version 15.3

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: uni; Type: SCHEMA; Schema: -; Owner: bdlab
--

CREATE SCHEMA uni;


ALTER SCHEMA uni OWNER TO bdlab;

--
-- Name: email; Type: DOMAIN; Schema: uni; Owner: bdlab
--

CREATE DOMAIN uni.email AS character varying(100)
	CONSTRAINT email_check CHECK (((VALUE)::text ~~ '%@%.%'::text));


ALTER DOMAIN uni.email OWNER TO bdlab;

--
-- Name: check_anno_insegnamento_i_u_f(); Type: FUNCTION; Schema: uni; Owner: bdlab
--

CREATE FUNCTION uni.check_anno_insegnamento_i_u_f() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
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


ALTER FUNCTION uni.check_anno_insegnamento_i_u_f() OWNER TO bdlab;

--
-- Name: check_appelli_anno_i_u_f(); Type: FUNCTION; Schema: uni; Owner: bdlab
--

CREATE FUNCTION uni.check_appelli_anno_i_u_f() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
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


ALTER FUNCTION uni.check_appelli_anno_i_u_f() OWNER TO bdlab;

--
-- Name: check_corretta_propedeuticità_i_u_f(); Type: FUNCTION; Schema: uni; Owner: bdlab
--

CREATE FUNCTION uni."check_corretta_propedeuticità_i_u_f"() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
    begin
        if NEW.corso_insegnamento <> NEW.corso_propedeutico then
            raise 'La propedeuticità deve esitere nello stesso corso';
        end if;

        if NEW.codice_insegnamento = NEW.codice_propedeutico then
            raise 'Un esame non può essere propedeutico con se stesso';
        end if;

        perform * from uni.propedeuticità
        where codice_insegnamento = NEW.codice_propedeutico and NEW.codice_insegnamento = codice_propedeutico;

        if FOUND then
            raise 'Propedeuticità ciclica: % è già propedeutico per %', NEW.codice_insegnamento, NEW.codice_propedeutico;
        end if;

        perform * from uni.insegnamento ins1, uni.insegnamento ins2
        where ins1.codice = NEW.codice_insegnamento and ins1.corso = NEW.corso_insegnamento and
                ins2.codice = NEW.codice_propedeutico and ins2.corso = NEW.corso_propedeutico and
                ins1.anno < ins2.anno;

        if FOUND then
            raise 'Un insegnamento non può essere propedeutico per uno di un anno precedente';
        end if;

        return NEW;
    end;
$$;


ALTER FUNCTION uni."check_corretta_propedeuticità_i_u_f"() OWNER TO bdlab;

--
-- Name: check_email_unique_i_u_f(); Type: FUNCTION; Schema: uni; Owner: bdlab
--

CREATE FUNCTION uni.check_email_unique_i_u_f() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
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


ALTER FUNCTION uni.check_email_unique_i_u_f() OWNER TO bdlab;

--
-- Name: check_insegnamenti_docente_i_u_f(); Type: FUNCTION; Schema: uni; Owner: bdlab
--

CREATE FUNCTION uni.check_insegnamenti_docente_i_u_f() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
    declare
        n int := 0;
        doc uni.docente%rowtype;
    begin
        if (OLD.responsabile = NEW.responsabile) then
            return NEW;
        end if;

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


ALTER FUNCTION uni.check_insegnamenti_docente_i_u_f() OWNER TO bdlab;

--
-- Name: check_sostiene_propedeuticità_i_u_f(); Type: FUNCTION; Schema: uni; Owner: bdlab
--

CREATE FUNCTION uni."check_sostiene_propedeuticità_i_u_f"() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
    declare
        prop_codice uni.insegnamento.codice%type;
        prop_corso uni.insegnamento.corso%type := NEW.corso;
        voto uni.sostiene.voto%type;
    begin
        for prop_codice in
            select propedeuticità.codice_propedeutico from uni.propedeuticità
            where propedeuticità.codice_insegnamento = NEW.insegnamento and
                propedeuticità.corso_insegnamento = NEW.corso
        loop
            select sostiene.voto into voto from uni.sostiene
            where sostiene.insegnamento = prop_codice and
                sostiene.corso = prop_corso
            order by sostiene.data desc
            limit 1;

            if not FOUND then
                raise 'Propedeuticità non rispettata: mai sostenuto esame per %', prop_codice;
            end if;

            if voto is NULL then
                raise 'Propedeuticità non rispettata: iscritto a un esame di % senza voto', prop_codice;
            end if;

            if voto < 18 then
                raise 'Propedeuticità non rispettata: ultimo voto per esame di % insufficiente', prop_codice;
            end if;
        end loop;

        return NEW;
    end;
$$;


ALTER FUNCTION uni."check_sostiene_propedeuticità_i_u_f"() OWNER TO bdlab;

--
-- Name: check_studente_corso_appello_i_u_f(); Type: FUNCTION; Schema: uni; Owner: bdlab
--

CREATE FUNCTION uni.check_studente_corso_appello_i_u_f() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
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


ALTER FUNCTION uni.check_studente_corso_appello_i_u_f() OWNER TO bdlab;

--
-- Name: get_carriera_completa(character); Type: FUNCTION; Schema: uni; Owner: bdlab
--

CREATE FUNCTION uni.get_carriera_completa(matr character) RETURNS TABLE(data date, insegnamento character varying, nome character varying, anno integer, tipo character varying, voto integer)
    LANGUAGE plpgsql
    AS $$
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
            where studente.matricola = matr and sostiene.voto is not null
            order by insegnamento.anno, insegnamento.nome, sostiene.data
        );
    end;
$$;


ALTER FUNCTION uni.get_carriera_completa(matr character) OWNER TO bdlab;

--
-- Name: remove_studente_d_f(); Type: FUNCTION; Schema: uni; Owner: bdlab
--

CREATE FUNCTION uni.remove_studente_d_f() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
    declare
        risultato uni.sostiene%rowtype;
    begin
        insert into uni.studente_rimosso values (OLD.matricola, OLD.email, OLD.nome, OLD.cognome);

        for risultato in
            select * from uni.sostiene
            where sostiene.studente = OLD.matricola and voto is not null
        loop
            insert into uni.carriera_rimossa values (OLD.matricola, risultato.data, risultato.insegnamento, risultato.corso, risultato.voto);
        end loop;

        return OLD;
    end;
$$;


ALTER FUNCTION uni.remove_studente_d_f() OWNER TO bdlab;

--
-- Name: studente_corso_laurea_i_u_f(); Type: FUNCTION; Schema: uni; Owner: bdlab
--

CREATE FUNCTION uni.studente_corso_laurea_i_u_f() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
    begin
        perform * from uni.insegnamento
        where insegnamento.corso = NEW.corso;

        if not FOUND then
            raise 'Il corso % non ha insegnamenti associati', NEW.corso;
        end if;

        return NEW;
    end;
$$;


ALTER FUNCTION uni.studente_corso_laurea_i_u_f() OWNER TO bdlab;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: appello; Type: TABLE; Schema: uni; Owner: bdlab
--

CREATE TABLE uni.appello (
    data date NOT NULL,
    insegnamento character varying(10) NOT NULL,
    corso character varying(50) NOT NULL,
    tipo character varying(7) NOT NULL,
    CONSTRAINT appello_tipo_check CHECK ((((tipo)::text = 'scritto'::text) OR ((tipo)::text = 'orale'::text)))
);


ALTER TABLE uni.appello OWNER TO bdlab;

--
-- Name: carriera_rimossa; Type: TABLE; Schema: uni; Owner: bdlab
--

CREATE TABLE uni.carriera_rimossa (
    studente character(6) NOT NULL,
    data date NOT NULL,
    insegnamento character varying(10) NOT NULL,
    corso character varying(50) NOT NULL,
    voto integer NOT NULL,
    CONSTRAINT carriera_rimossa_voto_check CHECK (((voto >= 0) AND (voto <= 30)))
);


ALTER TABLE uni.carriera_rimossa OWNER TO bdlab;

--
-- Name: corso_laurea; Type: TABLE; Schema: uni; Owner: bdlab
--

CREATE TABLE uni.corso_laurea (
    nome character varying(50) NOT NULL,
    tipo character varying(10) NOT NULL,
    CONSTRAINT corso_laurea_tipo_check CHECK ((((tipo)::text = 'triennale'::text) OR ((tipo)::text = 'magistrale'::text)))
);


ALTER TABLE uni.corso_laurea OWNER TO bdlab;

--
-- Name: docente; Type: TABLE; Schema: uni; Owner: bdlab
--

CREATE TABLE uni.docente (
    email uni.email NOT NULL,
    password character varying(24) NOT NULL,
    nome character varying(20) NOT NULL,
    cognome character varying(20) NOT NULL
);


ALTER TABLE uni.docente OWNER TO bdlab;

--
-- Name: insegnamento; Type: TABLE; Schema: uni; Owner: bdlab
--

CREATE TABLE uni.insegnamento (
    codice character varying(10) NOT NULL,
    corso character varying(50) NOT NULL,
    anno integer NOT NULL,
    nome character varying(50) NOT NULL,
    descrizione text NOT NULL,
    responsabile uni.email NOT NULL,
    CONSTRAINT insegnamento_anno_check CHECK (((anno = 1) OR (anno = 2) OR (anno = 3)))
);


ALTER TABLE uni.insegnamento OWNER TO bdlab;

--
-- Name: propedeuticità; Type: TABLE; Schema: uni; Owner: bdlab
--

CREATE TABLE uni."propedeuticità" (
    codice_insegnamento character varying(10) NOT NULL,
    corso_insegnamento character varying(50) NOT NULL,
    codice_propedeutico character varying(10) NOT NULL,
    corso_propedeutico character varying(50) NOT NULL
);


ALTER TABLE uni."propedeuticità" OWNER TO bdlab;

--
-- Name: segretario; Type: TABLE; Schema: uni; Owner: bdlab
--

CREATE TABLE uni.segretario (
    email uni.email NOT NULL,
    password character varying(24) NOT NULL,
    nome character varying(20) NOT NULL,
    cognome character varying(20) NOT NULL
);


ALTER TABLE uni.segretario OWNER TO bdlab;

--
-- Name: sostiene; Type: TABLE; Schema: uni; Owner: bdlab
--

CREATE TABLE uni.sostiene (
    studente character(6) NOT NULL,
    data date NOT NULL,
    insegnamento character varying(10) NOT NULL,
    corso character varying(50) NOT NULL,
    voto integer,
    CONSTRAINT sostiene_voto_check CHECK (((voto IS NULL) OR ((voto >= 0) AND (voto <= 30))))
);


ALTER TABLE uni.sostiene OWNER TO bdlab;

--
-- Name: studente; Type: TABLE; Schema: uni; Owner: bdlab
--

CREATE TABLE uni.studente (
    matricola character(6) NOT NULL,
    email uni.email NOT NULL,
    password character varying(24) NOT NULL,
    nome character varying(20) NOT NULL,
    cognome character varying(20) NOT NULL,
    tel character varying(15) NOT NULL,
    indirizzo character varying(100) NOT NULL,
    corso character varying(50) NOT NULL
);


ALTER TABLE uni.studente OWNER TO bdlab;

--
-- Name: studente_rimosso; Type: TABLE; Schema: uni; Owner: bdlab
--

CREATE TABLE uni.studente_rimosso (
    matricola character(6) NOT NULL,
    email uni.email NOT NULL,
    nome character varying(20) NOT NULL,
    cognome character varying(20) NOT NULL
);


ALTER TABLE uni.studente_rimosso OWNER TO bdlab;

--
-- Data for Name: appello; Type: TABLE DATA; Schema: uni; Owner: bdlab
--

COPY uni.appello (data, insegnamento, corso, tipo) FROM stdin;
2023-07-10	INS001	Informatica	scritto
2023-07-15	INS002	Matematica	orale
2023-07-20	INS003	Elettronica	scritto
2023-07-25	INS004	Architettura	orale
\.


--
-- Data for Name: carriera_rimossa; Type: TABLE DATA; Schema: uni; Owner: bdlab
--

COPY uni.carriera_rimossa (studente, data, insegnamento, corso, voto) FROM stdin;
000004	2023-06-30	INS001	Informatica	25
000005	2023-06-30	INS002	Matematica	22
000007	2023-06-30	INS001	Informatica	22
000008	2023-06-30	INS002	Matematica	20
\.


--
-- Data for Name: corso_laurea; Type: TABLE DATA; Schema: uni; Owner: bdlab
--

COPY uni.corso_laurea (nome, tipo) FROM stdin;
Informatica	triennale
Matematica	magistrale
Fisica	triennale
Chimica	magistrale
Economia	triennale
Elettronica	triennale
Architettura	magistrale
Storia dell arte	triennale
Biologia	magistrale
Scienze politiche	triennale
\.


--
-- Data for Name: docente; Type: TABLE DATA; Schema: uni; Owner: bdlab
--

COPY uni.docente (email, password, nome, cognome) FROM stdin;
docente1@example.com	password1	Michael	Johnson
docente2@example.com	password2	Sarah	Wilson
docente3@example.com	password3	Emma	Wilson
docente4@example.com	password4	Matthew	Davis
\.


--
-- Data for Name: insegnamento; Type: TABLE DATA; Schema: uni; Owner: bdlab
--

COPY uni.insegnamento (codice, corso, anno, nome, descrizione, responsabile) FROM stdin;
INS001	Informatica	1	Programming Fundamentals	Introduction to programming concepts	docente1@example.com
INS002	Matematica	2	Advanced Calculus	Higher-level calculus topics	docente2@example.com
INS002	Informatica	3	Database	Manage databases	docente1@example.com
INS003	Elettronica	1	Digital Electronics	Introduction to digital circuits	docente3@example.com
INS004	Architettura	2	Urban Planning	Study of urban design and planning principles	docente4@example.com
\.


--
-- Data for Name: propedeuticità; Type: TABLE DATA; Schema: uni; Owner: bdlab
--

COPY uni."propedeuticità" (codice_insegnamento, corso_insegnamento, codice_propedeutico, corso_propedeutico) FROM stdin;
INS002	Informatica	INS001	Informatica
\.


--
-- Data for Name: segretario; Type: TABLE DATA; Schema: uni; Owner: bdlab
--

COPY uni.segretario (email, password, nome, cognome) FROM stdin;
segretario1@example.com	password1	John	Doe
segretario2@example.com	password2	Jane	Smith
\.


--
-- Data for Name: sostiene; Type: TABLE DATA; Schema: uni; Owner: bdlab
--

COPY uni.sostiene (studente, data, insegnamento, corso, voto) FROM stdin;
000001	2023-07-10	INS001	Informatica	28
000002	2023-07-15	INS002	Matematica	\N
000004	2023-07-20	INS003	Elettronica	26
\.


--
-- Data for Name: studente; Type: TABLE DATA; Schema: uni; Owner: bdlab
--

COPY uni.studente (matricola, email, password, nome, cognome, tel, indirizzo, corso) FROM stdin;
000001	studente1@example.com	password1	Robert	Brown	123456789	123 Main St	Informatica
000002	studente2@example.com	password2	Emily	Davis	987654321	456 Elm St	Matematica
000003	studente3@example.com	password3	David	Miller	555555555	789 Oak St	Informatica
000004	studente4@example.com	password4	Sophia	Taylor	111111111	321 Maple St	Elettronica
\.


--
-- Data for Name: studente_rimosso; Type: TABLE DATA; Schema: uni; Owner: bdlab
--

COPY uni.studente_rimosso (matricola, email, nome, cognome) FROM stdin;
000004	studente4@example.com	Laura	Wilson
000005	studente5@example.com	Mark	Taylor
000007	studente7@example.com	Ava	Clark
000008	studente8@example.com	James	Johnson
\.


--
-- Name: appello appello_pkey; Type: CONSTRAINT; Schema: uni; Owner: bdlab
--

ALTER TABLE ONLY uni.appello
    ADD CONSTRAINT appello_pkey PRIMARY KEY (data, insegnamento, corso);


--
-- Name: carriera_rimossa carriera_rimossa_pkey; Type: CONSTRAINT; Schema: uni; Owner: bdlab
--

ALTER TABLE ONLY uni.carriera_rimossa
    ADD CONSTRAINT carriera_rimossa_pkey PRIMARY KEY (studente, data, insegnamento, corso);


--
-- Name: corso_laurea corso_laurea_pkey; Type: CONSTRAINT; Schema: uni; Owner: bdlab
--

ALTER TABLE ONLY uni.corso_laurea
    ADD CONSTRAINT corso_laurea_pkey PRIMARY KEY (nome);


--
-- Name: docente docente_pkey; Type: CONSTRAINT; Schema: uni; Owner: bdlab
--

ALTER TABLE ONLY uni.docente
    ADD CONSTRAINT docente_pkey PRIMARY KEY (email);


--
-- Name: insegnamento insegnamento_pkey; Type: CONSTRAINT; Schema: uni; Owner: bdlab
--

ALTER TABLE ONLY uni.insegnamento
    ADD CONSTRAINT insegnamento_pkey PRIMARY KEY (codice, corso);


--
-- Name: propedeuticità propedeuticità_pkey; Type: CONSTRAINT; Schema: uni; Owner: bdlab
--

ALTER TABLE ONLY uni."propedeuticità"
    ADD CONSTRAINT "propedeuticità_pkey" PRIMARY KEY (codice_insegnamento, corso_insegnamento, codice_propedeutico, corso_propedeutico);


--
-- Name: segretario segretario_pkey; Type: CONSTRAINT; Schema: uni; Owner: bdlab
--

ALTER TABLE ONLY uni.segretario
    ADD CONSTRAINT segretario_pkey PRIMARY KEY (email);


--
-- Name: sostiene sostiene_pkey; Type: CONSTRAINT; Schema: uni; Owner: bdlab
--

ALTER TABLE ONLY uni.sostiene
    ADD CONSTRAINT sostiene_pkey PRIMARY KEY (studente, data, insegnamento, corso);


--
-- Name: studente studente_email_key; Type: CONSTRAINT; Schema: uni; Owner: bdlab
--

ALTER TABLE ONLY uni.studente
    ADD CONSTRAINT studente_email_key UNIQUE (email);


--
-- Name: studente studente_pkey; Type: CONSTRAINT; Schema: uni; Owner: bdlab
--

ALTER TABLE ONLY uni.studente
    ADD CONSTRAINT studente_pkey PRIMARY KEY (matricola);


--
-- Name: studente_rimosso studente_rimosso_email_key; Type: CONSTRAINT; Schema: uni; Owner: bdlab
--

ALTER TABLE ONLY uni.studente_rimosso
    ADD CONSTRAINT studente_rimosso_email_key UNIQUE (email);


--
-- Name: studente_rimosso studente_rimosso_pkey; Type: CONSTRAINT; Schema: uni; Owner: bdlab
--

ALTER TABLE ONLY uni.studente_rimosso
    ADD CONSTRAINT studente_rimosso_pkey PRIMARY KEY (matricola);


--
-- Name: insegnamento check_anno_insegnamento_i_u_t; Type: TRIGGER; Schema: uni; Owner: bdlab
--

CREATE TRIGGER check_anno_insegnamento_i_u_t BEFORE INSERT OR UPDATE ON uni.insegnamento FOR EACH ROW EXECUTE FUNCTION uni.check_anno_insegnamento_i_u_f();


--
-- Name: appello check_appelli_anno_i_u_t; Type: TRIGGER; Schema: uni; Owner: bdlab
--

CREATE TRIGGER check_appelli_anno_i_u_t BEFORE INSERT OR UPDATE ON uni.appello FOR EACH ROW EXECUTE FUNCTION uni.check_appelli_anno_i_u_f();


--
-- Name: propedeuticità check_corretta_propedeuticità_i_u_t; Type: TRIGGER; Schema: uni; Owner: bdlab
--

CREATE TRIGGER "check_corretta_propedeuticità_i_u_t" BEFORE INSERT OR UPDATE ON uni."propedeuticità" FOR EACH ROW EXECUTE FUNCTION uni."check_corretta_propedeuticità_i_u_f"();


--
-- Name: docente check_email_unique_i_u_t; Type: TRIGGER; Schema: uni; Owner: bdlab
--

CREATE TRIGGER check_email_unique_i_u_t BEFORE INSERT OR UPDATE ON uni.docente FOR EACH ROW EXECUTE FUNCTION uni.check_email_unique_i_u_f();


--
-- Name: segretario check_email_unique_i_u_t; Type: TRIGGER; Schema: uni; Owner: bdlab
--

CREATE TRIGGER check_email_unique_i_u_t BEFORE INSERT OR UPDATE ON uni.segretario FOR EACH ROW EXECUTE FUNCTION uni.check_email_unique_i_u_f();


--
-- Name: studente check_email_unique_i_u_t; Type: TRIGGER; Schema: uni; Owner: bdlab
--

CREATE TRIGGER check_email_unique_i_u_t BEFORE INSERT OR UPDATE ON uni.studente FOR EACH ROW EXECUTE FUNCTION uni.check_email_unique_i_u_f();


--
-- Name: insegnamento check_insegnamenti_docente_i_u_t; Type: TRIGGER; Schema: uni; Owner: bdlab
--

CREATE TRIGGER check_insegnamenti_docente_i_u_t BEFORE INSERT OR UPDATE ON uni.insegnamento FOR EACH ROW EXECUTE FUNCTION uni.check_insegnamenti_docente_i_u_f();


--
-- Name: sostiene check_sostiene_propedeuticità_i_u_t; Type: TRIGGER; Schema: uni; Owner: bdlab
--

CREATE TRIGGER "check_sostiene_propedeuticità_i_u_t" BEFORE INSERT OR UPDATE ON uni.sostiene FOR EACH ROW EXECUTE FUNCTION uni."check_sostiene_propedeuticità_i_u_f"();


--
-- Name: sostiene check_studente_corso_appello_i_u_t; Type: TRIGGER; Schema: uni; Owner: bdlab
--

CREATE TRIGGER check_studente_corso_appello_i_u_t BEFORE INSERT OR UPDATE ON uni.sostiene FOR EACH ROW EXECUTE FUNCTION uni.check_studente_corso_appello_i_u_f();


--
-- Name: studente remove_studente_d_t; Type: TRIGGER; Schema: uni; Owner: bdlab
--

CREATE TRIGGER remove_studente_d_t BEFORE DELETE ON uni.studente FOR EACH ROW EXECUTE FUNCTION uni.remove_studente_d_f();


--
-- Name: studente studente_corso_laurea_i_u_t; Type: TRIGGER; Schema: uni; Owner: bdlab
--

CREATE TRIGGER studente_corso_laurea_i_u_t AFTER INSERT OR UPDATE ON uni.studente FOR EACH ROW EXECUTE FUNCTION uni.studente_corso_laurea_i_u_f();


--
-- Name: appello appello_insegnamento_corso_fkey; Type: FK CONSTRAINT; Schema: uni; Owner: bdlab
--

ALTER TABLE ONLY uni.appello
    ADD CONSTRAINT appello_insegnamento_corso_fkey FOREIGN KEY (insegnamento, corso) REFERENCES uni.insegnamento(codice, corso) ON UPDATE CASCADE;


--
-- Name: carriera_rimossa carriera_rimossa_studente_fkey; Type: FK CONSTRAINT; Schema: uni; Owner: bdlab
--

ALTER TABLE ONLY uni.carriera_rimossa
    ADD CONSTRAINT carriera_rimossa_studente_fkey FOREIGN KEY (studente) REFERENCES uni.studente_rimosso(matricola) ON UPDATE CASCADE;


--
-- Name: insegnamento insegnamento_corso_fkey; Type: FK CONSTRAINT; Schema: uni; Owner: bdlab
--

ALTER TABLE ONLY uni.insegnamento
    ADD CONSTRAINT insegnamento_corso_fkey FOREIGN KEY (corso) REFERENCES uni.corso_laurea(nome) ON UPDATE CASCADE;


--
-- Name: insegnamento insegnamento_responsabile_fkey; Type: FK CONSTRAINT; Schema: uni; Owner: bdlab
--

ALTER TABLE ONLY uni.insegnamento
    ADD CONSTRAINT insegnamento_responsabile_fkey FOREIGN KEY (responsabile) REFERENCES uni.docente(email) ON UPDATE CASCADE;


--
-- Name: propedeuticità propedeuticità_codice_insegnamento_corso_insegnamento_fkey; Type: FK CONSTRAINT; Schema: uni; Owner: bdlab
--

ALTER TABLE ONLY uni."propedeuticità"
    ADD CONSTRAINT "propedeuticità_codice_insegnamento_corso_insegnamento_fkey" FOREIGN KEY (codice_insegnamento, corso_insegnamento) REFERENCES uni.insegnamento(codice, corso) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: propedeuticità propedeuticità_codice_propedeutico_corso_propedeutico_fkey; Type: FK CONSTRAINT; Schema: uni; Owner: bdlab
--

ALTER TABLE ONLY uni."propedeuticità"
    ADD CONSTRAINT "propedeuticità_codice_propedeutico_corso_propedeutico_fkey" FOREIGN KEY (codice_propedeutico, corso_propedeutico) REFERENCES uni.insegnamento(codice, corso) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: sostiene sostiene_data_insegnamento_corso_fkey; Type: FK CONSTRAINT; Schema: uni; Owner: bdlab
--

ALTER TABLE ONLY uni.sostiene
    ADD CONSTRAINT sostiene_data_insegnamento_corso_fkey FOREIGN KEY (data, insegnamento, corso) REFERENCES uni.appello(data, insegnamento, corso) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: sostiene sostiene_studente_fkey; Type: FK CONSTRAINT; Schema: uni; Owner: bdlab
--

ALTER TABLE ONLY uni.sostiene
    ADD CONSTRAINT sostiene_studente_fkey FOREIGN KEY (studente) REFERENCES uni.studente(matricola) ON UPDATE CASCADE;


--
-- Name: studente studente_corso_fkey; Type: FK CONSTRAINT; Schema: uni; Owner: bdlab
--

ALTER TABLE ONLY uni.studente
    ADD CONSTRAINT studente_corso_fkey FOREIGN KEY (corso) REFERENCES uni.corso_laurea(nome) ON UPDATE CASCADE;


--
-- PostgreSQL database dump complete
--

