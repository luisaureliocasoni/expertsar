/* Conecta o banco de dados Locadora da Lista de Exercícios */
/* CONNECT "C:\ib\loc\gdb\loc.gdb" USER 'ADMEMP' PASSWORD 'adm01';*/

SELECT setval('public.tipoconta_idtpct_seq', 10, true);
SELECT setval('public.artista_idart_seq', 68, true);
SELECT setval('public.cliente_idcli_seq', 21, true);
SELECT setval('public.conta_idct_seq', 181, true);
SELECT setval('public.filme_idfil_seq', 163, true);
SELECT setval('public.genero_idgen_seq', 18, true);
SELECT setval('public.itemloc_iditloc_seq', 233, true);
SELECT setval('public.locacao_idloc_seq', 131, true);
SELECT setval('public.particip_idpart_seq', 9, true);
SELECT setval('public.produtora_idprod_seq', 33, true);




ALTER TABLE Cliente
      ADD FOREIGN KEY (idcliresp) REFERENCES Cliente(idcli);
ALTER TABLE Filme
      ADD FOREIGN KEY(idgen) REFERENCES Genero(idgen);
ALTER TABLE Filme
      ADD FOREIGN KEY(idprod) REFERENCES Produtora(idprod);
ALTER TABLE Fita
      ADD FOREIGN KEY(idfil) REFERENCES Filme(idfil);
ALTER TABLE Conta
      ADD FOREIGN KEY(idtpct) REFERENCES TipoConta(idtpct);
ALTER TABLE ContaLiq
      ADD FOREIGN KEY(idct) REFERENCES Conta(idct);
ALTER TABLE Locacao
      ADD FOREIGN KEY(idcli) REFERENCES Cliente(idcli);
ALTER TABLE Locacao
      ADD FOREIGN KEY(idct) REFERENCES Conta(idct);
ALTER TABLE ItemLoc
      ADD FOREIGN KEY(idloc) REFERENCES Locacao(idloc);
ALTER TABLE ItemLoc
      ADD FOREIGN KEY(idfil) REFERENCES Filme(idfil);
ALTER TABLE ItemLoc
      ADD FOREIGN KEY(idfil,ordfita) REFERENCES Fita(idfil,ordfita);
ALTER TABLE FilBaixa
      ADD FOREIGN KEY(idfil,ordfita) REFERENCES Fita(idfil,ordfita);
ALTER TABLE FilBaixa
      ADD FOREIGN KEY(idct) REFERENCES Conta(idct);
ALTER TABLE FilPart
      ADD FOREIGN KEY(idfil) REFERENCES Filme(idfil);
ALTER TABLE FilPart
      ADD FOREIGN KEY(idart) REFERENCES Artista(idart);
ALTER TABLE FilPart
      ADD FOREIGN KEY(idpart) REFERENCES Particip(idpart);

/* fim */
commit;
