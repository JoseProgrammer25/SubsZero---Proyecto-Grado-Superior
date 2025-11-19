<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registro - SubsZero</title>
        <link rel="icon" href="../assets/favicon.ico">
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    </head>
    <body>
        <div class="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-12">
            <div class="w-full max-w-md">
                <div class="mb-8 text-center">
                    <a href="../index.php" class="inline-flex items-center gap-2">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-800">
                            <span class="text-xl font-bold text-white">S</span>
                        </div>
                        <span class="text-2xl font-bold text-black">SubsZero</span>
                    </a>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-md">
                    <!-- Header -->
                    <h2 class="text-2xl font-bold text-gray-800">Crear Cuenta</h2>
                    <p class="text-gray-500 text-sm mb-6">
                    Regístrate gratis y comienza a gestionar tus suscripciones
                    </p>

                    <!-- Mensajes de error -->
                    <?php
                    if (isset($_GET['error'])) {
                        $error = $_GET['error'];
                        $errorMessage = '';
                        
                        switch($error) {
                            case 'empty_fields':
                                $errorMessage = 'Por favor, completa todos los campos.';
                                break;
                            case 'terms_not_accepted':
                                $errorMessage = 'Debes aceptar los términos y condiciones.';
                                break;
                            case 'invalid_email':
                                $errorMessage = 'El formato del correo electrónico no es válido.';
                                break;
                            case 'passwords_not_match':
                                $errorMessage = 'Las contraseñas no coinciden.';
                                break;
                            case 'weak_password':
                                $errorMessage = 'La contraseña no cumple con los requisitos mínimos.';
                                break;
                            case 'email_exists':
                                $errorMessage = 'Este correo ya está registrado.';
                                break;
                            case 'db_prepare_error':
                            case 'db_execute_error':
                                $errorMessage = 'Error en el servidor. Por favor, intenta más tarde.';
                                break;
                        }
                        
                        if ($errorMessage) {
                            echo '<div class="mb-4 p-4 text-sm rounded-lg bg-red-50 text-red-600">' . $errorMessage . '</div>';
                        }
                    }
                    ?>

                    <!-- Form -->
                    <form class="space-y-4" action="../app/controllers/process_signup.php" method="POST">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nombre completo</label>
                            <input
                            id="name"
                            name="name"
                            type="text"
                            placeholder="Juan Pérez"
                            required
                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input
                            id="email"
                            name="email"
                            type="email"
                            placeholder="tu@email.com"
                            required
                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                            <input
                            id="password"
                            name="password"
                            type="password"
                            placeholder="••••••••"
                            required
                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />

                            <!-- Validaciones de contraseña -->
                            <div class="mt-3 rounded-lg bg-gray-50 p-3">
                                <p class="text-xs font-medium text-gray-600 mb-1">Requisitos de contraseña:</p>
                                <ul class="space-y-1 text-xs">
                                    <li class="flex items-center gap-2 text-gray-500">
                                        <span class="text-green-500">✔</span> Mínimo 8 caracteres
                                    </li>
                                    <li class="flex items-center gap-2 text-gray-500">
                                        <span class="text-green-500">✔</span> Una letra mayúscula
                                    </li>
                                    <li class="flex items-center gap-2 text-gray-500">
                                        <span class="text-green-500">✔</span> Una letra minúscula
                                    </li>
                                    <li class="flex items-center gap-2 text-gray-500">
                                        <span class="text-green-500">✔</span> Un número
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div>
                            <label for="confirmPassword" class="block text-sm font-medium text-gray-700">
                            Confirmar contraseña
                            </label>
                            <input
                            id="confirmPassword"
                            name="confirmPassword"
                            type="password"
                            placeholder="••••••••"
                            required
                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                        </div>

                        <!-- Checkbox -->
                        <div class="flex items-start gap-2">
                            <input
                            id="terms"
                            name="terms"
                            type="checkbox"
                            required
                            class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            />
                            <label for="terms" class="text-sm text-gray-600 leading-relaxed">
                            Acepto los
                            <a href="../documentation/terms-and-conditions.html" class="text-blue-600 hover:underline">términos y condiciones</a>
                            y la
                            <a href="../documentation/privacy-policy.html" class="text-blue-600 hover:underline">política de privacidad</a>
                            </label>
                        </div>

                        <!-- Botón -->
                        <button
                            type="submit"
                            class="w-full bg-blue-600 text-white py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors"
                        >
                            Crear Cuenta
                        </button>
                    </form>

                    <!-- Footer -->
                    <div class="text-center text-sm text-gray-500 mt-6">
                        ¿Ya tienes una cuenta?
                        <a href="login.php" class="text-blue-600 font-medium hover:underline">Inicia sesión</a>
                    </div>

                    <div class="text-center mt-4">
                        <a href="../index.php" class="text-sm text-gray-400 hover:text-gray-600">← Volver al inicio</a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>