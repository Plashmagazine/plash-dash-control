<?php
/**
 * Plataforma Editorial Plash
 * Página de Erro 500 - Erro Interno
 */

http_response_code(500);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro Interno - Plash Magazine</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center">
        <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Erro Interno do Servidor</h1>
        <p class="text-gray-600 mb-8 max-w-md">
            Ocorreu um erro interno no servidor. Tente novamente em alguns minutos.
        </p>
        
        <div class="space-x-4">
            <a href="/" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Voltar ao Início
            </a>
            <button onclick="window.location.reload()" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                Tentar Novamente
            </button>
        </div>
    </div>
</body>
</html>