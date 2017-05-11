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
    
    $sessao = new \Lib\SessionManager();
    $render = new Lib\RenderTemplate("view/");
    \Lib\DAO::setFilePathConfig("assets/conexao.ini");
    $info = [];
    
    
    if ($sessao->keyExists("logado")){
      
        $info = $info + $sessao->getAllKeys();
        //seta a key de fazendo licao como null
        $sessao->addKey("fazendoLicao", NULL);
        
        $info["primeiroNome"] = explode(" ", $_SESSION["nome"])[0];
        
        
        if (isset($_GET["msg"]) && \Lib\DAOUtilis::isIntString($_GET["msg"])){
            switch ($_GET["msg"]) {
                case 1:
                    $info["success"] = "<p>Senha alterada com êxito.</p>";
                    break;
                
                case 10:
                    $info["errors"] = "<p>Um dos campos está faltando. Revise seus dados e tente novamente.</p>";
                    break;

                case 15:
                    $info["errors"] = "<p>A senha deve ter, no mínimo 8 caracteres.</p>";
                    break;
                
                case 20:
                    $info["errors"] = "<p>Senha atual incorreta e/ou senhas não correspondem.</p>";
                    break;
                
                default:
                    break;
            }
        }
        
        $render->render("profile.html", $info);
    }else{
        header("Location: index.php");
    }
} catch (Exception $ex) {
    $handler = new \Lib\ExceptionHandler($ex, basename($_SERVER['PHP_SELF']));
    $handler->run();
}
