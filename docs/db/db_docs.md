# Database Documentation
 + Segretario
 + Docente
 + Studente

Questi devono avere le mail univoche tra loro: create un trigger su
inserimento che faccia prima un controllo su tutti gli altri (magari
vedere implementazione con vista materializzata per semplificare)

```postgresql
create or replace function check_email_i_f()
    returns trigger
language plpgsql as $$
    declare
        
    begin
        
    end;
$$
```
