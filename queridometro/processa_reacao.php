<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Verificação de login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

// Verificação dos dados POST
if (!isset($_POST['emoji'])) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
    exit;
}

// Conexão com o banco
$conn = new mysqli('localhost', 'u529068110_recadosnovo', '@Erick91492832', 'u529068110_recadosnovo');
mysqli_set_charset($conn, "utf8mb4");

try {
    $id_usuario = $_SESSION['id'];
    $emojis = $_POST['emoji'];

    // Verifica se já reagiu hoje
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS count 
        FROM reacoes 
        WHERE id_usuario = ? AND DATE(data_reacao) = CURDATE()
    ");
    
    if (!$stmt) {
        throw new Exception("Erro na preparação da consulta: " . $conn->error);
    }

    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $ja_reagiu = $result->fetch_assoc()['count'] > 0;
    $stmt->close();

    if (!$ja_reagiu) {
        // Inicia transação
        $conn->begin_transaction();

        $stmt = $conn->prepare("
            INSERT INTO reacoes (id_usuario, id_receptor, id_emoji, data_reacao) 
            VALUES (?, ?, ?, NOW())
        ");
        
        if (!$stmt) {
            throw new Exception("Erro na preparação do insert: " . $conn->error);
        }

        foreach ($emojis as $id_receptor => $id_emoji) {
            $stmt->bind_param("iii", $id_usuario, $id_receptor, $id_emoji);
            
            if (!$stmt->execute()) {
                throw new Exception("Erro ao executar insert: " . $stmt->error);
            }
        }

        $conn->commit();
        $stmt->close();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Reações registradas com sucesso'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Você já reagiu hoje'
        ]);
    }

} catch (Exception $e) {
    if ($conn->connect_error) {
        $error = "Erro de conexão: " . $conn->connect_error;
    } else {
        $error = $e->getMessage();
    }
    
    if (isset($conn) && $conn->connect_errno === 0) {
        $conn->rollback();
    }
    
    error_log("Erro no queridômetro: " . $error);
    echo json_encode([
        'success' => false, 
        'message' => 'Erro ao processar reação: ' . $error
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>