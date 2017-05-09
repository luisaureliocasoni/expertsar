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
        if (isset($_POST["senha"]) && isset($_POST["senhaConfirm"]) && isset($_POST["senhaatual"])){
            $info["errors"] = "";

            if (strlen($_POST["senha"]) < 8 || strlen($_POST["senhaConfirm"]) < 8){
                header("Location profile.php?msg=15");
                die();
            }

            if ($_POST["senha"] !== $_POST["senhaConfirm"]){
                header("Location profile.php?msg=20");
                die();
            }

            $user = Lib\DAO::selectById("\ExpertsAR\Usuario", "Usuarios", $sessao->getKey("id"));
            
            if ($user === NULL){ //Usuario nao existe
                throw new Exception("Erro inesperado. Usuario {$sessao->getKey("id")} não foi encontrado na hora de alterar a senha.");
            }
            
            $pass = Lib\DAOUtilis::criptografaSenha($_POST["senhaatual"]);
            
            if ($user->getPass() !== $pass){
                header("Location: profile.php?msg=20");
                die();
            }

            //se não houve erros salva o dado e redireciona para o login
            $user = new ExpertsAR\Usuario();
            $user->setPass(\Lib\DAOUtilis::criptografaSenha($_POST["senha"]));
            Lib\DAO::updateById($user, $sessao->getKey("id"));
            
            //Envia um e-mail para comunicar o envio da senha
            $destinatario = $sessao->getKey("email");
            $nome = $sessao->getKey("nome");
            $titulo = "ExpertsAR - Senha Alterada!";
            $corpo = "Caro $nome,<br />Comunicamos que a sua senha foi alterada no site ExpertsAR.<br/>";
            $corpo .= "Se não foi você que alterou, por favor, redefina a senha na tela de login.<br/>";
            $corpo .= "Atenciosamente,<br />Equipe ExpertsAR";
            $corpo .= "<i>Esta é uma mensagem automática, favor não responder.</i>";
            Lib\Email::setFilePathConfig("assets/configEmail.ini");
            $email = new Lib\Email($destinatario, $nome, $titulo, $corpo);
            $email->enviar();

            header("Location: profile.php?msg=1");
            die();
        }else{
            header("Location profile.php?msg=10");
            die();
        }
    }else{
        header("Location: index.php");
        die();
    }
} catch (Exception $ex) {
    $handler = new \Lib\ExceptionHandler($ex, "createConta.php");
    $handler->run();
}