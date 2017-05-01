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
 **/

//este arquivo faz a interpretação de uma query no banco de dados de teste e devolve a
// resposta ou o erro.

require_once "../vendor/autoload.php";

//error_reporting(0);

if (isset($_POST["query"])){
    \Lib\DAO::setFilePathConfig("../assets/conexaoLoc.ini");
    $query = $_POST["query"];
    $query = trim($query);

    $illegalOperators = ["INSERT", "DROP", "DELETE", "UPDATE"];

    if (strpos($query, "SELECT") !== 0){
        http_response_code(400);
        echo "ERRO: Uso ilegal da query. Somente são permitidas operações de SELECT.";
        die();
    }

    foreach ($illegalOperators as $illegalOperator) {
        if (strpos($query, $illegalOperator) !== FALSE){
            http_response_code(400);
            echo "ERRO: Uso ilegal da query. Somente são permitidas operações de SELECT.";
            die();
        }
    }

    try{
        $result = Lib\DAO::execute($query);
        $array = \Lib\DAO::transformResourceInArray($result);

        echo json_encode($array);
    } catch (Exception $ex) {
        http_response_code(500);
        echo "Erro: {$ex->getMessage()}";
        die();
    }




}else{
    http_response_code(405); //Method not Allowed
}
