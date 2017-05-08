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
    
    if ($sessao->keyExists("logado") && isset($_GET["id"]) && Lib\DAOUtilis::isIntString($_GET["id"])){
        $info = $info + $sessao->getAllKeys();
        $info["primeiroNome"] = explode(" ", $_SESSION["nome"])[0];
        
        $licao = \Lib\DAO::selectById("\ExpertsAR\Licao", "Licoes", $_GET["id"]);
        
        if ($licao === NULL){ //se a lição não for encontrada
            http_response_code(404);
            $render->render("404.html");
            die();
        }
        
        //Verifica se o usuário não tinha feito nenhuma lição;
        $cond1 = new Lib\Condicao("idUsuario", "=", $sessao->getKey("id"));
        $licoesConcluidasUsuario = Lib\DAO::select("\ExpertsAR\LicaoConcluida", "UsuariosLicoes", $cond1);
        if ($licoesConcluidasUsuario !== NULL){//Se o usuario ja concluiu lições
            //Verifica se o usuário já tinha feito essa licao
            $cond2 = new Lib\Condicao("idLicao", "=", $_GET["id"]);
            $cond = new Lib\Condicao($cond1, "AND", $cond2);

            $licoesConcluidas = Lib\DAO::select("\ExpertsAR\LicaoConcluida", "UsuariosLicoes", $cond);

            //se tiver feito, passa, se não vai para mais um teste
            if ($licoesConcluidas === NULL){ //se é null, siginifca que não concluiu a licao
                $query = "SELECT \"id\", \"nome\" FROM \"Licoes\" WHERE \"id\" > (SELECT MAX(\"idLicao\") FROM \"UsuariosLicoes\" WHERE \"idUsuario\" = {$sessao->getKey("id")}) ORDER BY \"id\";";
                $array = \Lib\DAO::transformResourceInArray(\Lib\DAO::execute($query));
                //Pega o primeiro indice do array, que é a próxima licao a ser feita
                //Se o id da primeira licao bater com o id da licao a ser feita, passa
                //Caso nao seja, quer dizer que o usuário está tentando acessar uma lição mais avançada
                //E isso não é permitido.
                if ($array[0]["id"] !== $_GET["id"]){ //Se o id da próxima lição a ser feita não bate com o id da licao pedida
                    //redireciona
                    header("Location:index.php?error=2");
                    die();
                }
            }
        }else{ //se for a primeira licao, pega todas as lições
            $query = "SELECT \"id\", \"nome\" FROM \"Licoes\" ORDER BY \"id\";";
            $array = \Lib\DAO::transformResourceInArray(\Lib\DAO::execute($query));
            //Pega o primeiro indice do array, que é a próxima licao a ser feita
            //Se o id da primeira licao bater com o id da licao a ser feita, passa
            //Caso nao seja, quer dizer que o usuário está tentando acessar uma lição mais avançada
            //E isso não é permitido.
            if ($array[0]["id"] !== $_GET["id"]){ //Se o id da próxima lição a ser feita não bate com o id da licao pedida
                //redireciona
                header("Location:index.php?error=2");
                die();
            }
        }

        
        $info["licao"] = $licao;
        
        //Busca pelas perguntas
        $cond = new \Lib\Condicao("idLicao", "=", $_GET["id"]);
        $perguntas = \Lib\DAO::select("\ExpertsAR\Pergunta", "Perguntas", $cond);
        
        $info["perguntas"] = $perguntas;
        
        $sessao->addKey("fazendoLicao", $_GET["id"]);
        
        $render->render("lession.html", $info);
    }else{
        header("Location: index.php");
    }
} catch (Exception $ex) {
    $handler = new \Lib\ExceptionHandler($ex, basename($_SERVER['PHP_SELF']));
    $handler->run();
}