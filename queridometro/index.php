<?php
$conn = new mysqli('localhost', 'u529068110_recadosnovo', '@Erick91492832', 'u529068110_recadosnovo');

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Obter todos os emojis possíveis
$sqlEmojis = "SELECT id, codigo FROM emojis";
$resultEmojis = $conn->query($sqlEmojis);

$emojis = [];
while ($row = $resultEmojis->fetch_assoc()) {
    $emojis[$row['id']] = $row['codigo'];
}

// Buscar dados dos usuários e reações, excluindo o administrador com id 1
$sql = "SELECT membros.nome, emojis.id AS emoji_id, COUNT(reacoes.id) AS quantidade
        FROM membros
        LEFT JOIN reacoes ON membros.id = reacoes.id_receptor
        LEFT JOIN emojis ON reacoes.id_emoji = emojis.id
        WHERE membros.id != 1 AND DATE(reacoes.data_reacao) = CURDATE()
        GROUP BY membros.nome, emojis.id
        HAVING quantidade > 0
        ORDER BY membros.nome, emojis.id";

$result = $conn->query($sql);

$dados = [];
$totalReacoes = 0;

while ($row = $result->fetch_assoc()) {
    $nome = $row['nome'];
    $emoji_id = $row['emoji_id'];
    $quantidade = $row['quantidade'];

    // Só adiciona ao array se houver pelo menos uma reação
    if ($quantidade > 0) {
        if (!isset($dados[$nome])) {
            $dados[$nome] = [];
        }
        $dados[$nome][$emoji_id] = ['emoji' => $emojis[$emoji_id], 'quantidade' => $quantidade];
        $totalReacoes += $quantidade;
    }
}

$conn->close();
?>

    </div>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Queridômetro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ6HtD4iG9LQ8rUq1WfrX1+JwqF1M6h5c5OQdONbwhdF2hbPAB2Rcx7XhbP8" crossorigin="anonymous">
    
    <style>
    
.image-container {
        position: relative;
        width: 100%;
        height: 300px; /* Altura padrão */
        overflow: hidden;
        padding: 0; /* Remova qualquer padding do contêiner */
        margin: 0; /* Remova qualquer margem do contêiner */
        top: 0;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        
    }

    .responsive-image {
        position: absolute;
        top: -30; /* Coloca a imagem colada no topo do contêiner */
        left: 50%;
        transform: translateX(-50%); /* Centraliza a imagem horizontalmente */
        height: 100%; /* Ajusta a altura da imagem para preencher o contêiner */
        width: auto; /* Mantém a proporção da imagem */
    }

    @media (max-width: 768px) {
        .image-container {
            height: 200px; /* Ajuste a altura para dispositivos móveis */
        }

        .responsive-image {
            height: auto; /* Ajusta a altura da imagem automaticamente */
            width: 100%; /* Ajusta a largura da imagem para preencher o contêiner */
            max-height: 100%; /* Limita a altura máxima para evitar distorções */
            top: 0; /* Garante que a imagem fique colada ao topo também em dispositivos móveis */
        }
    }
    
    
    
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 50vh;
            text-align: center;
            padding-top: 0px;
        }
        h1 {
            color: #333;
        }
        .user-reactions-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            padding: 20px;
            max-width: 355px;
        }
        .user-reactions {
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            width: 100%;
            margin-bottom: 20px;
        }
        .user-reactions strong {
            display: block;
            margin-bottom: 10px;
            font-size: 1.2em;
            color: #555;
        }
        .user-reactions .emoji {
            font-size: 1.5em;
            margin-left: 10px;
            margin-right: 10px;
            margin-top: 8px;
            position: relative;
            display: inline-block;
        }
        .user-reactions .badge {
            font-size: 0.4em; /* Ajuste o tamanho do badge */
            padding: 5px 10px;
            position: absolute;
            top: -10px; /* Ajuste para cima */
            left: 18px; /* Ajuste para a esquerda */
            border-radius: 50%; /* Faz o badge circular */
            background-color: #007bff; /* Cor padrão do badge, substituível por classes específicas */
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 1px; /* Ajuste o tamanho do badge */
            height: 10px; /* Ajuste o tamanho do badge */
        }
    </style>
</head>
<body>
    <div class="user-reactions-container">
        <?php if ($totalReacoes > 0): ?>
            <?php foreach ($dados as $nome => $emojis_usuario): ?>
                <div class="user-reactions">
                    <strong><?php echo $nome; ?>:</strong>
                    <?php foreach ($emojis_usuario as $emoji_data): ?>
                        <span class="emoji">
                            <?php echo $emoji_data['emoji']; ?>
                            <span class="badge">
                                <?php echo $emoji_data['quantidade']; ?>
                            </span>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Ainda não tem nem uma reação hoje!</p>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    
    

    
    
    
    
</body>
</html>
