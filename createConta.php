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
        //Se está logado, obviamente tem que redirecionar ao indice
        header("Location: index.php");
        die();
    }
    
    //geracao do captcha
    $info["op1"] = \Lib\DAOUtilis::sorteiaNumero();
    $info["op2"] = \Lib\DAOUtilis::sorteiaNumero();
    $info["operador"] = \Lib\DAOUtilis::sorteiaOperador();
    
    if (isset($_POST["nome"]) && isset($_POST["email"]) && isset($_POST["senha"]) && isset($_POST["senhaConfirm"]) && isset($_POST["captcha"])){
        $info["errors"] = "";
        
        if (strlen($_POST["nome"]) === 0){
            $info["errors"] .= "<p>O nome deve ser preenchido!</p>";
        }
        
        if (strlen($_POST["senha"]) < 8 || strlen($_POST["senhaConfirm"]) < 8){
            $info["errors"] .= "<p>A senha deve ter oito caracteres ou mais!</p>";
        }
        
        if ($_POST["senha"] !== $_POST["senhaConfirm"]){
            $info["errors"] .= "<p>As senhas não batem!</p>";
        }
        
        if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) === FALSE){
            $info["errors"] .= "<p>O e-mail fornecido é inválido!</p>";
        }
        
        $cond = new Lib\Condicao("email", "=", $_POST["email"]);
        $result = Lib\DAO::select("\ExpertsAR\Usuario", "Usuarios", $cond);
        
        if ($result !== NULL){
            $info["errors"] .= "<p>O e-mail {$_POST["email"]} já está cadastrado. <a href=\"forgot.php\">Deseja gerar uma nova senha?</a></p>";
        }
        
        
        if (filter_var($_POST["captcha"], FILTER_VALIDATE_INT) === FALSE){
            $info["errors"] .= "<p>A resposta do desafio fornecido é inválido!</p>";
        }
        
        $eq = $_POST["operando1"].$_POST["operador"].$_POST["operando2"];
        if ($_POST["captcha"] != jlawrence\eos\Parser::solve($eq)){
            $info["errors"] .= "<p>A resposta do desafio fornecido é incorreta!</p>";
        }
        
        if(!(isset($_POST["aceitaTermos"]))){
            $info["errors"] .= "<p>Por favor, aceite nossos termos de uso para prosseguir.</p>";
        }
        
        
        
        if ($info["errors"] === ""){ //se não houve erros salva o dado e redireciona para o login
            $user = new ExpertsAR\Usuario();
            $user->setEmail(Lib\DAO::escapeString($_POST["email"]));
            $user->setNome(Lib\DAO::escapeString($_POST["nome"]));
            $user->setPass(\Lib\DAOUtilis::criptografaSenha($_POST["senha"]));
            Lib\DAO::insert($user);
            
            header("Location: login.php?icode=1");
            die();
        }
    }
    //Renderiza página
    $render->render("createConta.html", $info);
    
} catch (Exception $ex) {
    $handler = new \Lib\ExceptionHandler($ex, "createConta.php");
    $handler->run();
}