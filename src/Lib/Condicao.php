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
    private $operadores = ["E" => "AND", "OU"=> "OR", 
        "IGUAL" => "=", "MAIOR" => ">", "MENOR" => "<", 
        "MENORIGUAL" => "<=", "MAIORIGUAL" => ">=", "DIFERENTE" => "<>"];
    
    /**
     * Construtor
     * @param Condicao|Identificador|string $esquerdo Parâmetro esquerdo da condição, podendo ser outra condição
     * @param type $operador Operador da condição
     * @param mixed $direito Parâmetro direito da condição, podendo ser outra condição
     * @throws \Exception Caso haja um uso incorreto do operador AND ou OR ou se operador utilizado não estiver na lista de permitidos
     */
    function __construct($esquerdo, $operador, $direito) {
        if (array_search($operador, $this->operadores) !== FALSE){
            if ($operador === "AND" || $operador === "OR"){
                if (!($esquerdo instanceof Condicao && $direito instanceof Condicao)){
                    throw new \Exception("Operador AND ou OR só pode ser usado diante de duas condições!");
                }
            }
            $this->operador = $operador;
        }else{
            throw new \Exception("Operador usado é inválido!");
        }
        if ($esquerdo instanceof Condicao){
            $this->esquerdo = $esquerdo;
        }else if ($esquerdo instanceof Identificador){
            $this->esquerdo = $esquerdo;
        }else{
            $this->esquerdo = new Identificador($esquerdo);
        }
        
        $this->direito = $direito;
    }
    
    /**
     * Transforma a condição em string SQL
     * @return string String com a condição SQL
     */
    public function toString(){
        if ($this->esquerdo instanceof Condicao){
            $strEsq = $this->esquerdo->toString();
        }else{
            $strEsq = DAOUtilis::toStr($this->esquerdo);
        }
        
        if ($this->direito instanceof Condicao){
            $strDir = $this->direito->toString();
        }else if ($this->direito === NULL){
            $strDir = "IS NULL";
        }else{
            $strDir = DAOUtilis::toStr($this->direito);
        }
        
        if ($strDir === "IS NULL"){
            return "(".$strEsq." IS NULL)";
        }
        return "(".$strEsq." ".$this->operador." ".$strDir.")";
    }
    
    

    
}
