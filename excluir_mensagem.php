<html>
<head>
    <meta name="robots" content="noindex">
    <!-- Outras tags e metadados do cabeçalho -->
</head>
</html>



<?php

include_once 'conexao.php';

// Verificar se a conexão foi estabelecida com sucesso
if (!$conn) {
  die("Erro ao conectar ao banco de dados: " . mysqli_connect_error());
}

// Verificar se o parâmetro "mensagem_id" foi recebido
if (isset($_POST["mensagem_id"])) {
  $mensagem_id = $_POST["mensagem_id"];

  // Excluir a mensagem do banco de dados
  $sql = "DELETE FROM mensagens WHERE id = '$mensagem_id'";
  if (mysqli_query($conn, $sql)) {
    // A exclusão foi realizada com sucesso
    echo "Recado excluído com sucesso!";
  } else {
    // Ocorreu um erro ao excluir a mensagem
    echo "Erro ao excluir o recado: " . mysqli_error($conn);
  }
}

// Fechar a conexão com o banco de dados
mysqli_close($conn);
?>
