<script>
    const BASE_URL = '<?php echo rtrim(dirname($_SERVER['PHP_SELF']), '/\\'); ?>';
</script>

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
            max-width: 800px;
        }

        .content.sidebar-collapsed {
            margin-left: 60px;
        }

        #nome {
            width: 100%;
            margin-bottom: 16px;
            border-radius: 4px;
            font-size: 16px;
            padding: 16px;
            border: 2px solid #808080;
            max-width: 100%;
        }

        textarea {
            width: 100%;
            margin-bottom: 16px;
            border-radius: 4px;
            padding: 16px;
            border: 2px solid #808080;
            font-size: 16px;
            resize: vertical;
            max-width: 100%;
            min-height: 100px;
            max-height: 300px;
        }

        .whatsapp {
            width: 100%;
            padding: 12px;
            margin-bottom: 10px;
            background-color: #25d366;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            max-width: 100%;
        }

        .whatsapp:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        .whatsapp img {
            width: 24px;
            margin-right: 8px;
        }

        h1 {
            margin-bottom: 20px;
        }

        p {
            margin-bottom: 20px;
            line-height: 1.6;
        }

        form {
            max-width: 100%;
        }
        
        #content form {
    height: auto;
    min-height: auto;
}
    </style>
</head>
<body>
    <div class="sidebar collapsed" id="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
<div class="menu">
    <a href="#" onclick="loadContent('index.php'); return false;">
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
        <!-- Conteúdo inicial -->
        <h1>RECADO ANÔNIMO</h1>
        <p>
            <b>ENVIADO RECADO!</b> No campo <b>SELECIONAR MEMBRO</b> você vai selecionar o nome da pessoa na lista que receberá o recado, no campo <b>RECADO</b> você colocará a mensagem que a pessoa vai receber; após basta apertar um dos botões que um dos <b>ADM</b> irá destinar o recado.
        </p>

        <form method="post" action="salvar_mensagem.php">
            <select name="nome" id="nome" required>
                <option value="" disabled selected>Selecione um membro</option>
                <?php
                include_once 'conexao.php';
                $sql = "SELECT nome, whatsapp FROM membros ORDER BY nome ASC";
                $result = mysqli_query($conn, $sql);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='{$row['nome']}' data-nome='{$row['nome']}'>{$row['nome']}</option>";
                }
                mysqli_close($conn);
                ?>
            </select>

            <textarea name="msg" id="msg" placeholder="RECADO:" rows="5" required></textarea>

            <button type="submit" name="botao" value="Goulart" class="whatsapp" onclick="return enviarWhatsapp('5551984112140', 'Goulart')" disabled>
                <img src="/recados/assets/whatsapp.svg" alt="Ícone do WhatsApp">ENVIAR PARA O GOULART
            </button>

            <button type="submit" name="botao" value="Lisi" class="whatsapp" onclick="return enviarWhatsapp('5551981433345', 'Lisi')" disabled>
                <img src="/recados/assets/whatsapp.svg" alt="Ícone do WhatsApp">ENVIAR PARA A LISI
            </button>

            <button type="submit" name="botao" value="Mateus" class="whatsapp" onclick="return enviarWhatsapp('555183220100', 'Mateus')" disabled>
                <img src="/recados/assets/whatsapp.svg" alt="Ícone do WhatsApp">ENVIAR PARA A MATEUS
            </button>

            <input type="hidden" id="whatsappHidden" name="whatsappHidden">
            <input type="hidden" id="idGrupoHidden" name="idGrupoHidden" value="120363040997517301@g.us">
        </form>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('sidebar-collapsed');
        }

   function loadContent(page) {
    if (page === 'index.php') {
        window.location.href = 'index.php';
    } else if (page === 'queridometro/reagir.php') {
        fetch(page)
            .then(response => response.text())
            .then(data => {
                document.getElementById('content').innerHTML = data;
                initializeQueridometro(); // Inicializa os eventos do queridômetro
            })
            .catch(error => console.error('Erro:', error));
    } else {
        fetch(page)
            .then(response => response.text())
            .then(data => {
                document.getElementById('content').innerHTML = data;
            })
            .catch(error => console.error('Erro:', error));
    }
}

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
                    warningMessage.textContent = data.message;
                    warningMessage.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao processar reações');
            });
        });
    }
}

        function validarCampos() {
            var nome = document.getElementById("nome").value;
            var msg = document.getElementById("msg").value;
            var buttons = document.getElementsByClassName("whatsapp");
            
            for (var i = 0; i < buttons.length; i++) {
                buttons[i].disabled = (nome === '' || msg === '');
            }
        }

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

        var nomeSelect = document.getElementById("nome");
        var msgInput = document.getElementById("msg");
        nomeSelect.addEventListener("change", validarCampos);
        msgInput.addEventListener("input", validarCampos);
    </script>
</body>
</html>