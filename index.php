<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SubsZero - Subcription Control App</title>
    <link rel="icon" href="assets/favicon.ico">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- PayPal SDK — añade tu client-id -->
    <script src="https://www.paypal.com/sdk/js?client-id=AQVfX8raEBeV1w-SsJs0ZODKEEH1soC064hlMoVVAYgBuvrb6R9_OfitdZaMVCox3RYVE2m29dxn0EGM&currency=EUR"></script>
</head>
<body class="bg-white">

    <!-- HEADER -->
    <header class="flex flex-col md:flex-row h-auto md:h-16 border-b border-gray-300 p-4 md:p-0">
        <div class="flex flex-row items-center mx-4 md:ml-8 lg:ml-32 space-x-4">
            <div class="pl-3 pr-3 pt-1 pb-1 md:pl-4 md:pr-4 md:pt-2 md:pb-2 bg-blue-800 rounded-xl">
                <h1 class="text-white text-lg md:text-xl font-bold">S</h1>
            </div>
            <h1 class="text-lg md:text-xl font-bold"><a href="index.php">SubsZero</a></h1>
        </div>

        <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-4 mt-4 md:mt-0 md:ml-auto mx-4 md:mr-8 lg:mr-32">
            <a href="auth/login.php">
                <button class="w-full md:w-auto p-2 pl-4 pr-4 rounded-xl text-black duration-300 hover:bg-cyan-600 hover:text-white">
                    Iniciar Sesión
                </button>
            </a>

            <a href="auth/signup.php">
                <button class="w-full md:w-auto p-2 pl-4 pr-4 bg-blue-800 rounded-xl text-white duration-300 hover:bg-blue-700">
                    Registrarse
                </button>
            </a>
        </div>
    </header>

    <main>

        <!-- HERO -->
        <section class="container mx-auto px-4 py-20 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-4xl text-center">
                <h1 class="text-balance text-5xl font-bold tracking-tight sm:text-6xl lg:text-7xl">
                    Controla tus suscripciones en un solo lugar
                </h1>

                <p class="mt-6 text-gray-600 text-lg leading-relaxed sm:text-xl">
                    SubsZero te ayuda a gestionar todas tus suscripciones, analizar tus gastos y
                    recibir recordatorios antes de cada pago.
                </p>

                <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                    <a href="auth/signup.php">   
                        <button class="p-3 px-6 bg-blue-800 rounded-xl text-white hover:bg-blue-700 duration-300">
                            Comenzar Gratis
                        </button>
                    </a>

                    <a href="auth/login.php">
                        <button class="p-3 px-6 border border-gray-400 rounded-xl hover:bg-cyan-600 hover:text-white duration-300">
                            Iniciar Sesión
                        </button>
                    </a>
                </div>

                <p class="mt-4 text-sm text-gray-600">
                    Sin tarjeta de crédito requerida · Cancela cuando quieras
                </p>
            </div>
        </section>

        <!-- STATS -->
        <section class="border-y border-gray-300 bg-gray-50 py-12"> 
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 text-center">
                    <div>
                        <div class="text-4xl font-bold text-blue-800">€2400</div>
                        <div class="mt-2 text-sm text-gray-600">Ahorro promedio anual</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-blue-800">15+</div>
                        <div class="mt-2 text-sm text-gray-600">Suscripciones gestionadas</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold text-blue-800">10k+</div>
                        <div class="mt-2 text-sm text-gray-600">Usuarios activos</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FEATURES -->
        <section id="features" class="container mx-auto px-4 py-20 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold sm:text-4xl">Todo lo que necesitas</h2>
                <p class="mt-4 text-gray-600 text-lg">
                    Herramientas potentes para mantener el control de tus gastos.
                </p>
            </div>

            <div class="mt-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

                <!-- CARD 1 -->
                <div class="border border-gray-300 rounded-xl shadow-sm p-6 bg-white">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <rect width="20" height="14" x="2" y="5" rx="2"/>
                            <line x1="2" x2="22" y1="10" y2="10"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-xl font-semibold">Gestión Centralizada</h3>
                    <p class="mt-2 text-gray-600">Todas tus suscripciones en un solo panel.</p>
                </div>

                <!-- CARD 2 -->
                <div class="border border-gray-300 rounded-xl shadow-sm p-6 bg-white">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M3 3v16a2 2 0 0 0 2 2h16"/>
                            <path d="M18 17V9"/>
                            <path d="M13 17V5"/>
                            <path d="M8 17v-3"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-xl font-semibold">Análisis Detallado</h3>
                    <p class="mt-2 text-gray-600">Gráficos e informes de tus gastos.</p>
                </div>

                <!-- CARD 3 -->
                <div class="border border-gray-300 rounded-xl shadow-sm p-6 bg-white">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M10.268 21a2 2 0 0 0 3.464 0"/>
                            <path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-xl font-semibold">Recordatorios Automáticos</h3>
                    <p class="mt-2 text-gray-600">Avisa antes de cada pago.</p>
                </div>

                <!-- CARD 4 -->
                <div class="border border-gray-300 rounded-xl shadow-sm p-6 bg-white">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M16 17h6v-6"/>
                            <path d="m22 17-8.5-8.5-5 5L2 7"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-xl font-semibold">Ahorra Dinero</h3>
                    <p class="mt-2 text-gray-600">Detecta suscripciones que no usas.</p>
                </div>

                <!-- CARD 5 -->
                <div class="border border-gray-300 rounded-xl shadow-sm p-6 bg-white">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/>
                            <path d="m9 12 2 2 4-4"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-xl font-semibold">Seguro y Privado</h3>
                    <p class="mt-2 text-gray-600">Encriptación y cumplimiento RGPD.</p>
                </div>

                <!-- CARD 6 -->
                <div class="border border-gray-300 rounded-xl shadow-sm p-6 bg-white">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-xl font-semibold">Rápido y Fácil</h3>
                    <p class="mt-2 text-gray-600">Interfaz intuitiva en todos los dispositivos.</p>
                </div>

            </div>
        </section>

        <!-- PRICING -->
        <!-- ⭐⭐ ESTA ES LA SECCIÓN QUE HAS PEDIDO MEJORADA ⭐⭐ -->
        <section id="pricing" role="region" aria-label="Planes y precios" class="pt-20 pb-28 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="mx-auto max-w-2xl text-center mb-12">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Planes para cada necesidad</h2>
                    <p class="mt-3 text-gray-600">Comienza gratis y actualiza cuando necesites más funcionalidades.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

                    <!-- FREE PLAN -->
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-md p-8 flex flex-col justify-between">
                        <div class="space-y-6">

                            <div class="flex items-center gap-3">
                                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Usuario Estándar</h3>
                                    <p class="text-sm text-gray-500">Ideal para empezar</p>
                                </div>
                            </div>

                            <div>
                                <span class="text-4xl font-bold text-gray-900">Gratis</span>
                                <p class="mt-2 text-sm text-gray-600">Empieza sin pagar nada.</p>
                            </div>

                            <ul class="space-y-3">
                                <li class="flex gap-3"><span class="bg-green-100 text-green-600 rounded-full h-7 w-7 flex items-center justify-center">✔</span>Hasta 10 suscripciones</li>
                                <li class="flex gap-3"><span class="bg-green-100 text-green-600 rounded-full h-7 w-7 flex items-center justify-center">✔</span>Estadísticas básicas</li>
                                <li class="flex gap-3"><span class="bg-green-100 text-green-600 rounded-full h-7 w-7 flex items-center justify-center">✔</span>Recordatorios por email</li>
                                <li class="flex gap-3"><span class="bg-green-100 text-green-600 rounded-full h-7 w-7 flex items-center justify-center">✔</span>Acceso a foros</li>
                            </ul>
                        </div>

                        <div class="mt-8">
                            <a href="auth/signup.php">
                                <button class="w-full p-3 rounded-xl border border-gray-400 hover:bg-cyan-600 hover:text-white duration-300">
                                    Comenzar Gratis
                                </button>
                            </a>
                        </div>
                    </div>

                    <!-- PREMIUM PLAN -->
                    <div class="bg-white border border-blue-600 rounded-2xl shadow-lg p-8 flex flex-col justify-between relative">

                        <span class="absolute top-4 right-4 bg-blue-600 text-white text-xs font-semibold px-3 py-1 rounded-full">
                            Popular
                        </span>

                        <div class="space-y-6">

                            <div class="flex items-center gap-3">
                                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-yellow-100 text-yellow-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Usuario Premium</h3>
                                    <p class="text-sm text-gray-500">Funcionalidades avanzadas</p>
                                </div>
                            </div>

                            <div>
                                <div class="flex items-end gap-2">
                                    <span class="text-4xl font-extrabold text-gray-900">€4.99</span>
                                    <span class="text-sm text-gray-500">/mes</span>
                                </div>
                                <p class="mt-2 text-sm text-gray-600">Para un control total.</p>
                            </div>

                            <ul class="space-y-3">
                                <li class="flex gap-3"><span class="bg-green-100 text-green-600 rounded-full h-7 w-7 flex items-center justify-center">✔</span>Suscripciones ilimitadas</li>
                                <li class="flex gap-3"><span class="bg-green-100 text-green-600 rounded-full h-7 w-7 flex items-center justify-center">✔</span>Estadísticas avanzadas</li>
                                <li class="flex gap-3"><span class="bg-green-100 text-green-600 rounded-full h-7 w-7 flex items-center justify-center">✔</span>Exportación CSV</li>
                                <li class="flex gap-3"><span class="bg-green-100 text-green-600 rounded-full h-7 w-7 flex items-center justify-center">✔</span>Historial completo</li>
                                <li class="flex gap-3"><span class="bg-green-100 text-green-600 rounded-full h-7 w-7 flex items-center justify-center">✔</span>Soporte prioritario</li>
                            </ul>
                        </div>

                        <div class="mt-8" id="paypal-button-container"></div>

                        <script>
                            paypal.Buttons({
                                createOrder: function(data, actions) {
                                    return actions.order.create({
                                        purchase_units: [{
                                            description: "Suscripción Premium SubsZero",
                                            amount: { value: '9.99', currency_code: 'EUR' }
                                        }]
                                    });
                                },
                                onApprove: function(data, actions) {
                                    return actions.order.capture().then(function(details) {
                                        alert('Pago completado con éxito por ' + details.payer.name.given_name);
                                    });
                                },
                                onCancel: function() {
                                    alert('Pago cancelado. No se te ha cobrado nada.');
                                },
                                onError: function(err) {
                                    alert('Ocurrió un error. Inténtalo de nuevo.');
                                }
                            }).render('#paypal-button-container');
                        </script>

                    </div>

                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="container mx-auto px-4 py-20 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl rounded-2xl bg-blue-800 px-8 py-16 text-center">
                <h2 class="text-white text-3xl font-bold">Comienza a ahorrar hoy mismo</h2>
                <p class="mt-4 text-white text-lg">Miles de usuarios ya están controlando sus suscripciones.</p>

                <a href="auth/signup.php">
                    <button class="mt-8 p-3 px-6 rounded-xl bg-gray-100 hover:bg-cyan-600 hover:text-white duration-300">
                        Crear Cuenta Gratis
                    </button>
                </a>
            </div>
        </section>

    </main>

    <!-- FOOTER -->
    <footer class="border-t border-gray-300 bg-gray-50 py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

                <div>
                    <div class="flex items-center gap-2">
                        <div class="h-8 w-8 flex items-center justify-center bg-blue-800 rounded-xl text-white font-bold">S</div>
                        <span class="text-xl font-bold">SubsZero</span>
                    </div>
                    <p class="mt-4 text-sm text-gray-600">Control total de tus suscripciones.</p>
                </div>

                <div>
                    <h4 class="font-semibold">Producto</h4>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li><a href="#features">Características</a></li>
                        <li><a href="#pricing">Precios</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold">Legal</h4>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li><a href="documentation/privacy-policy.html">Privacidad</a></li>
                        <li><a href="documentation/terms-and-conditions.html">Términos</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold">Soporte</h4>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li><a href="pages/ayuda.php">Ayuda</a></li>
                        <li><a href="pages/contact.php">Contacto</a></li>
                    </ul>
                </div>

            </div>

            <div class="mt-12 border-t border-gray-300 pt-8 text-center text-sm text-gray-600">
                © <?php echo date('Y'); ?> SubsZero. Todos los derechos reservados.
            </div>

        </div>
    </footer>

</body>
</html>
