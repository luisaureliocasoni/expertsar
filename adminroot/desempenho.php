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

require_once("../vendor/autoload.php");

session_name(md5('rootAlgebra'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']));
session_start();


try{
    Lib\DAO::setFilePathConfig("../assets/conexao.ini");

    if (isset($_SESSION["logado"]) && isset($_SESSION["managerRoot"])){
        $info["primeiroNome"] = explode(" ", $_SESSION["nome"])[0];
        $info["nome"] = $_SESSION["nome"];
        $info["email"] = $_SESSION["email"];
        
        //Carrega o total de lições
        $arr = \Lib\DAO::transformResourceInArray(\Lib\DAO::execute("SELECT COUNT(*) AS `count` FROM `Licoes`;"));
        $totalLicoes = $arr[0]["count"];
        $info["total"] = "Total de Lições Cadastradas: $totalLicoes.";
        
        //Pega o total de lições concluídas 
        $resource = Lib\DAO::execute("SELECT `idUsuario`,COUNT(`idLicao`) AS `LicoesConcluidas` FROM `UsuariosLicoes` GROUP BY `idUsuario`;");
        $licoesConcluidas = ($resource === NULL) ? NULL : \Lib\DAO::transformResourceInArray($resource);
        
        //Pega os usuarios
        $users = \Lib\DAO::selectAll("\ExpertsAR\Usuario", "Usuarios");
        
        //itera na lista de usuarios. Por prevencao, o for não executa quando usuarios for null
        for ($index = 0; $users !== NULL && $index < $users->count(); $index++) {
            $idUser = $users[$index]->getId();
            $info["users"][$idUser]["nome"] = $users[$index]->getNome();
            
            //Pesquisa quantas lições o aluno concluiu
            $qtdeConcluidos = 0;
            if ($licoesConcluidas !== NULL){
                foreach ($licoesConcluidas as $qtde) {
                    if($qtde["idUsuario"]  === $idUser){
                        $qtdeConcluidos = $qtde["LicoesConcluidas"];
                        break;
                    }
                }
            }
            $info["users"][$idUser]["concluiu"] = $qtdeConcluidos;
            
            //Porcentagem de licoes
            $info["users"][$idUser]["porcentagem"] = ($totalLicoes == 0) ? "---" : (($qtdeConcluidos/$totalLicoes)*100)."%"; 
        }
        
        //Possveís mensagens
        $info["errors"] = "";
        if ($totalLicoes == 0){
            $info["errors"] .= "<p>AVISO: Não temos lições cadastradas.</p>";
        }
        if ($users === NULL){
            $info["errors"] .= "<p>AVISO: Não temos usuários cadastrados.</p>";
        }
        
        
        $render = new Lib\RenderTemplate("../view/root");
        $render->render("desempenho.html", $info);
    }else{
        header("Location: index.php");
    }
} catch (Exception $ex) {
    http_response_code(500);
    echo "Erro 500 ".$ex;
}