<?php
/**
 * Plataforma Editorial Plash
 * Página de Erro 404
 */

http_response_code(404);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página não encontrada - Plash Magazine</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center">
        <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <span class="text-4xl font-bold text-red-600">404</span>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Página não encontrada</h1>
        <p class="text-gray-600 mb-8 max-w-md">
            A página que você está procurando não existe ou foi movida.
        </p>
        
        <div class="space-x-4">
            <a href="/" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Voltar ao Início
            </a>
            <a href="/login.php" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                Fazer Login
            </a>
        </div>
    </div>
</body>
</html>