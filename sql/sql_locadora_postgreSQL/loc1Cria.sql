

/* Criando domínios */
CREATE DOMAIN d_boolean    SMALLINT CHECK( VALUE IN (0, 1) );
CREATE DOMAIN d_estconserv CHAR(3)  CHECK( UPPER(VALUE) IN ('OTI', 'BOM', 'REG', 'RUI') ) ;
CREATE DOMAIN d_formapagto SMALLINT CHECK( VALUE between 0 and 32 ) ;
CREATE DOMAIN d_dub_leg    CHAR(1)  CHECK( VALUE IN ('D', 'L', 'd', 'l') ) ;
CREATE DOMAIN d_sexo       CHAR(1)  CHECK( VALUE IN ('M', 'F', 'm', 'f') ) ;


CREATE DOMAIN d_idade_minima_cliente DATE CHECK( (  extract(year FROM age(current_date, VALUE)))  > 5 ) ;
CREATE DOMAIN d_idade_minima_resp DATE CHECK( (  extract(year FROM age(current_date, VALUE)))  > 18 ) ;

/* TipoConta */
CREATE TABLE TipoConta( 
       idtpct       SERIAL      NOT NULL PRIMARY KEY, 
       nometpct     VARCHAR(15) NOT NULL UNIQUE
);

/* Genero */
CREATE TABLE Genero(
       idgen     SERIAL       NOT NULL PRIMARY KEY, 
       nomegen   VARCHAR(15)  NOT NULL UNIQUE
);

/* Produtora */
CREATE TABLE Produtora(
       idprod    SERIAL       NOT NULL PRIMARY KEY, 
       nomeprod  VARCHAR(20)  not null UNIQUE
);

/* Cliente */
CREATE TABLE Cliente(
       idcli      SERIAL       NOT NULL PRIMARY KEY,
       nomecli    VARCHAR(30)  NOT NULL,
       RG         VARCHAR(15),
       CPF        CHAR(11),
       dtnasc     DATE, 
       dtadm      DATE, 
       formapagto d_formapagto,
       idcliresp  INT          NOT NULL 
);

/* Filme */
CREATE TABLE Filme(
       idfil    SERIAL      NOT NULL PRIMARY KEY, 
       nomefil  VARCHAR(30) NOT NULL,
       ano      INT         NOT NULL, 
       idgen    INT, 
       censura  INT, 
       idprod   INT,
       pais     VARCHAR(3), 
       durminut INT, 
       precoloc NUMERIC(5, 2),
UNIQUE (nomefil, ano)
);

/* Fita */
CREATE TABLE Fita(
       idfil          INT      NOT NULL, 
       ordfita        SMALLINT NOT NULL, 
       precocusto     INT, 
       dtaquis        DATE, 
       estadoconserv  VARCHAR(3),
       dub_leg        d_dub_leg,
PRIMARY KEY(idfil, ordfita)
);

/* Conta */
CREATE TABLE Conta(
       idct       SERIAL      NOT NULL PRIMARY KEY, 
       dtlanc     DATE, 
       historico  VARCHAR(20), 
       valor      NUMERIC(5, 2), 
       dtvenc     DATE, 
       idtpct     SMALLINT
);

/* ContaLiq */
CREATE TABLE ContaLiq(
       idct     INT       NOT NULL PRIMARY KEY, 
       dtliq    DATE, 
       valorliq NUMERIC(5, 2)
);

/* Locacao */
CREATE TABLE Locacao(
       idloc       SERIAL NOT NULL PRIMARY KEY, 
       idcli       INT    NOT NULL, 
       dtretirada  DATE   NOT NULL, 
       dtdevolprev DATE   NOT NULL, 
       dtdevolreal DATE, 
       idct        INT    NOT NULL
);

/* ItemLoc */
CREATE TABLE ItemLoc( 
       iditloc      SERIAL   NOT NULL PRIMARY KEY,
       idloc        INT      NOT NULL, 
       idfil        INT      NOT NULL, 
       ordfita      SMALLINT NOT NULL, 
       valorlocfita NUMERIC(5,2),
UNIQUE(idloc, idfil, ordfita)
);

/* FilBaixa */
CREATE TABLE FilBaixa(
       idfil          INT      NOT NULL, 
       ordfita        SMALLINT NOT NULL, 
       dtbaixa        DATE, 
       idct           INT,
PRIMARY KEY(idfil, ordfita)
);

/* Particip */
CREATE TABLE Particip( 
       idpart       SERIAL      NOT NULL PRIMARY KEY, 
       nomepart     VARCHAR(20) NOT NULL UNIQUE
);


/* Artista */
CREATE TABLE Artista( 
       idart       SERIAL      NOT NULL PRIMARY KEY, 
       nomeart     VARCHAR(30) NOT NULL UNIQUE,
       sexo        D_sexo
);

/* FilPArt */
CREATE TABLE FilPArt( 
       idfil       INT         NOT NULL,
       idart       INT         NOT NULL, 
       idpart      SMALLINT    NOT NULL, 
PRIMARY KEY(idfil, idart, idpart)
);

/* fim */

