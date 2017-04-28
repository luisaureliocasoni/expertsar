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
 * Utilitários para o DAO
 *
 * @author Luís Aurélio Casoni
 */
class DAOUtilis {
    /**
     * Verifica uma variável e retorna uma string com o valor SQL válido
     * Ex: "teste" => "\"teste\""
     *     TRUE => "TRUE"
     * @param mixed $x Valor a ser avaliado
     * @return string A string convertida
     */
    public static function toStr($x){
        $type = gettype($x);
        if ($type === "integer" || $type === "double"){
            return pg_escape_string($x);
        }else if ($type === "string"){
            //Constantes de string são colocados entre aspas simples
            return "'".pg_escape_string($x)."'";
        }else if ($type === "boolean") {
            return ($x ? "TRUE" : "FALSE");
        }else if ($x instanceof Identificador){
            return $x->nome;
        }else{
            return $type;
        }
    }
}
