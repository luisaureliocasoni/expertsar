<?php
function startSession(){

}
function getUser(){
    if (isset($_SESSION["usuario"]) && $_SESSION["usuario"]){
        return($_SESSION["usuario"]);
    }
}

function getIsRoot(){
    return (isset($_SESSION["usuario"]) && $_SESSION["usuario"] == "root");
}

function isLogado(){
    if ((isset($_SESSION["usuario"]) && $_SESSION["usuario"]) == FALSE){
        header("Location:login.html");
    }
}

/**
 * Função para gerar senhas aleatórias
 *
 * @author    Thiago Belem <contato@thiagobelem.net>
 *
 * @param integer $tamanho Tamanho da senha a ser gerada
 * @param boolean $maiusculas Se terá letras maiúsculas
 * @param boolean $numeros Se terá números
 * @param boolean $simbolos Se terá símbolos
 *
 * @return string A senha gerada
 */
function geraSenha($tamanho = 10, $maiusculas = true, $numeros = true, $simbolos = false)
{
    $lmin = 'abcdefghijklmnopqrstuvwxyz';
    $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $num = '1234567890';
    $simb = '!@#$%*-';
    $retorno = '';
    $caracteres = '';

    $caracteres .= $lmin;
    if ($maiusculas) $caracteres .= $lmai;
    if ($numeros) $caracteres .= $num;
    if ($simbolos) $caracteres .= $simb;

    $len = strlen($caracteres);
    for ($n = 1; $n <= $tamanho; $n++) {
        $rand = mt_rand(1, $len);
        $retorno .= $caracteres[$rand-1];
    }
    return $retorno;
}

function sessionOpen(){
    session_name(md5('10mroot'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']));
    session_start();
}