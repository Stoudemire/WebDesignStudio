
<?php
session_start();

// Optimized headers for better performance
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Handle preflight OPTIONS request quickly
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

// Set timezone once
date_default_timezone_set('America/Mexico_City');

// Include database configuration
require_once __DIR__ . '/../config/database.php';

// Optimized password validation
function isPasswordSecure($password) {
    return strlen($password) >= 8 &&
           preg_match('/[a-z]/', $password) &&
           preg_match('/[A-Z]/', $password) &&
           preg_match('/\d/', $password) &&
           preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password);
}

// Valid roles validation
function isValidRole($role) {
    $validRoles = ['usuario', 'operador', 'administrador', 'desarrollador'];
    return in_array($role, $validRoles);
}

// Get role display name
function getRoleDisplayName($role) {
    $roleNames = [
        'usuario' => 'Usuario',
        'operador' => 'Operador', 
        'administrador' => 'Administrador',
        'desarrollador' => 'Desarrollador'
    ];
    return $roleNames[$role] ?? ucfirst($role);
}

// Optimized error response
function sendError($message, $code = 400) {
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'message' => $message,
        'timestamp' => date('c') // ISO 8601 format
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// Optimized success response
function sendSuccess($data, $message = 'Success') {
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('c')
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// Initialize database with error handling and connection pooling
try {
    $configFile = __DIR__ . '/../config/config.php';
    if (!file_exists($configFile)) {
        error_log('Configuration file not found: ' . $configFile);
        sendError('Archivo de configuración no encontrado', 503);
    }

    $database = new Database($configFile);
    $db = $database->getConnection();

    if (!$database->testConnection()) {
        error_log('Database connection test failed');
        sendError('No se puede conectar a la base de datos. Verifica que MySQL esté ejecutándose.', 503);
    }

} catch (Exception $e) {
    error_log('Database configuration error: ' . $e->getMessage());
    sendError('Error de configuración del servidor: ' . $e->getMessage(), 503);
}

// Get and validate input - support both GET and POST for logout
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_GET['action'] ?? '';

// Input validation
if (empty($action)) {
    sendError('Acción requerida', 400);
}

switch ($action) {
    case 'login':
        $username = trim($input['username'] ?? '');
        $password = $input['password'] ?? '';

        if (empty($username) || empty($password)) {
            sendError('Por favor completa todos los campos');
        }

        try {
            // Ultra-fast query - only essential fields
            $stmt = $db->prepare("SELECT id, username, password, is_verified, role FROM users WHERE habbo_username = ? AND is_verified = 1 LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Minimal session setup for speed
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Minimal response for faster processing
                http_response_code(200);
                echo '{"success":true}';
                exit();
            } else {
                // Remove delay for faster response
                sendError('Credenciales inválidas');
            }
        } catch (Exception $e) {
            error_log('Login error: ' . $e->getMessage());
            sendError('Error del sistema');
        }
        break;

    case 'register':
        $username = trim($input['username'] ?? '');
        $password = $input['password'] ?? '';

        if (empty($username) || empty($password)) {
            sendError('Por favor completa todos los campos');
        }

        // Validate username length and characters
        if (strlen($username) < 3 || strlen($username) > 50) {
            sendError('El nombre de usuario debe tener entre 3 y 50 caracteres');
        }

        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $username)) {
            sendError('El nombre de usuario solo puede contener letras, números, puntos, guiones y guiones bajos');
        }

        if (!isPasswordSecure($password)) {
            sendError('La contraseña debe tener al menos 8 caracteres, incluyendo mayúsculas, minúsculas, números y caracteres especiales');
        }

        try {
            // Check if user exists with optimized query
            $stmt = $db->prepare("SELECT 1 FROM users WHERE habbo_username = ? LIMIT 1");
            $stmt->execute([$username]);

            if ($stmt->fetch()) {
                sendError('Este nombre de Habbo ya está registrado');
            }

            // Generate unique verification code
            $verificationCode = 'RH' . str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT);

            // Create new user with optimized query
            $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
            $stmt = $db->prepare("INSERT INTO users (username, password, habbo_username, verification_code, is_verified, role, created_at) VALUES (?, ?, ?, ?, FALSE, 'usuario', NOW())");

            if ($stmt->execute([$username, $hashedPassword, $username, $verificationCode])) {
                $user_id = (int)$db->lastInsertId();

                sendSuccess([
                    'user_id' => $user_id,
                    'username' => $username,
                    'verification_code' => $verificationCode,
                    'needs_verification' => true
                ], 'Cuenta creada. Coloca el código en tu misión de Habbo para verificar tu cuenta.');
            } else {
                sendError('Error al crear la cuenta. Intenta más tarde.');
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry error
                sendError('Este nombre de usuario ya está en uso');
            } else {
                error_log('Registration error: ' . $e->getMessage());
                sendError('Error del sistema. Intenta más tarde.');
            }
        } catch (Exception $e) {
            error_log('Registration error: ' . $e->getMessage());
            sendError('Error del sistema. Intenta más tarde.');
        }
        break;

    case 'verify_account':
        $username = trim($input['username'] ?? '');
        
        if (empty($username)) {
            sendError('Nombre de usuario requerido');
        }

        try {
            // Get verification code with optimized query
            $stmt = $db->prepare("SELECT verification_code, is_verified FROM users WHERE habbo_username = ? LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                sendError('Usuario no encontrado');
            }

            if ($user['is_verified']) {
                sendError('Esta cuenta ya está verificada');
            }

            // Verify with Habbo API with timeout and better error handling
            $habboApiUrl = "https://www.habbo.es/api/public/users?name=" . urlencode($username);
            
            $context = stream_context_create([
                'http' => [
                    'timeout' => 8,
                    'user_agent' => 'Reino de Habbo Verification Bot/1.0',
                    'ignore_errors' => true
                ]
            ]);
            
            $response = @file_get_contents($habboApiUrl, false, $context);
            
            if ($response === false) {
                sendError('No se pudo conectar con la API de Habbo. Inténtalo más tarde.');
            }

            $habboData = json_decode($response, true);
            
            if (!$habboData || !isset($habboData['motto'])) {
                sendError('No se pudo obtener la información de tu perfil de Habbo. Verifica que tu perfil sea público.');
            }

            // Check verification code in motto
            $motto = $habboData['motto'];
            if (strpos($motto, $user['verification_code']) !== false) {
                // Verify account with transaction for data consistency
                $db->beginTransaction();
                
                try {
                    $stmt = $db->prepare("UPDATE users SET is_verified = TRUE, verified_at = NOW() WHERE habbo_username = ?");
                    $stmt->execute([$username]);
                    
                    $db->commit();

                    // Auto login after verification
                    $stmt = $db->prepare("SELECT id, username, role FROM users WHERE habbo_username = ? LIMIT 1");
                    $stmt->execute([$username]);
                    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

                    $_SESSION['user_id'] = $userData['id'];
                    $_SESSION['username'] = $userData['username'];
                    $_SESSION['role'] = $userData['role'];

                    sendSuccess([
                        'user_id' => (int)$userData['id'],
                        'username' => $userData['username'],
                        'role' => $userData['role'],
                        'verified' => true
                    ], 'Cuenta verificada exitosamente. ¡Bienvenido al Reino de Habbo!');
                    
                } catch (Exception $e) {
                    $db->rollback();
                    throw $e;
                }
            } else {
                sendError('El código de verificación no se encontró en tu misión. Asegúrate de colocar: ' . $user['verification_code']);
            }

        } catch (Exception $e) {
            error_log('Verification error: ' . $e->getMessage());
            sendError('Error del sistema durante la verificación');
        }
        break;

    case 'logout':
        // Fastest possible session cleanup
        $_SESSION = [];
        session_destroy();
        
        // Ultra-fast redirect
        header('Location: ../index.php', true, 302);
        exit();
        break;

    case 'check_session':
        if (isset($_SESSION['user_id'])) {
            sendSuccess([
                'user_id' => (int)$_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role'] ?? 'usuario'
            ], 'Sesión activa');
        } else {
            sendError('No hay sesión activa', 401);
        }
        break;

    default:
        sendError('Acción no válida', 400);
        break;
}
?>
