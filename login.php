<?php
session_start();

// Configurações do banco de dados
$dbHost = "localhost";
$dbUser = "u529068110_recadosnovo";
$dbPass = "@Erick91492832";
$dbName = "u529068110_recadosnovo";

// Conectando ao banco de dados
$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);

// Verificando a conexão
if (!$conn) {
    die("Erro de conexão: " . mysqli_connect_error());
}

// Inicializando a mensagem de erro
$errorMessage = "";

// Capturando dados do formulário
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Primeiro verifica se é admin
    $query_admin = "SELECT * FROM login_admin WHERE login='$username' AND senha='$password'";
    $result_admin = mysqli_query($conn, $query_admin);

    // Depois verifica se é usuário normal
    $query_user = "SELECT id, login, senha FROM membros WHERE login='$username' AND senha='$password'";
    $result_user = mysqli_query($conn, $query_user);

    if (mysqli_num_rows($result_admin) == 1) {
        $admin = mysqli_fetch_assoc($result_admin);
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['usuario'] = $username;
        $_SESSION['tipo'] = 'admin';
        $_SESSION['id'] = $admin['id']; // Adicionando ID do admin
        header("Location: admin.php");
        exit;
    } 
    else if (mysqli_num_rows($result_user) == 1) {
        $user = mysqli_fetch_assoc($result_user);
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['usuario'] = $username;
        $_SESSION['tipo'] = 'usuario';
        $_SESSION['id'] = $user['id']; // Adicionando ID do usuário
        header("Location: index.php");
        exit;
    } 
    else {
        $errorMessage = "Senha ou login incorretos. Tente novamente.";
    }
}

// Fechando a conexão
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        input {
            width: 80%;
            margin: 10px;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            width: 85%;
            padding: 12px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #25d366;
            color: white;
            border: none;
            border-radius: 4px;
            margin-top: 10px;
        }
        button:hover {
            background-color: #128C7E;
        }
        .logo {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
            width: 250px;
        }
        @media (max-width: 768px) {
            .logo {
                width: 150px;
            }
        }
        @media (max-width: 480px) {
            .logo {
                width: 200px;
            }
            .container {
                margin: 20px;
            }
        }
        .error-message {
            color: #dc3545;
            margin: 10px 0;
            padding: 10px;
            background-color: #f8d7da;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="assets/logo.png" alt="Logo" class="logo">
        <?php if (!empty($errorMessage)): ?>
            <div class="error-message"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        <form action="login.php" method="post">
            <input type="text" name="username" placeholder="Usuário" required><br>
            <input type="password" name="password" placeholder="Senha" required><br>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>