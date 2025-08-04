<?php
/**
 * Plataforma Editorial Plash
 * Painel do Atleta
 */

require_once '../includes/auth.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Verificar se está logado e é atleta
requireRole('athlete');

$user = getCurrentUser();

// Buscar capas do atleta
$covers = fetchAll(
    "SELECT c.*, e.name as edition_name, e.number as edition_number 
     FROM capas c 
     JOIN edicoes e ON c.edition_id = e.id 
     WHERE c.athlete_id = ? 
     ORDER BY c.created_at DESC",
    [$user['id']]
);

// Buscar uploads do atleta
$uploads = fetchAll(
    "SELECT * FROM uploads 
     WHERE user_id = ? 
     ORDER BY created_at DESC 
     LIMIT 10",
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
    <title>Painel Atleta - Plash Magazine</title>
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
                    <h1 class="ml-3 text-xl font-semibold text-gray-900">Plash Atleta</h1>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Notificações -->
                    <div class="relative">
                        <button class="p-2 text-gray-400 hover:text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"></path>
                            </svg>
                        </button>
                        <?php if (count($notifications) > 0): ?>
                            <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400"></span>
                        <?php endif; ?>
                    </div>
                    
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
        <div class="bg-gradient-to-r from-plash-blue to-blue-600 rounded-lg shadow-lg p-6 mb-8 text-white">
            <h2 class="text-2xl font-bold mb-2">Bem-vindo, <?php echo e($user['name']); ?>!</h2>
            <p class="text-blue-100">Gerencie suas participações nas edições da Plash Magazine</p>
        </div>

        <!-- Minhas Capas -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Minhas Participações</h2>
                
                <?php if (empty($covers)): ?>
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z"></path>
                        </svg>
                        <p class="text-gray-500">Você ainda não foi selecionado para nenhuma edição</p>
                    </div>
                <?php else: ?>
                    <div class="grid gap-6">
                        <?php foreach ($covers as $cover): ?>
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            <?php echo e($cover['edition_name']); ?> - Edição #<?php echo $cover['edition_number']; ?>
                                        </h3>
                                        <p class="text-sm text-gray-600">Tipo: <?php echo e($cover['tipo']); ?></p>
                                    </div>
                                    <div>
                                        <?php echo getStatusHtml($cover['status']); ?>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <a href="upload.php?cover_id=<?php echo $cover['id']; ?>" 
                                       class="flex items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-plash-blue hover:bg-blue-50">
                                        <div class="text-center">
                                            <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                            </svg>
                                            <p class="text-sm font-medium text-gray-900">Fazer Upload</p>
                                            <p class="text-xs text-gray-500">Vídeos e fotos</p>
                                        </div>
                                    </a>
                                    
                                    <a href="interview.php?cover_id=<?php echo $cover['id']; ?>" 
                                       class="flex items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-plash-blue hover:bg-blue-50">
                                        <div class="text-center">
                                            <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p class="text-sm font-medium text-gray-900">Entrevista</p>
                                            <p class="text-xs text-gray-500">Responder perguntas</p>
                                        </div>
                                    </a>
                                    
                                    <a href="contract.php?cover_id=<?php echo $cover['id']; ?>" 
                                       class="flex items-center justify-center p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-plash-blue hover:bg-blue-50">
                                        <div class="text-center">
                                            <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <p class="text-sm font-medium text-gray-900">Contrato</p>
                                            <p class="text-xs text-gray-500">Assinar digitalmente</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Meus Uploads Recentes -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Uploads Recentes</h2>
                
                <?php if (empty($uploads)): ?>
                    <p class="text-gray-500 text-center py-4">Nenhum upload realizado ainda</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($uploads as $upload): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <?php if ($upload['tipo'] === 'video'): ?>
                                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                        <?php else: ?>
                                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900"><?php echo e($upload['original_name']); ?></p>
                                        <p class="text-xs text-gray-500">
                                            <?php echo formatFileSize($upload['file_size']); ?> • 
                                            <?php echo formatDate($upload['created_at']); ?>
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <?php echo getStatusHtml($upload['status']); ?>
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