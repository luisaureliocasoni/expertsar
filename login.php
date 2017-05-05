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
    
    if (isset($_GET["icode"]) && \Lib\DAOUtilis::isIntString($_GET["icode"])){
        if ($_GET["icode"] == 1){
            $info["success"] = "Quase lá! Agora faça o login para começar a aprender!";
        }
    }
    
    
    if (isset($_POST["email"]) && isset($_POST["senha"])){
        $info["errors"] = "";
        
        if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) === FALSE){
            $info["errors"] .= "<p>O e-mail fornecido é inválido!</p>";
        }
        
        $cond = new Lib\Condicao("email", "=", $_POST["email"]);
        $result = Lib\DAO::select("\ExpertsAR\Usuario", "Usuarios", $cond);
        
        if ($result === NULL || ($result[0]->getPass() !== \Lib\DAOUtilis::criptografaSenha($_POST["senha"]))){
            $info["errors"] .= "<p>Usuário ou senha incorretos.</p>";
        }
        
        
        
        if ($info["errors"] === ""){ //se não houve erros salva o dado e redireciona para o login
            $sessao->destroy();
            $sessao = new \Lib\SessionManager();
            
            $sessao->addKey("logado", TRUE);
            $sessao->addKey("nome", $result[0]->getNome());
            $sessao->addKey("id", $result[0]->getId());
            $sessao->addKey("email", $result[0]->getEmail());
            
            header("Location: index.php");
            die();
        }
    }
    //Renderiza página
    $render->render("login.html", $info);
    
} catch (Exception $ex) {
    $handler = new \Lib\ExceptionHandler($ex, basename($_SERVER['PHP_SELF']));
    $handler->run();
}