<?php
/**
 * Plataforma Editorial Plash
 * Painel do Colaborador
 */

require_once '../includes/auth.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Verificar se está logado e é colaborador
requireRole('collaborator');

$user = getCurrentUser();

// Buscar capas do colaborador
$covers = fetchAll(
    "SELECT c.*, e.name as edition_name, e.number as edition_number, u.name as athlete_name
     FROM capas c 
     JOIN edicoes e ON c.edition_id = e.id 
     JOIN users u ON c.athlete_id = u.id
     WHERE c.collaborator_id = ? 
     ORDER BY c.created_at DESC",
    [$user['id']]
);

// Buscar comissões
$commissions = fetchAll(
    "SELECT co.*, e.name as edition_name 
     FROM comissoes co
     JOIN edicoes e ON co.edition_id = e.id
     WHERE co.collaborator_id = ?
     ORDER BY co.created_at DESC
     LIMIT 5",
    [$user['id']]
);

// Notificações não lidas
$notifications = getUnreadNotifications($user['id'], 5);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Colaborador - Plash Magazine</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'plash-blue': '#1e40af',
                        'plash-yellow': '#fbbf24',
                        'plash-gray': '#374151'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-plash-blue rounded-full flex items-center justify-center">
                        <span class="text-white font-bold text-sm">P</span>
                    </div>
                    <h1 class="ml-3 text-xl font-semibold text-gray-900">Plash Colaborador</h1>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Menu do usuário -->
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-700"><?php echo e($user['name']); ?></span>
                        <a href="../logout.php" class="text-sm text-red-600 hover:text-red-800">Sair</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Bem-vindo -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-6 mb-8 text-white">
            <h2 class="text-2xl font-bold mb-2">Bem-vindo, <?php echo e($user['name']); ?>!</h2>
            <p class="text-green-100">Gerencie seus projetos e colaborações</p>
        </div>

        <!-- Menu de Ações -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <a href="indicate.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Indicar Talento</h3>
                        <p class="text-sm text-gray-600">Indique novos atletas</p>
                    </div>
                </div>
            </a>

            <a href="uploads.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Meus Uploads</h3>
                        <p class="text-sm text-gray-600">Gerenciar arquivos</p>
                    </div>
                </div>
            </a>

            <a href="commissions.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Comissões</h3>
                        <p class="text-sm text-gray-600">Ver ganhos</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Meus Projetos -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Meus Projetos</h2>
                
                <?php if (empty($covers)): ?>
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <p class="text-gray-500">Você ainda não foi designado para nenhum projeto</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($covers as $cover): ?>
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-semibold text-gray-900">
                                            <?php echo e($cover['edition_name']); ?> - Edição #<?php echo $cover['edition_number']; ?>
                                        </h3>
                                        <p class="text-sm text-gray-600">
                                            Atleta: <?php echo e($cover['athlete_name']); ?> • 
                                            Tipo: <?php echo e($cover['tipo']); ?>
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Criado em <?php echo formatDate($cover['created_at']); ?>
                                        </p>
                                    </div>
                                    <div>
                                        <?php echo getStatusHtml($cover['status']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Comissões Recentes -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Comissões Recentes</h2>
                
                <?php if (empty($commissions)): ?>
                    <p class="text-gray-500 text-center py-4">Nenhuma comissão registrada ainda</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($commissions as $commission): ?>
                            <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900"><?php echo e($commission['edition_name']); ?></p>
                                    <p class="text-sm text-gray-600">
                                        <?php echo $commission['percentage']; ?>% • 
                                        <?php echo formatDate($commission['created_at']); ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-green-600">R$ <?php echo number_format($commission['amount'], 2, ',', '.'); ?></p>
                                    <?php echo getStatusHtml($commission['status']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>