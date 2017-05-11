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
    require_once("vendor/autoload.php");
    Lib\DAO::setFilePathConfig("assets/conexao.ini");
    $render = new \Lib\RenderTemplate("view/");
    $sessao = new \Lib\SessionManager();
    $info = [];
    if ($sessao->keyExists("logado")){
        var_dump($sessao);
        if (isset($_POST["pass"]) && isset($_POST["confirmacao"])){
            $info["errors"] = "";

            if ($_POST["pass"] !== $_POST["confirmacao"]){
                header("Location: profile.php?msg=20");
                die();
            }

            $user = Lib\DAO::selectById("\ExpertsAR\Usuario", "Usuarios", $sessao->getKey("id"));
            
            if ($user === NULL){ //Usuario nao existe
                throw new Exception("Erro inesperado. Usuario {$sessao->getKey("id")} não foi encontrado na hora de excluir a sua conta.");
            }
            
            $pass = Lib\DAOUtilis::criptografaSenha($_POST["pass"]);
            
            if ($user->getPass() !== $pass){
                header("Location: profile.php?msg=20");
                die();
            }
            
            //Envia um e-mail para comunicar a exclusão da conta
            $destinatario = $sessao->getKey("email");
            $nome = $sessao->getKey("nome");
            
            date_default_timezone_set("America/Campo_Grande");
            $date = date('Y-m-d H:i:s');
            
            $titulo = "ExpertsAR - Exclusão de conta";
            $corpo = "Caro $nome,<br />Comunicamos que a sua conta no site foi excluída às $date (AMT).<br/>";
            $corpo .= "Lamentamos a sua decisão. Caso você queira voltar, estaremos a sua disposição.<br/>";
            $corpo .= "Atenciosamente,<br />Equipe ExpertsAR";
            $corpo .= "<i>Esta é uma mensagem automática, favor não responder.</i>";
            Lib\Email::setFilePathConfig("assets/configEmail.ini");
            $email = new Lib\Email($destinatario, $nome, $titulo, $corpo);
            $email->enviar();
            
            //Exclui a conta
            \Lib\DAO::removeById($sessao->getKey("id"), "Usuarios");
            
            //Exclui a sessao
            
            $sessao->destroy();
            header("Location: index.php");
            die();
        }else{
            header("Location: profile.php?msg=10");
            die();
        }
    }else{
        header("Location: index.php");
        die();
    }
} catch (Exception $ex) {
    $handler = new \Lib\ExceptionHandler($ex, "deleteconta.php");
    $handler->run();
}