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
    }
    
    protected function setUp() {
        parent::setUp();
        DAO::setFilePathConfig("assets/conexaoTest.ini");
        DAO::execute("TRUNCATE TABLE \"Mantenedores\" RESTART IDENTITY CASCADE;");
        DAO::execute("TRUNCATE TABLE \"Licoes\" RESTART IDENTITY CASCADE;");
        DAO::execute("TRUNCATE TABLE \"Perguntas\" RESTART IDENTITY CASCADE;");
    }
    
    protected function tearDown() {
        parent::tearDown();
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
        $ask1->setIdLicao(1)->setEnunciado("Teste")->setResposta("Resposta");
        
        $ask2 = new \ExpertsAR\Pergunta();
        $ask2->setIdLicao(1)->setEnunciado("Teste2")->setResposta("Resposta");
       
        DAO::insert($teste);
        DAO::insert($teste2);
        DAO::insert($licao);
        DAO::insert($licao2);
        DAO::insert($ask1);
        DAO::insert($ask2);
        
        $result = DAO::execute("SELECT * FROM \"Mantenedores\";");
        $this->assertEquals(2, pg_affected_rows($result));
        
        $result = DAO::execute("SELECT * FROM \"Licoes\";");
        $this->assertEquals(2, pg_affected_rows($result));
        
        $result = DAO::execute("SELECT * FROM \"Perguntas\";");
        $this->assertEquals(2, pg_affected_rows($result));
    }
    
}
    