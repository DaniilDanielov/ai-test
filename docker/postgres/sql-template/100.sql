do $$ begin
  create role "${APP_DB_ROLE_WRITER}";
  exception
    when duplicate_object
      then raise notice 'not creating role "${APP_DB_ROLE_WRITER}"';
end $$;


do $$ begin
  create user "${POSTGRES_USER}" with createrole password '${PG_PASSWORD}';
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

