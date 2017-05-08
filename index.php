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
        
        if (isset($_GET["error"]) && \Lib\DAOUtilis::isIntString($_GET["error"])){
            switch ($_GET["error"]) {
                case 2:
                    $info["errors"] = "<p>Você estava tentando acessar uma lição mais avançada! Por favor faça outras lições para desbloquear.</p>";
                    break;

                default:
                    break;
            }
        }
        $info = $info + $sessao->getAllKeys();
        //seta a key de fazendo licao como null
        $sessao->addKey("fazendoLicao", NULL);
        
        $info["primeiroNome"] = explode(" ", $_SESSION["nome"])[0];
        
        //Carrega o total de lições
        $arr = \Lib\DAO::transformResourceInArray(\Lib\DAO::execute("SELECT COUNT(*) FROM \"Licoes\""));
        $info["totalLicoes"] = $arr[0]["count"];
        
        //Pega todas as liçoes que o aluno concluiu
        $query = "SELECT \"id\", \"nome\" FROM \"Licoes\" WHERE \"id\" <= (SELECT MAX(\"idLicao\") FROM \"UsuariosLicoes\" WHERE \"idUsuario\" = {$sessao->getKey("id")}) ORDER BY \"id\";";
        $array = \Lib\DAO::transformResourceInArray(\Lib\DAO::execute($query));
        //Pega a quantidade de lições concluídas
        $info["licoesConcluidas"] = count($array);
        $info["porcentagemConcluidas"] = ($info["licoesConcluidas"] / $info["totalLicoes"])*100;
        
        $maxIdLicaoConcluida = 0;
        $i = 0;
        
        if ($array !== NULL){
            foreach ($array as $key => $value) {
                $info["licoes"][$i] = $value; //Copia o array da instância de licao
                $info["licoes"][$i++]["concluido"] = TRUE;
                $maxIdLicaoConcluida = $value["id"]; //Pega o id da licao que foi concluido
            }
        }
        
        //Pega a lição seguinte a última concluida
        $query2 = "SELECT \"id\", \"nome\" FROM \"Licoes\" WHERE \"id\" > $maxIdLicaoConcluida ORDER BY \"id\";";
        $array2 = \Lib\DAO::transformResourceInArray(\Lib\DAO::execute($query2));
        
        if ($array2 === NULL){ //Se nao tiver nada, quer dizer que ele concluiu todas as lições
            $info["concluiu"] = TRUE;
        }else{ //Se tiver, pega a próxima licao
            $info["licoes"][$i] = $array2[0];
        }
        
        $render->render("home.html", $info);
    }else{
        $render->render("index.html");
    }
} catch (Exception $ex) {
    $handler = new \Lib\ExceptionHandler($ex, basename($_SERVER['PHP_SELF']));
    $handler->run();
}
