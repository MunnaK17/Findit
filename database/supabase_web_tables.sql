-- Tabel tambahan untuk fitur web (testimoni & notifikasi). Idempotent.
create table if not exists public.testimonials (
    id bigint generated always as identity primary key,
    id_user uuid not null references public.profiles(id) on delete cascade,
    id_claim bigint references public.claims(id) on delete set null,
    id_report bigint references public.reports(id) on delete set null,
    isi_testimoni text not null,
    rating integer not null default 5,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now()
);

create table if not exists public.notifications (
    id uuid primary key,
    type text not null,
    notifiable_type text not null,
    notifiable_id text not null,
    data text not null,
    read_at timestamptz,
    created_at timestamptz,
    updated_at timestamptz
);
create index if not exists notifications_notifiable_idx on public.notifications (notifiable_type, notifiable_id);

alter table public.testimonials enable row level security;
alter table public.notifications enable row level security;

do $$
begin
    if not exists (select 1 from pg_policies where tablename='testimonials' and policyname='testimonials_public_read') then
        create policy testimonials_public_read on public.testimonials for select using (true);
    end if;
end $$;
