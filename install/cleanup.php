<?php
/**
 * Plataforma Editorial Plash
 * Limpeza pós-instalação
 */

// Verificar se a instalação foi concluída
if (!file_exists('../.env.php')) {
    http_response_code(403);
    exit('Instalação não concluída');
}

// Função para remover diretório recursivamente
function removeDirectory($dir) {
    if (!is_dir($dir)) {
        return false;
    }
    
    $files = array_diff(scandir($dir), ['.', '..']);
    
    foreach ($files as $file) {
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($path)) {
            removeDirectory($path);
        } else {
            unlink($path);
        }
    }
    
    return rmdir($dir);
}

// Remover pasta de instalação
$installDir = __DIR__;
$removed = removeDirectory($installDir);

if ($removed) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Pasta de instalação removida com sucesso']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao remover pasta de instalação']);
}
?>