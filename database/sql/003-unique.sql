alter table public.kvks
    add constraint kvks_pk
        unique (kvk);


alter table public.uras
    add constraint uras_pk
        unique (ura);
