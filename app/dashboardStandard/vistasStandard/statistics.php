<?php
session_start();
// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php"); 
    exit();
}

// Obtener datos del usuario de la sesión
$user_id = $_SESSION['user_id'];

// Conectar a la base de datos
require_once '../../config/db.php'; // Asegúrate de que la ruta sea correcta

// --- BLOQUE DE AUTENTICACIÓN Y ROL ---
$stmt = $conn->prepare("
    SELECT 
        u.username, u.email,
        a.user_id as is_admin, 
        p.user_id as is_premium,
        up.profile_pic_url
    FROM users u
    LEFT JOIN admin_users a ON u.id = a.user_id
    LEFT JOIN premium_users p ON u.id = p.user_id
    LEFT JOIN user_profiles up ON u.id = up.user_id
    WHERE u.id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

$username = $user_data['username'] ?? $_SESSION['username'];
$email = $user_data['email'] ?? $_SESSION['email'];
$profile_pic = $user_data['profile_pic_url'] ?? null;

if ($user_data['is_admin'] !== null) {
    $role = 'admin';
    $role_name = 'Admin';
    $role_badge_color = 'bg-blue-700';
} elseif ($user_data['is_premium'] !== null) {
    $role = 'premium';
    $role_name = 'Premium';
    $role_badge_color = 'bg-gradient-to-r from-yellow-500 to-amber-600';
} else {
    $role = 'user';
    $role_name = 'Usuario';
    $role_badge_color = 'bg-gray-600';
}
// Obtener la inicial del nombre de usuario
$user_initial = strtoupper(substr($username, 0, 1));
$current_page_script = basename(__FILE__); // Debe ser 'statistics.php'


$stats = [
    'GastoMensual' => 0.00,
    'GastoAnual' => 0.00,
    'SuscripcionesActivas' => 0,
    'PromedioPorSuscripcion' => 0.00,
    'AhorroPotencial' => 12.20, 
    'DistribucionGastos' => [],
    'DesgloseDetallado' => [],
    'ProyeccionGastos' => ['labels' => [], 'values' => []]
];

$sql_key_metrics = "
    SELECT
        SUM(CASE
            WHEN s.billing_cycle = 'anual' THEN s.price / 12.0
            WHEN s.billing_cycle = 'trimestral' THEN s.price / 3.0
            WHEN s.billing_cycle = 'semestral' THEN s.price / 6.0
            ELSE s.price
        END) AS GastoMensualTotal,
        (SUM(CASE
            WHEN s.billing_cycle = 'anual' THEN s.price / 12.0
            WHEN s.billing_cycle = 'trimestral' THEN s.price / 3.0
            WHEN s.billing_cycle = 'semestral' THEN s.price / 6.0
            ELSE s.price
        END)) * 12.0 AS GastoAnualProyectado,
        COUNT(s.id) AS SuscripcionesActivas
    FROM
        subscriptions s
    WHERE
        s.user_id = ?
";

if ($stmt = $conn->prepare($sql_key_metrics)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $GastoMensualTotal = (float)($row['GastoMensualTotal'] ?? 0.00);
        $SuscripcionesActivas = (int)($row['SuscripcionesActivas'] ?? 0);
        
        $stats['GastoMensual'] = $GastoMensualTotal;
        $stats['GastoAnual'] = (float)($row['GastoAnualProyectado'] ?? 0.00);
        $stats['SuscripcionesActivas'] = $SuscripcionesActivas;
        $stats['PromedioPorSuscripcion'] = $SuscripcionesActivas > 0 ? $GastoMensualTotal / $SuscripcionesActivas : 0.00;
    }
    $stmt->close();
}

$sql_distribution = "
    SELECT
        c.name AS Categoria,
        s.name AS Suscripcion,
        CASE
            WHEN s.billing_cycle = 'anual' THEN s.price / 12.0
            WHEN s.billing_cycle = 'trimestral' THEN s.price / 3.0
            WHEN s.billing_cycle = 'semestral' THEN s.price / 6.0
            ELSE s.price
        END AS GastoMensualAjustado
    FROM
        subscriptions s
    JOIN
        categories c ON s.category_id = c.id
    WHERE
        s.user_id = ?
    ORDER BY
        GastoMensualAjustado DESC
";

$gastos_por_categoria = [];
if ($stmt = $conn->prepare($sql_distribution)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $gasto = (float)$row['GastoMensualAjustado'];
        $categoria = $row['Categoria'];
        
        // Para el Desglose Detallado
        $stats['DesgloseDetallado'][] = [
            'categoria' => $categoria,
            'gasto' => $gasto
        ];
        
        // Para la Distribución de Gastos (Gráfico de Pastel)
        if (!isset($gastos_por_categoria[$categoria])) {
            $gastos_por_categoria[$categoria] = 0;
        }
        $gastos_por_categoria[$categoria] += $gasto;
    }
    $stmt->close();
}

