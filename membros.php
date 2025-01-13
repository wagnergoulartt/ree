<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Membros</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

    .button-group {
        display: flex;
        justify-content: space-between;
    }

    .whatsapp-button,
    .apagar-button,
    .editar-button,
    .reset-button {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }

    .whatsapp-button {
        background-color: #25D366;
        color: #fff;
    }

    .whatsapp-button:hover {
        background-color: #128C7E;
    }

    .apagar-button {
        background-color: #FF0000;
        color: #fff;
    }

    .apagar-button:hover {
        background-color: #CC0000;
    }

    .editar-button {
        background-color: #FFD700;
        color: #000;
    }

    .editar-button:hover {
        background-color: #FFC300;
    }

    .reset-button {
        background-color: #4169E1;
        color: #fff;
    }

    .reset-button:hover {
        background-color: #0000CD;
    }

    .whatsapp-button i,
    .apagar-button i,
    .editar-button i,
    .reset-button i {
        margin-right: 8px;
    }
</style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">LISTA DE MEMBROS</h2>

        <?php
        include_once 'conexao.php';

        if (!$conn) {
            die("Erro ao conectar ao Banco de Dados: " . mysqli_connect_error());
        }

        $sql = "SELECT id, nome, whatsapp, login, senha FROM membros ORDER BY nome ASC";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='mensagem'>";
                echo "<p><strong>Nome:</strong> <span id='nome_" . $row["id"] . "'>" . htmlspecialchars($row["nome"]) . "</span></p>";
                echo "<p><strong>WhatsApp:</strong> <span id='whatsapp_" . $row["id"] . "'>" . htmlspecialchars($row["whatsapp"]) . "</span></p>";
                echo "<p><strong>Login:</strong> <span id='login_" . $row["id"] . "'>" . htmlspecialchars($row["login"]) . "</span></p>";
                if ($row["senha"] == '123') {
                    echo "<p style='color: red; margin-top: -10px; margin-bottom: 10px;'><b>Solicitar a alteração da senha (URGENTE)</b></p>";
                }
                echo "<div class='button-group'>";
                echo "<button class='apagar-button' onclick='apagarMembro(" . $row["id"] . ");'><i class='fas fa-trash'></i> Apagar</button>";
                echo "<button class='editar-button' onclick='editarMembro(" . $row["id"] . ");'><i class='fas fa-edit'></i> Editar</button>";
                echo "<button class='reset-button' onclick='redefinirSenha(" . $row["id"] . ");'><i class='fas fa-key'></i> Redefinir</button>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<center><p>Nenhum membro encontrado.</p></center>";
        }

        mysqli_close($conn);
        ?>

        <script>
            function apagarMembro(id) {
                if (confirm("Deseja apagar este membro?")) {
                    var xhr = new XMLHttpRequest();
                    
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === XMLHttpRequest.DONE) {
                            if (xhr.status === 200) {
                                window.location.reload();
                            } else {
                                alert("Ocorreu um erro ao excluir o membro. Por favor, tente novamente mais tarde.");
                            }
                        }
                    };
                    
                    xhr.open("POST", "excluir_membro.php", true);
                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xhr.send("membro_id=" + id);
                }
            }

            function editarMembro(id) {
                var nome = document.getElementById('nome_' + id).innerText;
                var whatsapp = document.getElementById('whatsapp_' + id).innerText;
                var login = document.getElementById('login_' + id).innerText;

                var novoNome = prompt("Digite o novo nome:", nome);
                var novoWhatsapp = prompt("Digite o novo WhatsApp:", whatsapp);
                var novoLogin = prompt("Digite o novo login:", login);
                var novaSenha = prompt("Digite a nova senha (deixe em branco para manter a atual):");

                if (novoNome !== null && novoWhatsapp !== null && novoLogin !== null) {
                    var xhr = new XMLHttpRequest();
                    
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === XMLHttpRequest.DONE) {
                            if (xhr.status === 200) {
                                document.getElementById('nome_' + id).innerText = novoNome;
                                document.getElementById('whatsapp_' + id).innerText = novoWhatsapp;
                                document.getElementById('login_' + id).innerText = novoLogin;
                                alert("Dados atualizados com sucesso!");
                                window.location.reload();
                            } else {
                                alert("Ocorreu um erro ao editar o membro. Por favor, tente novamente mais tarde.");
                            }
                        }
                    };
                    
                    xhr.open("POST", "editar_membro.php", true);
                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xhr.send(
                        "membro_id=" + encodeURIComponent(id) + 
                        "&nome=" + encodeURIComponent(novoNome) + 
                        "&whatsapp=" + encodeURIComponent(novoWhatsapp) + 
                        "&login=" + encodeURIComponent(novoLogin) + 
                        "&senha=" + encodeURIComponent(novaSenha)
                    );
                }
            }

            function redefinirSenha(id) {
                if (confirm("Deseja redefinir a senha deste membro para '123'?")) {
                    var xhr = new XMLHttpRequest();
                    
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === XMLHttpRequest.DONE) {
                            if (xhr.status === 200) {
                                var whatsapp = document.getElementById('whatsapp_' + id).innerText;
                                var nome = document.getElementById('nome_' + id).innerText;
                                var message = "*Olá " + nome + "*\nSua senha foi redefinida para: *123*\n*Acesse o painel é altere a sua senha!*";
                                window.open(`https://api.whatsapp.com/send?phone=${whatsapp}&text=${encodeURIComponent(message)}`, '_blank');
                                window.location.reload();
                            } else {
                                alert("Ocorreu um erro ao redefinir a senha. Por favor, tente novamente mais tarde.");
                            }
                        }
                    };
                    
                    xhr.open("POST", "redefinir_senha.php", true);
                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xhr.send("membro_id=" + id + "&nova_senha=123");
                }
            }
        </script>
    </div>
</body>
</html>