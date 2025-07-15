<?php
/**
 * API Endpoints para Reino de Habbo
 * Compatible con XAMPP MySQL
 */

// Headers de seguridad y CORS
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

// Configurar zona horaria
date_default_timezone_set('America/Mexico_City');

// Incluir archivos necesarios
require_once __DIR__ . '/../config/database.php';

// Función para manejar errores de manera segura
function handleError($message, $code = 500) {
    http_response_code($code);
    echo json_encode([
        'error' => true,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit();
}

// Función para respuesta exitosa
function handleSuccess($data, $message = 'Success') {
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

// Initialize database connection with error handling
try {
    $database = new Database(__DIR__ . '/../config/config.php');
    $db = $database->getConnection();

    // Verificar conexión
    if (!$database->testConnection()) {
        handleError('No se puede conectar a la base de datos', 503);
    }

} catch (Exception $e) {
    handleError('Error de configuración de base de datos: ' . $e->getMessage(), 503);
}

// Get request method and endpoint parameter
$request_method = $_SERVER['REQUEST_METHOD'];

// Get action from POST data or GET parameter
$action = '';
$input = [];

if ($request_method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    $action = $input['action'] ?? '';
}

if (empty($action) && isset($_GET['action'])) {
    $action = $_GET['action'];
}

// Get endpoint from URL parameter or query string for backwards compatibility
$endpoint = '';
if (isset($_GET['endpoint'])) {
    $endpoint = '/' . ltrim($_GET['endpoint'], '/');
} else {
    // Para compatibilidad directa sin mod_rewrite
    $request_uri = $_SERVER['REQUEST_URI'];
    $path = parse_url($request_uri, PHP_URL_PATH);
    $endpoint = str_replace('/api/endpoints.php', '', $path);
    if (empty($endpoint)) {
        $endpoint = isset($_GET['action']) ? '/' . $_GET['action'] : '/';
    }
}

// Route handling basado en action parameter para XAMPP
if (!empty($action)) {
    switch ($action) {
        case 'update_content':
            if ($request_method === 'POST') {
                try {
                    $data = $input['data'] ?? [];

                    // Validate required fields
                    $requiredFields = ['main_title', 'main_description', 'feature_1', 'feature_2', 'feature_3', 'footer_text'];
                    foreach ($requiredFields as $field) {
                        if (empty($data[$field])) {
                            handleError("Campo requerido: $field", 400);
                        }
                    }

                    // Check if content table exists, create if not
                    $createTable = "CREATE TABLE IF NOT EXISTS site_content (
                        id INT PRIMARY KEY AUTO_INCREMENT,
                        content_key VARCHAR(50) UNIQUE NOT NULL,
                        content_value TEXT NOT NULL,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    )";
                    $db->exec($createTable);

                    // Update or insert content
                    $stmt = $db->prepare("INSERT INTO site_content (content_key, content_value) VALUES (?, ?) 
                                         ON DUPLICATE KEY UPDATE content_value = VALUES(content_value)");

                    foreach ($data as $key => $value) {
                        $stmt->execute([$key, $value]);
                    }

                    handleSuccess([], 'Contenido actualizado exitosamente');
                } catch (Exception $e) {
                    handleError('Error al actualizar contenido: ' . $e->getMessage());
                }
            } else {
                handleError('Método no permitido', 405);
            }
            break;

        case 'upload_logo':
           
            if (!isset($_FILES['logo'])) {
                echo json_encode(['success' => false, 'message' => 'No se seleccionó ningún archivo']);
                exit;
            }

            $file = $_FILES['logo'];

            // Validate file type
            $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml', 'image/gif'];
            if (!in_array($file['type'], $allowedTypes)) {
                echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido']);
                exit;
            }

            // Validate file size (2MB max)
            if ($file['size'] > 2 * 1024 * 1024) {
                echo json_encode(['success' => false, 'message' => 'Archivo demasiado grande']);
                exit;
            }

            // Create uploads directory if it doesn't exist
            $uploadsDir = 'uploads/';
            if (!is_dir($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'logo_' . time() . '.' . $extension;
            $filepath = $uploadsDir . $filename;

            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                // Save to database
                $logoUrl = 'uploads/' . $filename;
                $stmt = $db->prepare("INSERT INTO site_content (content_key, content_value) VALUES ('site_logo', ?) ON DUPLICATE KEY UPDATE content_value = ?");
                $stmt->execute([$logoUrl, $logoUrl]);

                echo json_encode(['success' => true, 'logo_url' => $logoUrl, 'message' => 'Logo subido exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al subir el archivo']);
            }
            break;

        case 'get_logo':
            try{
                $stmt = $db->prepare("SELECT content_value FROM site_content WHERE content_key = 'site_logo'");
                $stmt->execute();
                $result = $stmt->fetch();

                if ($result && file_exists($result['content_value'])) {
                    echo json_encode(['success' => true, 'logo_url' => $result['content_value']]);
                } else {
                    echo json_encode(['success' => true, 'logo_url' => null]);
                }
            } catch (Exception $e) {
                 handleError('Error al obtener logo: ' . $e->getMessage());
            }
            break;

        case 'reset_logo':
           
            $stmt = $db->prepare("DELETE FROM site_content WHERE content_key = 'site_logo'");
            $stmt->execute();

            echo json_encode(['success' => true, 'message' => 'Logo restablecido']);
            break;

        case 'get_content':
            if ($request_method === 'GET' || $request_method === 'POST') {
                try {
                    $stmt = $db->prepare("SELECT content_key, content_value FROM site_content");
                    $stmt->execute();
                    $content = $stmt->fetchAll();

                    $result = [];
                    foreach ($content as $item) {
                        $result[$item['content_key']] = $item['content_value'];
                    }

                    handleSuccess($result);
                } catch (Exception $e) {
                    handleError('Error al obtener contenido: ' . $e->getMessage());
                }
            } else {
                handleError('Método no permitido', 405);
            }
            break;

        default:
            handleError('Acción no encontrada', 404);
            break;
    }
} else {
    // Fallback to endpoint routing
    switch ($endpoint) {
        case '/ranks':
            if ($request_method === 'GET') {
                try {
                    $stmt = $db->prepare("SELECT * FROM ranks ORDER BY level ASC");
                    $stmt->execute();
                    $ranks = $stmt->fetchAll();
                    echo json_encode($ranks);
                } catch (Exception $e) {
                    handleError('Error al obtener rangos: ' . $e->getMessage());
                }
            } else {
                handleError('Método no permitido', 405);
            }
            break;

        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
            break;
    }
}
?>