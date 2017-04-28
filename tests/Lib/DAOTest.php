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
    

}
    