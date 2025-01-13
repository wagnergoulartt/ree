<?php
include_once 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $whatsapp = mysqli_real_escape_string($conn, $_POST['whatsapp']);
    $login = mysqli_real_escape_string($conn, $_POST['login']); // Novo campo
    $senha = mysqli_real_escape_string($conn, $_POST['senha']); // Novo campo

    // Inserir dados na tabela membros
    $sql = "INSERT INTO membros (nome, whatsapp, login, senha) 
            VALUES ('$nome', '$whatsapp', '$login', '$senha')";

    if (mysqli_query($conn, $sql)) {
        echo "Membro cadastrado com sucesso!";
    } else {
        echo "Erro ao cadastrar o membro: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>