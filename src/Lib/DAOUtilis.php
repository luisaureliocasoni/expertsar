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
            return DAO::escapeString($x);
        }else if ($type === "string"){
            //Constantes de string são colocados entre aspas simples
            return "\"".DAO::escapeString($x)."\"";
        }else if ($type === "boolean") {
            return ($x ? "TRUE" : "FALSE");
        }else if ($x instanceof Identificador){
            return $x->nome;
        }else{
            return $type;
        }
    }
    
     /**
     * Função para gerar senhas aleatórias
     *
     * @author    Thiago Belem <contato@thiagobelem.net>
     *
     * @param integer $tamanho Tamanho da senha a ser gerada
     * @param boolean $maiusculas Se terá letras maiúsculas
     * @param boolean $numeros Se terá números
     * @param boolean $simbolos Se terá símbolos
     *
     * @return string A senha gerada
     */
    public static function geraSenha($tamanho = 10, $maiusculas = true, $numeros = true, $simbolos = false)
    {
        $lmin = 'abcdefghijklmnopqrstuvwxyz';
        $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num = '1234567890';
        $simb = '!@#$%*-';
        $retorno = '';
        $caracteres = '';
        $caracteres .= $lmin;
        if ($maiusculas) {
            $caracteres .= $lmai;
        }
        if ($numeros) {
            $caracteres .= $num;
        }
        if ($simbolos) {
            $caracteres .= $simb;
        }
        $len = strlen($caracteres);
        for ($n = 1; $n <= $tamanho; $n++) {
            $rand = mt_rand(1, $len);
            $retorno .= $caracteres[$rand-1];
        }
        return $retorno;
    }
    
    public static function sorteiaNumero($tamanho = 2)
    {
        $caracteres = "0123456789";
        $retorno = '';
        $len = strlen($caracteres);
        for ($n = 1; $n <= $tamanho; $n++) {
            $rand = mt_rand(1, $len);
            $retorno .= $caracteres[$rand-1];
        }
        return $retorno;
    }
    
    public static function sorteiaOperador()
    {
        $caracteres = "-+";
        $len = strlen($caracteres);
        $rand = mt_rand(1, $len);
        return $caracteres[$rand-1];
    }
    
    /**
     * Criptografa uma senha, pelo algoritmo bluefish
     * Retorna sempre uma string de <b>60</b> caracteres.
     * @param string $pass Senha a ser criptografada
     * @return string Senha criptografada
     */
    public static function criptografaSenha(string $pass){
        $salt = '$o2L4XFMrexe0OW1R1r6uff$';
        $hash = crypt($pass, '$2a$' . '10' . $salt);
        return $hash;
    }
    
    /**
     * Verifica se uma string é um inteiro de fato
     * @param string $str String a ser testada
     * @return boolean TRUE se for um inteiro, FALSE se não for
     */
    public static function isIntString($str){
        $result = preg_match("/[0-9]+/", $str);
        //A função retorna 1 se o padrão bate, assim, basta este teste de retorno.
        return $result === 1; 
    }
}
