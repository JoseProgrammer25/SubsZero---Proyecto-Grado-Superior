<?php
session_start();
// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php"); // Ruta corregida
    exit();
}

// Obtener datos del usuario de la sesión
$user_id = $_SESSION['user_id'];

// Conectar a la base de datos
require_once '../../config/db.php';

// --- ¡BLOQUE DE AUTENTICACIÓN Y ROL (Igual que en Dashboard) ---
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
// --- FIN BLOQUE DE AUTENTICACIÓN ---

// Obtener la inicial del nombre de usuario
$user_initial = strtoupper(substr($username, 0, 1));

// Obtener la página actual
$current_page_script = basename($_SERVER['PHP_SELF']); // Esto será 'subscriptions.php'

// --- ¡MODIFICADO! LÓGICA DE LÍMITE (10 Suscripciones) ---
$limit_reached = false;
$user_total_subs = 0;
if ($role == 'user') {
    // Contamos el total de suscripciones del usuario (sin filtros)
    $stmt_count_total = $conn->prepare("SELECT COUNT(id) as total FROM subscriptions WHERE user_id = ?");
    $stmt_count_total->bind_param("i", $user_id);
    $stmt_count_total->execute();
    $result_count_total = $stmt_count_total->get_result();
    $user_total_subs = $result_count_total->fetch_assoc()['total'];
    $stmt_count_total->close();

    // Límite de 10
    if ($user_total_subs >= 10) {
        $limit_reached = true;
    }
}

// --- ¡AÑADIDO! LÓGICA PARA PROCESAR EL FORMULARIO DEL MODAL ---
$error_message = '';
$success_message = '';

// Mostrar mensaje de éxito si venimos de un redirect
if (isset($_GET['status']) && $_GET['status'] == 'added') {
    $success_message = "¡Suscripción añadida con éxito!";
}

// Procesar el formulario (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_action']) && $_POST['form_action'] == 'add_subscription') {
    
    // Volver a chequear el límite por seguridad
    if ($limit_reached) {
        $error_message = "Has alcanzado el límite de 10 suscripciones para usuarios gratuitos.";
    } else {
        // Recoger datos del formulario
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = $_POST['price'];
        $billing_cycle = $_POST['billing_cycle']; // (ej. 'monthly', 'annually')
        $next_billing_date = $_POST['next_billing_date'];
        $category_id = $_POST['category_id'];

        // Validaciones básicas
        if (empty($name) || empty($price) || empty($billing_cycle) || empty($next_billing_date)) {
            $error_message = "Por favor, completa los campos obligatorios (Nombre, Precio, Ciclo y Próxima Fecha).";
        } elseif (!is_numeric($price) || $price < 0) {
            $error_message = "El precio debe ser un número válido.";
        } else {
            $category_id_to_insert = empty($category_id) || $category_id == 'none' ? NULL : (int)$category_id;

            try {
                $stmt_insert = $conn->prepare("
                    INSERT INTO subscriptions 
                    (user_id, name, description, price, billing_cycle, next_billing_date, category_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt_insert->bind_param("issdssi", $user_id, $name, $description, $price, $billing_cycle, $next_billing_date, $category_id_to_insert);

                if ($stmt_insert->execute()) {
                    // Redirigir a la misma página con un parámetro de éxito (Patrón Post-Redirect-Get)
                    header("Location: subscriptions.php?status=added");
                    exit();
                } else {
                    $error_message = "Error al guardar la suscripción. Inténtalo de nuevo.";
                }
                $stmt_insert->close();
            } catch (Exception $e) {
                $error_message = "Error de base de datos: " . $e->getMessage();
            }
        }
    }
}
// --- FIN LÓGICA POST ---


// --- LÓGICA DE PAGINACIÓN Y FILTROS (GET) ---
$subs_per_page = 6;
$current_page_num = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page_num < 1) $current_page_num = 1;

$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter_id = isset($_GET['category']) ? $_GET['category'] : 'all';

$sql_where_clauses = ["s.user_id = ?"];
$sql_params_types = "i";
$sql_params_values = [$user_id];

if ($search_term !== '') {
    $sql_where_clauses[] = "s.name LIKE ?";
    $sql_params_types .= "s";
    $sql_params_values[] = "%" . $search_term . "%";
}
if ($category_filter_id !== 'all' && is_numeric($category_filter_id)) {
    $sql_where_clauses[] = "s.category_id = ?";
    $sql_params_types .= "i";
    $sql_params_values[] = (int)$category_filter_id;
}
$where_sql = "WHERE " . implode(" AND ", $sql_where_clauses);

