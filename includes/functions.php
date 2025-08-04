<?php
/**
 * Plataforma Editorial Plash
 * Funções auxiliares gerais
 */

/**
 * Formatar data para exibição
 */
function formatDate($date, $format = 'd/m/Y H:i') {
    if (empty($date)) return '-';
    
    try {
        $dateObj = new DateTime($date);
        return $dateObj->format($format);
    } catch (Exception $e) {
        return '-';
    }
}

/**
 * Formatar data relativa (há X tempo)
 */
function timeAgo($date) {
    if (empty($date)) return '-';
    
    try {
        $dateObj = new DateTime($date);
        $now = new DateTime();
        $diff = $now->diff($dateObj);
        
        if ($diff->days > 30) {
            return formatDate($date, 'd/m/Y');
        } elseif ($diff->days > 0) {
            return $diff->days . ' dia' . ($diff->days > 1 ? 's' : '') . ' atrás';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hora' . ($diff->h > 1 ? 's' : '') . ' atrás';
        } elseif ($diff->i > 0) {
            return $diff->i . ' minuto' . ($diff->i > 1 ? 's' : '') . ' atrás';
        } else {
            return 'Agora mesmo';
        }
    } catch (Exception $e) {
        return '-';
    }
}

/**
 * Formatar tamanho de arquivo
 */
function formatFileSize($bytes) {
    if ($bytes == 0) return '0 B';
    
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = floor(log($bytes, 1024));
    
    return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
}

/**
 * Gerar slug a partir de string
 */
function generateSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[áàâãä]/u', 'a', $string);
    $string = preg_replace('/[éèêë]/u', 'e', $string);
    $string = preg_replace('/[íìîï]/u', 'i', $string);
    $string = preg_replace('/[óòôõö]/u', 'o', $string);
    $string = preg_replace('/[úùûü]/u', 'u', $string);
    $string = preg_replace('/[ç]/u', 'c', $string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    return trim($string, '-');
}

/**
 * Validar upload de arquivo
 */
function validateUpload($file, $allowedTypes = [], $maxSize = 10485760) { // 10MB default
    $errors = [];
    
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        $errors[] = 'Nenhum arquivo foi enviado.';
        return $errors;
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errors[] = 'Arquivo muito grande.';
                break;
            case UPLOAD_ERR_PARTIAL:
                $errors[] = 'Upload incompleto.';
                break;
            case UPLOAD_ERR_NO_FILE:
                $errors[] = 'Nenhum arquivo enviado.';
                break;
            default:
                $errors[] = 'Erro no upload.';
        }
        return $errors;
    }
    
    // Verificar tamanho
    if ($file['size'] > $maxSize) {
        $errors[] = 'Arquivo muito grande. Máximo: ' . formatFileSize($maxSize);
    }
    
    // Verificar tipo
    if (!empty($allowedTypes)) {
        $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = 'Tipo de arquivo não permitido. Permitidos: ' . implode(', ', $allowedTypes);
        }
    }
    
    // Verificar se é realmente um arquivo
    if (!is_uploaded_file($file['tmp_name'])) {
        $errors[] = 'Arquivo inválido.';
    }
    
    return $errors;
}

/**
 * Fazer upload de arquivo
 */
function uploadFile($file, $destination, $newName = null) {
    if (!is_dir(dirname($destination))) {
        mkdir(dirname($destination), 0755, true);
    }
    
    $fileName = $newName ?: $file['name'];
    $filePath = $destination . '/' . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return $fileName;
    }
    
    return false;
}

/**
 * Redimensionar imagem
 */
function resizeImage($source, $destination, $maxWidth, $maxHeight, $quality = 85) {
    $imageInfo = getimagesize($source);
    if (!$imageInfo) return false;
    
    $originalWidth = $imageInfo[0];
    $originalHeight = $imageInfo[1];
    $imageType = $imageInfo[2];
    
    // Calcular novas dimensões
    $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
    $newWidth = round($originalWidth * $ratio);
    $newHeight = round($originalHeight * $ratio);
    
    // Criar imagem original
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $originalImage = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $originalImage = imagecreatefrompng($source);
            break;
        case IMAGETYPE_GIF:
            $originalImage = imagecreatefromgif($source);
            break;
        default:
            return false;
    }
    
    // Criar nova imagem
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preservar transparência para PNG e GIF
    if ($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_GIF) {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
    }
    
    // Redimensionar
    imagecopyresampled($newImage, $originalImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
    
    // Salvar
    $result = false;
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $result = imagejpeg($newImage, $destination, $quality);
            break;
        case IMAGETYPE_PNG:
            $result = imagepng($newImage, $destination);
            break;
        case IMAGETYPE_GIF:
            $result = imagegif($newImage, $destination);
            break;
    }
    
    // Limpar memória
    imagedestroy($originalImage);
    imagedestroy($newImage);
    
    return $result;
}

/**
 * Obter badge formatado
 */
function getBadgeHtml($badge) {
    $badges = [
        'verificado' => ['text' => 'Verificado', 'class' => 'bg-blue-100 text-blue-800'],
        'participante_problematico' => ['text' => 'Problemático', 'class' => 'bg-red-100 text-red-800'],
        'colaborador_atraso' => ['text' => 'Atraso', 'class' => 'bg-yellow-100 text-yellow-800'],
        'reputacao_risco' => ['text' => 'Risco', 'class' => 'bg-red-100 text-red-800'],
        'responsavel_proativo' => ['text' => 'Proativo', 'class' => 'bg-green-100 text-green-800'],
        'entrevista_pendente' => ['text' => 'Entrevista Pendente', 'class' => 'bg-orange-100 text-orange-800'],
        'entrevista_aprovada' => ['text' => 'Entrevista OK', 'class' => 'bg-green-100 text-green-800'],
        'compromisso_editorial' => ['text' => 'Compromisso Editorial', 'class' => 'bg-purple-100 text-purple-800'],
        'parceiro_ouro' => ['text' => 'Parceiro Ouro', 'class' => 'bg-yellow-100 text-yellow-800'],
        'primeira_edicao' => ['text' => 'Primeira Edição', 'class' => 'bg-indigo-100 text-indigo-800'],
    ];
    
    if (!isset($badges[$badge])) {
        return '';
    }
    
    $badgeInfo = $badges[$badge];
    return "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$badgeInfo['class']}\">{$badgeInfo['text']}</span>";
}

