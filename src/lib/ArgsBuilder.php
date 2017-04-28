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
 * Construtor de argumentos para o controller
 *
 * @author Luís Aurélio Casoni
 */
class ArgsBuilder
{
    private $array;

    /**
     * ArgsBuilder constructor.
     */
    public function __construct()
    {
        $this->array = new \ArrayObject();
        return $this;
    }
    
    /**
     * Adiciona um argumento a lista
     * @param string $nome Nome da chave do argumento
     * @param mixed $value Valor associado
     * @return $this Referência para o próprio objeto
     */
    public function addArg(string $nome, $value){
        $this->array[$nome] = $value;
        return $this;
    }
    
    /**
     * Retorna um clone da lista atual de argumentos
     * @return array O clone da lista de argumentos
     */
    public function returnAllArgs(){
        $clone = array();

        foreach ($this->array as $k => $v) {
            $clone[$k] = clone $v;
        }
        
        return $clone;
    }
    
    /**
     * Retorna um argumento presente na lista
     * @param string $nome Nome do argumento a ser buscado
     * @return mixed O valor associado ao nome se encontrado
     * @throws Exception Se o argumento não for encontrado
     */
    public function returnArg($nome){
        if (array_key_exists($nome, $this->array)){
            return $this->array[$nome];
        }
        throw new Exception("Argumento $nome não existe no array de argumentos!");
    }
}