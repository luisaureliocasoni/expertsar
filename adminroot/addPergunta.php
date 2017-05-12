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

function loadNomeLicao($idLicao){
    $licao = Lib\DAO::selectById("\ExpertsAR\Licao", "Licoes", $idLicao);
    return $licao->getNome();
}


try{
    require_once("../vendor/autoload.php");

    session_name(md5('rootAlgebra'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']));
    session_start();

    Lib\DAO::setFilePathConfig("../assets/conexao.ini");

    if (isset($_SESSION["logado"]) && isset($_SESSION["managerRoot"])){
        $info["primeiroNome"] = explode(" ", $_SESSION["nome"])[0];
        $info["nome"] = $_SESSION["nome"];
        $info["email"] = $_SESSION["email"];
        $info["lession"] = TRUE;

        if (isset($_GET["id"])){
            $info["id"] = $_GET["id"];
            $info["nomeLicao"] = loadNomeLicao($_GET["id"]);
            $render = new Lib\RenderTemplate("../view/root/");
            $render->render("addPergunta.html", $info);
        }else if (isset($_POST["enunciado"]) && isset($_POST["resposta"]) && isset($_POST["idLicao"]) && isset($_POST["query"])){
            $info["nomeLicao"] = loadNomeLicao($_POST["idLicao"]);
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
            
            $info["id"] = $_POST["idLicao"];
            
            //Se tiver erros manda corrigir, se não salva o dado
            if($errors !== TRUE){
                $info["error"] = NULL;
                $pergunta = new \ExpertsAR\Pergunta();
                $pergunta->setEnunciado(Lib\DAO::escapeString($_POST["enunciado"]));
                $pergunta->setResposta($_POST["resposta"]);
                $pergunta->setIdLicao($_POST["idLicao"]);
                $pergunta->setRespostaAlgebra(Lib\DAO::escapeString($_POST["query"]));

                Lib\DAO::insert($pergunta);

                $info["success"] = "<p>A pergunta foi salva com êxito!</p>";  
            }
            
            $render = new Lib\RenderTemplate("../view/root/");
            $render->render("addPergunta.html", $info);
        }else{
            header("Location: verLicoes.php");
        }
    }else{
        header("Location: index.php");
    }
} catch (Exception $ex) {
    http_response_code(500);
    echo "Erro 500 ".$ex;
}
