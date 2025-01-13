<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    die("Você precisa estar logado para enviar mensagens.");
}

include_once 'conexao.php';

// Verificar se a conexão foi estabelecida com sucesso
if (!$conn) {
    die("Erro ao conectar ao banco de dados: " . mysqli_connect_error());
}

// Definir a codificação correta para suportar emojis
mysqli_set_charset($conn, "utf8mb4");

// Obter os dados do formulário
$nome = $_POST['nome'];
$mensagem = $_POST["msg"];
$botao = $_POST["botao"];
$idGrupo = $_POST['idGrupoHidden'];

// Buscar o nome do remetente baseado no login da sessão
$username = $_SESSION['username'];
$sql_remetente = "SELECT nome FROM membros WHERE login = '$username'";
$result_remetente = mysqli_query($conn, $sql_remetente);

if ($result_remetente && mysqli_num_rows($result_remetente) > 0) {
    $row_remetente = mysqli_fetch_assoc($result_remetente);
    $remetente = $row_remetente['nome'];
} else {
    $remetente = $username; // Caso não encontre, usa o username como fallback
}

// Capturar o endereço IP do usuário
$ip_envio = $_SERVER['REMOTE_ADDR'];

// Escapar caracteres especiais
$nome = mysqli_real_escape_string($conn, $nome);
$mensagem = mysqli_real_escape_string($conn, $mensagem);
$botao = mysqli_real_escape_string($conn, $botao);
$idGrupo = mysqli_real_escape_string($conn, $idGrupo);
$remetente = mysqli_real_escape_string($conn, $remetente);

// Buscar o número de WhatsApp da tabela membros com base no nome selecionado
$sql = "SELECT whatsapp FROM membros WHERE nome = '$nome'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $whatsapp = $row['whatsapp'];

    // Escapar o número de WhatsApp
    $whatsapp = mysqli_real_escape_string($conn, $whatsapp);

    // Inserir a mensagem no banco de dados junto com o endereço IP, ID do grupo e remetente
    $sql_insert = "INSERT INTO mensagens (nome, whats, mensagem, botao, id_grupo, ip_envio, remetente) 
                   VALUES ('$nome', '$whatsapp', '$mensagem', '$botao', '$idGrupo', '$ip_envio', '$remetente')";
    
    if (mysqli_query($conn, $sql_insert)) {
        // Redirecionar de volta para o formulário
        header("Location: index.php");
        exit();
    } else {
        echo "Erro ao salvar mensagem: " . mysqli_error($conn);
    }
} else {
    echo "Erro: Membro não encontrado.";
}

// Fechar a conexão
mysqli_close($conn);
?>