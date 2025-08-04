<?php
/**
 * Plataforma Editorial Plash
 * Logout do sistema
 */

require_once '.env.php';
require_once 'includes/auth.php';

// Log da atividade antes de destruir a sessão
if (isLoggedIn()) {
    $user = getCurrentUser();
    logActivity($user['id'], 'logout', 'user', $user['id'], 'Logout realizado');
}

// Destruir sessão
destroyUserSession();

// Redirecionar para login
header('Location: login.php?msg=logout');
exit;
?>