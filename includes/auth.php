<?php
/**
 * Plataforma Editorial Plash
 * Sistema de Autenticação e Autorização
 */

require_once 'database.php';

// Iniciar sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Autenticar usuário com email e senha
 */
function authenticateUser($email, $password) {
    $sql = "SELECT * FROM users WHERE email = ? AND status = 'ativo'";
    $user = fetchOne($sql, [$email]);
    
    if ($user && verifyPassword($password, $user['password'])) {
        // Atualizar último login
        $updateSql = "UPDATE users SET last_login = NOW() WHERE id = ?";
        executeQuery($updateSql, [$user['id']]);
        
        return $user;
    }
    
    return false;
}

/**
 * Iniciar sessão do usuário
 */
function startUserSession($user, $remember = false) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['login_time'] = time();
    
    // Cookie "lembrar de mim"
    if ($remember) {
        $token = generateToken();
        $expiry = time() + (30 * 24 * 60 * 60); // 30 dias
        
        // Salvar token no banco
        $sql = "INSERT INTO user_tokens (user_id, token, expires_at) VALUES (?, ?, FROM_UNIXTIME(?))
                ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at)";
        executeQuery($sql, [$user['id'], $token, $expiry]);
        
        // Definir cookie
        setcookie('remember_token', $token, $expiry, '/', '', false, true);
    }
}

/**
 * Verificar se usuário está logado
 */
function isLoggedIn() {
    // Verificar sessão
    if (isset($_SESSION['user_id']) && isset($_SESSION['login_time'])) {
        // Verificar timeout da sessão (2 horas)
        if (time() - $_SESSION['login_time'] < 7200) {
            return true;
        } else {
            destroyUserSession();
        }
    }
    
    // Verificar cookie "lembrar de mim"
    if (isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        $sql = "SELECT u.* FROM users u 
                JOIN user_tokens ut ON u.id = ut.user_id 
                WHERE ut.token = ? AND ut.expires_at > NOW() AND u.status = 'ativo'";
        
        $user = fetchOne($sql, [$token]);
        if ($user) {
            startUserSession($user, true);
            return true;
        } else {
            // Token inválido, remover cookie
            setcookie('remember_token', '', time() - 3600, '/');
        }
    }
    
    return false;
}

/**
 * Obter dados do usuário atual
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $sql = "SELECT * FROM users WHERE id = ? AND status = 'ativo'";
    return fetchOne($sql, [$_SESSION['user_id']]);
}

/**
 * Destruir sessão do usuário
 */
function destroyUserSession() {
    // Remover token do banco se existir
    if (isset($_SESSION['user_id'])) {
        $sql = "DELETE FROM user_tokens WHERE user_id = ?";
        executeQuery($sql, [$_SESSION['user_id']]);
    }
    
    // Limpar sessão
    session_unset();
    session_destroy();
    
    // Remover cookie
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }
}

/**
 * Verificar se usuário tem permissão para acessar área
 */
function hasPermission($requiredRole, $userRole = null) {
    if ($userRole === null) {
        $user = getCurrentUser();
        $userRole = $user['role'] ?? null;
    }
    
    if (!$userRole) {
        return false;
    }
    
    // Admin tem acesso a tudo
    if ($userRole === 'admin') {
        return true;
    }
    
    // Verificar role específico
    return $userRole === $requiredRole;
}

/**
 * Middleware de autenticação
 */
function requireAuth($redirectTo = '/login.php') {
    if (!isLoggedIn()) {
        header("Location: {$redirectTo}");
        exit;
    }
}

/**
 * Middleware de autorização por role
 */
function requireRole($requiredRole, $redirectTo = '/error/403.php') {
    requireAuth();
    
    if (!hasPermission($requiredRole)) {
        header("Location: {$redirectTo}");
        exit;
    }
}

/**
 * Verificar se email já existe
 */
function emailExists($email, $excludeUserId = null) {
    $sql = "SELECT id FROM users WHERE email = ?";
    $params = [$email];
    
    if ($excludeUserId) {
        $sql .= " AND id != ?";
        $params[] = $excludeUserId;
    }
    
    $result = fetchOne($sql, $params);
    return $result !== false;
}

