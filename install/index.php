<?php
/**
 * Plataforma Editorial Plash
 * Instalador do Sistema
 */

// Verificar se já está instalado
if (file_exists('../.env.php')) {
    die('Sistema já instalado. Remova o arquivo .env.php para reinstalar.');
}

$step = $_GET['step'] ?? 1;
$error = '';
$success = '';

// Processar formulários
if ($_POST) {
    switch ($step) {
        case 1:
            // Verificar requisitos
            $requirements = checkRequirements();
            if ($requirements['all_ok']) {
                header('Location: ?step=2');
                exit;
            } else {
                $error = 'Alguns requisitos não foram atendidos.';
            }
            break;
            
        case 2:
            // Configurar banco de dados
            $dbConfig = [
                'host' => trim($_POST['db_host'] ?? ''),
                'name' => trim($_POST['db_name'] ?? ''),
                'user' => trim($_POST['db_user'] ?? ''),
                'pass' => $_POST['db_pass'] ?? '',
            ];
            
            if (empty($dbConfig['host']) || empty($dbConfig['name']) || empty($dbConfig['user'])) {
                $error = 'Todos os campos do banco são obrigatórios.';
            } else {
                // Testar conexão
                try {
                    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['name']};charset=utf8mb4";
                    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    ]);
                    
                    // Salvar configuração na sessão
                    session_start();
                    $_SESSION['db_config'] = $dbConfig;
                    
                    header('Location: ?step=3');
                    exit;
                } catch (PDOException $e) {
                    $error = 'Erro de conexão: ' . $e->getMessage();
                }
            }
            break;
            
        case 3:
            // Criar admin
            session_start();
            if (!isset($_SESSION['db_config'])) {
                header('Location: ?step=2');
                exit;
            }
            
            $adminData = [
                'name' => trim($_POST['admin_name'] ?? ''),
                'email' => trim($_POST['admin_email'] ?? ''),
                'password' => $_POST['admin_password'] ?? '',
                'confirm_password' => $_POST['confirm_password'] ?? '',
            ];
            
            if (empty($adminData['name']) || empty($adminData['email']) || empty($adminData['password'])) {
                $error = 'Todos os campos são obrigatórios.';
            } elseif (!filter_var($adminData['email'], FILTER_VALIDATE_EMAIL)) {
                $error = 'Email inválido.';
            } elseif (strlen($adminData['password']) < 6) {
                $error = 'A senha deve ter pelo menos 6 caracteres.';
            } elseif ($adminData['password'] !== $adminData['confirm_password']) {
                $error = 'As senhas não coincidem.';
            } else {
                // Instalar sistema
                try {
                    $result = installSystem($_SESSION['db_config'], $adminData);
                    if ($result) {
                        session_destroy();
                        header('Location: ?step=4');
                        exit;
                    } else {
                        $error = 'Erro na instalação.';
                    }
                } catch (Exception $e) {
                    $error = 'Erro na instalação: ' . $e->getMessage();
                }
            }
            break;
    }
}

/**
 * Verificar requisitos do sistema
 */
function checkRequirements() {
    $requirements = [
        'php_version' => version_compare(PHP_VERSION, '7.4.0', '>='),
        'pdo_mysql' => extension_loaded('pdo_mysql'),
        'gd' => extension_loaded('gd'),
        'fileinfo' => extension_loaded('fileinfo'),
        'uploads_writable' => is_writable('../uploads') || mkdir('../uploads', 0755, true),
        'root_writable' => is_writable('../'),
    ];
    
    $requirements['all_ok'] = !in_array(false, $requirements, true);
    
    return $requirements;
}

/**
 * Instalar sistema
 */
