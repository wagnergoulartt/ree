<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    exit('Acesso negado');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include_once 'conexao.php';

    $nova_senha = trim($_POST['nova_senha']);
    $confirmar_senha = trim($_POST['confirmar_senha']);
    $usuario_id = $_SESSION['id'];

    if ($nova_senha === $confirmar_senha) {
        $sql = "UPDATE membros SET senha = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $nova_senha, $usuario_id);

        if (mysqli_stmt_execute($stmt)) {
            echo "Senha alterada com sucesso!";
        } else {
            echo "Erro ao alterar a senha: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    } else {
        echo "As senhas não coincidem!";
    }
    exit;
}
?>

<div class="content">
    <h1>Alterar Senha</h1>
    <p>Preencha os campos abaixo para alterar sua senha.</p>
    
    <form class="senha-form" id="alterarSenhaForm">
        <input type="password" id="nova_senha" name="nova_senha" placeholder="Nova Senha" required>
        <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Confirmar Senha" required>
        <button type="submit">Alterar Senha</button>
    </form>
    <div id="mensagem-senha" class="mensagem"></div>
</div>

<style>
    .content {
        display: grid;
        place-content: center;
        width: 98%;
        max-width: 400px;
        margin: 20px auto;
        padding: 20px;
        background-color: #ffffff;
        color: #808080;
    }

    .senha-form {
        width: 100%;
        max-width: 400px;
        margin: 0 auto;
    }

    .senha-form input[type="password"] {
        width: 100%;
        margin-bottom: 16px;
        border-radius: 4px;
        font-size: 16px;
        padding: 16px;
        border: 2px solid #808080;
        height: auto;
    }

    .senha-form button {
        width: 100%;
        margin-bottom: 16px;
        border-radius: 4px;
        font-size: 16px;
        background-color: #25d366;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        height: 48px;
        cursor: pointer;
        border: none;
    }

    .mensagem {
        padding: 10px;
        margin-top: 20px;
        text-align: center;
        border-radius: 5px;
        display: none;
    }
</style>

<script>
document.getElementById('alterarSenhaForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const novaSenha = document.getElementById('nova_senha').value;
    const confirmarSenha = document.getElementById('confirmar_senha').value;
    const mensagemDiv = document.getElementById('mensagem-senha');

    if (novaSenha !== confirmarSenha) {
        mensagemDiv.style.backgroundColor = '#f8d7da';
        mensagemDiv.style.color = '#721c24';
        mensagemDiv.textContent = 'As senhas não coincidem!';
        mensagemDiv.style.display = 'block';
        return;
    }

    const formData = new FormData();
    formData.append('nova_senha', novaSenha);
    formData.append('confirmar_senha', confirmarSenha);

    fetch('alterar_senha.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        mensagemDiv.style.display = 'block';
        
        if (data.includes('sucesso')) {
            mensagemDiv.style.backgroundColor = '#d4edda';
            mensagemDiv.style.color = '#155724';
            document.getElementById('nova_senha').value = '';
            document.getElementById('confirmar_senha').value = '';
        } else {
            mensagemDiv.style.backgroundColor = '#f8d7da';
            mensagemDiv.style.color = '#721c24';
        }
        
        mensagemDiv.textContent = data;
    })
    .catch(error => {
        mensagemDiv.style.backgroundColor = '#f8d7da';
        mensagemDiv.style.color = '#721c24';
        mensagemDiv.textContent = 'Erro ao alterar a senha. Tente novamente.';
        mensagemDiv.style.display = 'block';
    });
});
</script>