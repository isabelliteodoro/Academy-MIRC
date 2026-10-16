<?php

$host    = 'localhost';
$db_name = 'academia';
$usuario = 'root';
$senha   = '12345678'; 

try {
    $conexao = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $usuario, $senha);
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>

 