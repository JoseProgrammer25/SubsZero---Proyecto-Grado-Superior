<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Iniciar Sesión - SubsZero</title>
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

                <div class="bg-white rounded-2xl shadow-md p-8 w-full max-w-md">
                    <!-- Header -->
                    <h2 class="text-2xl font-bold text-gray-800">Iniciar Sesión</h2>
                    <p class="text-gray-500 text-sm mb-6">Ingresa tus credenciales para acceder a tu cuenta</p>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="bg-red-50 text-red-600 p-3 rounded-md mb-4">
                            <?php 
                                switch($_GET['error']) {
                                    case 'empty':
                                        echo 'Por favor, completa todos los campos.';
                                        break;
                                    case 'invalid':
                                        echo 'Email o contraseña incorrectos.';
                                        break;
                                    default:
                                        echo 'Ha ocurrido un error. Por favor, intenta de nuevo.';
                                }
                            ?>
                        </div>
                    <?php endif; ?>

                    <!-- Formulario -->
                    <form class="space-y-4" action="../app/controllers/process_login.php" method="POST">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input
                            id="email"
                            name="email"
                            type="email"
                            placeholder="tu@email.com"
                            required
                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600"
                            />
                        </div>

                        <div>
                            <div class="flex items-center justify-between">
                                <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                                <a href="#" class="text-sm text-blue-600 hover:underline">¿Olvidaste tu contraseña?</a>
                            </div>
                            <input
                            id="password"
                            name="password"
                            type="password"
                            placeholder="••••••••"
                            required
                            class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600"
                            />
                        </div>

                        <button
                            type="submit"
                            class="w-full bg-blue-600 text-white py-2 rounded-md font-medium hover:bg-blue-700 transition-colors"
                        >
                            Iniciar Sesión
                        </button>
                    </form>

                    <!-- Footer -->
                    <div class="text-center text-sm text-gray-500 mt-6">
                        ¿No tienes una cuenta?
                        <a href="signup.php" class="text-blue-600 font-medium hover:underline">Regístrate</a>
                    </div>

                    <div class="text-center mt-4">
                        <a href="../index.php" class="text-sm text-gray-400 hover:text-gray-600">← Volver al inicio</a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>