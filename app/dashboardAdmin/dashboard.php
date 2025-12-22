<?php
session_start();
// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Conectar a la base de datos
require_once '../config/db.php'; // Asegúrate de que esta ruta sea correcta

// --- BLOQUE DE VERIFICACIÓN DE ADMIN ---
$user_id = $_SESSION['user_id'];
$stmt_role = $conn->prepare("
    SELECT a.user_id as is_admin, u.username, u.email, up.profile_pic_url
    FROM users u
    LEFT JOIN admin_users a ON u.id = a.user_id
    LEFT JOIN user_profiles up ON u.id = up.user_id
    WHERE u.id = ?
");
$stmt_role->bind_param("i", $user_id);
$stmt_role->execute();
$result_role = $stmt_role->get_result();
$user_data = $result_role->fetch_assoc();
$stmt_role->close();

// Si no es admin, redirigir
if ($user_data['is_admin'] === null) {
    header("Location: /dashboard/user_dashboard.php"); // Asegúrate de que esta ruta sea correcta
    exit();
}

$username = $user_data['username'] ?? 'Admin';
$email = $user_data['email'] ?? 'admin@subszero.com';
$profile_pic = $user_data['profile_pic_url'] ?? null; 

$role = 'admin';
$role_name = 'Administrador';
$role_badge_color = 'bg-blue-600';

$user_initial = strtoupper(substr($username, 0, 1));
$current_page = basename($_SERVER['PHP_SELF']);

// --- ESTADÍSTICAS DE ADMINISTRADOR ---
try {
    // 1. Total de Usuarios Registrados
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users");
    $stmt->execute();
    $total_users = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    // 2. Total de Suscripciones Activas (en todo el sistema)
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM subscriptions");
    $stmt->execute();
    $total_subscriptions = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();
    
    // 3. Gasto Mensual Total Proyectado del Sistema
    $stmt = $conn->prepare("SELECT SUM(price) as total FROM subscriptions");
    $stmt->execute();
    $monthly_system_expense = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();
    
    // 4. Categoría Más Popular (la que tiene más suscripciones)
    $stmt = $conn->prepare("
        SELECT c.name, COUNT(s.id) as count
        FROM subscriptions s
        JOIN categories c ON s.category_id = c.id
        GROUP BY c.id
        ORDER BY count DESC
        LIMIT 1
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $popular_category = $result->fetch_assoc();
    $popular_category_name = $popular_category['name'] ?? 'N/A';
    $popular_category_count = $popular_category['count'] ?? 0;
    $stmt->close();

    // 5. Conteo de usuarios por rol
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM admin_users");
    $stmt->execute();
    $total_admins = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM premium_users");
    $stmt->execute();
    $total_premiums = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    $stmt->close();

    // Calcula usuarios estándar (total - admins - premiums)
    $total_standard = $total_users - $total_admins - $total_premiums;
    if ($total_standard < 0) $total_standard = 0; // Prevenir negativos

} catch (Exception $e) {
    // Valores por defecto en caso de error de DB
    $total_users = 0;
    $total_subscriptions = 0;
    $monthly_system_expense = 0;
    $popular_category_name = 'Error';
    $popular_category_count = 0;
    $total_admins = 0;
    $total_premiums = 0;
    $total_standard = 0;
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SubsZero</title>
    <link rel="icon" href="../../assets/favicon.ico">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <div id="overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden" onclick="toggleSidebar()"></div>

        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-white shadow-xl flex flex-col transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between bg-white"> 
                <div class="flex items-center">
                    <img src="../../assets/favicon.ico" width="64" height="64" alt="Icono de la app SubsZero">
                    <span class="ml-3 text-xl font-bold text-gray-900">SubsZero</span>
                </div>
                <button onclick="toggleSidebar()" class="lg:hidden text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-4 bg-white border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 text-white font-semibold text-lg flex-shrink-0 border-2 border-blue-300">
                        <?php if (isset($profile_pic) && $profile_pic !== 'default_avatar.png' && $profile_pic !== '' && $profile_pic !== null): ?>
                            <img class="h-10 w-10 rounded-full object-cover" src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Avatar">
                        <?php else: ?>
                            <?php echo $user_initial; ?>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate"><?php echo htmlspecialchars($username); ?></p>
                        <p class="text-xs text-gray-500 truncate"><?php echo htmlspecialchars($email); ?></p>
                    </div>
                </div>
                <div class="mt-3 flex items-center justify-between"> 
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $role_badge_color; ?> text-white">
                        <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <?php echo $role_name; ?>
                    </span>
                    <a href="../../auth/logout.php" class="text-gray-500 hover:text-blue-600 transition-colors" title="Cerrar Sesión">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </a>
                </div>
            </div>

            <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                <a href="dashboard.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo ($current_page == 'dashboard.php') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100 hover:text-blue-600'; ?>">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0 
                        <?php echo ($current_page == 'dashboard.php') ? 'text-white' : 'text-blue-600'; ?> 
                        " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Vista General (Admin)
                </a>
                
                <a href="vistas/users.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo ($current_page == 'users.php') ? 'bg-blue-100 text-blue-900' : 'text-gray-700 hover:bg-gray-100 hover:text-blue-600'; ?>">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0 <?php echo ($current_page == 'users.php') ? 'text-blue-600' : 'text-gray-500'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20v-2c0-.656-.126-1.283-.356-1.857m0 0a2.001 2.001 0 00-3.132 0M9 20h9v-2a3 3 0 00-9-2.143M9 20v-2a3 3 0 013-3h5.356m1.857-4.143a4 4 0 11-8 0 4 4 0 018 0zm-8 0a4 4 0 10-8 0 4 4 0 008 0z"/>
                    </svg>
                    Gestión de Usuarios
                </a>
                
                <a href="vistas/categories.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo ($current_page == 'categories.php') ? 'bg-blue-100 text-blue-900' : 'text-gray-700 hover:bg-gray-100 hover:text-blue-600'; ?>">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0 <?php echo ($current_page == 'categories.php') ? 'text-blue-600' : 'text-gray-500'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h.01M7 11h.01M7 15h.01M7 19h.01M17 7h.01M17 3h.01M17 11h.01M17 15h.01M17 19h.01M10 13a3 3 0 100-6 3 3 0 000 6zM10 17a3 3 0 100-6 3 3 0 000 6z"/>
                    </svg>
                    Gestión de Categorías
                </a>
                
                <a href="vistas/moderation.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo ($current_page == 'moderation.php') ? 'bg-blue-100 text-blue-900' : 'text-gray-700 hover:bg-gray-100 hover:text-blue-600'; ?>">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0 <?php echo ($current_page == 'moderation.php') ? 'text-blue-600' : 'text-gray-500'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944c2.816 0 5.56 1.056 7.618 3.016zM12 21.056a11.955 11.955 0 01-7.618-3.016l7.618 3.016z"/>
                    </svg>
                    Moderación (Foros)
                </a>
                
                <a href="vistas/reports.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo ($current_page == 'reports.php') ? 'bg-blue-100 text-blue-900' : 'text-gray-700 hover:bg-gray-100 hover:text-blue-600'; ?>">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0 <?php echo ($current_page == 'reports.php') ? 'text-blue-600' : 'text-gray-500'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.92 9.92 0 0112 3a9.96 9.96 0 019 12.053M10 13l2-2 3-4m3.055-7.945A9.92 9.92 0 0112 3c-5.523 0-10 4.477-10 10s4.477 10 10 10a9.96 9.96 0 01-9-12.053"/>
                    </svg>
                    Reportes y Análisis
                </a>
                
                <a href="vistas/settings.php?section=premium_pricing" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo ($current_page == 'settings.php') ? 'bg-blue-100 text-blue-900' : 'text-gray-700 hover:bg-gray-100 hover:text-blue-600'; ?>">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0 <?php echo ($current_page == 'settings.php') ? 'text-blue-600' : 'text-gray-500'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Config. Tarifa Premium
                </a>
            </nav>
            </aside>

        <main class="flex-1 w-full lg:w-auto">
            <div class="lg:hidden bg-white shadow-sm sticky top-0 z-30">
                <div class="flex items-center justify-between p-4">
                    <button onclick="toggleSidebar()" class="text-gray-500 hover:text-gray-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div class="flex items-center">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600">
                            <span class="text-lg font-bold text-white">S</span>
                        </div>
                        <span class="ml-2 text-lg font-bold text-gray-900">SubsZero Admin</span>
                    </div>
                    <div class="w-6"></div> 
                </div>
            </div>

            <div class="p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    <div class="mb-8 border-b-2 border-blue-500 pb-4">
                        <h1 class="text-3xl sm:text-4xl font-extrabold text-blue-600 mb-2">Panel de Administración</h1>
                        <p class="text-gray-600 text-base sm:text-lg">Vista global y gestión del sistema. Bienvenido, <span class="font-bold text-blue-600"><?php echo htmlspecialchars($username); ?></span>.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                        
                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 transition transform hover:scale-[1.02]">
                            <div class="flex items-start justify-between mb-4">
                                <h3 class="text-sm font-medium text-gray-600">Usuarios Totales</h3>
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20v-2c0-.656-.126-1.283-.356-1.857m0 0a2.001 2.001 0 00-3.132 0M9 20h9v-2a3 3 0 00-9-2.143M9 20v-2a3 3 0 013-3h5.356m1.857-4.143a4 4 0 11-8 0 4 4 0 018 0zm-8 0a4 4 0 10-8 0 4 4 0 008 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <span class="text-3xl font-bold text-gray-900"><?php echo $total_users; ?></span>
                            <p class="text-sm text-gray-500 mt-1">Usuarios registrados en el sistema</p>
                        </div>

                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 transition transform hover:scale-[1.02]">
                            <div class="flex items-start justify-between mb-4">
                                <h3 class="text-sm font-medium text-gray-600">Suscripciones Totales</h3>
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                </div>
                            </div>
                            <span class="text-3xl font-bold text-gray-900"><?php echo $total_subscriptions; ?></span>
                            <p class="text-sm text-gray-500 mt-1">Suscripciones activas en todas las cuentas</p>
                        </div>

                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 transition transform hover:scale-[1.02]">
                            <div class="flex items-start justify-between mb-4">
                                <h3 class="text-sm font-medium text-gray-600">Gasto Global Mensual</h3>
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.318 2.845.895l-.763 1.144M12 8V5m0 16v-3m0-12c-4.418 0-8 3.582-8 8s3.582 8 8 8 8-3.582 8-8-3.582-8-8-8z"/>
                                    </svg>
                                </div>
                            </div>
                            <span class="text-3xl font-bold text-gray-900">€<?php echo number_format($monthly_system_expense, 2, '.', ','); ?></span>
                            <p class="text-sm text-gray-500 mt-1">Costo total de las suscripciones al mes</p>
                        </div>

                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 transition transform hover:scale-[1.02]">
                            <div class="flex items-start justify-between mb-4">
                                <h3 class="text-sm font-medium text-gray-600">Categoría Más Popular</h3>
                                <div class="p-2 bg-blue-100 rounded-lg">
                                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                            </div>
                            <span class="text-2xl font-bold text-gray-900 truncate"><?php echo htmlspecialchars($popular_category_name); ?></span>
                            <p class="text-sm text-gray-500 mt-1">Con <?php echo $popular_category_count; ?> suscripcion/es</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        
                        <div class="lg:col-span-2 bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                            <div class="mb-6 border-b pb-4">
                                <h2 class="text-2xl font-bold text-gray-900">Estadísticas Detalladas</h2>
                                <p class="text-gray-600 mt-1">Distribución visual de roles de usuarios.</p>
                            </div>
                            
                            <canvas id="userRolesChart" width="400" height="200"></canvas>
                            
                            <div class="mt-4 text-sm text-gray-500">
                                *El gráfico muestra la proporción de Administradores, Usuarios Premium y Usuarios Estándar.
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                            <div class="mb-6 border-b pb-4">
                                <h2 class="text-2xl font-bold text-gray-900">Acciones Rápidas del Admin</h2>
                                <p class="text-gray-600 mt-1">Tareas de gestión esenciales para el sistema.</p>
                            </div>

                            <div class="space-y-3">
                                
                                <a href="vistas/users.php" class="flex items-center w-full px-4 py-3 text-left text-sm font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-500 transition-colors shadow-md">
                                    <svg class="mr-3 h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292 4 4 0 010-5.292zM15 13a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6h-6z"/>
                                    </svg>
                                    Gestionar Cuentas de Usuarios
                                </a>

                                <a href="vistas/settings.php?action=edit_premium_price" class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-blue-50 transition-colors">
                                    <svg class="mr-3 h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.318 2.845.895l-.763 1.144M12 8V5m0 16v-3m0-12c-4.418 0-8 3.582-8 8s3.582 8 8 8 8-3.582 8-8-3.582-8-8-8z"/>
                                    </svg>
                                    Modificar Precio Premium
                                </a>

                                <a href="vistas/notifications.php?action=create" class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-blue-50 transition-colors">
                                    <svg class="mr-3 h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Publicar Anuncio Global
                                </a>

                                <a href="vistas/reports.php?type=errors" class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-blue-50 transition-colors">
                                    <svg class="mr-3 h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Revisar Reportes de Errores
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        document.querySelectorAll('#sidebar a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    toggleSidebar();
                }
            });
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                document.getElementById('sidebar').classList.remove('-translate-x-full');
                document.getElementById('overlay').classList.add('hidden');
            }
        });
    </script>

    <script>
        // Inicialización del gráfico de roles
        const ctx = document.getElementById('userRolesChart').getContext('2d');
        const userRolesChart = new Chart(ctx, {
            type: 'bar', 
            data: {
                labels: ['Administradores', 'Premium', 'Estándar'],
                datasets: [{
                    label: 'Número de Usuarios',
                    data: [<?php echo $total_admins; ?>, <?php echo $total_premiums; ?>, <?php echo $total_standard; ?>],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)', // Azul (Admin)
                        'rgba(16, 185, 129, 0.8)', // Verde (Premium)
                        'rgba(156, 163, 175, 0.8)' // Gris (Estándar)
                    ],
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(156, 163, 175, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad de Usuarios'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false 
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y;
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>