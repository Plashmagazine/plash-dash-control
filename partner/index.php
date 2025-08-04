<?php
/**
 * Plataforma Editorial Plash
 * Painel da Editora Parceira
 */

require_once '../includes/auth.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Verificar se está logado e é parceiro
requireRole('partner');

$user = getCurrentUser();

// Buscar produtos digitais
$digitalProducts = fetchAll(
    "SELECT pd.*, e.name as edition_name 
     FROM produtos_digitais pd
     JOIN edicoes e ON pd.edition_id = e.id
     WHERE pd.partner_id = ?
     ORDER BY pd.created_at DESC
     LIMIT 5",
    [$user['id']]
);

// Buscar produtos impressos
$printProducts = fetchAll(
    "SELECT pi.*, e.name as edition_name 
     FROM produtos_impressos pi
     JOIN edicoes e ON pi.edition_id = e.id
     WHERE pi.partner_id = ?
     ORDER BY pi.created_at DESC
     LIMIT 5",
    [$user['id']]
);

// Estatísticas
$stats = [
    'total_digital' => countRecords('produtos_digitais', 'partner_id = ?', [$user['id']]),
    'total_print' => countRecords('produtos_impressos', 'partner_id = ?', [$user['id']]),
    'pending_products' => countRecords('produtos_digitais', 'partner_id = ? AND status = ?', [$user['id'], 'aguardando_envio']) + 
                         countRecords('produtos_impressos', 'partner_id = ? AND status = ?', [$user['id'], 'aguardando_envio'])
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Editora - Plash Magazine</title>
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
                    <h1 class="ml-3 text-xl font-semibold text-gray-900">Plash Editora</h1>
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
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 mb-8 text-white">
            <h2 class="text-2xl font-bold mb-2">Bem-vindo, <?php echo e($user['name']); ?>!</h2>
            <p class="text-purple-100">Gerencie seus produtos e vendas</p>
        </div>

        <!-- Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Produtos Digitais</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo $stats['total_digital']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Produtos Impressos</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo $stats['total_print']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pendentes</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo $stats['pending_products']; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <a href="digital-product.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Produto Digital</h3>
                        <p class="text-sm text-gray-600">Enviar revista digital (PDF/ZIP)</p>
                    </div>
                </div>
            </a>

            <a href="print-product.php" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Produto Impresso</h3>
                        <p class="text-sm text-gray-600">Cadastrar dados do impresso</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Produtos Digitais Recentes -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Produtos Digitais Recentes</h2>
                    <a href="digital-products.php" class="text-sm text-plash-blue hover:text-blue-800">Ver todos</a>
                </div>
                
                <?php if (empty($digitalProducts)): ?>
                    <p class="text-gray-500 text-center py-4">Nenhum produto digital cadastrado</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($digitalProducts as $product): ?>
                            <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900"><?php echo e($product['edition_name']); ?></p>
                                    <p class="text-sm text-gray-600">
                                        <?php echo e($product['category']); ?> • 
                                        <?php echo e($product['quality']); ?> • 
                                        <?php echo formatFileSize($product['file_size']); ?>
                                    </p>
                                    <p class="text-xs text-gray-500"><?php echo formatDate($product['created_at']); ?></p>
                                </div>
                                <div>
                                    <?php echo getStatusHtml($product['status']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Produtos Impressos Recentes -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Produtos Impressos Recentes</h2>
                    <a href="print-products.php" class="text-sm text-plash-blue hover:text-blue-800">Ver todos</a>
                </div>
                
                <?php if (empty($printProducts)): ?>
                    <p class="text-gray-500 text-center py-4">Nenhum produto impresso cadastrado</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($printProducts as $product): ?>
                            <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900"><?php echo e($product['edition_name']); ?></p>
                                    <p class="text-sm text-gray-600">
                                        <?php echo e($product['size']); ?> • 
                                        <?php echo $product['pages']; ?> páginas • 
                                        <?php echo $product['weight']; ?>g
                                    </p>
                                    <p class="text-xs text-gray-500"><?php echo formatDate($product['created_at']); ?></p>
                                </div>
                                <div>
                                    <?php echo getStatusHtml($product['status']); ?>
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