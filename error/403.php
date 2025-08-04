<?php
/**
 * Plataforma Editorial Plash
 * Página de Erro 403 - Acesso Negado
 */

http_response_code(403);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Negado - Plash Magazine</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center">
        <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m4-6V9a4 4 0 00-8 0v2m0 0V9a6 6 0 1112 0v2m-6 0h.01M12 12h.01"></path>
            </svg>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Acesso Negado</h1>
        <p class="text-gray-600 mb-8 max-w-md">
            Você não tem permissão para acessar esta área da plataforma.
        </p>
        
        <div class="space-x-4">
            <a href="/" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Voltar ao Início
            </a>
            <a href="/logout.php" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                Fazer Logout
            </a>
        </div>
    </div>
</body>
</html>