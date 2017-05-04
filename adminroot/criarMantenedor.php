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
        
        if (isset($_POST["nome"]) && isset($_POST["usuario"]) && isset($_POST["email"])){
            $errors = FALSE;
            $info["error"] = "";
            if(strlen($_POST["nome"]) === 0){
                $info["error"] .= "<p>O campo nome deve ser preenchido!</p>";
                $errors = TRUE;
            }
            
            if(strlen($_POST["usuario"]) === 0){
                $info["error"] .= "<p>O nome do usuário deve ser preenchido!</p>";
                $errors = TRUE;
            }
            
            $cond = new Lib\Condicao("usuario", "=", $_POST["usuario"]);
            if(Lib\DAO::select("\ExpertsAR\Mantenedor", "Mantenedores", $cond) !== NULL){
                $info["error"] .= "<p>Já existe um usuário de mesmo nome ({$_POST["usuario"]}) no sistema!</p>";
                $errors = TRUE;
            }
            
            if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) === FALSE){
                $info["error"] .= "<p>O e-mail informado é inválido!</p>";
                $errors = TRUE;
            }
            
            //Se tiver erros manda corrigir, se não salva o dado
            if($errors === FALSE){
                $info["error"] = NULL;
                
                $senha = \Lib\DAOUtilis::geraSenha(15);
                $hash = \Lib\DAOUtilis::criptografaSenha($senha);
                
                $mantenedor = new \ExpertsAR\Mantenedor();
                $mantenedor->setNome(Lib\DAO::escapeString($_POST["nome"]));
                $mantenedor->setEmail(\Lib\DAO::escapeString($_POST["email"]));
                $mantenedor->setUsuario(\Lib\DAO::escapeString($_POST["usuario"]));
                $mantenedor->setSenha($hash);
                
                Lib\DAO::insert($mantenedor);
                
                
                $titulo = "Bem-vindo ao ExpertsAR!";
                
                $corpo = "<p> Bem vindo ao ExpertsAR, caro(a) {$mantenedor->getNome()}. </p>";
                $corpo .= "<p>Agora, você pode colaborar com a gente!</p>";        
                $corpo .= "<p>Seus dados:</p><p><b>Usuário: {$mantenedor->getUsuario()}</b></p>";
                $corpo .= "<p><b>Senha: {$senha}</b></p>";
                $corpo .= "<p>AVISO: Esta senha foi gerada automaticamente. Em nossos bancos de dados, ela foi criptografada. Você pode alterar essa senha em nosso painel</p>";
                $corpo .= "<p>Para entrar em nosso painel, aguarde as próximas instruções.</p>";
                $corpo .= "<p>Atenciosamente</p><p>Equipe ExpertsAR - OA para Álgebra Relacional </p>";
                
                $email = new \Lib\Email($mantenedor->getEmail(), $mantenedor->getNome(), $titulo, $corpo);
                $email->enviar();
                
                $info["success"] = "<p>O usuário foi criado com êxito!</p>";
            }
        }
        
        $render = new Lib\RenderTemplate("../view/root/");
        $render->render("criarMantenedor.html", $info);
        
        
    }else{
        header("Location: index.php");
    }
} catch (Exception $ex) {
    http_response_code(500);
    echo "Erro 500 ".$ex;
}