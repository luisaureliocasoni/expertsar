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

try{
    Lib\DAO::setFilePathConfig("../assets/conexao.ini");
    $result = Lib\DAO::selectAll("\ExpertsAR\Mantenedor", "Mantenedores");
    if ($result !== NULL){
        echo "Este script só roda se não tiver nenhum mantenedor cadastrado!";
        die();
    }
    $manager = new \ExpertsAR\Mantenedor();
    $manager->setUsuario("root");
    $manager->setEmail("root@root");
    $manager->setNome("root");
    $pass = Lib\DAOUtilis::geraSenha(15);
    $manager->setSenha(Lib\DAOUtilis::criptografaSenha($pass));
    echo $pass;
    \Lib\DAO::insert($manager);
} catch (Exception $ex){
    echo $ex;
}