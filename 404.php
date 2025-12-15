<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>404 - Página no encontrada</title>
        <link rel="icon" href="assets/favicon.ico">
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    </head>
    <body class="flex flex-col min-h-screen bg-white"> 

        <header class="flex flex-col md:flex-row h-auto md:h-16 border-b border-gray-300 p-4 md:p-0">
            <div class="flex flex-row items-center mx-4 md:ml-8 lg:ml-32 space-x-4">
                <div class="pl-3 pr-3 pt-1 pb-1 md:pl-4 md:pr-4 md:pt-2 md:pb-2 bg-blue-800 rounded-xl max-h-max">
                    <h1 class="text-white text-lg md:text-xl font-bold">S</h1>
                </div>
                <h1 class="text-lg md:text-xl font-bold"><a href="index.php">SubsZero</a></h1>
            </div>

            <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-4 mt-4 md:mt-0 md:ml-auto mx-4 md:mr-8 lg:mr-32">
                <a href="auth/login.php">
                    <button class="w-full md:w-auto p-2 pl-4 pr-4 rounded-xl text-black text-center duration-300 hover:bg-cyan-600 hover:text-white hover:cursor-pointer">
                        Iniciar Sesión
                    </button>
                </a>

                <a href="auth/signup.php">
                    <button class="w-full md:w-auto p-2 pl-4 pr-4 bg-blue-800 rounded-xl text-white text-center duration-300 hover:bg-blue-700 hover:cursor-pointer">
                        Registrarse
                    </button>
                </a>
            </div>
        </header>

        <main class="flex-grow">

            <section class="container mx-auto px-4 py-20 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-4xl text-center">
                    
                    <h2 class="text-8xl font-bold text-blue-800 tracking-tight sm:text-9xl">
                        404
                    </h2>

                    <h1 class="mt-8 text-balance text-4xl font-bold tracking-tight sm:text-5xl">
                        ¡Oops! Página no encontrada
                    </h1>

                    <p class="mt-6 text-pretty text-gray-600 text-lg leading-relaxed text-muded-foreground sm:text-xl">
                        Parece que la página que buscas se escapó como una suscripción cancelada. No te preocupes, te ayudaremos a encontrar el camino.
                    </p>

                    <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                        <a href="/subszero/index.php">   
                            <button class="w-full md:w-auto p-2 pl-4 pr-4 bg-blue-800 rounded-xl text-white text-center duration-300 hover:bg-blue-700 hover:cursor-pointer">
                                Ir al inicio
                            </button>
                        </a>
                        
                        <button onclick="window.history.back()" class="w-full border-1 border-gray-400 md:w-auto p-2 pl-4 pr-4 rounded-xl text-black text-center duration-300 hover:bg-cyan-600 hover:text-white hover:cursor-pointer">
                            Volver atrás
                        </button>
                    </div>

                </div>
             </section>

        </main>

        <footer class="border-t-1 border-gray-300 bg-gray-50 py-12">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                
                <div>
                    <div class="flex items-center gap-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-800">
                            <span class="text-lg font-bold text-white">S</span>
                        </div>
                        <span class="text-xl font-bold">SubsZero</span>
                        </div>
                        <p class="mt-4 text-sm">
                            Control total de tus suscripciones en un solo lugar.
                        </p>
                </div>

                <div>
                    <h4 class="font-semibold">Producto</h4>
                    <ul class="mt-4 space-y-2">
                    <li>
                        <a href="index.php#features" class="text-sm"> 
                        Características
                        </a>
                    </li>
                    <li>
                        <a href="index.php#pricing" class="text-sm">
                        Precios
                        </a>
                    </li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold">Legal</h4>
                    <ul class="mt-4 space-y-2">
                    <li>
                        <a href="documentation/privacy-policy.html" class="text-sm">
                        Privacidad
                        </a>
                    </li>
                    <li>
                        <a href="documentation/terms-and-conditions.html" class="text-sm">
                        Términos
                        </a>
                    </li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold">Soporte</h4>
                    <ul class="mt-4 space-y-2">
                    <li>
                        <a href="#" class="text-sm">
                        Ayuda
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-sm">
                        Contacto
                        </a>
                    </li>
                    </ul>
                </div>

                </div>

                <div class="mt-12 border-t-1 border-gray-300 pt-8 text-center">
                <p class="text-sm">
                    © <?php echo date('Y'); ?> SubsZero. Todos los derechos reservados.
                </p>
                </div>
            </div>
        </footer>
    </body>
</html>