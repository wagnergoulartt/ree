<?php
session_start();

// Verifica se o usuário está logado E se é um admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['tipo'] !== 'admin') {
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
    <title>ADMIN RECADOS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
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
            min-width: 20px;
            text-align: center;
        }

        .menu a:hover {
            background-color: #fff;
        }

        .menu .active {
            background-color: #fff;
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
            margin-left: 60px; /* Alterado para começar recolhido */
            padding: 20px;
            transition: 0.3s;
        }

        .menu a.logout {
            color: #dc3545; /* Vermelho */
        }

        .menu a.logout i {
            color: #dc3545; /* Vermelho */
        }

        .menu a.logout:hover {
            background-color: #dc3545;
            color: white;
        }

        .menu a.logout:hover i {
            color: white;
        }

        .sidebar.collapsed .menu a {
            width: 60px;
            padding: 15px 20px;
            justify-content: center;
        }

        .sidebar.collapsed .menu a i {
            margin-right: 30px;
        }

        .sidebar.collapsed .menu a span {
            display: none;
        }
    </style>

</head>
<body>
    <div class="sidebar collapsed" id="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="menu">
            <a href="javascript:void(0)" onclick="loadPage('recados.php', this)" id="recados">
                <i class="fas fa-comments"></i>
                <span>RECADOS</span>
            </a>
            <a href="javascript:void(0)" onclick="loadPage('adicionar.php', this)" id="adicionar">
                <i class="fas fa-plus"></i>
                <span>ADICIONAR</span>
            </a>
            <a href="javascript:void(0)" onclick="loadPage('membros.php', this)" id="membros">
                <i class="fas fa-users"></i>
                <span>MEMBROS</span>
            </a>
            <a href="javascript:void(0)" onclick="loadPage('emoji.php', this)" id="emoji">
    <i class="fas fa-smile"></i>
    <span>EMOJIS</span>
</a>
            <a href="javascript:void(0)" onclick="logout()" class="logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Sair</span>
            </a>
        </div>
    </div>

    <div class="content expanded" id="content"></div>

    <script>
        // Função para alternar a sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            sidebar.classList.toggle('collapsed');
            
            if (sidebar.classList.contains('collapsed')) {
                content.style.marginLeft = '60px';
            } else {
                content.style.marginLeft = '250px';
            }
        }

        // Função melhorada para carregar páginas
        async function loadPage(page, element) {
            try {
                const response = await fetch(page, {
                    method: 'GET',
                    headers: {
                        'Cache-Control': 'no-cache',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.text();
                const contentDiv = document.getElementById('content');
                contentDiv.innerHTML = data;

                // Atualiza classe ativa
                document.querySelectorAll('.menu a').forEach(link => {
                    link.classList.remove('active');
                });
                
                if (element) {
                    element.classList.add('active');
                }

                // Salva página atual
                localStorage.setItem('currentPage', page);

                // Reexecuta scripts
                const scripts = Array.from(contentDiv.getElementsByTagName('script'));
                scripts.forEach(oldScript => {
                    const newScript = document.createElement('script');
                    
                    // Copia atributos
                    Array.from(oldScript.attributes).forEach(attr => {
                        newScript.setAttribute(attr.name, attr.value);
                    });

                    // Copia conteúdo inline
                    newScript.textContent = oldScript.textContent;
                    
                    // Substitui script antigo
                    oldScript.parentNode.replaceChild(newScript, oldScript);
                });

            } catch (error) {
                console.error('Erro ao carregar página:', error);
                alert('Erro ao carregar a página. Por favor, tente novamente.');
            }
        }

        // Função de logout
        function logout() {
            if (confirm('Deseja realmente sair?')) {
                localStorage.clear();
                window.location.href = 'logout.php';
            }
        }

        // Inicialização quando o documento carrega
        document.addEventListener('DOMContentLoaded', () => {
            // Carrega a última página visitada ou a página padrão
            const currentPage = localStorage.getItem('currentPage') || 'recados.php';
            const menuItem = document.getElementById(currentPage.split('.')[0]);
            
            if (menuItem) {
                loadPage(currentPage, menuItem);
            } else {
                loadPage('recados.php', document.getElementById('recados'));
            }

            // Define margem inicial do conteúdo
            const content = document.getElementById('content');
            content.style.marginLeft = '60px';
        });

        // Delegação de eventos para conteúdo dinâmico
        document.addEventListener('click', function(e) {
            const target = e.target.closest('[data-action]');
            if (target) {
                const action = target.getAttribute('data-action');
                if (typeof window[action] === 'function') {
                    window[action](e);
                }
            }
        });
    </script>
</body>
</html>