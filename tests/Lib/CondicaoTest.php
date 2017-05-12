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
require_once "./vendor/autoload.php";
use PHPUnit\Framework\TestCase as PHPUnit;

/**
 * Description of CondicaoTest
 *
 * @author root
 */
class CondicaoTest extends PHPUnit{
    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        DAO::setFilePathConfig("assets/conexaoTest.ini");
    }
    
    public function testCondicaoIdentificadorNumero(){
        $condicao = new Condicao(new Identificador("teste"), "=", 85);
        $this->assertEquals("(`teste` = 85)", $condicao->toString());
    }
    
    public function testCondicaoIdentificadorString(){
        $condicao = new Condicao(new Identificador("teste"), ">=", "leonardo");
        $this->assertEquals("(`teste` >= \"leonardo\")", $condicao->toString());
    }
    
    public function testCondicaoIdentificadorBoolean(){
        $condicao = new Condicao(new Identificador("teste"), "<>", TRUE);
        $this->assertEquals("(`teste` <> TRUE)", $condicao->toString());
    }
    
    public function testCondicaoOperadorInvalido(){
        $this->expectException(\Exception::class);
        $condicao = new Condicao("olpeflwefp", "&", 85);
    }
    
    public function testCondicaoOperadorInvalidoAND(){
        $this->expectException(\Exception::class);
        $condicao = new Condicao("olpeflwefp", "AND", 85);
    }
    
    public function testCondicaoOperadorInvalidoOR(){
        $this->expectException(\Exception::class);
        $condicao = new Condicao("olpeflwefp", "OR", 85);
    }
    
    public function testCondicaoCompostaEsquerda(){
        $this->expectException(\Exception::class);
        $condicao = new Condicao(new Identificador("teste"), "<>", TRUE);
        $condicao2 = new Condicao($condicao, "AND", TRUE);
    }
    
    public function testCondicaoCompostaDireita(){
        $this->expectException(\Exception::class);
        $condicao = new Condicao(new Identificador("teste"), "<>", TRUE);
        $condicao2 = new Condicao(TRUE, "AND", $condicao);
    }
    
    public function testCondicaoCompostaDosDoisLadosAND(){
        $condicao1 = new Condicao("Guilherme", "=", 25);
        $condicao2 = new Condicao(new Identificador("nome"), "<>", "Silva");
        $condicao = new Condicao($condicao1, "AND", $condicao2);
        
        $this->assertEquals("((`Guilherme` = 25) AND (`nome` <> \"Silva\"))", 
                $condicao->toString());
    }
    
    public function testCondicaoCompostaDosDoisLadosOR(){
        $condicao1 = new Condicao("Guilherme", "=", 25);
        $condicao2 = new Condicao(new Identificador("nome"), "<>", "Silva");
        $condicao = new Condicao($condicao1, "OR", $condicao2);
        
        $this->assertEquals("((`Guilherme` = 25) OR (`nome` <> \"Silva\"))", 
                $condicao->toString());
    }
    
}
