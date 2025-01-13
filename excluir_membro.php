<?php

include_once 'conexao.php';

// Verificar conexão
if (!$conn) {
    die("Erro ao conectar ao Banco de Dados: " . mysqli_connect_error());
}

// Verificar se o ID do membro foi fornecido
if (isset($_POST['membro_id'])) {
    $membro_id = intval($_POST['membro_id']);

    // Consulta SQL para deletar o membro
    $sql = "DELETE FROM membros WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $membro_id);

    if (mysqli_stmt_execute($stmt)) {
        echo "Membro deletado com sucesso.";
    } else {
        echo "Erro ao deletar o membro: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
} else {
    echo "ID do membro não fornecido.";
}

// Fechar a conexão
mysqli_close($conn);
?>
