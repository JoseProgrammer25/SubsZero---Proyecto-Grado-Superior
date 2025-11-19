<?php
session_start();
require_once('../config/db.php'); // Asegúrate que la ruta a db.php es correcta

// Habilitar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validar que los campos no estén vacíos
    if (empty($email) || empty($password)) {
        header("Location: ../../auth/login.php?error=empty");
        exit();
    }

    // --- ¡CAMBIO IMPORTANTE EN LA CONSULTA! ---
    // 1. Buscamos al usuario en la tabla 'users'.
    // 2. Usamos LEFT JOIN para ver si tiene una entrada en 'admin_users'.
    // 3. Usamos LEFT JOIN para ver si tiene una entrada en 'premium_users'.
    // 4. Usamos LEFT JOIN para coger datos del perfil (ej. foto) de 'user_profiles'.
    $sql = "SELECT 
                u.id, u.username, u.email, u.password,
                a.user_id as is_admin, 
                p.user_id as is_premium,
                up.profile_pic_url
            FROM users u
            LEFT JOIN admin_users a ON u.id = a.user_id
            LEFT JOIN premium_users p ON u.id = p.user_id
            LEFT JOIN user_profiles up ON u.id = up.user_id
            WHERE u.email = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        // Error en la preparación de la consulta
        header("Location: ../../auth/login.php?error=db_error");
        exit();
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verificar la contraseña
        if (password_verify($password, $user['password'])) {
            // Contraseña correcta, iniciar sesión
            
            // --- ¡CAMBIO EN LA CREACIÓN DE SESIÓN! ---
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            
            // Guardar la foto de perfil (si existe) para usar en el header
            $_SESSION['profile_pic'] = $user['profile_pic_url'] ?? 'default_avatar.png'; // Ten una imagen por defecto

            // Determinar el ROL basado en la Jerarquía
            if ($user['is_admin'] !== null) {
                $_SESSION['role'] = 'admin';
            } elseif ($user['is_premium'] !== null) {
                $_SESSION['role'] = 'premium';
            } else {
                $_SESSION['role'] = 'user'; // Usuario estándar (no es admin ni premium)
            }
            
            // Redirigir al dashboard
            header("Location: ../dashboard/dashboard.php"); // Asegúrate que esta ruta es correcta
            exit();
        } else {
            // Contraseña incorrecta
            header("Location: ../../auth/login.php?error=invalid");
            exit();
        }
    } else {
        // Usuario no encontrado
        header("Location: ../../auth/login.php?error=invalid");
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    // Si alguien intenta acceder directamente a este archivo
    header("Location: ../../auth/login.php");
    exit();
}
?>