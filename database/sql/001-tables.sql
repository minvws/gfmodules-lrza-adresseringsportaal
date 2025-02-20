create table public.migrations
(
    id        serial
        primary key,
    migration varchar(255) not null,
    batch     integer      not null
);


create table public.uras
(
    id          uuid         not null
        primary key,
    ura         varchar(255) not null,
    description varchar(255),
    created_at  timestamp(0),
    updated_at  timestamp(0)
);

create table public.suppliers
(
    id         uuid          not null
        primary key,
    endpoint   varchar(1024) not null,
    created_at timestamp(0),
    updated_at timestamp(0),
    ura_id     uuid          not null
    kvk_id     uuid          not null
        constraint suppliers_ura_id_foreign
            references public.uras
            on delete cascade
        constraint suppliers_kvk_id_foreign
            references public.kvks
            on delete cascade
);

alter table public.migrations
    owner to gfmodules_portal_register_private;

alter table public.uras
    owner to gfmodules_portal_register_private;

alter table public.suppliers
    owner to gfmodules_portal_register_private;
