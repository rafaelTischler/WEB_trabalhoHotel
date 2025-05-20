<?php
function conectar()
{
    $host = "localhost";
    $bd = "bdhotel";
    $usuario = "root";
    $senha = "";
    return new PDO("mysql:host=$host;dbname=$bd", $usuario, $senha);
}
function encerrar()
{
    return null;
}
