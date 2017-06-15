# Projeto ExpertsAR

Neste repositório estão todos os códigos do ExpertsAR, um objeto de aprendizagem para Álgebra Relacional.
**Nota:** algumas pastas podem estar diferentes como estão listadas no site, por questões de segurança

## Por que você decidiu colocar este repositório como público?

Para dar mais visibilidade ao projeto, e propiciar contribuições de outros usuários.

## Objetivo

Criar um Objeto de Aprendizagem para Álgebra Relacional. 

## Como fazer o deploy?

Você precisará do PostgreSQL e do MySQL como bancos de dados. **Por que isso?** Pois a nossas hospedagens não suportam bancos PostgreSQL, e o MySQL não suporta EXCEPT e INTERSECT (previsto na versão 10.3 do MariaDB).

Na pasta sql, você encontra os scripts para geração dos bancos de dados. O arquivo _algebraMySQL.sql_ gera o banco de dados de manutenção do site. Enquanto que os scripts da pasta _sqlLocadoraScripts_ geram o banco de dados de teste (onde as consultas em Álgebra Relacional são rodadas), no PostgreSQL.

Use o arquivo ``compile.sh`` para gerar o css dos arquivos SASS.

Nos arquivos do parser (parserv\*.pegjs), você precisará do [peg.js](https://pegjs.org/) para compilar e gerar o novo parser. Rode o seguinte comando para obtê-lo:
`` pegjs --format globals  --export-var parser parser.pegjs``

Instale o [Composer](https://getcomposer.org/) e rode o comando ``composer install --dev``.

Na pasta adminroot você encontra o arquivo _generateFirstManager.php_. Rode esse script no navegador, para gerar o primeiro mantenedor. P.S.: Esse script só é executado quando não tem nenhum mantenedor cadastrado no sistema.

## Criadores

Luís Aurélio Casoni e Ademir Martinez Sanches

### Dependências de Terceiros

Agradecemos aos desenvolvedores das seguintes dependências

* [peg.js](https://pegjs.org/);
* [Materialize CSS](http://materializecss.com/);
* [Slug.php de Kevin Le Bruin](https://github.com/kevinlebrun/slug.php);
* [PHPMailer](https://github.com/PHPMailer/PHPMailer);
* [Twig](https://twig.sensiolabs.org/);
* [Equation Operating System, de Jon Lawrence](https://github.com/jlawrence11/eos);
* [Mockery](http://docs.mockery.io/en/latest/) - Para testes;
* [PHPUnit](https://phpunit.de/) - Para testes.

Feito para a disciplina de Tópicos em Computação, do Curso de Ciência da Computação da UNIGRAN.

(C) 2017.