/**
 * Criar novo usuário
 */
function createUser($data) {
    // Validações
    if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
        throw new Exception('Nome, email e senha são obrigatórios.');
    }
    
    if (!isValidEmail($data['email'])) {
        throw new Exception('Email inválido.');
    }
    
    if (emailExists($data['email'])) {
        throw new Exception('Este email já está em uso.');
    }
    
    if (strlen($data['password']) < 6) {
        throw new Exception('A senha deve ter pelo menos 6 caracteres.');
    }
    
    // Preparar dados
    $userData = [
        'name' => sanitizeString($data['name']),
        'email' => strtolower(trim($data['email'])),
        'password' => hashPassword($data['password']),
        'role' => $data['role'] ?? 'athlete',
        'sub_role' => $data['sub_role'] ?? null,
        'status' => $data['status'] ?? 'ativo',
        'badges' => json_encode($data['badges'] ?? []),
        'bio' => sanitizeString($data['bio'] ?? ''),
    ];
    
    // Inserir no banco
    $sql = "INSERT INTO users (name, email, password, role, sub_role, status, badges, bio, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $userId = insertRecord($sql, [
        $userData['name'],
        $userData['email'],
        $userData['password'],
        $userData['role'],
        $userData['sub_role'],
        $userData['status'],
        $userData['badges'],
        $userData['bio']
    ]);
    
    // Log da atividade
    logActivity($userId, 'create', 'user', $userId, 'Usuário criado');
    
    return $userId;
}

/**
 * Atualizar dados do usuário
 */
function updateUser($userId, $data) {
    $user = fetchOne("SELECT * FROM users WHERE id = ?", [$userId]);
    if (!$user) {
        throw new Exception('Usuário não encontrado.');
    }
    
    $updates = [];
    $params = [];
    
    // Campos que podem ser atualizados
    $allowedFields = ['name', 'email', 'role', 'sub_role', 'status', 'badges', 'bio'];
    
    foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
            if ($field === 'email') {
                if (!isValidEmail($data[$field])) {
                    throw new Exception('Email inválido.');
                }
                if (emailExists($data[$field], $userId)) {
                    throw new Exception('Este email já está em uso.');
                }
                $updates[] = "email = ?";
                $params[] = strtolower(trim($data[$field]));
            } elseif ($field === 'badges') {
                $updates[] = "badges = ?";
                $params[] = json_encode($data[$field]);
            } else {
                $updates[] = "{$field} = ?";
                $params[] = sanitizeString($data[$field]);
            }
        }
    }
    
    // Atualizar senha se fornecida
    if (!empty($data['password'])) {
        if (strlen($data['password']) < 6) {
            throw new Exception('A senha deve ter pelo menos 6 caracteres.');
        }
        $updates[] = "password = ?";
        $params[] = hashPassword($data['password']);
    }
    
    if (empty($updates)) {
        return true; // Nada para atualizar
    }
    
    $updates[] = "updated_at = NOW()";
    $params[] = $userId;
    
    $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
    executeQuery($sql, $params);
    
    // Log da atividade
    logActivity($userId, 'update', 'user', $userId, 'Dados do usuário atualizados');
    
    return true;
}

/**
 * Buscar usuários com filtros
 */
function getUsers($filters = [], $limit = 50, $offset = 0) {
    $where = [];
    $params = [];
    
    if (!empty($filters['role'])) {
        $where[] = "role = ?";
        $params[] = $filters['role'];
    }
    
    if (!empty($filters['status'])) {
        $where[] = "status = ?";
        $params[] = $filters['status'];
    }
    
    if (!empty($filters['search'])) {
        $where[] = "(name LIKE ? OR email LIKE ?)";
        $searchTerm = '%' . $filters['search'] . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $sql = "SELECT id, name, email, role, sub_role, status, badges, created_at, last_login 
            FROM users";
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    
    $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    return fetchAll($sql, $params);
}
?>