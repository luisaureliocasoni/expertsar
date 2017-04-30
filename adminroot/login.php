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

require_once '../vendor/autoload.php';
//error_reporting(0);

try{
    //Verifica se preencheu o usuário e a senha
    if (isset($_POST["user"]) && isset($_POST["senha"])){
        Lib\DAO::setFilePathConfig("../assets/conexao.ini");
        $user = pg_escape_string($_POST["user"]);
        $cond = new Lib\Condicao("usuario", "=", $user);
        $mantenedor = Lib\DAO::select("\ExpertsAR\Mantenedor", "Mantenedores", $cond);
        if ($mantenedor === NULL){
            $render = new Lib\RenderTemplate();
            $array["informacao"] = "Usuário ou senha incorretos.";
            $render->render("loginroot.html", $array);
            die();
        }
        
        $senhaDigitada = Lib\DAOUtilis::criptografaSenha($_POST["senha"]);
        if ($senhaDigitada === $mantenedor[0]->getSenha()){
            echo "Conseguiu";
            //inicia a sessão
            session_name(md5('rootAlgebra'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']));
            session_start();
            $_SESSION["logado"] = TRUE;
            $_SESSION["usuario"] = $mantenedor[0]->getUsuario();
            $_SESSION["nome"] = $mantenedor[0]->getNome();
            $_SESSION["email"] = $mantenedor[0]->getEmail();
            header("Location: index.php");
            die();
        }else{
            $render = new Lib\RenderTemplate();
            $array["informacao"] = "Usuário ou senha incorretos.";
            $render->render("loginroot.html", $array);
            die();
        }
        
    }else{
        $render = new Lib\RenderTemplate();
        $array["informacao"] = "Você deve preencher os dois campos!";
        $render->render("loginroot.html", $array);
        die();
    }
} catch (Exception $ex) {
    http_response_code(500);
    echo "Erro 500 ".$ex;
}
