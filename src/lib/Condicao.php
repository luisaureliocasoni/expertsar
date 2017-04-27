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

namespace lib;

/**
 * Classe que representa uma condição SQL
 *
 * @author Luís Aurélio Casoni
 */
class Condicao {
    /*
     * Condicao ou valor da condição esquerda
     * @var mixed
     */
    private $esquerdo;
    /*
     * O operador da condicao
     * @var string
     */
    private $operador;
    /*
     * Condicao ou valor da condição direita
     * @var mixed
     */
    private $direito;
    
    /*
     * Constante de operadores válidos
     */
    const operadores = ["E" => "AND", "OU"=> "OR", 
        "IGUAL" => "=", "MAIOR" => ">", "MENOR" => "<", 
        "MENORIGUAL" => "<=", "MAIORIGUAL" => ">=", "DIFERENTE" => "<>"];
    
    /**
     * Construtor
     * @param mixed $esquerdo Parâmetro esquerdo da condição, podendo ser outra condição
     * @param type $operador Operador da condição
     * @param mixed $direito Parâmetro direito da condição, podendo ser outra condição
     * @throws \Exception Caso haja um uso incorreto do operador AND ou OR ou se operador utilizado não estiver na lista de permitidos
     */
    function __construct($esquerdo, $operador, $direito) {
        $this->esquerdo = $esquerdo;
        if (array_search($operador, self::operadores) !== FALSE){
            if ($operador === "AND" || $operador === "OR"){
                if (!($esquerdo instanceof Condicao) && !($direito instanceof Condicao)){
                    throw new \Exception("Operador AND ou OR só pode ser usado diante de duas condições!");
                }
            }
            $this->operador = $operador;
        }else{
            throw new \Exception("Operador usado é inválido!");
        }
        $this->direito = $direito;
    }
    
    public function toString(){
        if ($this->esquerdo instanceof Condicao){
            $strEsq = $this->esquerdo->toString();
        }else{
            $strEsq = $this->esquerdo;
        }
        
        if ($this->direito instanceof Condicao){
            $strDir = $this->direito->toString();
        }else{
            $strDir = $this->direito;
        }
        
        return $strEsq.$this->operador.$strDir;
    }
    
    

    
}