/**
 * Obter status formatado
 */
function getStatusHtml($status) {
    $statuses = [
        'ativo' => ['text' => 'Ativo', 'class' => 'bg-green-100 text-green-800'],
        'inativo' => ['text' => 'Inativo', 'class' => 'bg-gray-100 text-gray-800'],
        'pendente' => ['text' => 'Pendente', 'class' => 'bg-yellow-100 text-yellow-800'],
        'aprovado' => ['text' => 'Aprovado', 'class' => 'bg-green-100 text-green-800'],
        'reprovado' => ['text' => 'Reprovado', 'class' => 'bg-red-100 text-red-800'],
        'aguardando' => ['text' => 'Aguardando', 'class' => 'bg-blue-100 text-blue-800'],
        'criacao' => ['text' => 'Em Criação', 'class' => 'bg-gray-100 text-gray-800'],
        'aguardando_envio' => ['text' => 'Aguardando Envio', 'class' => 'bg-yellow-100 text-yellow-800'],
        'entregue' => ['text' => 'Entregue', 'class' => 'bg-blue-100 text-blue-800'],
        'lancado' => ['text' => 'Lançado', 'class' => 'bg-green-100 text-green-800'],
        'assinado' => ['text' => 'Assinado', 'class' => 'bg-green-100 text-green-800'],
        'pago' => ['text' => 'Pago', 'class' => 'bg-green-100 text-green-800'],
    ];
    
    if (!isset($statuses[$status])) {
        return "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800\">{$status}</span>";
    }
    
    $statusInfo = $statuses[$status];
    return "<span class=\"inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$statusInfo['class']}\">{$statusInfo['text']}</span>";
}

/**
 * Gerar breadcrumb
 */
function generateBreadcrumb($items) {
    if (empty($items)) return '';
    
    $html = '<nav class="flex mb-6" aria-label="Breadcrumb">';
    $html .= '<ol class="inline-flex items-center space-x-1 md:space-x-3">';
    
    foreach ($items as $index => $item) {
        $isLast = ($index === count($items) - 1);
        
        $html .= '<li class="inline-flex items-center">';
        
        if ($index > 0) {
            $html .= '<svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>';
        }
        
        if ($isLast) {
            $html .= '<span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">' . htmlspecialchars($item['title']) . '</span>';
        } else {
            $html .= '<a href="' . htmlspecialchars($item['url']) . '" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">' . htmlspecialchars($item['title']) . '</a>';
        }
        
        $html .= '</li>';
    }
    
    $html .= '</ol>';
    $html .= '</nav>';
    
    return $html;
}

/**
 * Paginar resultados
 */
function paginate($totalItems, $itemsPerPage, $currentPage, $baseUrl) {
    $totalPages = ceil($totalItems / $itemsPerPage);
    
    if ($totalPages <= 1) {
        return '';
    }
    
    $html = '<div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">';
    
    // Informações
    $start = ($currentPage - 1) * $itemsPerPage + 1;
    $end = min($currentPage * $itemsPerPage, $totalItems);
    
    $html .= '<div class="flex flex-1 justify-between sm:hidden">';
    
    // Botão anterior (mobile)
    if ($currentPage > 1) {
        $html .= '<a href="' . $baseUrl . '?page=' . ($currentPage - 1) . '" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Anterior</a>';
    }
    
    // Botão próximo (mobile)
    if ($currentPage < $totalPages) {
        $html .= '<a href="' . $baseUrl . '?page=' . ($currentPage + 1) . '" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Próximo</a>';
    }
    
    $html .= '</div>';
    
    // Desktop
    $html .= '<div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">';
    $html .= '<div><p class="text-sm text-gray-700">Mostrando <span class="font-medium">' . $start . '</span> a <span class="font-medium">' . $end . '</span> de <span class="font-medium">' . $totalItems . '</span> resultados</p></div>';
    
    $html .= '<div><nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">';
    
    // Botão anterior
    if ($currentPage > 1) {
        $html .= '<a href="' . $baseUrl . '?page=' . ($currentPage - 1) . '" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">Anterior</a>';
    }
    
    // Números das páginas
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);
    
    for ($i = $start; $i <= $end; $i++) {
        if ($i == $currentPage) {
            $html .= '<span class="relative z-10 inline-flex items-center bg-indigo-600 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">' . $i . '</span>';
        } else {
            $html .= '<a href="' . $baseUrl . '?page=' . $i . '" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">' . $i . '</a>';
        }
    }
    
    // Botão próximo
    if ($currentPage < $totalPages) {
        $html .= '<a href="' . $baseUrl . '?page=' . ($currentPage + 1) . '" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">Próximo</a>';
    }
    
    $html .= '</nav></div>';
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}

/**
 * Escapar output para HTML
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Verificar se string contém
 */
function contains($haystack, $needle) {
    return strpos($haystack, $needle) !== false;
}

/**
 * Truncar texto
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . $suffix;
}
?>