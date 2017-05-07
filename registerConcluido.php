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
    
    if ($sessao->keyExists("logado") && $sessao->keyExists("fazendoLicao") && $sessao->getKey("fazendoLicao") !== NULL){
        $info = $info + $sessao->getAllKeys();
        $info["primeiroNome"] = explode(" ", $_SESSION["nome"])[0];
        
        //Verifica se o usuário já tinha feito essa licao
        $cond1 = new Lib\Condicao("idLicao", "=", $sessao->getKey("fazendoLicao"));
        $cond2 = new Lib\Condicao("idUsuario", "=", $sessao->getKey("id"));
        $cond = new Lib\Condicao($cond1, "AND", $cond2);
        
        $licoesConcluidas = Lib\DAO::select("\ExpertsAR\LicaoConcluida", "UsuariosLicoes", $cond);
        
        if ($licoesConcluidas === NULL){
            $ok = new ExpertsAR\LicaoConcluida();
            $ok->setIdUsuario($sessao->getKey("id"));
            $ok->setIdLicao($sessao->getKey("fazendoLicao"));
            Lib\DAO::insert($ok);
        }

        $sessao->addKey("fazendoLicao", NULL);
    }
    
    header("Location: index.php");
} catch (Exception $ex) {
    $handler = new \Lib\ExceptionHandler($ex, basename($_SERVER['PHP_SELF']));
    $handler->run();
}