<?php

/**
 * ExpertsAR - Um Objeto de Aprendizagem para Álgebra Relacional
 * (C) 2017 - Luís Aurélio Casoni e Ademir Martinez Sanches
 * Este código-fonte está licenciado sob a licença MIT.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * */

namespace Lib;

require_once './vendor/autoload.php';

use PHPUnit\Framework\TestCase as PHPUnit;

/**
 * Description of DAOTest
 *
 * @author root
 */ 
class DAOTest extends PHPUnit{
    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        DAO::setFilePathConfig("assets/conexaoTest.ini");
        self::truncateTable();
    }
    
    protected function setUp() {
        parent::setUp();
        self::truncateTable();
    }
    
    protected function tearDown() {
        parent::tearDown();
    }
    
    public static function truncateTable(){
        DAO::execute("TRUNCATE TABLE \"Mantenedores\" RESTART IDENTITY CASCADE;");
        DAO::execute("TRUNCATE TABLE \"Licoes\" RESTART IDENTITY CASCADE;");
        DAO::execute("TRUNCATE TABLE \"Perguntas\" RESTART IDENTITY CASCADE;");
    }
    
    public static function insert() {
        $teste = new \ExpertsAR\Mantenedor();
        $teste->setEmail("abc@abc.com")->setNome("Fulano");
        $teste->setSenha("senha")->setUsuario("fulano95ABC");
        
        $teste2 = new \ExpertsAR\Mantenedor();
        $teste2->setEmail("abc@abc.com")->setNome("Xico Sa");
        $teste2->setSenha("senha")->setUsuario("fulano95ABC");
        
        $licao = new \ExpertsAR\Licao();
        $licao->setIdMantenedorAlterou(1)->setIdMantenedorCriou(1);
        $licao->setNome("TESTE")->setSlug("teste")->setTextoLicao("Ned");
        
        $licao2 = new \ExpertsAR\Licao();
        $licao2->setIdMantenedorAlterou(2)->setIdMantenedorCriou(1);
        $licao2->setNome("TESTE2")->setSlug("teste")->setTextoLicao("Ned");
        
        
        $ask1 = new \ExpertsAR\Pergunta();
        $ask1->setIdLicao(1)->setEnunciado("Quantas pessoas tem na canoa?")->setResposta("Resposta");
        
        $ask2 = new \ExpertsAR\Pergunta();
        $ask2->setIdLicao(1)->setEnunciado("Teste2")->setResposta("Resposta");
       
        DAO::insert($teste);
        DAO::insert($teste2);
        DAO::insert($licao);
        DAO::insert($licao2);
        DAO::insert($ask1);
        DAO::insert($ask2);
    }
    
    public static function tearDownAfterClass() {
        parent::tearDownAfterClass();
    }
    
    public function testInsertOne(){
        $teste = new \ExpertsAR\Mantenedor();
        $teste->setEmail("luis@luis.com")->setNome("Fulano");
        $teste->setSenha("senha")->setUsuario("fulano95");
        
        DAO::insert($teste);
        
        $result = DAO::execute("SELECT * FROM \"Mantenedores\";");
        
        $this->assertEquals(1, pg_affected_rows($result));
    }
    
    public function testInsertionMore(){
        self::insert();
        
        $result = DAO::execute("SELECT * FROM \"Mantenedores\";");
        $this->assertEquals(2, pg_affected_rows($result));
        
        $result = DAO::execute("SELECT * FROM \"Licoes\";");
        $this->assertEquals(2, pg_affected_rows($result));
        
        $result = DAO::execute("SELECT * FROM \"Perguntas\";");
        $this->assertEquals(2, pg_affected_rows($result));
    }
    
    public function testSelectUmObjeto() {
        self::insert();
        
        
        $result = DAO::selectById("ExpertsAR\Mantenedor", "Mantenedores", 1);
        $this->assertInstanceOf("ExpertsAR\Mantenedor", $result);
        $this->assertEquals(1, $result->getId());
        
        $result = DAO::selectById("ExpertsAR\Licao", "Licoes", 2);
        $this->assertInstanceOf("ExpertsAR\Licao", $result);
        $this->assertEquals(2, $result->getId());
        
        $result = DAO::selectById("ExpertsAR\Pergunta", "Perguntas", 1);
        $this->assertInstanceOf("ExpertsAR\Pergunta", $result);
        $this->assertEquals("Quantas pessoas tem na canoa?", $result->getEnunciado());
    }
    
    public function testSelectVariosObjetos() {
        self::insert();
        $teste = new \ExpertsAR\Mantenedor();
        $teste->setEmail("luis@luis.com")->setNome("luis");
        $teste->setSenha("senha")->setUsuario("luisaureliocasoni");
        DAO::insert($teste);
        
        $result = DAO::selectAll("ExpertsAR\Mantenedor", "Mantenedores");
        
        $this->assertInstanceOf("ArrayObject", $result);
        //são três que estão na tabela, então deve reportar tres
        $this->assertEquals(3, $result->count());
        
        $this->assertInstanceOf("ExpertsAR\Mantenedor", $result[0]);
        $this->assertInstanceOf("ExpertsAR\Mantenedor", $result[1]);
        $this->assertInstanceOf("ExpertsAR\Mantenedor", $result[2]);
        
        $this->assertEquals(1, $result[0]->getId());
        $this->assertEquals("luis@luis.com", $result[2]->getEmail());
        $this->assertEquals("luisaureliocasoni", $result[2]->getUsuario());
        $this->assertEquals("senha", $result[2]->getSenha());
    }
    
    public function testUpdate() {
        self::insert();
        $cond = new Condicao(new Identificador("id"), "=", 2);
        
        $mantenedor = new \ExpertsAR\Mantenedor();
        $mantenedor->setUsuario("xicosa");
        $mantenedor->setEmail("xicosa@chico.com");
        $mantenedor->setSenha("Senha que todo mundo sabe");
        
        $result = DAO::update($mantenedor, $cond);
        
        
        $salvo = DAO::selectById("ExpertsAR\Mantenedor", "Mantenedores", 2);
        $this->assertEquals("xicosa", $salvo->getUsuario());
        $this->assertEquals("xicosa@chico.com", $salvo->getEmail());
        $this->assertEquals(2, $salvo->getId());
        $this->assertEquals("Senha que todo mundo sabe", $salvo->getSenha());
        $this->assertEquals("Xico Sa", $salvo->getNome());
    }
    
    public function testUpdateById() {
        self::insert();
        
        $mantenedor = new \ExpertsAR\Mantenedor();
        $mantenedor->setUsuario("xicosa");
        $mantenedor->setEmail("xicosa@chico.com");
        $mantenedor->setSenha("Senha que todo mundo sabe");
        
        $result = DAO::updateById($mantenedor, 2);
        
        
        $salvo = DAO::selectById("ExpertsAR\Mantenedor", "Mantenedores", 2);
        $this->assertEquals("xicosa", $salvo->getUsuario());
        $this->assertEquals("xicosa@chico.com", $salvo->getEmail());
        $this->assertEquals(2, $salvo->getId());
        $this->assertEquals("Senha que todo mundo sabe", $salvo->getSenha());
        $this->assertEquals("Xico Sa", $salvo->getNome());
    }
    
    public function testRemoveById() {
        self::insert();
        
        DAO::removeById(1, "Mantenedores");
        
        $result = DAO::selectById("ExpertsAR\Mantenedor", "Mantenedores", 1);
        
        $this->assertEquals(NULL, $result);
        
    }
    
    public function testRemoveByCondition(){
        $mantenedor = new \ExpertsAR\Mantenedor();
        $mantenedor->setNome("Luis")->setSenha("senha")->setUsuario("luisac");
        $mantenedor->setEmail("root@luisaurelio");
        DAO::insert($mantenedor);
        
        $mantenedor = new \ExpertsAR\Mantenedor();
        $mantenedor->setNome("Rodrigo")->setSenha("senha")->setUsuario("rodrigo");
        $mantenedor->setEmail("root@luisaurelio");
        DAO::insert($mantenedor);
        
        $licao1 = new \ExpertsAR\Licao();
        $licao1->setNome("Projecao")->setSlug("projecao")->setTextoLicao("umtexto")
                ->setIdMantenedorCriou(1);
        $licao2 = new \ExpertsAR\Licao();
        $licao2->setNome("Selecao")->setSlug("selecao")->setTextoLicao("umtexto")
                ->setIdMantenedorCriou(2);
        $licao3 = new \ExpertsAR\Licao();
        $licao3->setNome("Funcoes")->setSlug("funcoes")->setTextoLicao("umtexto")
                ->setIdMantenedorCriou(1);
        $licao4 = new \ExpertsAR\Licao();
        $licao4->setNome("Mais Funcoes")->setSlug("mais-funcoes")->setTextoLicao("umtexto")
                ->setIdMantenedorCriou(1);
        $licao5 = new \ExpertsAR\Licao();
        $licao5->setNome("Alberto")->setSlug("alberto")->setTextoLicao("umtexto")
                ->setIdMantenedorCriou(2);
        DAO::insert($licao1);
        DAO::insert($licao2);
        DAO::insert($licao3);
        DAO::insert($licao4);
        DAO::insert($licao5);
        
        $cond = new Condicao(new Identificador("idMantenedorCriou"), "=", 1);
        DAO::remove($cond, "Licoes");
        
        $this->assertEquals(NULL, DAO::select("\ExpertsAR\Licao", "Licoes", $cond));
        
        $result = DAO::selectAll("\ExpertsAR\Licao", "Licoes");
        $this->assertEquals(2, $result->count());
        
        
    }
    
    public function testTransformResourceInArray(){
        self::insert();
        $resource = DAO::execute("SELECT * FROM \"Mantenedores\";");
        $array = DAO::transformResourceInArray($resource);
        
        $this->assertInstanceOf("\ArrayObject", $array);
        $this->assertEquals("Fulano", $array[0]["nome"]);
        $this->assertEquals("Xico Sa", $array[1]["nome"]);
    }
    
    public function testTransformResourceInArrayNull(){
        self::insert();
        $resource = DAO::execute("SELECT * FROM \"Mantenedores\" WHERE \"id\" = 50;");
        $array = DAO::transformResourceInArray($resource);
        
        $this->assertEquals(NULL, $array);
    }
    
    /**A seguir, testes que não testam um comportamento do código, e sim do banco
     * Espera que fica NULO**/
    public function testRemoveMantenedorCascade(){
        $mantenedor = new \ExpertsAR\Mantenedor();
        $mantenedor->setNome("Luis")->setSenha("senha")->setUsuario("luisac");
        $mantenedor->setEmail("root@luisaurelio");
        DAO::insert($mantenedor);
        
        $mantenedor = new \ExpertsAR\Mantenedor();
        $mantenedor->setNome("Rodrigo")->setSenha("senha")->setUsuario("rodrigo");
        $mantenedor->setEmail("root@luisaurelio");
        DAO::insert($mantenedor);
        
        $licao1 = new \ExpertsAR\Licao();
        $licao1->setNome("Projecao")->setSlug("projecao")->setTextoLicao("umtexto")
                ->setIdMantenedorCriou(1);
        $licao2 = new \ExpertsAR\Licao();
        $licao2->setNome("Selecao")->setSlug("selecao")->setTextoLicao("umtexto")
                ->setIdMantenedorCriou(2);
        $licao3 = new \ExpertsAR\Licao();
        $licao3->setNome("Funcoes")->setSlug("funcoes")->setTextoLicao("umtexto")
                ->setIdMantenedorCriou(1);
        $licao4 = new \ExpertsAR\Licao();
        $licao4->setNome("Mais Funcoes")->setSlug("mais-funcoes")->setTextoLicao("umtexto")
                ->setIdMantenedorCriou(1);
        $licao5 = new \ExpertsAR\Licao();
        $licao5->setNome("Alberto")->setSlug("alberto")->setTextoLicao("umtexto")
                ->setIdMantenedorCriou(2);
        DAO::insert($licao1);
        DAO::insert($licao2);
        DAO::insert($licao3);
        DAO::insert($licao4);
        DAO::insert($licao5);
        
        DAO::removeById(1, "Mantenedores");
        
        $this->assertEquals(NULL, DAO::selectById("\ExpertsAR\Mantenedor", "Mantenedores", 1));
        
        $cond = new Condicao(new Identificador("idMantenedorCriou"), "=", 1);       
        $this->assertEquals(NULL, DAO::select("\ExpertsAR\Licao", "Licoes", $cond));
        
        $cond = new Condicao(new Identificador("idMantenedorCriou"), "=", NULL);
        $result = DAO::select("\ExpertsAR\Licao", "Licoes", $cond);
        $this->assertEquals(3, $result->count());
        
        $result = DAO::selectAll("\ExpertsAR\Licao", "Licoes");
        $this->assertEquals(5, $result->count());  
    }
    
    /** Espera alterar nas perguntas**/
    public function testUpdateIdMantenedorCascade(){
        $mantenedor = new \ExpertsAR\Mantenedor();
        $mantenedor->setNome("Luis")->setSenha("senha")->setUsuario("luisac");
        $mantenedor->setEmail("root@luisaurelio");
        DAO::insert($mantenedor);
        
        $mantenedor = new \ExpertsAR\Mantenedor();
        $mantenedor->setNome("Rodrigo")->setSenha("senha")->setUsuario("rodrigo");
        $mantenedor->setEmail("root@luisaurelio");
        DAO::insert($mantenedor);
        
        $licao1 = new \ExpertsAR\Licao();
        $licao1->setNome("Projecao")->setSlug("projecao")->setTextoLicao("umtexto")
                ->setIdMantenedorCriou(1);
        $licao2 = new \ExpertsAR\Licao();
        $licao2->setNome("Selecao")->setSlug("selecao")->setTextoLicao("umtexto")
                ->setIdMantenedorCriou(2);
        $licao3 = new \ExpertsAR\Licao();
        $licao3->setNome("Funcoes")->setSlug("funcoes")->setTextoLicao("umtexto")
                ->setIdMantenedorCriou(1);
        $licao4 = new \ExpertsAR\Licao();
        $licao4->setNome("Mais Funcoes")->setSlug("mais-funcoes")->setTextoLicao("umtexto")
                ->setIdMantenedorCriou(1);
        $licao5 = new \ExpertsAR\Licao();
        $licao5->setNome("Alberto")->setSlug("alberto")->setTextoLicao("umtexto")
                ->setIdMantenedorCriou(2);
        
        DAO::insert($licao1);
        DAO::insert($licao2);
        DAO::insert($licao3);
        DAO::insert($licao4);
        DAO::insert($licao5);
        
        $usuario = new \ExpertsAR\Mantenedor(50);
        $usuario->setNome("Ezequiel");
        DAO::updateById($usuario, 1);
        
        $this->assertEquals(NULL, DAO::selectById("\ExpertsAR\Mantenedor", "Mantenedores", 1));
        
        $cond = new Condicao(new Identificador("idMantenedorCriou"), "=", 1);       
        $this->assertEquals(NULL, DAO::select("\ExpertsAR\Licao", "Licoes", $cond));
        
        $cond = new Condicao(new Identificador("idMantenedorCriou"), "=", 1);
        
        $cond = new Condicao(new Identificador("idMantenedorCriou"), "=", 50);
        $result = DAO::select("\ExpertsAR\Licao", "Licoes", $cond);
        $this->assertEquals(3, $result->count());
        
        $result = DAO::selectAll("\ExpertsAR\Licao", "Licoes");
        $this->assertEquals(5, $result->count());  
    }
    
    /**A seguir, testes que não testam um comportamento do código, e sim do banco
     * Espera que apaga tudo**/
    public function testRemovePerguntaCascade(){
        $mantenedor = new \ExpertsAR\Mantenedor();
        $mantenedor->setNome("Luis")->setSenha("senha")->setUsuario("luisac");
        $mantenedor->setEmail("root@luisaurelio");
        DAO::insert($mantenedor);
        
        $mantenedor = new \ExpertsAR\Mantenedor();
        $mantenedor->setNome("Rodrigo")->setSenha("senha")->setUsuario("rodrigo");
        $mantenedor->setEmail("root@luisaurelio");
        DAO::insert($mantenedor);
        
        $licao1 = new \ExpertsAR\Licao();
        $licao1->setNome("Projecao")->setSlug("projecao")->setTextoLicao("umtexto")
                ->setIdMantenedorCriou(1);
        $licao2 = new \ExpertsAR\Licao();
        $licao2->setNome("Selecao")->setSlug("selecao")->setTextoLicao("umtexto")
                ->setIdMantenedorCriou(2);
        $licao3 = new \ExpertsAR\Licao();
        $licao3->setNome("Funcoes")->setSlug("funcoes")->setTextoLicao("umtexto")
                ->setIdMantenedorCriou(1);
        $licao4 = new \ExpertsAR\Licao();
        $licao4->setNome("Mais Funcoes")->setSlug("mais-funcoes")->setTextoLicao("umtexto")
                ->setIdMantenedorCriou(1);
        $licao5 = new \ExpertsAR\Licao();
        $licao5->setNome("Alberto")->setSlug("alberto")->setTextoLicao("umtexto")
                ->setIdMantenedorCriou(2);
        
        $pergunta = new \ExpertsAR\Pergunta();
        $pergunta->setEnunciado("Teste")->setResposta("r")->setIdLicao(5);
        
        DAO::insert($licao1);
        DAO::insert($licao2);
        DAO::insert($licao3);
        DAO::insert($licao4);
        DAO::insert($licao5);
        DAO::insert($pergunta);
        
        DAO::removeById(5, "Licoes");
        
        $this->assertEquals(NULL, DAO::selectById("\ExpertsAR\Licao", "Licoes", 5));
        
        $cond = new Condicao(new Identificador("idLicao"), "=", 5);       
        $this->assertEquals(NULL, DAO::select("\ExpertsAR\Pergunta", "Perguntas", $cond));
        
        $this->assertEquals(NULL, DAO::selectAll("\ExpertsAR\Pergunta", "Perguntas"));
    }
    
    /** Espera alterar nas perguntas**/
    public function testUpdateIdPerguntaCascade(){
        $mantenedor = new \ExpertsAR\Mantenedor();
        $mantenedor->setNome("Luis")->setSenha("senha")->setUsuario("luisac");
        $mantenedor->setEmail("root@luisaurelio");
        DAO::insert($mantenedor);
        
        $mantenedor = new \ExpertsAR\Mantenedor();
        $mantenedor->setNome("Rodrigo")->setSenha("senha")->setUsuario("rodrigo");
        $mantenedor->setEmail("root@luisaurelio");
        DAO::insert($mantenedor);
        
        $licao1 = new \ExpertsAR\Licao();
        $licao1->setNome("Projecao")->setSlug("projecao")->setTextoLicao("umtexto")
                ->setIdMantenedorCriou(1);
        $licao2 = new \ExpertsAR\Licao();
        $licao2->setNome("Selecao")->setSlug("selecao")->setTextoLicao("umtexto")
                ->setIdMantenedorCriou(2);
        $licao3 = new \ExpertsAR\Licao();
        $licao3->setNome("Funcoes")->setSlug("funcoes")->setTextoLicao("umtexto")
                ->setIdMantenedorCriou(1);
        $licao4 = new \ExpertsAR\Licao();
        $licao4->setNome("Mais Funcoes")->setSlug("mais-funcoes")->setTextoLicao("umtexto")
                ->setIdMantenedorCriou(1);
        $licao5 = new \ExpertsAR\Licao();
        $licao5->setNome("Alberto")->setSlug("alberto")->setTextoLicao("umtexto")
                ->setIdMantenedorCriou(2);
        
        $pergunta = new \ExpertsAR\Pergunta();
        $pergunta->setEnunciado("Teste")->setResposta("r")->setIdLicao(5);
        
        DAO::insert($licao1);
        DAO::insert($licao2);
        DAO::insert($licao3);
        DAO::insert($licao4);
        DAO::insert($licao5);
        DAO::insert($pergunta);
        
        $licao = new \ExpertsAR\Licao(50);
        DAO::updateById($licao, 5);
        
        $this->assertEquals(NULL, DAO::selectById("\ExpertsAR\Licao", "Licoes", 5));
        
        $cond = new Condicao(new Identificador("idLicao"), "=", 5);       
        $this->assertEquals(NULL, DAO::select("\ExpertsAR\Pergunta", "Perguntas", $cond));
        
        
        $cond = new Condicao(new Identificador("idLicao"), "=", 50);       
        $result = DAO::select("\ExpertsAR\Pergunta", "Perguntas", $cond);
        $this->assertEquals(1, $result->count());
         
    }
}
    