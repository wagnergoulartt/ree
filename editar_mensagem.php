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

// Verificar se o método da requisição é POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Obter os parâmetros da requisição
  $mensagemId = $_POST["mensagem_id"];
  $nome = $_POST["nome"];
  $mensagem = $_POST["mensagem"];

  // Atualizar a mensagem no banco de dados
  $sql = "UPDATE mensagens SET nome = '$nome', mensagem = '$mensagem' WHERE id = $mensagemId";
  $result = mysqli_query($conn, $sql);

  if ($result) {
    // Responder com o código de status 200 (OK) para indicar sucesso na atualização
    http_response_code(200);
  } else {
    // Responder com o código de status 500 (Internal Server Error) caso ocorra algum erro na atualização
    http_response_code(500);
  }
} else {
  // Responder com o código de status 405 (Method Not Allowed) caso o método da requisição não seja POST
  http_response_code(405);
}

// Fechar a conexão com o banco de dados
mysqli_close($conn);
?>
