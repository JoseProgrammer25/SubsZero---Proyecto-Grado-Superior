<?php
// Incluir conexión a la base de datos
include('../config/db.php'); // Cambia la ruta si tu conexión está en otra carpeta

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

    // Asignar un role_id por defecto (por ejemplo, 2 = usuario normal)
    $role_id = 2;

    // Insertar usuario
    try {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            header("Location: ../../auth/signup.php?error=db_prepare_error");
            exit();
        }
        
        $stmt->bind_param("sssi", $name, $email, $hashedPassword, $role_id);
        
        if (!$stmt->execute()) {
            header("Location: ../../auth/signup.php?error=db_execute_error");
            exit();
        }
        
        // Registro exitoso
        $stmt->close();
        $conn->close();
        header("Location: ../../auth/login.php?success=1");
        exit();
        
    } catch (Exception $e) {
        die("Error en el registro: " . $e->getMessage());
    }
} else {
    // Si se accede directamente sin enviar el formulario
    header("Location: register.php");
    exit();
}
?>