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
        $id = $_SESSION["id"];
        
        if (isset($_POST["senhaAnterior"]) && isset($_POST["senhaNova"]) && isset($_POST["senhaNovaConfirm"])){
            $errors = FALSE;
            $info["error"] = "";
            if(strlen($_POST["senhaAnterior"]) === 0){
                $info["error"] .= "<p>A senha anterior deve ser preenchido!</p>";
                $errors = TRUE;
            }
            if(strlen($_POST["senhaNova"]) < 8){
                $info["error"] .= "<p>A senha nova deve ter mais de oito caracteres!</p>";
                $errors = TRUE;
            }
            if($_POST["senhaNova"] !== $_POST["senhaNovaConfirm"]){
                $info["error"] .= "<p>As senhas não batem!</p>";
                $errors = TRUE;
            }
            $user = Lib\DAO::selectById("\ExpertsAR\Mantenedor", "Mantenedores", $id);
            if($user->getSenha() !== \Lib\DAOUtilis::criptografaSenha($_POST["senhaAnterior"])){
                $info["error"] .= "<p>A senha antiga não confere!</p>";
                $errors = TRUE;
            }
            
            //Se tiver erros manda corrigir, se não salva o dado
            if($errors === FALSE){
                $novoUser = new \ExpertsAR\Mantenedor();
                $novoUser->setSenha(Lib\DAOUtilis::criptografaSenha($_POST["senhaNova"]));
                
                Lib\DAO::updateById($novoUser, $id);
                
                $info["error"] = NULL;
                $info["success"] = "<p>A sua nova senha foi salva!</p>";
            }
        }
        
        $render = new Lib\RenderTemplate("../view/root/");
        $render->render("updateSenha.html", $info);
    }else{
        header("Location: index.php");
    }
} catch (Exception $ex) {
    http_response_code(500);
    echo "Erro 500 ".$ex;
}

