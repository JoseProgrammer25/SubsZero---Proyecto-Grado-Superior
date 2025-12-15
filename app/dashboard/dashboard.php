<?php
session_start();
// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Obtener datos del usuario de la sesión
$user_id = $_SESSION['user_id'];

// Conectar a la base de datos para obtener información actualizada del usuario
require_once '../config/db.php';

// Obtener datos actualizados del usuario desde la base de datos
$stmt = $conn->prepare("SELECT username, email, role_id FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

$username = $user_data['username'] ?? $_SESSION['username'];
$email = $user_data['email'] ?? $_SESSION['email'];
$role_id = $user_data['role_id'] ?? 2;

// Determinar el rol del usuario
switch ($role_id) {
    case 1:
        $role_name = 'Admin';
        $role_badge_color = 'bg-blue-700';
        break;
    case 3:
        $role_name = 'Premium';
        $role_badge_color = 'bg-gradient-to-r from-yellow-500 to-amber-600';
        break;
    default:
        $role_name = 'Usuario';
        $role_badge_color = 'bg-gray-600';
}

// Obtener la inicial del nombre de usuario
$user_initial = strtoupper(substr($username, 0, 1));

// Obtener la página actual
$current_page = basename($_SERVER['PHP_SELF']);

// Obtener estadísticas del usuario
try {
    // Contar suscripciones activas del usuario
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM subscriptions WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $subscriptions_count = $result->fetch_assoc()['total'];
    $stmt->close();
    
    // Calcular gasto mensual (suma de todos los precios de suscripciones)
    $stmt = $conn->prepare("SELECT SUM(price) as total FROM subscriptions WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $monthly_expense = $result->fetch_assoc()['total'] ?? 0;
    $stmt->close();
    
    // Calcular gasto anual
    $annual_expense = $monthly_expense * 12;
    
    // Obtener el próximo pago (suscripción con la fecha más cercana)
    $stmt = $conn->prepare("
        SELECT name, next_billing_date, price 
        FROM subscriptions 
        WHERE user_id = ? AND next_billing_date >= CURDATE()
        ORDER BY next_billing_date ASC 
        LIMIT 1
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $next_payment = $result->fetch_assoc();
    $stmt->close();
    
    // Formatear la fecha del próximo pago
    if ($next_payment) {
        $date = new DateTime($next_payment['next_billing_date']);
        setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'spanish');
        $next_payment_date = $date->format('d M');
        $next_payment_name = $next_payment['name'];
    } else {
        $next_payment_date = '-';
        $next_payment_name = 'Sin pagos próximos';
    }
    
} catch (Exception $e) {
    // En caso de error, valores por defecto
    $subscriptions_count = 0;
    $monthly_expense = 0;
    $annual_expense = 0;
    $next_payment_date = '-';
    $next_payment_name = 'Error al cargar';
    // Para debug (comentar en producción)
    // echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SubsZero</title>
    <link rel="icon" href="../../assets/favicon.ico">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <!-- Overlay para móvil -->
        <div id="overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden" onclick="toggleSidebar()"></div>

        <!-- Barra lateral -->
        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-white shadow-lg flex flex-col transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
            <!-- Logo -->
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-700">
                        <span class="text-xl font-bold text-white">S</span>
                    </div>
                    <span class="ml-3 text-xl font-bold text-gray-900">SubsZero</span>
                </div>
                <!-- Botón cerrar sidebar en móvil -->
                <button onclick="toggleSidebar()" class="lg:hidden text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Información del usuario -->
            <div class="p-4 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full <?php echo ($role_id == 3) ? 'bg-gradient-to-br from-yellow-400 to-amber-600' : 'bg-blue-100'; ?> <?php echo ($role_id == 3) ? 'text-white' : 'text-blue-700'; ?> font-semibold text-lg flex-shrink-0">
                        <?php echo $user_initial; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate"><?php echo htmlspecialchars($username); ?></p>
                        <p class="text-xs text-gray-500 truncate"><?php echo htmlspecialchars($email); ?></p>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $role_badge_color; ?> text-white">
                        <?php if ($role_id == 3): ?>
                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        <?php else: ?>
                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                <circle cx="10" cy="10" r="8"/>
                            </svg>
                        <?php endif; ?>
                        <?php echo $role_name; ?>
                    </span>
                </div>
            </div>

            <!-- Menú de navegación -->
            <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                <a href="dashboard.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo ($current_page == 'dashboard.php') ? 'bg-blue-700 text-white' : 'text-gray-700 hover:bg-gray-50'; ?>">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0 
                        <?php echo ($current_page == 'dashboard.php') ? 'text-white' : 'text-gray-500'; ?> 
                        " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
                
                <a href="vistas/subscriptions.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo ($current_page == 'subscriptions.php') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50'; ?>">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0 <?php echo ($current_page == 'subscriptions.php') ? 'text-gray-900' : 'text-gray-500'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Suscripciones
                </a>
                
                <a href="vistas/statistics.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo ($current_page == 'statistics.php') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50'; ?>">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0 <?php echo ($current_page == 'statistics.php') ? 'text-gray-900' : 'text-gray-500'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Estadísticas
                </a>
                
                <a href="vistas/notifications.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo ($current_page == 'notifications.php') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50'; ?>">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0 <?php echo ($current_page == 'notifications.php') ? 'text-gray-900' : 'text-gray-500'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    Notificaciones
                </a>
                
                <a href="vistas/forums.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo ($current_page == 'forums.php') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50'; ?>">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0 <?php echo ($current_page == 'forums.php') ? 'text-gray-900' : 'text-gray-500'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Foros
                </a>
                
                <a href="vistas/settings.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo ($current_page == 'settings.php') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50'; ?>">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0 <?php echo ($current_page == 'settings.php') ? 'text-gray-900' : 'text-gray-500'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Configuración
                </a>
            </nav>

            <!-- Botón de cerrar sesión -->
            <div class="p-4 border-t border-gray-200">
                <a href="../auth/logout.php" class="flex items-center px-3 py-2.5 text-sm font-medium text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Cerrar Sesión
                </a>
            </div>
        </aside>

        <!-- Contenido principal -->
        <main class="flex-1 w-full lg:w-auto">
            <!-- Header móvil -->
            <div class="lg:hidden bg-white shadow-sm sticky top-0 z-30">
                <div class="flex items-center justify-between p-4">
                    <button onclick="toggleSidebar()" class="text-gray-500 hover:text-gray-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div class="flex items-center">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-700">
                            <span class="text-lg font-bold text-white">S</span>
                        </div>
                        <span class="ml-2 text-lg font-bold text-gray-900">SubsZero</span>
                    </div>
                    <div class="w-6"></div> <!-- Espaciador para centrar el logo -->
                </div>
            </div>

            <!-- Contenido -->
            <div class="p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    <!-- Encabezado -->
                    <div class="mb-8">
                        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Dashboard</h1>
                        <p class="text-gray-600 text-base sm:text-lg">Bienvenido de nuevo, <span class="font-semibold"><?php echo htmlspecialchars($username); ?></span></p>
                    </div>

                    <!-- Tarjetas de estadísticas -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
                        <!-- Suscripciones Activas -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div class="flex items-start justify-between mb-4">
                                <h3 class="text-sm font-medium text-gray-600">Suscripciones Activas</h3>
                                <div class="p-2 bg-gray-100 rounded-lg">
                                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="mb-1">
                                <span class="text-3xl font-bold text-gray-900"><?php echo $subscriptions_count; ?></span>
                            </div>
                            <p class="text-sm text-gray-500">0 inactivas</p>
                        </div>

                        <!-- Gasto Mensual -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div class="flex items-start justify-between mb-4">
                                <h3 class="text-sm font-medium text-gray-600">Gasto Mensual</h3>
                                <div class="p-2 bg-gray-100 rounded-lg">
                                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="mb-1">
                                <span class="text-3xl font-bold text-gray-900">€<?php echo number_format($monthly_expense, 2, '.', ','); ?></span>
                            </div>
                            <p class="text-sm text-gray-500">Promedio por mes</p>
                        </div>

                        <!-- Gasto Anual -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div class="flex items-start justify-between mb-4">
                                <h3 class="text-sm font-medium text-gray-600">Gasto Anual</h3>
                                <div class="p-2 bg-gray-100 rounded-lg">
                                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="mb-1">
                                <span class="text-3xl font-bold text-gray-900">€<?php echo number_format($annual_expense, 2, '.', ','); ?></span>
                            </div>
                            <p class="text-sm text-gray-500">Total proyectado</p>
                        </div>

                        <!-- Próximo Pago -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div class="flex items-start justify-between mb-4">
                                <h3 class="text-sm font-medium text-gray-600">Próximo Pago</h3>
                                <div class="p-2 bg-gray-100 rounded-lg">
                                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="mb-1">
                                <span class="text-3xl font-bold text-gray-900"><?php echo $next_payment_date; ?></span>
                            </div>
                            <p class="text-sm text-gray-500 truncate"><?php echo htmlspecialchars($next_payment_name); ?></p>
                        </div>
                    </div>

                    <!-- Sección de Próximos Pagos -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Próximos Pagos -->
                        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div class="mb-6">
                                <h2 class="text-2xl font-bold text-gray-900">Próximos Pagos</h2>
                                <p class="text-gray-600 mt-1">Tus suscripciones que vencen pronto</p>
                            </div>

                            <?php
                            // Obtener todas las próximas suscripciones
                            $stmt = $conn->prepare("
                                SELECT name, next_billing_date, price 
                                FROM subscriptions 
                                WHERE user_id = ? AND next_billing_date >= CURDATE()
                                ORDER BY next_billing_date ASC
                            ");
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $upcoming_payments = $result->fetch_all(MYSQLI_ASSOC);
                            $stmt->close();

                            if (count($upcoming_payments) > 0):
                                foreach ($upcoming_payments as $payment):
                                    $date = new DateTime($payment['next_billing_date']);
                                    $now = new DateTime();
                                    $diff = $now->diff($date);
                                    $days = $diff->days;
                                    
                                    // Determinar si es en el pasado o futuro
                                    if ($date < $now) {
                                        $days_text = "Hace " . $days . " días";
                                    } else {
                                        $days_text = "En " . ($days == 0 ? "hoy" : ($days == 1 ? "1 día" : $days . " días"));
                                    }
                                    
                                    // Formatear fecha
                                    setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'spanish');
                                    $formatted_date = strftime('%d de %B de %Y', $date->getTimestamp());
                                    // Fallback si strftime no funciona
                                    if (!$formatted_date) {
                                        $months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 
                                                   'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
                                        $formatted_date = $date->format('d') . ' de ' . $months[$date->format('n') - 1] . ' de ' . $date->format('Y');
                                    }
                            ?>
                                <div class="border border-gray-200 rounded-xl p-4 mb-3 hover:border-gray-300 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($payment['name']); ?></h3>
                                            <p class="text-sm text-gray-600 mt-1"><?php echo $formatted_date; ?></p>
                                        </div>
                                        <div class="text-right ml-4">
                                            <p class="text-xl font-bold text-gray-900">€<?php echo number_format($payment['price'], 2, '.', ','); ?></p>
                                            <p class="text-sm text-gray-500 mt-1"><?php echo $days_text; ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                                endforeach;
                            else:
                            ?>
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="mt-4 text-gray-600">No tienes próximos pagos</p>
                                    <p class="text-sm text-gray-500 mt-1">Agrega suscripciones para ver tus próximos pagos aquí</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Acciones Rápidas -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <div class="mb-6">
                                <h2 class="text-2xl font-bold text-gray-900">Acciones Rápidas</h2>
                                <p class="text-gray-600 mt-1">Gestiona tu cuenta y suscripciones</p>
                            </div>

                            <div class="space-y-3">
                                <a href="vistas/subscriptions.php" class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <svg class="mr-3 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    Añadir Nueva Suscripción
                                </a>

                                <a href="vistas/statistics.php" class="flex items-center w-full px-4 py-3 text-left text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <svg class="mr-3 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    Ver Estadísticas Detalladas
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

        // Cerrar sidebar al hacer clic en un enlace en móvil
        document.querySelectorAll('#sidebar a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    toggleSidebar();
                }
            });
        });

        // Cerrar sidebar al cambiar de orientación
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                document.getElementById('sidebar').classList.remove('-translate-x-full');
                document.getElementById('overlay').classList.add('hidden');
            }
        });
    </script>
</body>
</html>