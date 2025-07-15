<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Include database configuration
include '../config/database.php';

try {
    $database = new Database(__DIR__ . '/../config/config.php');
    $db = $database->getConnection();

    // Get user information
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user) {
        session_destroy();
        header('Location: ../index.php');
        exit();
    }

} catch (Exception $e) {
    error_log('Dashboard error: ' . $e->getMessage());
    $error = 'Database connection error';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo htmlspecialchars($dynamicTitle ?? 'Reino de Habbo'); ?></title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Cinzel+Decorative:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/main.css">
    <style>
        /* Custom styles for this page */
    </style>
</head>
<body class="medieval-bg">
    <!-- Navigation -->
    <nav class="glass border-b border-white/20 sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="crown-icon text-yellow-400">
                        <svg class="icon-large" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 6l4 6h5l-7-11zm0 0L8 12H3l7-11zM2 12h20l-2 7H4z"/>
                        </svg>
                    </div>
                    <?php
                    // Get dynamic title from database
                    try {
                        $contentStmt = $db->prepare("SELECT content_value FROM site_content WHERE content_key = 'main_title'");
                        $contentStmt->execute();
                        $titleResult = $contentStmt->fetch();
                        $dynamicTitle = $titleResult ? $titleResult['content_value'] : 'Reino de Habbo';
                    } catch (Exception $e) {
                        $dynamicTitle = 'Reino de Habbo';
                    }
                    ?>
                    <h1 class="text-2xl font-cinzel-decorative font-bold text-yellow-400"><?php echo htmlspecialchars($dynamicTitle); ?></h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-300">Welcome, <strong class="text-yellow-400"><?php echo htmlspecialchars($user['habbo_username']); ?></strong></span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div id="sidebar" class="sidebar expanded fixed left-0 top-20 bottom-0 glass border-r border-white/20 z-40 overflow-hidden flex flex-col">
        <div class="p-4 flex-1">
            <div class="space-y-1">
                <!-- Dashboard -->
                <div class="sidebar-item active p-3 cursor-pointer" data-section="dashboard">
                    <div class="flex items-center space-x-3">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 13h1v7c0 1.103.897 2 2 2h12c1.103 0 2-.897 2-2v-7h1a1 1 0 0 0 .707-1.707l-9-9a.999.999 0 0 0-1.414 0l-9 9A1 1 0 0 0 3 13z"/>
                        </svg>
                        <span class="menu-text font-medium">Dashboard</span>
                    </div>
                </div>

                <!-- Perfil -->
                <div class="sidebar-item p-3 cursor-pointer" data-section="perfil">
                    <div class="flex items-center space-x-3">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                        <span class="menu-text font-medium">Mi Perfil</span>
                    </div>
                </div>

                <!-- Editor de Contenido -->
                <?php if (in_array($user['role'], ['administrador', 'desarrollador'])): ?>
                <div class="sidebar-item p-3 cursor-pointer" data-section="editor">
                    <div class="flex items-center space-x-3">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                        </svg>
                        <span class="menu-text font-medium">Editor de Contenido</span>
                    </div>
                </div>

                <!-- Editor de Logo -->
                <div class="sidebar-item p-3 cursor-pointer" data-section="logo">
                    <div class="flex items-center space-x-3">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                        </svg>
                        <span class="menu-text font-medium">Editor de Logo</span>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- Logout at bottom -->
        <div class="p-4 border-t border-white/20">
            <div class="sidebar-item p-3 cursor-pointer" id="logout-btn" data-action="logout">
                <div class="flex items-center space-x-3">
                    <svg class="icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.59L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                    </svg>
                    <span class="menu-text font-medium text-red-400">Logout</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main id="main-content" class="main-content ml-64 p-8">
        <?php if (isset($error)): ?>
            <div class="bg-red-600 text-white p-4 rounded-lg mb-6">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Dashboard Section -->
        <section id="dashboard-section" class="content-section">
            <div class="glass rounded-2xl p-8 mb-8">
                <div class="text-center">
                    <div class="text-yellow-400 mb-4">
                        <svg class="w-16 h-16 mx-auto" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 13h1v7c0 1.103.897 2 2 2h12c1.103 0 2-.897 2-2v-7h1a1 1 0 0 0 .707-1.707l-9-9a.999.999 0 0 0-1.414 0l-9 9A1 1 0 0 0 3 13z"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-cinzel-decorative font-bold text-yellow-400 mb-2">
                        Dashboard
                    </h2>
                    <p class="text-gray-300">Panel de control principal</p>
                    <div class="mt-8 p-8 bg-white/5 rounded-xl border border-white/10">
                        <p class="text-gray-400">Esta sección está en desarrollo...</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Perfil Section -->
        <section id="perfil-section" class="content-section hidden">
            <!-- Profile Header -->
            <div class="glass rounded-2xl p-8 mb-8">
                <div class="text-center mb-8">
                    <div class="text-yellow-400 mb-4">
                        <svg class="w-16 h-16 mx-auto" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-cinzel-decorative font-bold text-yellow-400 mb-2">
                        Mi Perfil
                    </h2>
                    <p class="text-gray-300">Reino de Habbo</p>
                </div>
            </div>

            <!-- Professional User Profile Card -->
            <div class="glass rounded-2xl p-8 mb-8">
                <div class="flex flex-col lg:flex-row items-center lg:items-start gap-8">
                    <!-- Avatar Section -->
                    <div class="flex-shrink-0">
                        <div class="relative avatar-container">
                            <div class="w-48 h-64 rounded-2xl overflow-hidden border-3 border-yellow-400/30 shadow-2xl bg-gradient-to-br from-gray-800 to-gray-900 avatar-frame flex items-center justify-center">
                                <img src="https://www.habbo.es/habbo-imaging/avatarimage?img_format=png&user=<?php echo urlencode($user['habbo_username']); ?>&direction=2&head_direction=2&size=l&gesture=std&action=wave" 
                                     alt="Avatar de <?php echo htmlspecialchars($user['habbo_username']); ?>" 
                                     class="max-w-full max-h-full object-contain avatar-image"
                                     id="user-avatar"
                                     data-username="<?php echo htmlspecialchars($user['habbo_username']); ?>"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-full h-full bg-gradient-to-br from-gray-800 to-gray-900 flex items-center justify-center text-white font-bold text-6xl" style="display: none;">
                                    <?php echo strtoupper(substr($user['habbo_username'], 0, 1)); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Information -->
                    <div class="flex-1 w-full">
                        <div class="text-center lg:text-left mb-6">
                            <h3 class="text-4xl font-cinzel font-bold text-white mb-2">
                                <?php echo htmlspecialchars($user['habbo_username']); ?>
                            </h3>
                            <p class="text-gray-400 text-lg">Usuario del Reino de Habbo</p>
                        </div>

                        <!-- Professional Information Grid -->
                        <div class="grid md:grid-cols-1 gap-6">
                            <div class="space-y-4">
                                <!-- Mission Section -->
                                <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                                    <div class="flex items-center gap-4">
                                        <span class="text-gray-400 font-medium">Misión:</span>
                                        <div class="flex items-center gap-2">
                                            <span class="text-white font-semibold" id="mission-text">Marroquí [R;M]</span>
                                            <button onclick="copyMission()" class="text-yellow-400 hover:text-yellow-300 transition-colors p-1 rounded" title="Copiar misión">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Rank Section -->
                                <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                                    <div class="flex items-center gap-4">
                                        <span class="text-gray-400 font-medium">Rango:</span>
                                        <?php 
                                        $roleNames = [
                                            'usuario' => 'Usuario',
                                            'operador' => 'Operador', 
                                            'administrador' => 'Administrador',
                                            'desarrollador' => 'Desarrollador'
                                        ];
                                        
                                        $roleColors = [
                                            'usuario' => 'text-blue-400 bg-blue-400/10 border-blue-400/20',
                                            'operador' => 'text-green-400 bg-green-400/10 border-green-400/20',
                                            'administrador' => 'text-red-400 bg-red-400/10 border-red-400/20',
                                            'desarrollador' => 'text-purple-400 bg-purple-400/10 border-purple-400/20'
                                        ];
                                        
                                        $roleName = $roleNames[$user['role']] ?? ucfirst($user['role']);
                                        $roleColorClass = $roleColors[$user['role']] ?? 'text-yellow-400 bg-yellow-400/10 border-yellow-400/20';
                                        ?>
                                        <span class="<?php echo $roleColorClass; ?> font-semibold px-3 py-1 rounded-full border">
                                            <?php echo htmlspecialchars($roleName); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Editor de Contenido Section -->
        <section id="editor-section" class="content-section hidden">
            <div class="glass rounded-2xl p-8 mb-8">
                <div class="text-center mb-8">
                    <div class="text-yellow-400 mb-4">
                        <svg class="w-16 h-16 mx-auto" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-cinzel-decorative font-bold text-yellow-400 mb-2">
                        Editor de Contenido
                    </h2>
                    <p class="text-gray-300">Edita los textos y títulos de la página principal</p>
                </div>
            </div>

            <?php if (in_array($user['role'], ['administrador', 'desarrollador'])): ?>
            <!-- Content Editor Form -->
            <div class="glass rounded-2xl p-8">
                <form id="content-editor-form" class="space-y-6">
                    <!-- Título Principal -->
                    <div>
                        <label class="block text-gray-300 text-sm font-medium mb-2">Título Principal del Reino</label>
                        <input type="text" id="main-title" 
                               class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white border border-gray-600 focus:border-yellow-400 focus:outline-none"
                               placeholder="Welcome to Reino de Habbo" 
                               value="Welcome to Reino de Habbo">
                    </div>

                    <!-- Descripción Principal -->
                    <div>
                        <label class="block text-gray-300 text-sm font-medium mb-2">Descripción Principal</label>
                        <textarea id="main-description" rows="4"
                                  class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white border border-gray-600 focus:border-yellow-400 focus:outline-none"
                                  placeholder="Descripción del reino...">Enter a legendary medieval kingdom where honor defines your destiny. Rise through ancient ranks, forge powerful alliances, and command respect in the most prestigious virtual realm ever created. Your journey from commoner to royalty starts here.</textarea>
                    </div>

                    <!-- Características -->
                    <div>
                        <label class="block text-gray-300 text-sm font-medium mb-2">Características del Reino</label>
                        <div class="grid md:grid-cols-3 gap-4">
                            <div>
                                <input type="text" id="feature-1" 
                                       class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white border border-gray-600 focus:border-yellow-400 focus:outline-none"
                                       placeholder="Medieval Combat" 
                                       value="Medieval Combat">
                            </div>
                            <div>
                                <input type="text" id="feature-2" 
                                       class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white border border-gray-600 focus:border-yellow-400 focus:outline-none"
                                       placeholder="Kingdom Building" 
                                       value="Kingdom Building">
                            </div>
                            <div>
                                <input type="text" id="feature-3" 
                                       class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white border border-gray-600 focus:border-yellow-400 focus:outline-none"
                                       placeholder="Royal Hierarchy" 
                                       value="Royal Hierarchy">
                            </div>
                        </div>
                    </div>

                    <!-- Footer Text -->
                    <div>
                        <label class="block text-gray-300 text-sm font-medium mb-2">Texto del Footer</label>
                        <input type="text" id="footer-text" 
                               class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white border border-gray-600 focus:border-yellow-400 focus:outline-none"
                               placeholder="Una comunidad medieval virtual" 
                               value="Una comunidad medieval virtual">
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex gap-4 pt-4">
                        <button type="submit" 
                                class="px-6 py-3 rounded-lg royal-gradient text-black font-semibold hover:scale-105 transition-transform">
                            Guardar Cambios
                        </button>
                        <button type="button" id="reset-content" 
                                class="px-6 py-3 rounded-lg bg-gray-600 text-white hover:bg-gray-700 transition-colors">
                            Restablecer
                        </button>
                    </div>
                </form>
            </div>
            <?php else: ?>
            <!-- Access Denied Message -->
            <div class="glass rounded-2xl p-8">
                <div class="text-center">
                    <div class="text-red-400 mb-4">
                        <svg class="w-16 h-16 mx-auto" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-cinzel font-bold text-red-400 mb-2">
                        Acceso Denegado
                    </h3>
                    <p class="text-gray-300 mb-4">
                        Esta sección requiere permisos de administrador o desarrollador.
                    </p>
                    <p class="text-gray-400 text-sm">
                        Tu rango actual: <span class="text-yellow-400 font-semibold"><?php echo htmlspecialchars(ucfirst($user['role'])); ?></span>
                    </p>
                </div>
            </div>
            <?php endif; ?>
        </section>

        <!-- Editor de Logo Section -->
        <section id="logo-section" class="content-section hidden">
            <div class="glass rounded-2xl p-8 mb-8">
                <div class="text-center mb-8">
                    <div class="text-yellow-400 mb-4">
                        <svg class="w-16 h-16 mx-auto" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-cinzel-decorative font-bold text-yellow-400 mb-2">
                        Editor de Logo
                    </h2>
                    <p class="text-gray-300">Personaliza el logo de tu reino</p>
                </div>
            </div>

            <?php if (in_array($user['role'], ['administrador', 'desarrollador'])): ?>
            <!-- Logo Editor Form -->
            <div class="glass rounded-2xl p-8">
                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Upload Section -->
                    <div>
                        <h3 class="text-xl font-cinzel font-bold text-yellow-400 mb-4">Subir Nuevo Logo</h3>
                        <div class="space-y-4">
                            <div class="border-2 border-dashed border-gray-600 rounded-lg p-6 text-center hover:border-yellow-400 transition-colors">
                                <input type="file" id="logo-upload" accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/gif" class="hidden">
                                <label for="logo-upload" class="cursor-pointer">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                                    </svg>
                                    <p class="text-gray-300 mb-2">Haz clic para seleccionar un archivo</p>
                                    <p class="text-gray-500 text-sm">PNG, JPG, SVG, GIF hasta 2MB</p>
                                </label>
                            </div>
                            <div id="logo-preview" class="hidden">
                                <img id="preview-image" class="w-full h-32 object-contain bg-gray-800 rounded-lg" alt="Preview">
                            </div>
                            <button id="save-logo" class="w-full px-6 py-3 rounded-lg royal-gradient text-black font-semibold hover:scale-105 transition-transform disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                Guardar Logo
                            </button>
                        </div>
                    </div>

                    <!-- Current Logo Section -->
                    <div>
                        <h3 class="text-xl font-cinzel font-bold text-yellow-400 mb-4">Logo Actual</h3>
                        <div class="bg-gray-800 rounded-lg p-6 text-center">
                            <div id="current-logo" class="h-32 flex items-center justify-center">
                                <svg class="w-16 h-16 text-yellow-400" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 6l4 6h5l-7-11zm0 0L8 12H3l7-11zM2 12h20l-2 7H4z"/>
                                </svg>
                            </div>
                            <p class="text-gray-400 mt-4">Logo por defecto</p>
                        </div>
                        <div class="mt-4 space-y-2">
                            <button id="reset-logo" class="w-full px-6 py-3 rounded-lg bg-gray-600 text-white hover:bg-gray-700 transition-colors">
                                Restablecer Logo por Defecto
                            </button>
                            <button id="test-float-effect" class="w-full px-6 py-3 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                                Probar Efecto Flotante
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <!-- Access Denied Message -->
            <div class="glass rounded-2xl p-8">
                <div class="text-center">
                    <div class="text-red-400 mb-4">
                        <svg class="w-16 h-16 mx-auto" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-cinzel font-bold text-red-400 mb-2">
                        Acceso Denegado
                    </h3>
                    <p class="text-gray-300 mb-4">
                        Esta sección requiere permisos de administrador o desarrollador.
                    </p>
                    <p class="text-gray-400 text-sm">
                        Tu rango actual: <span class="text-yellow-400 font-semibold"><?php echo htmlspecialchars(ucfirst($user['role'])); ?></span>
                    </p>
                </div>
            </div>
            <?php endif; ?>
        </section>


    </main>

    <!-- Custom JavaScript -->
    <script src="../js/main.js"></script>
    <script>
        // Function to copy mission text
        function copyMission() {
            const missionText = document.getElementById('mission-text').textContent;
            navigator.clipboard.writeText(missionText).then(function() {
                // Show success feedback
                const button = event.target.closest('button');
                const originalHTML = button.innerHTML;
                button.innerHTML = `
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                    </svg>
                `;
                button.classList.add('text-green-400');
                
                setTimeout(() => {
                    button.innerHTML = originalHTML;
                    button.classList.remove('text-green-400');
                    button.classList.add('text-yellow-400');
                }, 1500);
            }).catch(function(err) {
                console.error('Error al copiar: ', err);
            });
        }
    </script>
</body>
</html>