create table public.kvks
(
    id          uuid         not null
        primary key,
    kvk         varchar(255) not null,
    created_at  timestamp(0),
    updated_at  timestamp(0)
);

alter table public.kvks
    owner to gfmodules_portal_register_private;
