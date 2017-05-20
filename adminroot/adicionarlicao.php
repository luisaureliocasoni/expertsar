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
        
        if (isset($_POST["nome"]) && isset($_POST["texto"]) && isset($_POST["creator"])){
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
            
            //Verificacao se a slug gerada sera a mesma de outra slug presente no sistema
            $slug = Lib\Slugger::geraSlug(Lib\DAO::escapeString($_POST["nome"]));
            $cond = new Lib\Condicao("slug", "=", $slug);
            
            if(Lib\DAO::select("\ExpertsAR\Licao", "Licoes", $cond) !== NULL){
                $info["error"] .= "<p>Já existe uma lição do mesmo nome ({$_POST["nome"]}) no sistema!</p>";
                $errors = TRUE;
            }
            
            //Se tiver erros manda corrigir, se não salva o dado
            if($errors !== TRUE){
                $info["error"] = NULL;
                $licao = new \ExpertsAR\Licao();
                $licao->setIdMantenedorCriou($_POST["creator"]);
                $licao->setNome(Lib\DAO::escapeString($_POST["nome"]));
                $licao->setSlug($slug);
                $licao->setTextoLicao($_POST["texto"]);
                
                Lib\DAO::insert($licao);
                
                $info["success"] = "<p>A lição foi salva com êxito!</p>";
            }
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