<?php
include_once 'conexao.php';

if (!$conn) {
    die("Erro ao conectar ao Banco de Dados: " . mysqli_connect_error());
}

if (isset($_POST['membro_id']) && isset($_POST['nome']) && isset($_POST['whatsapp']) && isset($_POST['login'])) {
    $membro_id = intval($_POST['membro_id']);
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $whatsapp = mysqli_real_escape_string($conn, $_POST['whatsapp']);
    $login = mysqli_real_escape_string($conn, $_POST['login']);
    
    if (!empty($_POST['senha'])) {
        $senha = mysqli_real_escape_string($conn, $_POST['senha']);
        $sql = "UPDATE membros SET nome = ?, whatsapp = ?, login = ?, senha = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssssi', $nome, $whatsapp, $login, $senha, $membro_id);
    } else {
        $sql = "UPDATE membros SET nome = ?, whatsapp = ?, login = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'sssi', $nome, $whatsapp, $login, $membro_id);
    }

    if (mysqli_stmt_execute($stmt)) {
        echo "Membro atualizado com sucesso.";
    } else {
        echo "Erro ao atualizar o membro: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Dados insuficientes fornecidos.";
}

mysqli_close($conn);
?>