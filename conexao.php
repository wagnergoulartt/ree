<?php
// Conectar ao banco de dados
$dbHost = "localhost";
$dbUser = "u529068110_recadosnovo";
$dbPass = "@Erick91492832";
$dbName = "u529068110_recadosnovo";
$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

// Verificar conexão
if (!$conn) {
    die("Erro ao conectar ao Banco de Dados: " . mysqli_connect_error());
}
?>