$gasto_total = $stats['GastoMensual'];
foreach ($gastos_por_categoria as $categoria => $gasto) {
    $porcentaje = ($gasto_total > 0) ? ($gasto / $gasto_total) * 100 : 0;
    
    // Para el gráfico de Distribución y las etiquetas de categoría
    $stats['DistribucionGastos'][] = [
        'categoria' => $categoria,
        'gasto' => $gasto,
        'porcentaje' => round($porcentaje)
    ];
}


$GastoMensualBase = $stats['GastoMensual']; // Usamos el gasto mensual ya calculado
$projected_months = 12; // 6 meses pasados + 6 meses futuros
$month_names_short = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
$current_month_index = (int)date('n') - 1; 

for ($i = -6; $i < 6; $i++) {
    $month_label_index = ($current_month_index + $i) % 12;
    if ($month_label_index < 0) $month_label_index += 12;
    
    $stats['ProyeccionGastos']['labels'][] = $month_names_short[$month_label_index];
    $stats['ProyeccionGastos']['values'][] = number_format($GastoMensualBase, 2, '.', '');
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas - SubsZero</title>
    <link rel="icon" href="../../../assets/favicon.ico">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <style>
        /* Estilos específicos para la barra de navegación del teléfono */
        .modal { transition: opacity 0.25s ease; }
        .modal-content { transition: transform 0.25s ease; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <div id="overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden" onclick="toggleSidebar()"></div>

        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-white shadow-lg flex flex-col transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center">
                    <img src="../../../assets/favicon.ico" width="64" height="64" alt="Icono de la app SubsZero">
                    <span class="ml-3 text-xl font-bold text-gray-900">SubsZero</span>
                </div>
                <button onclick="toggleSidebar()" class="lg:hidden text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-4 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full <?php echo ($role == 'premium') ? 'bg-gradient-to-br from-yellow-400 to-amber-600' : 'bg-blue-100'; ?> <?php echo ($role == 'premium') ? 'text-white' : 'text-blue-700'; ?> font-semibold text-lg flex-shrink-0">
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
                <div class="mt-2 flex items-center justify-between">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $role_badge_color; ?> text-white">
                        <?php if ($role == 'premium'): ?>
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
                    <a href="../../../auth/logout.php" class="text-gray-500 hover:text-red-600 transition-colors" title="Cerrar Sesión">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </a>
                </div>
            </div>
            
            <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                <a href="../dashboard.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo ($current_page_script == 'dashboard.php') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50'; ?>">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/> </svg>
                    Dashboard
                </a>
                <a href="subscriptions.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo ($current_page_script == 'subscriptions.php') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50'; ?>">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/> </svg>
                    Suscripciones
                </a>
                <a href="statistics.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo ($current_page_script == 'statistics.php') ? 'bg-blue-700 text-white' : 'text-gray-700 hover:bg-gray-50'; ?>">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/> </svg>
                    Estadísticas
                </a>
                <a href="notifications.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo ($current_page_script == 'notifications.php') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50'; ?>">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/> </svg>
                    Notificaciones
                </a>
                 <a href="forums.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo ($current_page_script == 'forums.php') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50'; ?>">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/> </svg>
                    Foros
                </a>
                <a href="settings.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo ($current_page_script == 'settings.php') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50'; ?>">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/> </svg>
                    Configuración
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
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-700">
                            <span class="text-lg font-bold text-white">S</span>
                        </div>
                        <span class="ml-2 text-lg font-bold text-gray-900">SubsZero</span>
                    </div>
                    <div class="w-6"></div> 
                </div>
            </div>

            <div class="p-4 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Estadísticas</h1>
                            <p class="text-gray-600 text-base sm:text-lg">Analiza tus gastos y tendencias de suscripciones</p>
                        </div>
                        
                        <button onclick="showPremiumAlert()" class="bg-gray-300 text-gray-700 px-4 py-2.5 rounded-lg text-sm font-medium flex items-center cursor-pointer hover:bg-gray-400 transition-colors shadow-md">
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/> </svg>
                            Exportar CSV
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <p class="text-gray-500 text-sm font-medium mb-1">Gasto Mensual</p>
                            <span class="text-3xl font-bold text-gray-900">€<?php echo number_format($stats['GastoMensual'], 2, '.', ','); ?></span>
                            <div class="mt-2 text-green-600 text-sm flex items-center">
                                <svg class="w-4 h-4 mr-1 transform rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                Promedio por mes
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <p class="text-gray-500 text-sm font-medium mb-1">Gasto Anual</p>
                            <span class="text-3xl font-bold text-gray-900">€<?php echo number_format($stats['GastoAnual'], 2, '.', ','); ?></span>
                            <div class="mt-2 text-gray-600 text-sm flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                $ Total proyectado
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <p class="text-gray-500 text-sm font-medium mb-1">Promedio por Suscripción</p>
                            <span class="text-3xl font-bold text-gray-900">€<?php echo number_format($stats['PromedioPorSuscripcion'], 2, '.', ','); ?></span>
                            <div class="mt-2 text-gray-600 text-sm">
                                <?php echo $stats['SuscripcionesActivas']; ?> suscripciones activas
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <p class="text-gray-500 text-sm font-medium mb-1">Ahorro Potencial</p>
                            <span class="text-3xl font-bold text-gray-900">€<?php echo number_format($stats['AhorroPotencial'], 2, '.', ','); ?></span>
                            <div class="mt-2 text-red-600 text-sm flex items-center">
                                <svg class="w-4 h-4 mr-1 transform rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                                Cancelando 20%
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                        
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 lg:col-span-2">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Gasto por Categoría</h2>
                            <p class="text-gray-600 text-sm mb-4">Distribución mensual de tus suscripciones</p>
                            <div class="relative h-72">
                                <canvas id="categoryBarChart"></canvas>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 mb-4">Distribución de Gastos</h2>
                                <p class="text-gray-600 text-sm mb-4">Porcentaje por categoría</p>
                            </div>
                            <div class="relative h-48 w-48 mx-auto mb-4">
                                <canvas id="distributionPieChart"></canvas>
                            </div>
                            <div class="flex flex-wrap justify-center gap-x-4 text-sm text-gray-700">
                                <?php foreach ($stats['DistribucionGastos'] as $item): ?>
                                    <span class="font-medium"><?php echo htmlspecialchars($item['categoria']); ?> <?php echo $item['porcentaje']; ?>%</span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="premium-alert-modal" class="modal hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/75 bg-opacity-25 backdrop-blur-sm" onclick="closePremiumAlert()"></div>
        <div class="modal-content bg-white rounded-xl shadow-2xl w-full max-w-sm relative z-10 transform scale-95 opacity-0">
            <div class="p-6 text-center">
                <svg class="mx-auto h-12 w-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">Función Premium</h3>
                <p class="mt-2 text-sm text-gray-500">
                    Esta es una función premium. Actualiza a premium para acceder a la exportación de datos a CSV.
                </p>
            </div>
            <div class="p-4 bg-gray-50 border-t border-gray-200 rounded-b-xl flex justify-center">
                <button type="button" onclick="closePremiumAlert()" class="bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-blue-800 transition-colors">
                    Entendido
                </button>
            </div>
        </div>
    </div>
    
    <script>
        // --- Funciones de Sidebar ---
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
        
        // --- Funciones del Botón CSV ---
        const premiumModal = document.getElementById('premium-alert-modal');
        const modalContent = premiumModal.querySelector('.modal-content');

        function showPremiumAlert() {
            premiumModal.classList.remove('hidden');
            setTimeout(() => {
                premiumModal.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closePremiumAlert() {
            premiumModal.classList.add('opacity-0');
            modalContent.classList.add('scale-95', 'opacity-0');
            modalContent.classList.remove('scale-100', 'opacity-100');
            setTimeout(() => {
                premiumModal.classList.add('hidden');
            }, 250);
        }

        // --- LÓGICA DE GRÁFICOS (Chart.js) ---
        document.addEventListener('DOMContentLoaded', () => {
            
            // Datos PHP a JS
            const distribucionGastos = <?php echo json_encode($stats['DistribucionGastos']); ?>;
            const gastosPorCategoria = <?php echo json_encode($gastos_por_categoria); ?>; // Usamos el array simple para el bar chart de categoría

            // Generación de colores dinámicos (para el pastel y barras)
            const baseColors = ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d', '#17a2b8', '#fd7e14', '#e83e8c'];
            const labelsCategorias = distribucionGastos.map(item => item.categoria);
            const dataCategorias = distribucionGastos.map(item => item.gasto);

            // 1. Gráfico de Gasto por Categoría (Barras)
            if (document.getElementById('categoryBarChart')) {
                new Chart(document.getElementById('categoryBarChart'), {
                    type: 'bar',
                    data: {
                        labels: Object.keys(gastosPorCategoria), // Nombres de categorías
                        datasets: [{
                            label: 'Gasto Mensual (€)',
                            data: Object.values(gastosPorCategoria), // Gastos por categoría
                            backgroundColor: baseColors.slice(0, Object.keys(gastosPorCategoria).length).map(c => c + 'cc'), // Ligeramente más transparente
                            borderColor: baseColors.slice(0, Object.keys(gastosPorCategoria).length),
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, title: { display: true, text: 'Gasto (€)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }


            // 2. Gráfico de Distribución de Gastos (Pastel)
            if (document.getElementById('distributionPieChart')) {
                 new Chart(document.getElementById('distributionPieChart'), {
                    type: 'doughnut',
                    data: {
                        labels: labelsCategorias, // Etiquetas simples, el porcentaje va en el div de abajo
                        datasets: [{
                            data: dataCategorias,
                            backgroundColor: baseColors.slice(0, labelsCategorias.length),
                            borderWidth: 1,
                            borderColor: '#ffffff' // Borde blanco para separar las secciones
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { callbacks: { label: function(context) {
                                let label = context.label || '';
                                let porcentaje = distribucionGastos.find(item => item.categoria === context.label)?.porcentaje;
                                label += `: €${context.parsed.toFixed(2)} (${porcentaje}%)`;
                                return label;
                            }}}
                        }
                    }
                });
            }
            
            
        });
    </script>
</body>
</html>