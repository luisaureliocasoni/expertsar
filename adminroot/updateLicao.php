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

function carregaLicao($id, &$info) {
    $licao = Lib\DAO::selectById("\ExpertsAR\Licao", "Licoes", $id);
    if ($licao === NULL){
        throw new RuntimeException("A lição não foi encontrada!");
    }
    $info["update"] = TRUE; //Marca para o Twig que a página é de update
    $info["licaoNome"] = $licao->getNome();
    $info["licaoId"] = $licao->getId();
    $info["licaoTexto"] = $licao->getTextoLicao();
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
        $info["id"] = $_SESSION["id"];
        
        if (isset($_GET["id"]) && Lib\DAOUtilis::isIntString($_GET["id"])){
            try{
                carregaLicao($_GET["id"], $info);
            } catch (RuntimeException $ex) {
                //Licao nao foi encontrada
                header("Location: verLicoes.php");
            }
        }else if (isset($_POST["nome"]) && isset($_POST["texto"]) && isset($_POST["updater"]) && isset($_POST["idLicao"])){
            $errors = FALSE;
            $info["error"] = "";
            if(strlen($_POST["nome"]) === 0){
                $info["error"] .= "<p>O campo nome deve ser preenchido!</p>";
                $errors = TRUE;
            }
            if(strlen($_POST["texto"]) === 0){
                $info["error"] .= "<p>O texto da lição deve ser preenchido!</p>";
                $errors = TRUE;
            }
            
            //Se tiver erros manda corrigir, se não salva o dado
            if($errors !== TRUE){
                $licao = new \ExpertsAR\Licao();
                $licao->setIdMantenedorAlterou($_POST["updater"]);
                $licao->setNome(Lib\DAO::escapeString($_POST["nome"]));
                $licao->setSlug(Lib\Slugger::geraSlug($_POST["nome"]));
                $licao->setTextoLicao(Lib\DAO::escapeString($_POST["texto"]));
                
                Lib\DAO::updateById($licao, $_POST["idLicao"]);
                
                header("Location: verLicao.php?id=".$_POST["idLicao"]);
            }else{
                //Tem problemas, carrega a licao novamente para corrigir
                carregaLicao($_POST["idLicao"], $info);
            }
        }else{
            header("Location: index.php");
            die();
        }
        
        $render = new Lib\RenderTemplate("../view/root/");
        $render->render("addLicao.html", $info);
    }else{
        header("Location: index.php");
    }
} catch (Exception $ex) {
    http_response_code(500);
    echo "Erro 500 ".$ex;
}