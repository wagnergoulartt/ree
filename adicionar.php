<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Se não estiver logado, redireciona para a página de login
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
  <title>Cadastro de Membros</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <!-- Seus estilos CSS permanecem os mesmos -->
  <style>
    .form-group {
      margin-bottom: 16px;
    }

    .form-label {
      font-weight: bold;
    }

    .form-control {
      border-radius: 4px;
      padding: 12px;
      border: 1px solid #ced4da;
      width: 100%;
      max-width: 350px; /* Ajuste o valor conforme necessário */
    }

    .form-control:focus {
      border-color: #128C7E;
      box-shadow: 0 0 0 0.2rem rgba(18, 140, 126, 0.25);
    }

    .text-center {
      text-align: center;
    }

    .btn-primary {
      background-color: #25D366;
      border-color: #25D366;
      transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #128C7E;
      border-color: #128C7E;
    }

    .mensagem {
      background-color: #f8f9fa;
      border-radius: 4px;
      padding: 16px;
      margin-bottom: 16px;
      text-align: center;
    }

    .btn-success {
      background-color: #25D366;
      border-color: #25D366;
      color: #fff;
      padding: 8px 16px;
      border-radius: 4px;
      transition: background-color 0.3s ease;
    }

    .btn-success:hover {
      background-color: #128C7E;
      border-color: #128C7E;
    }
  </style>
</head>
<body>
  <h2 class="text-center">CADASTRO DE MEMBROS</h2>
  <div class="container">
    <div class="mensagem">
      <form id="formCadastro" class="text-center">
        <div class="form-group">
          <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" required>
        </div>
        <div class="field-wrapper form-group">
          <div class="field">
            <input type="tel" class="form-control" id="telefone" name="whatsapp" placeholder="Whatsapp" required maxlength="15" oninput="formatarTelefone(this);">
          </div>
        </div>
        <!-- Novos campos para login e senha -->
        <div class="form-group">
          <input type="text" class="form-control" id="login" name="login" placeholder="Login" required>
        </div>
        <div class="form-group">
          <input type="password" class="form-control" id="senha" name="senha" placeholder="Senha" required>
        </div>
        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Salvar</button>
      </form>
    </div>
  </div>

  <!-- Bootstrap JavaScript Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    function formatarTelefone(input) {
      var phoneNumber = input.value.replace(/\D/g, '');
      phoneNumber = phoneNumber.slice(0, 10);
      phoneNumber = phoneNumber.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
      input.value = phoneNumber;
    }

    document.getElementById('formCadastro').addEventListener('submit', function(event) {
      event.preventDefault();

      // Obter valores do formulário incluindo login e senha
      var nome = document.getElementById('nome').value;
      var whatsapp = document.getElementById('telefone').value;
      var login = document.getElementById('login').value;
      var senha = document.getElementById('senha').value;

      // Formatar o número de telefone
      var whatsappFormatado = whatsapp.replace(/\D/g, '');
      whatsappFormatado = '55' + whatsappFormatado + '@c.us';

      // Criar objeto XMLHttpRequest
      var xhr = new XMLHttpRequest();

      xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
          if (xhr.status === 200) {
            alert('Membro cadastrado com sucesso!');
            document.getElementById('formCadastro').reset();
          } else {
            alert('Ocorreu um erro ao cadastrar o membro. Por favor, tente novamente mais tarde.');
          }
        }
      };

      // Enviar requisição incluindo login e senha
      xhr.open("POST", "salvar_membro.php", true);
      xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhr.send(
        "nome=" + encodeURIComponent(nome) + 
        "&whatsapp=" + encodeURIComponent(whatsappFormatado) + 
        "&login=" + encodeURIComponent(login) + 
        "&senha=" + encodeURIComponent(senha)
      );
    });
  </script>
</body>
</html>