<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-R">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SubsZero - Contact</title>
        <link rel="icon" href="../assets/favicon.ico">
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    </head>
    <body class="bg-gray-50"> 
        
        <header class="flex flex-col md:flex-row h-auto md:h-16 border-b border-gray-300 p-4 md:p-0 bg-white">
            <div class="flex flex-row items-center mx-4 md:ml-8 lg:ml-32 space-x-4">
                <div class="pl-3 pr-3 pt-1 pb-1 md:pl-4 md:pr-4 md:pt-2 md:pb-2 bg-blue-800 rounded-xl max-h-max">
                    <h1 class="text-white text-lg md:text-xl font-bold">S</h1>
                </div>
                <h1 class="text-lg md:text-xl font-bold"><a href="../index.php">SubsZero</a></h1>
            </div>

            <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-4 mt-4 md:mt-0 md:ml-auto mx-4 md:mr-8 lg:mr-32">
                <a href="../auth/login.php">
                    <button class="w-full md:w-auto p-2 pl-4 pr-4 rounded-xl text-black text-center duration-300 hover:bg-cyan-600 hover:text-white hover:cursor-pointer">
                        Iniciar Sesión
                    </button>
                </a>

                <a href="../auth/signup.php">
                    <button class="w-full md:w-auto p-2 pl-4 pr-4 bg-blue-800 rounded-xl text-white text-center duration-300 hover:bg-blue-700 hover:cursor-pointer">
                        Registrarse
                    </button>
                </a>
            </div>
        </header>

        <main>
            <div class="py-16 md:py-20">
                <div class="container mx-auto px-4 text-center">
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900">Ponte en Contacto</h1>
                    <p class="mt-4 text-lg text-gray-700 max-w-2xl mx-auto">¿Preguntas? Nos encantaría saber de ti. Envíanos un mensaje y te responderemos lo antes posible.</p>
                </div>
            </div>

            <div class="container mx-auto px-4 pb-16 -mt-12 md:-mt-16 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
                    
                    <div class="lg:col-span-7">
                        <div class="bg-white p-8 rounded-2xl shadow-xl border border-gray-100">
                            <h2 class="text-3xl font-bold mb-6 text-gray-900">Envíanos un Mensaje</h2>
                            
                            <form action="#" method="POST" class="space-y-5">
                                <div>
                                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                                    <input type="text" name="nombre" id="nombre" placeholder="Tu nombre completo" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                                    <input type="email" name="email" id="email" placeholder="tu@email.com" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label for="asunto" class="block text-sm font-medium text-gray-700 mb-1">Asunto</label>
                                    <input type="text" name="asunto" id="asunto" placeholder="Asunto de tu mensaje" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label for="mensaje" class="block text-sm font-medium text-gray-700 mb-1">Mensaje</label>
                                    <textarea name="mensaje" id="mensaje" rows="6" placeholder="Cuéntanos más sobre tu consulta..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
                                </div>
                                <div>
                                    <button type="submit" class="w-full px-6 py-3 bg-blue-800 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 duration-300">
                                        Enviar Mensaje
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="lg:col-span-5">
                        <h2 class="text-3xl font-bold mb-6 text-gray-900">Información de Contacto</h2>
                        <div class="space-y-6">
                            
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0 bg-blue-100 p-3 rounded-full">
                                    <svg class="w-6 h-6 text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /> </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold">Email</h3>
                                    <a href="mailto:jabecerramorilla21@gmail.com" class="text-gray-700 hover:text-blue-600">jabecerramorilla21@gmail.com</a>
                                    <p class="text-sm text-gray-500">Respuesta en 24 horas</p>
                                </div>
                            </div>

                            <div class="bg-blue-50 border border-blue-200 p-6 rounded-2xl">
                                <h3 class="text-lg font-bold text-gray-900">¿Pregunta Frecuente?</h3>
                                <p class="mt-1 text-gray-600">Consulta nuestro Centro de Ayuda para respuestas rápidas</p>
                                <a href="ayuda.php" class="mt-4 inline-block text-blue-700 font-semibold group">
                                    Ir al Centro de Ayuda
                                    <span class="inline-block transition-transform group-hover:translate-x-1 motion-reduce:transform-none">&rarr;</span>
                                </a>
                            </div>

                            <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-lg">
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="flex-shrink-0">
                                        <svg class="w-8 h-8 text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /> </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold">Horario de Soporte</h3>
                                    </div>
                                </div>
                                <div class="space-y-2 text-gray-700">
                                    <div class="flex justify-between">
                                        <span>Lunes - Viernes:</span>
                                        <span class="font-medium">9:00 - 18:00</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Sábados:</span>
                                        <span class="font-medium">10:00 - 14:00</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Domingos:</span>
                                        <span class="font-medium">Cerrado</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </main>

        <section class="py-16 border-t border-gray-200">
            <div class="container mx-auto px-4">
                <div class="max-w-lg mx-auto text-center">
                    <h3 class="text-2xl font-bold text-gray-900">Solicitar Funcionalidad</h3>
                    <p class="mt-3 text-lg text-gray-600">¿Tienes una idea para mejorar SubsZero?</p>
                    <a href="#" class="mt-6 inline-block px-8 py-3 border border-gray-300 rounded-lg text-gray-900 font-semibold bg-white hover:bg-gray-50 shadow-sm transition-colors">
                        Sugerir
                    </a>
                </div>
            </div>
        </section>


        <footer class="border-t-1 border-gray-300 py-12">
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
                                <a href="#features" class="text-sm">
                                    Características
                                </a>
                            </li>
                            <li>
                                <a href="#pricing" class="text-sm">
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
                                <a href="pages/ayuda.php" class="text-sm">
                                    Ayuda
                                </a>
                            </li>
                            <li>
                                <a href="pages/contact.php" class="text-sm">
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