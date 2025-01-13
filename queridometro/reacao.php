<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login.php");
    exit;
}

include_once '../conexao.php';

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Consulta para obter as reações
$result = $conn->query("SELECT m1.nome AS usuario, m2.nome AS receptor, e.codigo
                        FROM reacoes r
                        JOIN membros m1 ON r.id_usuario = m1.id
                        JOIN membros m2 ON r.id_receptor = m2.id
                        JOIN emojis e ON r.id_emoji = e.id");

// Agrupa as reações por usuário
$reacoes = [];
while ($row = $result->fetch_assoc()) {
    $reacoes[$row['usuario']][] = [
        'receptor' => $row['receptor'],
        'emoji' => $row['codigo']
    ];
}

$conn->close();

// Ordena os usuários em ordem alfabética
ksort($reacoes);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reações - Queridômetro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ6HtD4iG9LQ8rUq1WfrX1+JwqF1M6h5c5OQdONbwhdF2hbPAB2Rcx7XhbP8" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 20;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 10vh;
            text-align: center;
        }
        h1 {
            color: #333;
            margin-bottom: 40px;
        }
        .reactions-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }
        .reaction-item {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            padding: 40px;
            width: 120%;
            text-align: center;
        }
        .reaction-details {
            padding: 5px 0;
        }
    </style>
</head>
<body>
    <h1>Reações</h1>
    <div class="reactions-container">
        <?php foreach ($reacoes as $usuario => $reacoes_usuario): ?>
            <div class="reaction-item">
                <?php foreach ($reacoes_usuario as $reacao): ?>
                    <div class="reaction-details">
                        <?php echo htmlspecialchars($usuario) . ' -> ' . htmlspecialchars($reacao['receptor']) . ': ' . htmlspecialchars($reacao['emoji']); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>