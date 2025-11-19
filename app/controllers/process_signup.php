<?php
// Incluir conexión a la base de datos
include('../config/db.php'); // Asegúrate que la ruta a db.php es correcta

// Habilitar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Recoger los datos del formulario
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $terms = isset($_POST['terms']);

    // Validaciones básicas
    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        header("Location: ../../auth/signup.php?error=empty_fields");
        exit();
    }

    // Validar checkbox de términos
    if (!$terms) {
        header("Location: ../../auth/signup.php?error=terms_not_accepted");
        exit();
    }

    // Validar formato del correo
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../../auth/signup.php?error=invalid_email");
        exit();
    }

    // Validar contraseñas
    if ($password !== $confirmPassword) {
        header("Location: ../../auth/signup.php?error=passwords_not_match");
        exit();
    }

    // Requisitos de seguridad de la contraseña
    if (strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password)) {
        header("Location: ../../auth/signup.php?error=weak_password");
        exit();
    }

    // Comprobar si el email ya existe
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        header("Location: ../../auth/signup.php?error=email_exists");
        exit();
    }
    $stmt->close();

    // Encriptar contraseña
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // --- ¡CAMBIOS IMPORTANTES AQUÍ! ---
    // Ya no se usa $role_id.
    // Usamos una transacción porque insertamos en DOS tablas:
    // 1. `users` (La superclase de la jerarquía)
    // 2. `user_profiles` (Para cumplir la relación 1:1)

    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // 1. Insertar usuario en la tabla 'users' (Superclase)
        $stmt_users = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if (!$stmt_users) {
            throw new Exception("db_prepare_error_users");
        }
        
        // El tipo de bind_param cambia de "sssi" a "sss" (sin role_id)
        $stmt_users->bind_param("sss", $name, $email, $hashedPassword);
        
        if (!$stmt_users->execute()) {
            // Esto puede fallar si el email ya existe (condición de carrera)
            throw new Exception("db_execute_error_users");
        }
        
        // Obtener el ID del usuario recién creado
        $new_user_id = $conn->insert_id;
        $stmt_users->close();

        // 2. Insertar en 'user_profiles' (para la relación 1:1)
        // Se asume que un usuario normal (no admin, no premium) solo necesita un perfil.
        $default_bio = "¡Hola! Soy un nuevo usuario en SubsZero.";
        
        $stmt_profile = $conn->prepare("INSERT INTO user_profiles (user_id, bio) VALUES (?, ?)");
        if (!$stmt_profile) {
            throw new Exception("db_prepare_error_profile");
        }
        
        $stmt_profile->bind_param("is", $new_user_id, $default_bio);
        
        if (!$stmt_profile->execute()) {
            throw new Exception("db_execute_error_profile");
        }
        $stmt_profile->close();
        
        // Si todo fue bien, confirmar los cambios
        $conn->commit();
        
        // Registro exitoso
        $conn->close();
        header("Location: ../../auth/login.php?success=1");
        exit();
        
    } catch (Exception $e) {
        // Si algo falló, deshacer todos los cambios
        $conn->rollback();
        $conn->close();

        // Manejar errores específicos
        $error_code = $e->getMessage();
        if ($error_code === 'db_execute_error_users') {
             header("Location: ../../auth/signup.php?error=email_exists");
        } else {
             // Error genérico de registro
             header("Location: ../../auth/signup.php?error=registration_failed");
        }
        exit();
    }
} else {
    // Si se accede directamente sin enviar el formulario
    // Corregido para apuntar a tu página de registro
    header("Location: ../../auth/signup.php");
    exit();
}
?>