function installSystem($dbConfig, $adminData) {
    // Conectar ao banco
    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['name']};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    
    // Executar SQL de estrutura
    $sql = file_get_contents('database.sql');
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($queries as $query) {
        if (!empty($query) && !str_starts_with($query, '--')) {
            $pdo->exec($query);
        }
    }
    
    // Criar admin
    $hashedPassword = password_hash($adminData['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, sub_role, status, badges, created_at) VALUES (?, ?, ?, 'admin', 'ceo', 'ativo', '[]', NOW())");
    $stmt->execute([$adminData['name'], $adminData['email'], $hashedPassword]);
    
    // Criar arquivo .env.php
    $envContent = "<?php\n";
    $envContent .= "// Configurações do banco de dados\n";
    $envContent .= "define('DB_HOST', '{$dbConfig['host']}');\n";
    $envContent .= "define('DB_NAME', '{$dbConfig['name']}');\n";
    $envContent .= "define('DB_USER', '{$dbConfig['user']}');\n";
    $envContent .= "define('DB_PASS', '{$dbConfig['pass']}');\n\n";
    $envContent .= "// Configurações gerais\n";
    $envContent .= "define('SITE_URL', 'http://' . \$_SERVER['HTTP_HOST'] . dirname(\$_SERVER['SCRIPT_NAME']));\n";
    $envContent .= "define('UPLOAD_PATH', __DIR__ . '/uploads/');\n";
    $envContent .= "define('MAX_UPLOAD_SIZE', 10485760); // 10MB\n\n";
    $envContent .= "// Timezone\n";
    $envContent .= "date_default_timezone_set('America/Sao_Paulo');\n\n";
    $envContent .= "// Configurações de erro\n";
    $envContent .= "ini_set('display_errors', 0);\n";
    $envContent .= "ini_set('log_errors', 1);\n";
    $envContent .= "ini_set('error_log', __DIR__ . '/error.log');\n";
    
    file_put_contents('../.env.php', $envContent);
    
    // Criar diretórios necessários
    $dirs = ['uploads', 'uploads/photos', 'uploads/videos', 'uploads/documents', 'uploads/temp'];
    foreach ($dirs as $dir) {
        if (!is_dir("../{$dir}")) {
            mkdir("../{$dir}", 0755, true);
        }
    }
    
    return true;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação - Plataforma Editorial Plash</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'plash-blue': '#1e40af',
                        'plash-yellow': '#fbbf24'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto py-12 px-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-plash-blue rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-white font-bold text-3xl">P</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Plataforma Editorial Plash</h1>
            <p class="text-gray-600 mt-2">Instalação do Sistema</p>
        </div>

        <!-- Progress -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">Progresso da Instalação</span>
                <span class="text-sm font-medium text-gray-700"><?php echo min($step, 4); ?>/4</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-plash-blue h-2 rounded-full" style="width: <?php echo (min($step, 4) / 4) * 100; ?>%"></div>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-lg shadow-md p-8">
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if ($step == 1): ?>
                <!-- Passo 1: Verificar Requisitos -->
                <h2 class="text-2xl font-bold mb-6">Verificação de Requisitos</h2>
                
                <?php $requirements = checkRequirements(); ?>
                
                <div class="space-y-4 mb-8">
                    <div class="flex items-center justify-between p-4 border rounded-lg">
                        <span>PHP 7.4 ou superior</span>
                        <?php if ($requirements['php_version']): ?>
                            <span class="text-green-600 font-semibold">✓ OK (<?php echo PHP_VERSION; ?>)</span>
                        <?php else: ?>
                            <span class="text-red-600 font-semibold">✗ Falhou</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 border rounded-lg">
                        <span>Extensão PDO MySQL</span>
                        <?php if ($requirements['pdo_mysql']): ?>
                            <span class="text-green-600 font-semibold">✓ OK</span>
                        <?php else: ?>
                            <span class="text-red-600 font-semibold">✗ Falhou</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 border rounded-lg">
                        <span>Extensão GD (imagens)</span>
                        <?php if ($requirements['gd']): ?>
                            <span class="text-green-600 font-semibold">✓ OK</span>
                        <?php else: ?>
                            <span class="text-red-600 font-semibold">✗ Falhou</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 border rounded-lg">
                        <span>Extensão Fileinfo</span>
                        <?php if ($requirements['fileinfo']): ?>
                            <span class="text-green-600 font-semibold">✓ OK</span>
                        <?php else: ?>
                            <span class="text-red-600 font-semibold">✗ Falhou</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 border rounded-lg">
                        <span>Pasta uploads/ gravável</span>
                        <?php if ($requirements['uploads_writable']): ?>
                            <span class="text-green-600 font-semibold">✓ OK</span>
                        <?php else: ?>
                            <span class="text-red-600 font-semibold">✗ Falhou</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 border rounded-lg">
                        <span>Pasta raiz gravável</span>
                        <?php if ($requirements['root_writable']): ?>
                            <span class="text-green-600 font-semibold">✓ OK</span>
                        <?php else: ?>
                            <span class="text-red-600 font-semibold">✗ Falhou</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($requirements['all_ok']): ?>
                    <form method="POST">
                        <button type="submit" class="w-full bg-plash-blue text-white py-3 px-4 rounded-md hover:bg-blue-700 font-semibold">
                            Continuar para Configuração do Banco
                        </button>
                    </form>
                <?php else: ?>
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                        <p class="font-semibold">Alguns requisitos não foram atendidos.</p>
                        <p class="text-sm mt-1">Entre em contato com seu provedor de hospedagem para resolver os problemas acima.</p>
                    </div>
                <?php endif; ?>

            <?php elseif ($step == 2): ?>
                <!-- Passo 2: Configuração do Banco -->
                <h2 class="text-2xl font-bold mb-6">Configuração do Banco de Dados</h2>
                
                <form method="POST" class="space-y-6">
                    <div>
                        <label for="db_host" class="block text-sm font-medium text-gray-700 mb-2">
                            Host do Banco
                        </label>
                        <input 
                            type="text" 
                            id="db_host" 
                            name="db_host" 
                            value="<?php echo htmlspecialchars($_POST['db_host'] ?? 'localhost'); ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-plash-blue"
                            placeholder="localhost"
                            required
                        >
                    </div>
                    
                    <div>
                        <label for="db_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome do Banco
                        </label>
                        <input 
                            type="text" 
                            id="db_name" 
                            name="db_name" 
                            value="<?php echo htmlspecialchars($_POST['db_name'] ?? ''); ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-plash-blue"
                            placeholder="plash_editorial"
                            required
                        >
                    </div>
                    
                    <div>
                        <label for="db_user" class="block text-sm font-medium text-gray-700 mb-2">
                            Usuário do Banco
                        </label>
                        <input 
                            type="text" 
                            id="db_user" 
                            name="db_user" 
                            value="<?php echo htmlspecialchars($_POST['db_user'] ?? ''); ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-plash-blue"
                            required
                        >
                    </div>
                    
                    <div>
                        <label for="db_pass" class="block text-sm font-medium text-gray-700 mb-2">
                            Senha do Banco
                        </label>
                        <input 
                            type="password" 
                            id="db_pass" 
                            name="db_pass" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-plash-blue"
                        >
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <h4 class="font-semibold text-blue-800 mb-2">Importante:</h4>
                        <ul class="text-sm text-blue-700 space-y-1">
                            <li>• O banco de dados deve existir e estar vazio</li>
                            <li>• O usuário deve ter permissões completas no banco</li>
                            <li>• Teste a conexão antes de continuar</li>
                        </ul>
                    </div>
                    
                    <button type="submit" class="w-full bg-plash-blue text-white py-3 px-4 rounded-md hover:bg-blue-700 font-semibold">
                        Testar Conexão e Continuar
                    </button>
                </form>

            <?php elseif ($step == 3): ?>
                <!-- Passo 3: Criar Admin -->
                <h2 class="text-2xl font-bold mb-6">Criar Administrador</h2>
                
                <form method="POST" class="space-y-6">
                    <div>
                        <label for="admin_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome Completo
                        </label>
                        <input 
                            type="text" 
                            id="admin_name" 
                            name="admin_name" 
                            value="<?php echo htmlspecialchars($_POST['admin_name'] ?? ''); ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-plash-blue"
                            required
                        >
                    </div>
                    
                    <div>
                        <label for="admin_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input 
                            type="email" 
                            id="admin_email" 
                            name="admin_email" 
                            value="<?php echo htmlspecialchars($_POST['admin_email'] ?? ''); ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-plash-blue"
                            required
                        >
                    </div>
                    
                    <div>
                        <label for="admin_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Senha
                        </label>
                        <input 
                            type="password" 
                            id="admin_password" 
                            name="admin_password" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-plash-blue"
                            minlength="6"
                            required
                        >
                        <p class="text-sm text-gray-500 mt-1">Mínimo de 6 caracteres</p>
                    </div>
                    
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirmar Senha
                        </label>
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-plash-blue"
                            minlength="6"
                            required
                        >
                    </div>
                    
                    <button type="submit" class="w-full bg-plash-blue text-white py-3 px-4 rounded-md hover:bg-blue-700 font-semibold">
                        Instalar Sistema
                    </button>
                </form>

            <?php elseif ($step == 4): ?>
                <!-- Passo 4: Concluído -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    
                    <h2 class="text-2xl font-bold text-green-800 mb-4">Instalação Concluída!</h2>
                    
                    <p class="text-gray-600 mb-8">
                        O sistema foi instalado com sucesso. A pasta de instalação será removida automaticamente.
                    </p>
                    
                    <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
                        <h4 class="font-semibold text-green-800 mb-2">Próximos Passos:</h4>
                        <ul class="text-sm text-green-700 space-y-1 text-left">
                            <li>• Faça login com as credenciais de administrador criadas</li>
                            <li>• Configure os usuários e permissões</li>
                            <li>• Personalize as configurações do sistema</li>
                            <li>• Faça backup regular do banco de dados</li>
                        </ul>
                    </div>
                    
                    <a href="../login.php" class="inline-flex items-center px-6 py-3 bg-plash-blue text-white font-semibold rounded-md hover:bg-blue-700">
                        Ir para Login
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                </div>
                
                <script>
                    // Auto-remover pasta de instalação após 10 segundos
                    setTimeout(function() {
                        fetch('cleanup.php', { method: 'POST' })
                            .then(() => {
                                console.log('Pasta de instalação removida');
                            })
                            .catch(() => {
                                console.log('Erro ao remover pasta de instalação');
                            });
                    }, 10000);
                </script>
            <?php endif; ?>
        </div>
        
        <!-- Footer -->
        <div class="text-center mt-8 text-sm text-gray-500">
            <p>&copy; <?php echo date('Y'); ?> Plash Magazine. Sistema de Gestão Editorial.</p>
        </div>
    </div>
</body>
</html>