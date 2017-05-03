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

require_once("../vendor/autoload.php");

session_name(md5('rootAlgebra'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']));
session_start();


try{
    Lib\DAO::setFilePathConfig("../assets/conexao.ini");
    
    //Verifica se o id foi preenchido e se o tipo é realmente um inteiro
    if ((isset($_GET["id"]) === FALSE) || (Lib\DAOUtilis::isIntString($_GET["id"]) === FALSE)){
        header("Location: verLicoes.php");
        die();
    }else{
        $id = $_GET["id"];
    }

    if (isset($_SESSION["logado"]) && isset($_SESSION["managerRoot"])){
        $info["primeiroNome"] = explode(" ", $_SESSION["nome"])[0];
        $info["nome"] = $_SESSION["nome"];
        $info["email"] = $_SESSION["email"];
        

        $licao = \Lib\DAO::selectById("\ExpertsAR\Licao", "Licoes", $id);
        
        $cond = new \Lib\Condicao("idLicao", "=", $id);
        $perguntas = \Lib\DAO::select("\ExpertsAR\Pergunta", "Perguntas", $cond);
        
        if ($licao !== NULL){
            $licao->setTextoLicao(html_entity_decode($licao->getTextoLicao()));
            $licao->setTextoLicao(htmlspecialchars_decode($licao->getTextoLicao()));
        }
        
        $info["licao"] = $licao;
        $info["perguntas"] = $perguntas;
        
        
        $render = new Lib\RenderTemplate("../view/root");
        $render->render("licao.html", $info);
    }else{
        header("Location: index.php");
    }
} catch (Exception $ex) {
    http_response_code(500);
    echo "Erro 500 ".$ex;
}