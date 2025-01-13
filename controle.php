
<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['tipo'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$arquivo_status = 'status_paginas.txt';

// Verifica se o arquivo existe, se não, cria com valores padrão
if (!file_exists($arquivo_status)) {
    $status_inicial = [
        'reagir' => true,
        'recados' => true
    ];
    file_put_contents($arquivo_status, json_encode($status_inicial));
}

// Se receber uma requisição POST para alterar status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pagina'])) {
    $status = json_decode(file_get_contents($arquivo_status), true);
    $pagina = $_POST['pagina'];
    $novo_status = $_POST['status'] === 'true';
    
    $status[$pagina] = $novo_status;
    file_put_contents($arquivo_status, json_encode($status));
    
    echo json_encode(['success' => true]);
    exit;
}

// Lê o status atual das páginas
$status = json_decode(file_get_contents($arquivo_status), true);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Controle de Páginas</title>
    <style>
        .controle-container {
            max-width: 600px;
            margin: 20px;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .pagina-controle {
            margin: 15px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #2196F3;
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        .status-text {
            margin-left: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="controle-container">
        <h2>Controle de Páginas</h2>
        
        <div class="pagina-controle">
            <span>Queridômetro</span>
            <div>
                <label class="switch">
                    <input type="checkbox" onchange="alterarStatus('reagir', this.checked)" 
                           <?php echo $status['reagir'] ? 'checked' : ''; ?>>
                    <span class="slider"></span>
                </label>
                <span class="status-text" id="status-reagir">
                    <?php echo $status['reagir'] ? 'Ativado' : 'Desativado'; ?>
                </span>
            </div>
        </div>

        <div class="pagina-controle">
            <span>Recados (recados</span>
            <div>
                <label class="switch">
                    <input type="checkbox" onchange="alterarStatus('recados', this.checked)"
                           <?php echo $status['recados'] ? 'checked' : ''; ?>>
                    <span class="slider"></span>
                </label>
                <span class="status-text" id="status-recados">
                    <?php echo $status['recados'] ? 'Ativado' : 'Desativado'; ?>
                </span>
            </div>
        </div>
    </div>

    <script>
        function alterarStatus(pagina, status) {
            fetch('controle.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `pagina=${pagina}&status=${status}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`status-${pagina}`).textContent = 
                        status ? 'Ativado' : 'Desativado';
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao alterar status da página');
            });
        }
    </script>
</body>
</html>
