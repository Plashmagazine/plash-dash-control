<?php
/**
 * Plataforma Editorial Plash
 * Página de Login Universal
 */

// Verificar se o sistema está instalado
if (!file_exists('.env.php')) {
    header('Location: install/');
    exit;
}

require_once '.env.php';
require_once 'includes/auth.php';
require_once 'includes/database.php';

// Se já estiver logado, redirecionar
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

// Processar login
if ($_POST) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($email) || empty($password)) {
        $error = 'Por favor, preencha todos os campos.';
    } else {
        $user = authenticateUser($email, $password);
        if ($user) {
            // Login bem-sucedido
            startUserSession($user, $remember);
            
            // Log da atividade
            logActivity($user['id'], 'login', 'user', $user['id'], 'Login realizado com sucesso');
            
            // Redirecionar baseado no role
            switch ($user['role']) {
                case 'admin':
                    header('Location: admin/');
                    break;
                case 'athlete':
                    header('Location: athlete/');
                    break;
                case 'collaborator':
                    header('Location: collaborator/');
                    break;
                case 'partner':
                    header('Location: partner/');
                    break;
                default:
                    $error = 'Tipo de usuário inválido.';
            }
            
            if (!$error) {
                exit;
            }
        } else {
            $error = 'Email ou senha incorretos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Plataforma Editorial Plash</title>
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
<body class="bg-gradient-to-br from-plash-blue to-blue-800 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md p-8">
        <!-- Logo/Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-plash-blue rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-white font-bold text-2xl">P</span>
            </div>
            <h1 class="text-2xl font-bold text-plash-gray">Plash Magazine</h1>
            <p class="text-gray-600 text-sm">Plataforma Editorial</p>
        </div>

        <!-- Mensagens -->
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <!-- Formulário de Login -->
        <form method="POST" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-plash-gray mb-2">
                    Email
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-plash-blue focus:border-transparent"
                    placeholder="seu@email.com"
                    required
                >
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-plash-gray mb-2">
                    Senha
                </label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-plash-blue focus:border-transparent pr-10"
                        placeholder="Sua senha"
                        required
                    >
                    <button 
                        type="button" 
                        onclick="togglePassword()"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                    >
                        <svg id="eye-icon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input 
                        id="remember" 
                        name="remember" 
                        type="checkbox" 
                        class="h-4 w-4 text-plash-blue focus:ring-plash-blue border-gray-300 rounded"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        Lembrar de mim
                    </label>
                </div>
            </div>

            <button 
                type="submit" 
                class="w-full bg-plash-blue text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-plash-blue focus:ring-offset-2 transition duration-200"
            >
                Entrar
            </button>
        </form>

        <!-- Credenciais de Demonstração -->
        <div class="mt-8 p-4 bg-gray-50 rounded-md">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Credenciais de Demonstração:</h3>
            <div class="text-xs text-gray-600 space-y-1">
                <div><strong>Admin:</strong> admin@plash.com / admin123</div>
                <div><strong>Atleta:</strong> atleta@plash.com / demo123</div>
                <div><strong>Colaborador:</strong> colaborador@plash.com / demo123</div>
                <div><strong>Editora:</strong> editora@plash.com / demo123</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center text-xs text-gray-500">
            <p>&copy; <?php echo date('Y'); ?> Plash Magazine. Todos os direitos reservados.</p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }

        // Auto-fill demo credentials
        function fillDemo(type) {
            const credentials = {
                admin: { email: 'admin@plash.com', password: 'admin123' },
                athlete: { email: 'atleta@plash.com', password: 'demo123' },
                collaborator: { email: 'colaborador@plash.com', password: 'demo123' },
                partner: { email: 'editora@plash.com', password: 'demo123' }
            };
            
            if (credentials[type]) {
                document.getElementById('email').value = credentials[type].email;
                document.getElementById('password').value = credentials[type].password;
            }
        }

        // Add click handlers to demo credentials
        document.addEventListener('DOMContentLoaded', function() {
            const demoDiv = document.querySelector('.bg-gray-50');
            if (demoDiv) {
                demoDiv.addEventListener('click', function(e) {
                    const text = e.target.textContent;
                    if (text.includes('Admin:')) fillDemo('admin');
                    else if (text.includes('Atleta:')) fillDemo('athlete');
                    else if (text.includes('Colaborador:')) fillDemo('collaborator');
                    else if (text.includes('Editora:')) fillDemo('partner');
                });
                
                // Make demo credentials clickable
                demoDiv.style.cursor = 'pointer';
                demoDiv.title = 'Clique para preencher automaticamente';
            }
        });
    </script>
</body>
</html>