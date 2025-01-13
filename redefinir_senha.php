<?php
include_once 'conexao.php';

if(isset($_POST['membro_id']) && isset($_POST['nova_senha'])) {
    $membro_id = $_POST['membro_id'];
    $nova_senha = $_POST['nova_senha']; // Removida a criptografia
    
    $sql = "UPDATE membros SET senha = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $nova_senha, $membro_id);
    
    if(mysqli_stmt_execute($stmt)) {
        echo "success";
    } else {
        http_response_code(500);
        echo "error";
    }
    
    mysqli_stmt_close($stmt);
} else {
    http_response_code(400);
    echo "invalid request";
}

mysqli_close($conn);
?>