<?php
session_start();
require_once 'conexao.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['tipo'] !== 'admin') {
    die(json_encode(['success' => false, 'message' => 'Acesso negado']));
}

$action = $_GET['action'] ?? '';

switch($action) {
    case 'get':
        $id = $_GET['id'] ?? '';
        
        if(empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID é obrigatório']);
            break;
        }
        
        $sql = "SELECT * FROM emojis WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $emoji = mysqli_fetch_assoc($result);
        
        if($emoji) {
            echo json_encode(['success' => true, 'emoji' => $emoji]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Emoji não encontrado']);
        }
        break;

    case 'add':
        $codigo = $_POST['codigo'] ?? '';
        $significado = $_POST['significado'] ?? '';
        
        if(empty($codigo) || empty($significado)) {
            echo json_encode(['success' => false, 'message' => 'Emoji e significado são obrigatórios']);
            break;
        }
        
        $sql = "INSERT INTO emojis (codigo, significado) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $codigo, $significado);
        
        $success = mysqli_stmt_execute($stmt);
        echo json_encode(['success' => $success]);
        break;
        
    case 'edit':
        $id = $_POST['id'] ?? '';
        $updateFields = [];
        $types = '';
        $params = [];
        
        if(empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID é obrigatório']);
            break;
        }
        
        if(isset($_POST['codigo'])) {
            $updateFields[] = "codigo = ?";
            $types .= "s";
            $params[] = $_POST['codigo'];
        }
        
        if(isset($_POST['significado'])) {
            $updateFields[] = "significado = ?";
            $types .= "s";
            $params[] = $_POST['significado'];
        }
        
        if(empty($updateFields)) {
            echo json_encode(['success' => false, 'message' => 'Nenhum campo para atualizar']);
            break;
        }
        
        $types .= "i";
        $params[] = $id;
        
        $sql = "UPDATE emojis SET " . implode(", ", $updateFields) . " WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        
        $bindParams = array($stmt, $types);
        foreach($params as $key => $value) {
            $bindParams[] = &$params[$key];
        }
        call_user_func_array('mysqli_stmt_bind_param', $bindParams);
        
        $success = mysqli_stmt_execute($stmt);
        echo json_encode(['success' => $success]);
        break;
        
    case 'delete':
        $id = $_POST['id'] ?? '';
        
        if(empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID é obrigatório']);
            break;
        }
        
        $sql = "DELETE FROM emojis WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        $success = mysqli_stmt_execute($stmt);
        echo json_encode(['success' => $success]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
}
?>