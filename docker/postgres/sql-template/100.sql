do $$ begin
create role "${APP_DB_ROLE_WRITER}";
exception
    when duplicate_object
      then raise notice 'not creating role "${APP_DB_ROLE_WRITER}"';
end $$;

do $$ begin
  create user "${POSTGRES_USER}" with createrole '${APP_DB_ROLE_READER}' '${PG_PASSWORD}';
exception
    when duplicate_object
      then raise notice 'not creating role "${POSTGRES_USER}"';
end $$;

revoke create on schema public from public;
revoke all on database "${POSTGRES_DB}" from public;

alter database "${POSTGRES_DB}" owner to "${POSTGRES_USER}";
alter schema public owner to "${POSTGRES_USER}";

grant connect,temporary,create on database "${POSTGRES_DB}" to "${POSTGRES_USER}";
grant connect,temporary on database "${POSTGRES_DB}" to "${APP_DB_ROLE_WRITER}";
grant connect,temporary on database "${POSTGRES_DB}" to "${APP_DB_ROLE_READER}";

do $$
begin
create schema "${SCHEMA}";
exception
    when duplicate_schema
      then raise notice 'schema "${SCHEMA}" already exists';
end $$;

ALTER ROLE "${POSTGRES_USER}" SET search_path TO "${SCHEMA}", public;
ALTER ROLE "${APP_DB_ROLE_WRITER}" SET search_path TO "${SCHEMA}", public;
ALTER ROLE "${APP_DB_ROLE_READER}" SET search_path TO "${SCHEMA}", public;

grant usage on schema "${SCHEMA}" to "${APP_DB_ROLE_WRITER}", "${APP_DB_ROLE_READER}";
grant create on schema "${SCHEMA}" to "${POSTGRES_USER}";
alter schema "${SCHEMA}" owner to "${POSTGRES_USER}";