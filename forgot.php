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
    
    //geracao do captcha
    $info["op1"] = \Lib\DAOUtilis::sorteiaNumero();
    $info["op2"] = \Lib\DAOUtilis::sorteiaNumero();
    $info["operador"] = \Lib\DAOUtilis::sorteiaOperador();
    
    if ($sessao->keyExists("logado")){
        //Se está logado, obviamente tem que redirecionar ao indice
        header("Location: index.php");
        die();
    }
    
    
    if (isset($_POST["email"]) && isset($_POST["captcha"])){
        $info["errors"] = "";
        
        if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) === FALSE){
            $info["errors"] .= "<p>O e-mail fornecido é inválido!</p>";
        }
        
        $cond = new Lib\Condicao("email", "=", $_POST["email"]);
        $result = Lib\DAO::select("\ExpertsAR\Usuario", "Usuarios", $cond);
        
        if ($result === NULL){
            $info["errors"] .= "<p>Não achamos o e-mail cadastrado em nossa base de dados. <a href=\"createConta.php\">Quer criar uma nova?</a></p>";
        }
        
        
        $eq = $_POST["operando1"].$_POST["operador"].$_POST["operando2"];
        if ($_POST["captcha"] != jlawrence\eos\Parser::solve($eq)){
            $info["errors"] .= "<p>A resposta do desafio fornecido é incorreta!</p>";
        }
        
        if ($info["errors"] === ""){ //se não houve erros salva o dado e redireciona para o login
            $info["errors"] = NULL;
            //gera a senha
            $pass = \Lib\DAOUtilis::geraSenha(15);
            //criptografa a senha
            $hash = \Lib\DAOUtilis::criptografaSenha($pass);
            //salva ela
            //A condicao do email está na linha 53
            $user = new \ExpertsAR\Usuario();
            $user->setPass($hash);
            \Lib\DAO::update($user, $cond);
            
            //Envia um e-mail para comunicar o envio da senha
            $destinatario = $result[0]->getEmail();
            $nome = $result[0]->getNome();
            
            date_default_timezone_set("America/Campo_Grande");
            $date = date('Y-m-d H:i:s');
            
            $titulo = "ExpertsAR - Nova Senha";
            $corpo = "Caro $nome,<br />Comunicamos que a sua senha foi alterada no site ExpertsAR às $date (AMT).<br/>";
            $corpo .= "Sua nova senha é <b>$pass</b><br />";
            $corpo .= "Lembrando que esta senha foi criptografada antes de salvar nas nossas bases de dados, ";
            $corpo .= "assim, só você tem acesso a ela. Recomendamos que você altere no seu painel de controle o mais rápido possível.<br/>";
            $corpo .= "Se não foi você que alterou, por favor, redefina a senha na tela de login.<br/>";
            $corpo .= "Atenciosamente,<br />Equipe ExpertsAR<br/>";
            $corpo .= "<i>Esta é uma mensagem automática, favor não responder.</i>";
            Lib\Email::setFilePathConfig("assets/configEmail.ini");
            $email = new Lib\Email($destinatario, $nome, $titulo, $corpo);
            $email->enviar();
            
            $info["success"] = "<p>Uma nova senha foi enviada para {$_POST["email"]}. Se não receber, verifique sua caixa de SPAM.</p>";
        }
    }
    //Renderiza página
    $render->render("forgot.html", $info);
    
} catch (Exception $ex) {
    $handler = new \Lib\ExceptionHandler($ex, basename($_SERVER['PHP_SELF']));
    $handler->run();
}