try {
    // 1. Conteo total filtrado
    $stmt_count = $conn->prepare("SELECT COUNT(s.id) as total FROM subscriptions s $where_sql");
    $stmt_count->bind_param($sql_params_types, ...$sql_params_values);
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    $filtered_total_subs = $result_count->fetch_assoc()['total'];
    $stmt_count->close();

    $total_pages = ceil($filtered_total_subs / $subs_per_page);
    if ($current_page_num > $total_pages && $total_pages > 0) $current_page_num = $total_pages;
    $offset = ($current_page_num - 1) * $subs_per_page;

    // 2. Obtener suscripciones paginadas
    $sql_params_types_pag = $sql_params_types . "ii"; // Añadir tipos para LIMIT y OFFSET
    $sql_params_values_pag = $sql_params_values;
    $sql_params_values_pag[] = $subs_per_page;
    $sql_params_values_pag[] = $offset;

    $stmt_subs = $conn->prepare("
        SELECT s.id, s.name, s.description, s.price, s.billing_cycle, s.next_billing_date,
               c.name AS category_name, c.id AS category_id
        FROM subscriptions s
        LEFT JOIN categories c ON s.category_id = c.id
        $where_sql
        ORDER BY s.next_billing_date ASC
        LIMIT ? OFFSET ?
    ");
    
    $stmt_subs->bind_param($sql_params_types_pag, ...$sql_params_values_pag);
    $stmt_subs->execute();
    $result_subs = $stmt_subs->get_result();
    $subscriptions = $result_subs->fetch_all(MYSQLI_ASSOC);
    $stmt_subs->close();

    // 3. Obtener categorías (PARA EL FILTRO Y PARA EL MODAL)
    $stmt_cat = $conn->prepare("SELECT id, name FROM categories ORDER BY name ASC");
    $stmt_cat->execute();
    $result_cat = $stmt_cat->get_result();
    $categories = $result_cat->fetch_all(MYSQLI_ASSOC);
    $stmt_cat->close();

} catch (Exception $e) {
    $subscriptions = [];
    $categories = [];
    $filtered_total_subs = 0;
    $total_pages = 0;
    $error_message = "Error al cargar las suscripciones: " . $e->getMessage();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suscripciones - SubsZero</title>
    <link rel="icon" href="../../../assets/favicon.ico">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        .modal {
            transition: opacity 0.25s ease;
        }
        .modal-content {
            transition: transform 0.25s ease;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <div id="overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden" onclick="toggleSidebar()"></div>

        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-white shadow-lg flex flex-col transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-700">
                        <span class="text-xl font-bold text-white">S</span>
                    </div>
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
                <div class="mt-2">
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
                 <a href="statistics.php" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors <?php echo ($current_page_script == 'statistics.php') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50'; ?>">
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

            <div class="p-4 border-t border-gray-200">
                <a href="../../../auth/logout.php" class="flex items-center px-3 py-2.5 text-sm font-medium text-red-600 rounded-lg hover:bg-red-50 transition-colors">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/> </svg>
                    Cerrar Sesión
                </a>
            </div>
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
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-8">
                        <div>
                            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Suscripciones</h1>
                            <p class="text-gray-600 text-base sm:text-lg">Gestiona todas tus suscripciones en un solo lugar</p>
                        </div>
                        
                        <?php if ($limit_reached): ?>
                            <div class="relative group mt-4 sm:mt-0">
                                <button class="bg-gray-400 text-white px-4 py-2.5 rounded-lg text-sm font-medium flex items-center cursor-not-allowed" disabled>
                                    <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20"> <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/> </svg>
                                    Añadir Suscripción
                                </button>
                                <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-max max-w-xs px-3 py-1.5 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity z-10 text-center">
                                    Límite de 10 suscripciones alcanzado.
                                </span>
                            </div>
                        <?php else: ?>
                            <button type="button" id="open-modal-btn" class="bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-blue-800 transition-colors flex items-center mt-4 sm:mt-0">
                                <svg class="mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20"> <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/> </svg>
                                Añadir Suscripción
                            </button>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($success_message): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
                            <span class="block sm:inline pr-10"><?php echo htmlspecialchars($success_message); ?></span>
                            <button type="button" class="close-alert-btn absolute top-1/2 right-3 transform -translate-y-1/2 text-green-700 hover:text-green-900" aria-label="Cerrar">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                     <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    <?php endif; ?>
                    <?php if ($error_message): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
                            <span class="block sm:inline pr-10"><?php echo htmlspecialchars($error_message); ?></span>
                            <button type="button" class="close-alert-btn absolute top-1/2 right-3 transform -translate-y-1/2 text-red-700 hover:text-red-900" aria-label="Cerrar">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                     <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    <?php endif; ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
                        <form method="GET" action="subscriptions.php" class="flex flex-col sm:flex-row gap-4">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"> <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/> </svg>
                                </div>
                                <input type="text" name="search" id="search-input" placeholder="Buscar suscripciones..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent" value="<?php echo htmlspecialchars($search_term); ?>">
                            </div>
                            <select name="category" id="category-filter" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                                <option value="all">Todas las categorías</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo ($category_filter_id == $category['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-800 transition-colors">
                                Filtrar
                            </button>
                            <a href="subscriptions.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium hover:bg-gray-300 transition-colors flex items-center justify-center text-center">
                                Limpiar
                            </a>
                        </form>
                    </div>

                    <div id="subscription-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php if (count($subscriptions) > 0): ?>
                            <?php setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'spanish'); ?>
                            <?php foreach ($subscriptions as $sub): ?>
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col subscription-card">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900"><?php echo htmlspecialchars($sub['name']); ?></h3>
                                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($sub['description']); ?></p>
                                        </div>
                                        <button class="text-gray-400 hover:text-gray-600">
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>
                                        </button>
                                    </div>
                                    <div class="flex items-end justify-between mb-4">
                                        <span class="text-3xl font-bold text-gray-900">€<?php echo number_format($sub['price'], 2, '.', ','); ?></span>
                                        <span class="ml-2 text-sm font-medium text-gray-600 bg-gray-100 px-2 py-0.5 rounded-full">
                                            <?php echo htmlspecialchars(ucfirst($sub['billing_cycle'])); ?>
                                        </span>
                                    </div>
                                    <div class="mt-auto">
                                        <div class="flex items-center space-x-2 mb-3">
                                            <?php if (!empty($sub['category_name'])): ?>
                                                <span class="text-xs font-medium text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded-full"><?php echo htmlspecialchars(ucfirst($sub['category_name'])); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-sm text-gray-500">Próximo pago: 
                                            <?php 
                                            $date = new DateTime($sub['next_billing_date']);
                                            echo strftime('%d %b %Y', $date->getTimestamp());
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="lg:col-span-3 text-center py-20 bg-white rounded-xl shadow-sm border border-gray-200">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/> </svg>
                                <?php if ($search_term !== '' || $category_filter_id !== 'all'): ?>
                                    <p class="mt-4 text-gray-600 font-semibold">No se encontraron suscripciones</p>
                                    <p class="text-sm text-gray-500 mt-1">Prueba a cambiar los términos de búsqueda o filtros.</p>
                                <?php else: ?>
                                    <p class="mt-4 text-gray-600 font-semibold">No tienes suscripciones añadidas</p>
                                    <p class="text-sm text-gray-500 mt-1">Haz clic en "Añadir Suscripción" para empezar a gestionar tus gastos.</p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($total_pages > 1): ?>
                        <nav class="mt-8 flex items-center justify-between border-t border-gray-200 pt-6" aria-label="Pagination">
                            <div class="flex w-0 flex-1">
                                <?php if ($current_page_num > 1): ?>
                                    <a href="?page=<?php echo $current_page_num - 1; ?>&search=<?php echo urlencode($search_term); ?>&category=<?php echo urlencode($category_filter_id); ?>" class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                        <svg class="mr-3 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"> <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/> </svg>
                                        Anterior
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="hidden md:flex">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search_term); ?>&category=<?php echo urlencode($category_filter_id); ?>" class="inline-flex items-center border-t-2 px-4 pt-4 text-sm font-medium <?php echo ($i == $current_page_num) ? 'border-blue-700 text-blue-700' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>
                            </div>
                            <div class="flex w-0 flex-1 justify-end">
                                <?php if ($current_page_num < $total_pages): ?>
                                    <a href="?page=<?php echo $current_page_num + 1; ?>&search=<?php echo urlencode($search_term); ?>&category=<?php echo urlencode($category_filter_id); ?>" class="inline-flex items-center border-t-2 border-transparent pl-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                                        Siguiente
                                        <svg class="ml-3 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"> <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/> </svg>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </nav>
                    <?php endif; ?>
                    
                </div>
            </div>
        </main>
    </div>

    <div id="subscription-modal" class="modal hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        
        <div id="modal-overlay" class="fixed inset-0 bg-black/75 bg-opacity-25 backdrop-blur-sm"></div>
        
        <div class="modal-content bg-white rounded-xl shadow-2xl w-full max-w-2xl relative z-10 transform scale-95 opacity-0">
            <form action="subscriptions.php" method="POST">
                <input type="hidden" name="form_action" value="add_subscription">
                
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-900">Añadir Suscripción</h3>
                    <button type="button" class="close-modal-btn text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-6">
                    <div>
                        <label for="modal-name" class="block text-sm font-medium text-gray-700">Nombre de la Suscripción <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="modal-name" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-600 focus:border-blue-600 sm:text-sm">
                    </div>

                    <div>
                        <label for="modal-description" class="block text-sm font-medium text-gray-700">Descripción (Opcional)</label>
                        <textarea name="description" id="modal-description" rows="3"
                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-600 focus:border-blue-600 sm:text-sm"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="modal-price" class="block text-sm font-medium text-gray-700">Precio (EUR) <span class="text-red-500">*</span></label>
                            <div class="relative mt-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">€</span>
                                </div>
                                <input type="number" name="price" id="modal-price" step="0.01" min="0" required
                                       class="block w-full pl-7 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-600 focus:border-blue-600 sm:text-sm" placeholder="0.00">
                            </div>
                        </div>

                        <div>
                            <label for="modal-billing_cycle" class="block text-sm font-medium text-gray-700">Ciclo de Facturación <span class="text-red-500">*</span></label>
                            <select name="billing_cycle" id="modal-billing_cycle" required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-600 focus:border-blue-600 sm:text-sm">
                                <option value="mensual">Mensual</option>
                                <option value="anual">Anual</option>
                                <option value="trimestral">Trimestral</option>
                                <option value="semanal">Semanal</option>
                                <option value="unico">Pago único</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="modal-next_billing_date" class="block text-sm font-medium text-gray-700">Próxima Fecha de Pago <span class="text-red-500">*</span></label>
                            <input type="date" name="next_billing_date" id="modal-next_billing_date" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-600 focus:border-blue-600 sm:text-sm" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div>
                            <label for="modal-category_id" class="block text-sm font-medium text-gray-700">Categoría (Opcional)</label>
                            <select name="category_id" id="modal-category_id"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-600 focus:border-Gblue-600 sm:text-sm">
                                <option value="none">-- Sin categoría --</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-gray-50 border-t border-gray-200 rounded-b-xl flex justify-end space-x-3">
                    <button type="button" class="close-modal-btn bg-white text-gray-700 px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-100 border border-gray-300">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-blue-800 transition-colors">
                        Guardar Suscripción
                    </button>
                </div>
            </form>
        </div>
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

        // --- JS PARA EL MODAL Y ALERTAS ---
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('subscription-modal');
            const modalContent = modal.querySelector('.modal-content');
            const openModalBtn = document.getElementById('open-modal-btn');
            const closeModalBtns = document.querySelectorAll('.close-modal-btn');

            function openModal() {
                modal.classList.remove('hidden');
                // Efecto de aparición
                setTimeout(() => {
                    modal.classList.remove('opacity-0');
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            }

            function closeModal() {
                // Efecto de desaparición
                modal.classList.add('opacity-0');
                modalContent.classList.add('scale-95', 'opacity-0');
                modalContent.classList.remove('scale-100', 'opacity-100');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 250); // Coincide con la duración de la transición
            }

            if (openModalBtn) {
                openModalBtn.addEventListener('click', openModal);
            }

            closeModalBtns.forEach(btn => {
                btn.addEventListener('click', closeModal);
            });

            // ¡CORREGIDO! Asegurándonos de que el overlay tiene el ID correcto
            const modalOverlay = document.getElementById('modal-overlay');
            if (modalOverlay) {
                modalOverlay.addEventListener('click', closeModal);
            }
            
            // Si hubo un error de PHP al enviar el formulario, vuelve a abrir el modal para mostrar el error.
            <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && $error_message): ?>
                openModal();
            <?php endif; ?>

            // --- ¡NUEVO! JS PARA CERRAR ALERTAS ---
            const alertCloseButtons = document.querySelectorAll('.close-alert-btn');
            
            alertCloseButtons.forEach(button => {
                button.addEventListener('click', (event) => {
                    // Sube al elemento padre (el div del alert) y lo oculta
                    const alertBox = event.currentTarget.parentElement;
                    if (alertBox) {
                        alertBox.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>