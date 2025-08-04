<?php
/**
 * Plataforma Editorial Plash
 * Conexão e funções do banco de dados
 */

// Conexão global com o banco
$db = null;

/**
 * Conectar ao banco de dados
 */
function connectDatabase() {
    global $db;
    
    if ($db !== null) {
        return $db;
    }
    
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $db = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $db;
    } catch (PDOException $e) {
        error_log("Erro de conexão com banco: " . $e->getMessage());
        die("Erro de conexão com o banco de dados. Verifique as configurações.");
    }
}

/**
 * Executar query preparada
 */
function executeQuery($sql, $params = []) {
    $db = connectDatabase();
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Erro na query: " . $e->getMessage() . " | SQL: " . $sql);
        throw $e;
    }
}

/**
 * Buscar um registro
 */
function fetchOne($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetch();
}

/**
 * Buscar múltiplos registros
 */
function fetchAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetchAll();
}

/**
 * Inserir registro e retornar ID
 */
function insertRecord($sql, $params = []) {
    $db = connectDatabase();
    $stmt = executeQuery($sql, $params);
    return $db->lastInsertId();
}

/**
 * Contar registros
 */
function countRecords($table, $where = '', $params = []) {
    $sql = "SELECT COUNT(*) as total FROM {$table}";
    if ($where) {
        $sql .= " WHERE {$where}";
    }
    $result = fetchOne($sql, $params);
    return (int) $result['total'];
}

/**
 * Verificar se tabela existe
 */
function tableExists($tableName) {
    $sql = "SHOW TABLES LIKE ?";
    $result = fetchOne($sql, [$tableName]);
    return $result !== false;
}

/**
 * Executar múltiplas queries (para instalação)
 */
function executeMultipleQueries($sqlContent) {
    $db = connectDatabase();
    
    // Dividir por ponto e vírgula, ignorando comentários
    $queries = array_filter(
        array_map('trim', explode(';', $sqlContent)),
        function($query) {
            return !empty($query) && !str_starts_with($query, '--') && !str_starts_with($query, '#');
        }
    );
    
    foreach ($queries as $query) {
        if (!empty($query)) {
            try {
                $db->exec($query);
            } catch (PDOException $e) {
                error_log("Erro ao executar query: " . $e->getMessage() . " | Query: " . $query);
                throw $e;
            }
        }
    }
    
    return true;
}

/**
 * Sanitizar string para uso em queries
 */
function sanitizeString($string) {
    return htmlspecialchars(strip_tags(trim($string)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validar email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Gerar hash seguro para senhas
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verificar senha
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Gerar token aleatório
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Log de atividades do sistema
 */
function logActivity($userId, $action, $entityType, $entityId, $details = '') {
    $sql = "INSERT INTO logs_atividade (user_id, action, entity_type, entity_id, details, ip_address, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
    
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    try {
        executeQuery($sql, [$userId, $action, $entityType, $entityId, $details, $ipAddress]);
    } catch (Exception $e) {
        error_log("Erro ao registrar log de atividade: " . $e->getMessage());
    }
}

/**
 * Criar notificação para usuário
 */
function createNotification($userId, $title, $message, $type = 'info') {
    $sql = "INSERT INTO notificacoes (user_id, title, message, type, created_at) 
            VALUES (?, ?, ?, ?, NOW())";
    
    try {
        return insertRecord($sql, [$userId, $title, $message, $type]);
    } catch (Exception $e) {
        error_log("Erro ao criar notificação: " . $e->getMessage());
        return false;
    }
}

/**
 * Buscar notificações não lidas do usuário
 */
function getUnreadNotifications($userId, $limit = 10) {
    $sql = "SELECT * FROM notificacoes 
            WHERE user_id = ? AND read_at IS NULL 
            ORDER BY created_at DESC 
            LIMIT ?";
    
    return fetchAll($sql, [$userId, $limit]);
}

/**
 * Marcar notificação como lida
 */
function markNotificationAsRead($notificationId, $userId) {
    $sql = "UPDATE notificacoes 
            SET read_at = NOW() 
            WHERE id = ? AND user_id = ?";
    
    executeQuery($sql, [$notificationId, $userId]);
}
?>