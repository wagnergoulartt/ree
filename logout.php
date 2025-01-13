<?php
session_start();
session_unset(); // Limpar todas as variáveis de sessão
session_destroy(); // Destruir a sessão

header("Location: login.php"); // Redirecionar para a página de login após o logout
exit;
?>
