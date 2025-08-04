<?php
/**
 * Plataforma Editorial Plash
 * Página inicial - Redireciona para login apropriado
 */

// Verificar se o sistema está instalado
if (!file_exists('.env.php')) {
    header('Location: install/');
    exit;
}

// Incluir configurações
require_once '.env.php';
require_once 'includes/auth.php';

// Se já estiver logado, redirecionar para o painel apropriado
if (isLoggedIn()) {
    $user = getCurrentUser();
    switch ($user['role']) {
        case 'admin':
            header('Location: admin/');
            break;
        case 'athlete':
            header('Location: athlete/');
            break;
        case 'collaborator':
            header('Location: collaborator/');
            break;
        case 'partner':
            header('Location: partner/');
            break;
        default:
            header('Location: login.php');
    }
    exit;
}

// Redirecionar para login
header('Location: login.php');
exit;
?>