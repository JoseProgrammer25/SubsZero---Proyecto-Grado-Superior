<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SubsZero - Subcription Control App</title>
        <link rel="icon" href="assets/favicon.ico">
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    </head>
    <body>
        <header class="flex flex-col md:flex-row h-auto md:h-16 border-b border-gray-300 p-4 md:p-0">

            <!-- Div para logo y titulo web -->
            <div class="flex flex-row items-center mx-4 md:ml-8 lg:ml-32 space-x-4">
                <div class="pl-3 pr-3 pt-1 pb-1 md:pl-4 md:pr-4 md:pt-2 md:pb-2 bg-blue-800 rounded-xl max-h-max">
                    <h1 class="text-white text-lg md:text-xl font-bold">S</h1>
                </div>
                <h1 class="text-lg md:text-xl font-bold"><a href="index.php">SubsZero</a></h1>
            </div>

            <!-- Div para botones header derechos -->
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

        <main>

            <!-- Hero Section -->
             <section class="container mx-auto px-4 py-20 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-4xl text-center">
                    <h1 class="text-balance text-5xl font-bold tracking-tight sm:text-6xl lg:text-7xl">
                        Controla tus suscripciones en un solo lugar
                    </h1>

                    <p class="mt-6 text-pretty text-gray-600 text-lg leading-relaxed text-muded-foreground sm:text-xl">
                        SubsZero te ayuda a gestionar todas tus suscripciones, analizar tus gastos y
                        recibir recordatrios antes de cada pago. Nunca más te sorprenderán
                        cargos inesperados.
                    </p>

                    <!-- Botones en hero section -->
                    <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                        <a href="auth/signup.php">   
                            <button class="w-full md:w-auto p-2 pl-4 pr-4 bg-blue-800 rounded-xl text-white text-center duration-300 hover:bg-blue-700 hover:cursor-pointer">
                                Comenzar Gratis
                            </button>
                        </a>

                        <a href="auth/login.php">
                            <button class="w-full border-1 border-gray-400 md:w-auto p-2 pl-4 pr-4 rounded-xl text-black text-center duration-300 hover:bg-cyan-600 hover:text-white hover:cursor-pointer">
                                Iniciar Sesión
                            </button>
                        </a>
                        
                    </div>

                    <p class="mt-4 text-sm text-gray-600">
                        Sin tarjeta de crédito requerida · Cancela cuando quieras
                    </p>
                </div>
             </section>

             <!-- Stats Section -->
              <section class="border-y border-gray-300 border-gray- bg-gray-50 py-12"> 
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 gap-8 sm:grid-cols-3">
                        <div class="text-center">
                            <div class="text-4xl font-bold text-primary text-blue-800">€2400</div>
                            <div class="mt-2 text-sm text-gray-600">Ahorro promedio anual</div>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-primary text-blue-800">15+</div>
                            <div class="mt-2 text-sm text-gray-600">Suscripciones gestionadas</div>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-primary text-blue-800">10k+</div>
                            <div class="mt-2 text-sm text-gray-600">Usuarios activos</div>
                        </div>
                    </div>
                </div>
              </section>

              <!-- Features Section -->
               <section id="features" class="container mx-auto px-4 py-20 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-balance text-3xl font-bold tracking-tight sm:text-4xl">
                        Todo lo que necesitas para gestionar tus suscripciones
                    </h2>
                    <p class="mt-4 text-pretty text-gray-600 text-lg">
                        Herramientas potentes y fáciles de usar para mantener el control total de tus gastos recurrentes
                    </p>
                </div>

                <div class="mt-16 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- Card Gestion -->
                    <div class="card border-1 border-gray-300 rounded-lg shadow-sm p-4">
                        <div class="pt-6">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="20" height="14" x="2" y="5" rx="2"/>
                                    <line x1="2" x2="22" y1="10" y2="10"/>
                                </svg>
                            </div>
                            <h3 class="mt-4 text-xl font-semibold">Gestión Centralizada</h3>
                            <p class="mt-2 text-gray-600">
                                Todas tus suscripciones en un solo dashboard. Añade, edita y elimina con facilidad
                            </p>
                        </div>
                    </div>

                    <!-- Card Analisis -->
                    <div class="card border-1 border-gray-300 rounded-lg shadow-sm p-4">
                        <div class="pt-6">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v16a2 2 0 0 0 2 2h16"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
                            </div>
                            <h3 class="mt-4 text-xl font-semibold">Análisis Detallado</h3>
                            <p class="mt-2 text-gray-600">
                                Visualiza tus gastos mensuales y anuales con gráficos interactivos y estadísticas claras.
                            </p>
                        </div>
                    </div>

                    <!-- Card Recordatorios -->
                    <div class="card border-1 border-gray-300 rounded-lg shadow-sm p-4">
                        <div class="pt-6">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.268 21a2 2 0 0 0 3.464 0"/><path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"/></svg>
                            </div>
                            <h3 class="mt-4 text-xl font-semibold">Recordatorios Automáticos</h3>
                            <p class="mt-2 text-gray-600">
                                Recibe notificaciones por email antes de cada pago para que nunca te sorprendan.
                            </p>
                        </div>
                    </div>

                    <!-- Card Ahorra -->
                    <div class="card border-1 border-gray-300 rounded-lg shadow-sm p-4">
                        <div class="pt-6">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 17h6v-6"/><path d="m22 17-8.5-8.5-5 5L2 7"/></svg>
                            </div>
                            <h3 class="mt-4 text-xl font-semibold">Ahorra Dinero</h3>
                            <p class="mt-2 text-gray-600">
                                Indentifica suscripciones que no usas y reduce tus gastos mensuales significativamente.
                            </p>
                        </div>
                    </div>

                    <!-- Card Seguro -->
                    <div class="card border-1 border-gray-300 rounded-lg shadow-sm p-4">
                        <div class="pt-6">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
                            </div>
                            <h3 class="mt-4 text-xl font-semibold">Seguro y Privado</h3>
                            <p class="mt-2 text-gray-600">
                                Tus datos están protegidos con encriptación de nivel bancario. Cumplimos con RGPD
                            </p>
                        </div>
                    </div>

                    <!-- Card Rapido -->
                    <div class="card border-1 border-gray-300 rounded-lg shadow-sm p-4">
                        <div class="pt-6">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/></svg>
                            </div>
                            <h3 class="mt-4 text-xl font-semibold">Rápido y Fácil</h3>
                            <p class="mt-2 text-gray-600">
                                Interfaz intuitiva y responsive. Accede desde cualquier dispositivo en segundos.
                            </p>
                        </div>
                    </div>
                </div>
               </section>

               <!-- Pricing Section -->
                <section id="pricing" role="region" aria-label="Planes y precios" class="pt-20 pb-28 bg-gray-50">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="mx-auto max-w-2xl text-center">
                            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Planes para cada necesidad</h2>
                            <p class="mt-3 text-gray-600">Comienza gratis y actualiza cuando necesites más funcionalidades.</p>
                        </div>

                        <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Free Plan -->
                            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                            <!-- Users icon -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 3.128a4 4 0 0 1 0 7.744" />
                                                <circle cx="9" cy="7" r="4" stroke="currentColor" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">Usuario Estándar</h3>
                                            <p class="text-sm text-gray-500">Ideal para empezar</p>
                                        </div>
                                    </div>

                                    <div class="mt-6">
                                        <div class="flex items-baseline gap-2">
                                            <span class="text-4xl font-bold text-gray-900">Gratis</span>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-600">Para empezar a gestionar tus suscripciones sin compromisos.</p>
                                    </div>

                                    <ul class="mt-6 space-y-3">
                                        <li class="flex items-start gap-3">
                                            <span class="flex-shrink-0 inline-flex items-center justify-center h-7 w-7 rounded-full bg-green-100 text-green-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4"/></svg>
                                            </span>
                                            <span class="text-sm text-gray-700">Hasta 10 suscripciones</span>
                                        </li>
                                        <li class="flex items-start gap-3">
                                            <span class="flex-shrink-0 inline-flex items-center justify-center h-7 w-7 rounded-full bg-green-100 text-green-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4"/></svg>
                                            </span>
                                            <span class="text-sm text-gray-700">Estadísticas básicas</span>
                                        </li>
                                        <li class="flex items-start gap-3">
                                            <span class="flex-shrink-0 inline-flex items-center justify-center h-7 w-7 rounded-full bg-green-100 text-green-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4"/></svg>
                                            </span>
                                            <span class="text-sm text-gray-700">Recordatorios por email</span>
                                        </li>
                                        <li class="flex items-start gap-3">
                                            <span class="flex-shrink-0 inline-flex items-center justify-center h-7 w-7 rounded-full bg-green-100 text-green-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4"/></svg>
                                            </span>
                                            <span class="text-sm text-gray-700">Acceso a foros</span>
                                        </li>
                                    </ul>
                                </div>

                                <div class="mt-6">
                                    <a href="auth/signup.php">
                                        <button aria-label="Comenzar plan gratis" class="w-full border-1 border-gray-400 md:w-auto p-2 pl-4 pr-4 rounded-xl text-black text-center duration-300 hover:bg-cyan-600 hover:text-white hover:cursor-pointer">
                                            Comenzar Gratis
                                        </button>
                                    </a>
                                </div>
                            </div>

                            <!-- Premium Plan -->
                            <div class="relative bg-white rounded-2xl p-6 border-2 border-transparent shadow-lg overflow-hidden">
                                <div class="absolute -inset-px rounded-2xl pointer-events-none" aria-hidden="true"></div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-50 text-yellow-600">
                                            <!-- Bolt icon -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">Usuario Premium</h3>
                                            <p class="text-sm text-gray-500">Funcionalidades avanzadas</p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center rounded-full bg-blue-600 px-3 py-1 text-sm font-medium text-white">Popular</span>
                                </div>

                                <div class="mt-6">
                                    <div class="flex items-baseline gap-3">
                                        <span class="text-4xl font-extrabold text-gray-900">€4.99</span>
                                        <span class="text-sm text-gray-500">/mes</span>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-600">Para usuarios avanzados que necesitan más control y datos.</p>
                                </div>

                                <ul class="mt-6 space-y-3">
                                    <li class="flex items-start gap-3">
                                        <span class="flex-shrink-0 inline-flex items-center justify-center h-7 w-7 rounded-full bg-green-100 text-green-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4"/></svg>
                                        </span>
                                        <span class="text-sm text-gray-700">Suscripciones ilimitadas</span>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="flex-shrink-0 inline-flex items-center justify-center h-7 w-7 rounded-full bg-green-100 text-green-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4"/></svg>
                                        </span>
                                        <span class="text-sm text-gray-700">Estadísticas avanzadas</span>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="flex-shrink-0 inline-flex items-center justify-center h-7 w-7 rounded-full bg-green-100 text-green-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4"/></svg>
                                        </span>
                                        <span class="text-sm text-gray-700">Exportación a CSV</span>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="flex-shrink-0 inline-flex items-center justify-center h-7 w-7 rounded-full bg-green-100 text-green-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4"/></svg>
                                        </span>
                                        <span class="text-sm text-gray-700">Historial detallado</span>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="flex-shrink-0 inline-flex items-center justify-center h-7 w-7 rounded-full bg-green-100 text-green-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4"/></svg>
                                        </span>
                                        <span class="text-sm text-gray-700">Soporte prioritario</span>
                                    </li>
                                </ul>

                                <div class="mt-6">
                                    <button aria-label="Actualizar a premium" class="w-full md:w-auto p-2 pl-4 pr-4 bg-blue-800 rounded-xl text-white text-center duration-300 hover:bg-blue-700 hover:cursor-pointer">
                                        Actualizar a Premium
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- CTA Section -->
                <section class="container mx-auto px-4 py-20 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-3xl rounded-2xl bg-blue-800 px-8 py-16 text-center">
                        <h2 class="text-white text-balance text-3xl font-bold tracking-tight text-primary-foreground/90">
                            Comienza a ahorrar hoy mismo
                        </h2>
                        <p class="mt-4 text-white text-pretty text-lg text-primary-foreground/90">
                            Únete a miles de usuarios que ya están controlando sus suscripciones con SubsZero
                        </p>
                        <div class="mt-8">
                            <a href="auth/signup.php">
                                <button class="w-full border-1 border-gray-400 bg-gray-100 md:w-auto p-2 pl-4 pr-4 rounded-xl text-black text-center duration-300 hover:bg-cyan-600 hover:text-white hover:cursor-pointer">
                                    Crear Cuenta Gratis
                                </button>
                            </a>
                        </div>
                    </div>
                </section>
        </main>

                <!-- Footer -->
                 <footer class="border-t-1 border-gray-300 bg-gray-50 py-12">
                    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                        
                        <!-- Logo y descripción -->
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

                        <!-- Producto -->
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

                        <!-- Legal -->
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

                        <!-- Soporte -->
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