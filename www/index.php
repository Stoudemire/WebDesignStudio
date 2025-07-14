<?php
$content = [
    'main_title' => 'Welcome to Reino de Habbo',
    'main_description' => 'Enter a legendary medieval kingdom where honor defines your destiny. Rise through ancient ranks, forge powerful alliances, and command respect in the most prestigious virtual realm ever created. Your journey from commoner to royalty starts here.',
    'feature_1' => 'Medieval Combat',
    'feature_2' => 'Kingdom Building', 
    'feature_3' => 'Royal Hierarchy',
    'footer_text' => 'Una comunidad medieval virtual'
];

try {
    require_once __DIR__ . '/config/database.php';
    $database = new Database(__DIR__ . '/config/config.php');
    $db = $database->getConnection();

    $stmt = $db->prepare("SELECT content_key, content_value FROM site_content");
    $stmt->execute();
    $dbContent = $stmt->fetchAll();

    foreach ($dbContent as $item) {
        $content[$item['content_key']] = $item['content_value'];
    }
} catch (Exception $e) {
    // Use default content if database fails
    error_log('Content loading error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($content['main_title']); ?> - Kingdom of Habbo</title>
    <meta name="description" content="Únete al Reino de Habbo, una comunidad medieval con sistema de rangos, membresías premium y alianzas estratégicas. Experimenta la vida real en un reino virtual.">

    <!-- Open Graph meta tags -->
    <meta property="og:title" content="Reino de Habbo - Kingdom of Habbo">
    <meta property="og:description" content="Únete al Reino de Habbo, una comunidad medieval con sistema de rangos, membresías premium y alianzas estratégicas.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://reinodehabbo.com">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Cinzel+Decorative:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/main.css">
</head>
<body class="medieval-bg">
    <!-- Navigation -->
    <nav class="glass border-b border-white/20 sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="crown-icon text-yellow-400">
                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 6l4 6h5l-7-11zm0 0L8 12H3l7-11zM2 12h20l-2 7H4z"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-cinzel-decorative font-bold text-yellow-400"><?php echo htmlspecialchars($content['main_title']); ?></h1>
                </div>
                <div class="flex items-center space-x-2">
                    <!-- Login icon first, Register icon second -->
                    <button id="user-menu-btn" class="text-white hover:text-yellow-400 transition-colors p-2" title="Login">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </button>
                    <button id="register-btn-nav" class="text-white hover:text-yellow-400 transition-colors p-2" title="Registro">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Hero Section -->
        <section id="inicio" class="text-center py-16">
            <div class="glass glass-hover rounded-3xl p-12 max-w-4xl mx-auto">
                <div class="text-yellow-400 mb-6">
                    <svg class="w-16 h-16 mx-auto" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 6l4 6h5l-7-11zm0 0L8 12H3l7-11zM2 12h20l-2 7H4z"/>
                    </svg>
                </div>
                <h2 class="text-4xl font-cinzel-decorative font-bold mb-6 text-yellow-400">
                    <?php echo $content['main_title']; ?>
                </h2>
                <p class="text-xl text-gray-300 mb-8 leading-relaxed">
                    <?php echo $content['main_description']; ?>
                </p>
                <div class="flex items-center justify-center space-x-8 text-gray-400">
                    <div class="flex items-center space-x-2">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M6.92 5H5l1.25 3.25L4.31 10.19c-.44.43-.44 1.12 0 1.56L6.25 13.69 5 17h1.92l1.58-3.75c.22-.52.22-1.15 0-1.67L6.92 5zm3.96 0h-1.92l1.58 6.58c.22.52.22 1.15 0 1.67L8.96 17h1.92l1.25-3.25 1.94-1.94c.44-.43.44-1.12 0-1.56L12.13 8.31 10.88 5zm7.04 0h-1.92l-1.58 6.58c-.22.52-.22 1.15 0 1.67L15.96 17h1.92l-1.25-3.25 1.94-1.94c.44-.43.44-1.12 0-1.56L16.63 8.31 17.92 5z"/>
                        </svg>
                        <span class="text-sm"><?php echo $content['feature_1']; ?></span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M2 20h20v2H2v-2zm1.15-4.05L4 16l.85.05L12 13l7.15 3.05L20 16l.85-.05L23 17v3H1v-3l2.15-1.05zM12 10l-8 3 8-3 8 3-8-3zm0-8L4 5v6l8-3 8 3V5l-8-3z"/>
                        </svg>
                        <span class="text-sm"><?php echo $content['feature_2']; ?></span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 6l4 6h5l-7-11zm0 0L8 12H3l7-11zM2 12h20l-2 7H4z"/>
                        </svg>
                        <span class="text-sm"><?php echo $content['feature_3']; ?></span>
                    </div>
                </div>
            </div>
        </section>


    </main>

    <!-- Footer -->
    <footer class="glass border-t border-white/20 mt-16">
        <div class="container mx-auto px-4 py-8">
            <div class="text-center">
                <div class="text-yellow-400 mb-4">
                    <svg class="w-8 h-8 mx-auto" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 6l4 6h5l-7-11zm0 0L8 12H3l7-11zM2 12h20l-2 7H4z"/>
                    </svg>
                </div>
                <p class="text-gray-300">© 2024 Reino de Habbo. Todos los derechos reservados.</p>
                <p class="text-gray-400 mt-2"><?php echo $content['footer_text']; ?></p>
            </div>
        </div>
    </footer>

    <!-- Login Modal -->
    <div id="login-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-overlay">
        <div id="login-content" class="glass rounded-3xl p-8 max-w-md w-full mx-4 relative modal-content">
            <button id="close-login" class="absolute top-4 right-4 text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="text-center">
                <div class="text-yellow-400 mb-4">
                    <svg class="w-12 h-12 mx-auto" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-cinzel-decorative font-bold mb-6 text-yellow-400">Login to Kingdom</h3>

                <div id="login-messages"></div>

                <form id="login-form" class="space-y-4">
                    <div class="text-left">
                        <label class="block text-gray-300 text-sm mb-2">Nombre de Habbo</label>
                        <input type="text" id="login-username" class="w-full px-4 py-3 rounded-full bg-gray-800 text-white border border-gray-600 focus:border-yellow-400 focus:outline-none" required>
                    </div>
                    <div class="text-left">
                        <label class="block text-gray-300 text-sm mb-2">Contraseña</label>
                        <input type="password" id="login-password" class="w-full px-4 py-3 rounded-full bg-gray-800 text-white border border-gray-600 focus:border-yellow-400 focus:outline-none" required>
                    </div>
                    <button type="submit" class="royal-gradient text-black px-8 py-3 rounded-full font-semibold hover:scale-105 transition-transform w-full mt-6">
                        Enter Kingdom
                    </button>
                </form>
                <p class="text-gray-400 text-sm mt-4">Don't have an account? <button id="switch-to-register" class="text-yellow-400 hover:underline">Register here</button></p>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="register-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal-overlay">
        <div id="register-content" class="glass rounded-3xl p-8 max-w-md w-full mx-4 relative modal-content">
            <button id="close-register" class="absolute top-4 right-4 text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="text-center">
                <div class="text-yellow-400 mb-4">
                    <svg class="w-12 h-12 mx-auto" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6.92 5H5l1.25 3.25L4.31 10.19c-.44.43-.44 1.12 0 1.56L6.25 13.69 5 17h1.92l1.58-3.75c.22-.52.22-1.15 0-1.67L6.92 5zm3.96 0h-1.92l1.58 6.58c.22.52.22 1.15 0 1.67L8.96 17h1.92l1.25-3.25 1.94-1.94c.44-.43.44-1.12 0-1.56L12.13 8.31 10.88 5zm7.04 0h-1.92l-1.58 6.58c-.22.52-.22 1.15 0 1.67L15.96 17h1.92l-1.25-3.25 1.94-1.94c.44-.43.44-1.12 0-1.56L16.63 8.31 17.92 5z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-cinzel-decorative font-bold mb-6 text-yellow-400">Join the Kingdom</h3>

                <div id="register-messages"></div>

                <div id="register-step-1">
                    <form id="register-form" class="space-y-4">
                        <div class="text-left">
                            <label class="block text-gray-300 text-sm mb-2">Nombre de usuario en Habbo</label>
                            <input type="text" id="register-username" class="w-full px-4 py-3 rounded-full bg-gray-800 text-white border border-gray-600 focus:border-yellow-400 focus:outline-none" required>
                        </div>
                        <div class="text-left">
                            <label class="block text-gray-300 text-sm mb-2">Contraseña</label>
                            <input type="password" id="register-password" class="w-full px-4 py-3 rounded-full bg-gray-800 text-white border border-gray-600 focus:border-yellow-400 focus:outline-none" required>
                        </div>
                        <div class="text-left">
                            <label class="block text-gray-300 text-sm mb-2">Confirmar Contraseña</label>
                            <input type="password" id="register-confirm-password" class="w-full px-4 py-3 rounded-full bg-gray-800 text-white border border-gray-600 focus:border-yellow-400 focus:outline-none" required>
                        </div>
                        <button type="submit" class="royal-gradient text-black px-8 py-3 rounded-full font-semibold hover:scale-105 transition-transform w-full mt-6">
                            Create Account
                        </button>
                    </form>
                </div>

                <div id="register-step-2" class="hidden">
                    <div class="text-center mb-6">
                        <div class="text-green-400 mb-4">
                            <svg class="w-16 h-16 mx-auto" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-green-400 mb-4">¡Cuenta Creada!</h4>
                        <div class="bg-gray-800 p-4 rounded-lg mb-4">
                            <p class="text-sm text-gray-300 mb-2">Copia este código en tu misión de Habbo:</p>
                            <div class="bg-yellow-400 text-black p-3 rounded font-bold text-lg" id="verification-code-display"></div>
                        </div>
                        <p class="text-sm text-gray-400 mb-4">Ve a tu perfil de Habbo, edita tu misión y pega el código. Luego haz clic en verificar.</p>
                    </div>
                    <button id="verify-account-btn" class="royal-gradient text-black px-8 py-3 rounded-full font-semibold hover:scale-105 transition-transform w-full">
                        Verificar Cuenta
                    </button>
                </div></form>
                <p class="text-gray-400 text-sm mt-4">Already have an account? <button id="switch-to-login" class="text-yellow-400 hover:underline">Login here</button></p>
            </div>
        </div>
    </div>

    <!-- Custom JavaScript -->
    <script src="js/main.js"></script>
</body>
</html>
</replit_final_file>