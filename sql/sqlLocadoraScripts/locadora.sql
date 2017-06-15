-- DROP DATABASE locadora;

CREATE DATABASE locadora
  WITH OWNER = postgres
       ENCODING = 'LATIN1'
	   TEMPLATE = template0
       TABLESPACE = pg_default
       LC_COLLATE = 'C'
       LC_CTYPE = 'C'
       CONNECTION LIMIT = -1;

COMMENT ON DATABASE locadora IS 'Banco de dados locadora';
