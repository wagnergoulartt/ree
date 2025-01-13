<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['tipo'] !== 'usuario') {
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
    <title>RECADOS - GRUPO SOCIALIZANDO</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #ffffff;
        }

        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            left: 0;
            top: 0;
            background-color: #f8f9fa;
            transition: 0.3s;
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            overflow: hidden;
            z-index: 1000;
        }

        .sidebar.collapsed {
            width: 60px;
        }

        .menu {
            display: flex;
            flex-direction: column;
            width: 100%;
            margin-top: 20px;
        }

        .menu a {
            color: black;
            padding: 15px 20px;
            text-decoration: none;
            display: flex;
            align-items: center;
            white-space: nowrap;
            overflow: hidden;
            font-weight: bold;
            width: 250px;
        }

        .menu a i {
            margin-right: 15px;
            min-width: 40px;
            text-align: center;
        }

        .menu a:hover {
            background-color: #fff;
        }

        .menu a.logout {
            color: #dc3545;
        }

        .menu a.logout i {
            color: #dc3545;
        }

        .menu a.logout:hover {
            background-color: #dc3545;
            color: white;
        }

        .menu a.logout:hover i {
            color: white;
        }

        .toggle-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            background: none;
            border: none;
            font-size: 20px;
            color: #000;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            transition: 0.3s;
        }

        .content.sidebar-collapsed {
            margin-left: 60px;
        }
    </style>
</head>
<body>
    <div class="sidebar collapsed" id="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="menu">
            <a href="#" onclick="loadContent('formrecados.php'); return false;">
                <i class="fas fa-comment"></i>Recado
            </a>
            <a href="#" onclick="loadContent('queridometro/reagir.php'); return false;">
                <i class="fas fa-bomb"></i>Queridometro
            </a>
            <a href="#" onclick="loadContent('alterar_senha.php'); return false;">
                <i class="fas fa-key"></i>Alterar Senha
            </a>
            <a href="logout.php" class="logout">
                <i class="fas fa-sign-out-alt"></i>Sair
            </a>
        </div>
    </div>

    <div class="content sidebar-collapsed" id="content">
        <!-- O conteúdo será carregado aqui -->
    </div>

<script>
    const BASE_URL = '<?php echo rtrim(dirname($_SERVER['PHP_SELF']), '/\\'); ?>';
    
    // Função para alternar a sidebar
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('sidebar-collapsed');
    }

    // Função para validar campos e habilitar/desabilitar botões
    function validarCampos() {
        var nome = document.getElementById("nome").value;
        var msg = document.getElementById("msg").value;
        var buttons = document.getElementsByClassName("whatsapp");
        
        for (var i = 0; i < buttons.length; i++) {
            buttons[i].disabled = (nome === '' || msg === '');
        }
    }

    // Função para inicializar eventos do formulário
    function initializeFormEvents() {
        const nomeSelect = document.getElementById("nome");
        const msgInput = document.getElementById("msg");
        if (nomeSelect && msgInput) {
            nomeSelect.addEventListener("change", validarCampos);
            msgInput.addEventListener("input", validarCampos);
            validarCampos(); // Validação inicial
        }
    }

    // Função principal para carregar conteúdo
    function loadContent(page) {
        document.getElementById('content').innerHTML = '<div class="loading">Carregando...</div>';
        
        if (page === 'formrecados.php') {
            fetch(page)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro ao carregar a página');
                    }
                    return response.text();
                })
                .then(data => {
                    document.getElementById('content').innerHTML = data;
                    setTimeout(() => {
                        initializeFormEvents();
                    }, 100);
                })
                .catch(error => {
                    console.error('Erro:', error);
                    document.getElementById('content').innerHTML = '<div class="error">Erro ao carregar o conteúdo</div>';
                });
        }
        else if (page === 'queridometro/reagir.php') {
            fetch(page)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro ao carregar a página');
                    }
                    return response.text();
                })
                .then(data => {
                    document.getElementById('content').innerHTML = data;
                    initializeQueridometro();
                })
                .catch(error => {
                    console.error('Erro:', error);
                    document.getElementById('content').innerHTML = '<div class="error">Erro ao carregar o queridômetro</div>';
                });
        }
        else if (page === 'index.php') {
            window.location.href = 'index.php';
        }
        else {
            fetch(page)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro ao carregar a página');
                    }
                    return response.text();
                })
                .then(data => {
                    document.getElementById('content').innerHTML = data;
                })
                .catch(error => {
                    console.error('Erro:', error);
                    document.getElementById('content').innerHTML = '<div class="error">Erro ao carregar o conteúdo</div>';
                });
        }
    }

    // Função para inicializar o queridômetro
    function initializeQueridometro() {
        const form = document.querySelector('#reactionForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('queridometro/processa_reacao.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Reações registradas com sucesso!');
                        loadContent('queridometro/reagir.php');
                    } else {
                        const warningMessage = document.getElementById('warning-message');
                        if (warningMessage) {
                            warningMessage.textContent = data.message;
                            warningMessage.style.display = 'block';
                        } else {
                            alert(data.message);
                        }
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao processar reações');
                });
            });
        }
    }

    // Função para enviar mensagem pelo WhatsApp
    function enviarWhatsapp(numero, nomeBotao) {
        var selectElement = document.getElementById("nome");
        var nome = selectElement.options[selectElement.selectedIndex].getAttribute('data-nome');
        var msg = document.getElementById("msg").value;
        var encodedMsg = encodeURIComponent(msg);
        var url = "https://wa.me/" + numero + "?text="
            + "*RECADO ANÔNIMO*" + "%0a"
            + "%0a"
            + "*RECADO:* " + encodedMsg + "%0a"
            + "*PARA:* " + nome;
        window.open(url, '_blank').focus();
        
        document.getElementById("whatsappHidden").value = numero;
        return true;
    }

    // Carrega o conteúdo inicial quando a página é carregada
    document.addEventListener('DOMContentLoaded', function() {
        loadContent('formrecados.php');
    });

    // Adiciona tratamento de erros global
    window.addEventListener('error', function(e) {
        console.error('Erro global:', e.error);
    });
</script>
</body>
</html>
