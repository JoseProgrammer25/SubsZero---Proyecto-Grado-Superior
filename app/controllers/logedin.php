<?php
session_start();
require_once('../config/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validar que los campos no estén vacíos
    if (empty($email) || empty($password)) {
        header("Location: ../../auth/login.php?error=empty");
        exit();
    }

    // Preparar la consulta para buscar el usuario por email (incluyendo role_id)
    $stmt = $conn->prepare("SELECT id, username, email, password, role_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verificar la contraseña
        if (password_verify($password, $user['password'])) {
            // Contraseña correcta, iniciar sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role_id'] = $user['role_id'];
            
            // Redirigir al dashboard
            header("Location: ../dashboard/dashboard.php");
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