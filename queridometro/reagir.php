<?php
session_start();


$status = json_decode(file_get_contents('./status_paginas.txt'), true);
if ($status['reagir'] === false) {  // Mudança aqui
    echo '<div style="text-align: center; padding: 50px; font-size: 18px;">
            <h2>Página Temporariamente Indisponível</h2>
            <p>O Queridômetro está temporariamente desativado.</p>
          </div>';
    exit;
}


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login.php");
    exit;
}

$conn = new mysqli('localhost', 'u529068110_recadosnovo', '@Erick91492832', 'u529068110_recadosnovo');
mysqli_set_charset($conn, "utf8mb4");

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$user_id = (int)$_SESSION['id'];

// Verifica se o usuário existe na tabela membros
$check_user = $conn->prepare("SELECT id FROM membros WHERE id = ?");
$check_user->bind_param("i", $user_id);
$check_user->execute();
$result = $check_user->get_result();

if ($result->num_rows === 0) {
    die("ID de usuário inválido");
}
$check_user->close();

// Verifica se o usuário já reagiu hoje
$hoje = date('Y-m-d');
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM reacoes WHERE id_usuario = ? AND DATE(data_reacao) = ?");
$stmt->bind_param("is", $user_id, $hoje);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$has_reacted = $row['total'] > 0;
$stmt->close();

$button_visible = !$has_reacted;
$warning_message = $has_reacted ? "Você já reagiu hoje, volte amanhã." : "";

// Carrega usuários excluindo o admin (ID 1)
$result = $conn->query("SELECT * FROM membros WHERE id != 1 ORDER BY nome ASC");
$usuarios = $result->fetch_all(MYSQLI_ASSOC);

// Carrega emojis
$result = $conn->query("SELECT * FROM emojis");
$emojis = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reagir - Queridômetro</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .emoji-label {
            font-size: 24px;
            cursor: pointer;
            padding: 5px;
            margin: 5px;
            border-radius: 5px;
        }
        .emoji-option:checked + .emoji-label {
            background-color: #e0e0e0;
        }
        .emoji-option {
            display: none;
        }
    </style>
</head>
<body>
    <div class="queridometro-container">
        <h1 class="queridometro-title">Queridômetro</h1>
        
        <div id="warning-message" style="display: <?php echo isset($warning_message) ? 'block' : 'none'; ?>">
            <?php echo isset($warning_message) ? htmlspecialchars($warning_message) : ''; ?>
        </div>

        <form id="reactionForm" method="POST">
            <div class="users-grid">
                <?php foreach ($usuarios as $usuario): ?>
                    <div class="user">
                        <h3 class="user-name"><?php echo htmlspecialchars($usuario['nome']); ?></h3>
                        <div class="emoji-options">
                            <?php foreach ($emojis as $emoji): ?>
                                <input type="radio" 
                                       id="emoji_<?php echo $usuario['id'].'_'.$emoji['id']; ?>" 
                                       name="emoji[<?php echo $usuario['id']; ?>]" 
                                       value="<?php echo $emoji['id']; ?>" 
                                       class="emoji-option" 
                                       required>
                                <label for="emoji_<?php echo $usuario['id'].'_'.$emoji['id']; ?>" class="emoji-label">
                                    <?php echo htmlspecialchars($emoji['codigo']); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($button_visible): ?>
                <button type="submit" class="submit-button">Enviar Reações</button>
            <?php endif; ?>
        </form>
    </div>

    <script>
        document.getElementById('reactionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const emojiInputs = document.querySelectorAll('input[type="radio"]:checked');
            const warningMessage = document.getElementById('warning-message');
            
            if (emojiInputs.length !== <?php echo count($usuarios); ?>) {
                warningMessage.textContent = "Você precisa selecionar um emoji para cada usuário";
                warningMessage.style.display = 'block';
                return;
            }
            
            const formData = new FormData(this);
            
            fetch('processa_reacao.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Reações registradas com sucesso!');
                    window.location.reload();
                } else {
                    warningMessage.textContent = data.message;
                    warningMessage.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                warningMessage.textContent = 'Erro ao processar reações';
                warningMessage.style.display = 'block';
            });
        });
    </script>
</body>
</html>
