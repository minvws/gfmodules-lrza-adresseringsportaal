ALTER TABLE public.suppliers
ADD COLUMN version INTEGER CHECK (version > 0) NOT NULL DEFAULT 1,
ADD COLUMN deleted_at TIMESTAMP(0) DEFAULT NULL;

ALTER TABLE public.suppliers
DROP CONSTRAINT suppliers_pkey,
ADD CONSTRAINT suppliers_pkey PRIMARY KEY (id, version);