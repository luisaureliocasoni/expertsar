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
    require_once 'vendor/autoload.php';
    $sessao = new \Lib\SessionManager();
    
    if ($sessao->keyExists("logado")){
        $email = $sessao->getKey("email");
        $nome = $sessao->getKey("nome");
    }else{
        if (isset($_POST["nome"]) && isset($_POST["email"])){
            $email = $_POST["email"];
            $nome = $_POST["nome"];
        }else{
            header("Location: index.php");
            die();
        }
    }
    
    $corpo = isset($_POST["mensagem"]) ? $_POST["mensagem"] : NULL;
    
    if($corpo == NULL){
        header("Location: index.php");
        die();
    }
    
    $titulo = "ExpertsAR - Contato";
    date_default_timezone_set("America/Campo_Grande");
    $data = date("d/m/Y - h:i:s A");
    $corpo = "Contato feito por $nome - $email às $data <br /> $corpo";
    
    $destino = parse_ini_file("assets/destEmail.ini");
    Lib\Email::setFilePathConfig("assets/configEmail.ini");
    $email = new Lib\Email($destino["destinatario"], $destino["nome"], $titulo, $corpo);
    $email->enviar();
    
    header("Location: index.php?msgEmailEnviado=1");
} catch (Exception $ex) {
    $handler = new \Lib\ExceptionHandler($ex, basename($_SERVER['PHP_SELF']));
    $handler->run();
}



