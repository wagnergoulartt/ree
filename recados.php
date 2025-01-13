<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MENSAGENS - SOCIALIZANDO</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <meta name="robots" content="noindex">
<style>
    .mensagem {
      background-color: #f8f9fa;
      border-radius: 4px;
      padding: 16px;
      margin-bottom: 16px;
    }

    .text-center {
      text-align: center;
    }

    .whatsapp-button {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      padding: 8px 16px;
      background-color: #25D366;
      border: none;
      border-radius: 4px;
      color: #fff;
      font-size: 16px;
      transition: background-color 0.3s ease;
    }

    .whatsapp-button:hover {
      background-color: #128C7E;
    }

    .whatsapp-button i {
      margin-right: 8px;
    }

    .apagar-button {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      padding: 8px 16px;
      background-color: #FF0000;
      border: none;
      border-radius: 4px;
      color: #fff;
      font-size: 16px;
      transition: background-color 0.3s ease;
    }

    .apagar-button:hover {
      background-color: #CC0000;
    }

    .apagar-button i {
      margin-right: 8px;
    }

    .editar-button {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      padding: 8px 16px;
      background-color: #FFD700;
      border: none;
      border-radius: 4px;
      color: #000;
      font-size: 16px;
      transition: background-color 0.3s ease;
    }

    .editar-button:hover {
      background-color: #FFC300;
    }

    .editar-button i {
      margin-right: 8px;
    }

    .button-group {
      display: flex;
      justify-content: space-between;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2 class="text-center">LISTA DE RECADOS</h2>

    <?php
    include_once 'conexao.php';

    $fuso_horario = new DateTimeZone('America/Sao_Paulo');
    $data_hora_atual = new DateTime('now');
    $data_hora_atual->setTimezone($fuso_horario);

    if (!$conn) {
        die("Erro ao conectar ao banco de dados: " . mysqli_connect_error());
    }

    mysqli_query($conn, "SET NAMES 'utf8mb4'");
    mysqli_query($conn, "SET CHARACTER SET utf8mb4");

    $sql = "SELECT * FROM mensagens ORDER BY data_envio ASC";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo '<div class="d-flex justify-content-center">';
        echo '<div class="row">';
        $count = 1;

        while ($row = mysqli_fetch_assoc($result)) {
            $whatsSemSufixo = str_replace('@c.us', '', $row["whats"]);
            echo '<div class="col-md-6 col-lg-4">';
            echo '<div class="mensagem text-left">';
            echo '<p><b>RECADO ANÔNIMO</b></p>';
            echo '<p><b>NÚMERO:</b> ' . $count . '</p>';
            

            
            echo '<p><b>NOME:</b> <span id="nome_' . $row["id"] . '">' . $row["nome"] . '</span></p>';
            echo '<p><b>WHATSAPP:</b> <span id="whats_' . $row["id"] . '">' . $whatsSemSufixo . '</span></p>';
            echo '<p><b>MENSAGEM:</b> <span id="mensagem_' . $row["id"] . '">' . $row["mensagem"] . '</span></p>';
            echo '<p><span id="info_' . $row["id"] . '">' . $row["botao"] . '</span></p>';

            $data_envio = new DateTime($row["data_envio"]);
            $data_envio->setTimezone($fuso_horario);
            $data_envio->modify("-3 hours");
            echo '<b><p>Recebido às: ' . $data_envio->format('H:i - d-m') . '</p></b>';

            echo '<div class="button-group">';
            echo '<button class="apagar-button" onclick="apagarMensagem(' . $row["id"] . ');"><i class="fas fa-trash"></i> Apagar</button>';
            echo '<button class="editar-button" onclick="editarMensagem(' . $row["id"] . ');"><i class="fas fa-edit"></i> Editar</button>';
            
            $conteudoCompartilhar = "*RECADO ANÔNIMO*%0A%0A*NÚMERO:* " . $count . 
                                   "%0A*NOME:* " . urlencode($row["nome"]) . 
                                   "%0A*MENSAGEM:* " . urlencode($row["mensagem"]) . 
                                   "%0A*PARA:* " . urlencode($whatsSemSufixo);

            echo '<button class="whatsapp-button" onclick="window.location.href=\'https://api.whatsapp.com/send?text=' . 
                 $conteudoCompartilhar . '\';"><i class="fab fa-whatsapp"></i> Postar</button>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            $count++;
        }

        echo '</div>';
        echo '</div>';
    } else {
        echo '<center><p>Nenhum recado encontrado.</p></center>';
    }

    mysqli_close($conn);
    ?>

<script>
      function apagarMensagem(id) {
    if (confirm("Deseja apagar o recado?")) {
      // Crie um objeto XMLHttpRequest
      var xhr = new XMLHttpRequest();
      
      // Defina a função de retorno de chamada para a requisição AJAX
      xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
          if (xhr.status === 200) {
            // Recarregue a página após excluir o recado
            window.location.reload();
          } else {
            // Exiba uma mensagem de erro caso ocorra algum problema na exclusão
            alert("Ocorreu um erro ao excluir o recado. Por favor, tente novamente mais tarde.");
          }
        }
      };
      
      // Envie a requisição para o arquivo "excluir_mensagem.php" com o parâmetro "id" da mensagem
      xhr.open("POST", "excluir_mensagem.php", true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.send("mensagem_id=" + id);
    }
  }

  function editarMensagem(id) {
    // Obter o conteúdo atual do campo "nome"
    var nome = document.getElementById('nome_' + id).innerText;

    // Obter o conteúdo atual do campo "mensagem"
    var mensagem = document.getElementById('mensagem_' + id).innerText;

    // Exibir prompts para o usuário inserir o novo nome e a nova mensagem
    var novoNome = prompt("Digite o novo nome:", nome);
    var novaMensagem = prompt("Digite a nova mensagem:", mensagem);

    if (novoNome !== null && novaMensagem !== null) {
      // Crie um objeto XMLHttpRequest
      var xhr = new XMLHttpRequest();
      
      // Defina a função de retorno de chamada para a requisição AJAX
      xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
          if (xhr.status === 200) {
            // Atualize o conteúdo dos campos "nome" e "mensagem"
            document.getElementById('nome_' + id).innerText = novoNome;
            document.getElementById('mensagem_' + id).innerText = novaMensagem;
          } else {
            // Exiba uma mensagem de erro caso ocorra algum problema na edição
            alert("Ocorreu um erro ao editar o recado. Por favor, tente novamente mais tarde.");
          }
        }
      };
      
      // Envie a requisição para o arquivo "editar_mensagem.php" com os parâmetros "id", "nome" e "mensagem" da mensagem
      xhr.open("POST", "editar_mensagem.php", true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.send("mensagem_id=" + id + "&nome=" + novoNome + "&mensagem=" + novaMensagem);
    }
  }
    </script>

  </div>
</body>
</html>