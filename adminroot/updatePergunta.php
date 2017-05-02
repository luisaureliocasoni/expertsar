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

function loadPergunta($id, &$info){
    $pergunta = Lib\DAO::selectById("\ExpertsAR\Pergunta", "Perguntas", $id);
    if($pergunta === NULL){
        throw new RuntimeException("A lição não foi encontrada!");
    }
    $info["enunciado"] = $pergunta->getEnunciado();
    $info["idPergunta"] = $pergunta->getId();
    $info["idLicao"] = $pergunta->getIdLicao();
    $info["query"] = $pergunta->getRespostaAlgebra();
    $info["resposta"] = $pergunta->getResposta();
}

try{
    require_once("../vendor/autoload.php");

    session_name(md5('rootAlgebra'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']));
    session_start();

    Lib\DAO::setFilePathConfig("../assets/conexao.ini");

    if (isset($_SESSION["logado"])){
        $info["primeiroNome"] = explode(" ", $_SESSION["nome"])[0];
        $info["nome"] = $_SESSION["nome"];
        $info["email"] = $_SESSION["email"];
        $info["lession"] = TRUE;
        $info["update"] = TRUE;
        $render = new Lib\RenderTemplate("../view/root/");

        if (isset($_GET["idPergunta"])){
            try{
                loadPergunta($_GET["idPergunta"], $info);
            } catch (RuntimeException $ex) {
                //Licao nao foi encontrada
                header("Location: verLicoes.php");
            }
            $render->render("addPergunta.html", $info);
        }else if (isset($_POST["enunciado"]) && isset($_POST["resposta"]) && isset($_POST["idPergunta"]) 
                && isset($_POST["query"]) && isset($_POST["idLicao"])){
            $errors = FALSE;
            $info["error"] = "";

            if(strlen($_POST["enunciado"]) === 0){
                $info["error"] .= "<p>O campo enunciado deve ser preenchido!</p>";
                $errors = TRUE;
            }
            if(strlen($_POST["resposta"]) === 0){
                $info["error"] .= "<p>Você deve fornecer uma resposta!</p>";
                $errors = TRUE;
            }
            if(strlen($_POST["query"]) === 0){
                $info["error"] .= "<p>Você deve fornecer uma query!</p>";
                $errors = TRUE;
            }

            //Se tiver erros manda corrigir, se não salva o dado
            if($errors !== TRUE){
                $pergunta = new \ExpertsAR\Pergunta();
                $pergunta->setEnunciado(Lib\DAO::escapeString($_POST["enunciado"]));
                $pergunta->setResposta(Lib\DAO::escapeString($_POST["resposta"]));
                $pergunta->setRespostaAlgebra(Lib\DAO::escapeString($_POST["query"]));
                
                Lib\DAO::updateById($pergunta, $_POST["idPergunta"]);

                header("Location: verLicao.php?id=".$_POST["idLicao"]);
                die();
            }else{
                //Tem problemas, carrega a licao novamente para corrigir
                try{
                    loadPergunta($_POST["idPergunta"], $info);
                } catch (RuntimeException $ex) {
                    //Licao nao foi encontrada
                    header("Location: verLicoes.php");
                }
                $render->render("addPergunta.html", $info);
            }
        }else{
            header("Location: verLicoes.php");
        }
        
    }else{
        $render = new Lib\RenderTemplate();
        $render->render("loginroot.html");
    }
} catch (Exception $ex) {
    http_response_code(500);
    echo "Erro 500 ".$ex;
}
