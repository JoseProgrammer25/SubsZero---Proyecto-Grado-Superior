<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ayuda - SubsZero</title>
        <link rel="icon" href="../assets/favicon.ico">
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <style>
            .icon-green { color: #4CAF50; } 
            .icon-purple { color: #9C27B0; } 
            /* Clase para ocultar el contenido de la respuesta por defecto en el acordeón */
            .faq-content {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease-out, padding 0.3s ease-out;
            }
            /* Clase para mostrar el contenido */
            .faq-content.active {
                max-height: 500px; 
                padding-bottom: 1rem;
            }
        </style>
    </head>
    <body>
        <header class="flex flex-col md:flex-row h-auto md:h-16 border-b border-gray-300 p-4 md:p-0">
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

        <main class="container mx-auto px-4 py-8 max-w-5xl">

            <div class="text-center">
                <h1 class="text-4xl sm:text-5xl font-bold mt-8">Centro de Ayuda</h1>
                <p class="text-gray-500 mt-2 text-lg">Encuentra respuestas a tus preguntas sobre SubsZero</p>
             </div>

            <div class="mt-10 mb-8">
                <div class="relative">
                    <input type="text" id="search-input" placeholder="Busca tu pregunta..." class="w-full p-4 pl-12 border border-gray-300 rounded-xl shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150" />
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 absolute top-1/2 left-3 transform -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-3" id="filter-buttons">
                <button class="filter-btn bg-blue-800 text-white font-medium px-4 py-2 rounded-xl transition duration-300" data-filter="todos">
                    Todos
                </button>
                <button class="filter-btn bg-gray-100 text-gray-700 font-medium px-4 py-2 rounded-xl hover:bg-gray-200 transition duration-300" data-filter="Cuenta">
                    Cuenta
                </button>
                <button class="filter-btn bg-gray-100 text-gray-700 font-medium px-4 py-2 rounded-xl hover:bg-gray-200 transition duration-300" data-filter="Suscripciones">
                    Suscripciones
                </button>
                <button class="filter-btn bg-gray-100 text-gray-700 font-medium px-4 py-2 rounded-xl hover:bg-gray-200 transition duration-300" data-filter="Premium">
                    Premium
                </button>
                <button class="filter-btn bg-gray-100 text-gray-700 font-medium px-4 py-2 rounded-xl hover:bg-gray-200 transition duration-300" data-filter="Estadísticas">
                    Estadísticas
                </button>
                <button class="filter-btn bg-gray-100 text-gray-700 font-medium px-4 py-2 rounded-xl hover:bg-gray-200 transition duration-300" data-filter="Notificaciones">
                    Notificaciones
                </button>
            </div>

            <div class="mt-10 space-y-4" id="faq-list">
                
                <div class="faq-item border border-gray-200 rounded-lg shadow-sm overflow-hidden" data-category="Cuenta">
                    <button class="faq-toggle w-full text-left p-4 flex justify-between items-center focus:outline-none">
                        <div>
                            <span class="text-sm text-gray-500 font-semibold uppercase">Cuenta</span>
                            <h3 class="faq-title text-lg font-semibold text-gray-800 mt-1">¿Cómo creo una cuenta en SubsZero?</h3>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="faq-arrow h-6 w-6 text-gray-500 transform transition duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="faq-content px-4 border-t border-gray-100">
                        <p class="pb-4">Crear tu cuenta es muy fácil. Ve a la sección de **Registrarse** o haz clic en el botón "Registrarse" del encabezado. Necesitarás proporcionar un email y una contraseña. ¡Comenzarás automáticamente con el plan Estándar (Gratis)!</p>
                    </div>
                </div>

                <div class="faq-item border border-gray-200 rounded-lg shadow-sm overflow-hidden" data-category="Suscripciones">
                    <button class="faq-toggle w-full text-left p-4 flex justify-between items-center focus:outline-none">
                        <div>
                            <span class="text-sm text-gray-500 font-semibold uppercase">Suscripciones</span>
                            <h3 class="faq-title text-lg font-semibold text-gray-800 mt-1">¿Cómo añado una nueva suscripción?</h3>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="faq-arrow h-6 w-6 text-gray-500 transform transition duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="faq-content px-4 border-t border-gray-100">
                        <p class="pb-4">Para añadir una nueva suscripción, ve a tu panel de control, haz clic en el botón **"Añadir Suscripción"** e introduce los detalles, como el nombre del servicio, la fecha de pago y la frecuencia.</p>
                    </div>
                </div>
                
                <div class="faq-item border border-gray-200 rounded-lg shadow-sm overflow-hidden" data-category="Premium">
                    <button class="faq-toggle w-full text-left p-4 flex justify-between items-center focus:outline-none">
                        <div>
                            <span class="text-sm text-gray-500 font-semibold uppercase">Premium</span>
                            <h3 class="faq-title text-lg font-semibold text-gray-800 mt-1">¿Qué métodos de pago acepta SubsZero Premium?</h3>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="faq-arrow h-6 w-6 text-gray-500 transform transition duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="faq-content px-4 border-t border-gray-100">
                        <p class="pb-4">Aceptamos las principales tarjetas de crédito (Visa, MasterCard, Amex) y también pagos a través de PayPal. Toda la información de pago es manejada por Stripe, garantizando la máxima seguridad.</p>
                    </div>
                </div>

                <div class="faq-item border border-gray-200 rounded-lg shadow-sm overflow-hidden" data-category="Cuenta">
                    <button class="faq-toggle w-full text-left p-4 flex justify-between items-center focus:outline-none">
                        <div>
                            <span class="text-sm text-gray-500 font-semibold uppercase">Cuenta</span>
                            <h3 class="faq-title text-lg font-semibold text-gray-800 mt-1">¿Puedo cambiar mi email de acceso?</h3>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="faq-arrow h-6 w-6 text-gray-500 transform transition duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="faq-content px-4 border-t border-gray-100">
                        <p class="pb-4">Sí, puedes cambiar tu dirección de email desde la configuración de tu perfil. Por motivos de seguridad, te pediremos que confirmes tu contraseña actual antes de realizar el cambio.</p>
                    </div>
                </div>

                <div class="faq-item border border-gray-200 rounded-lg shadow-sm overflow-hidden" data-category="Suscripciones">
                    <button class="faq-toggle w-full text-left p-4 flex justify-between items-center focus:outline-none">
                        <div>
                            <span class="text-sm text-gray-500 font-semibold uppercase">Suscripciones</span>
                            <h3 class="faq-title text-lg font-semibold text-gray-800 mt-1">¿Cómo puedo ver mi gasto total mensual?</h3>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="faq-arrow h-6 w-6 text-gray-500 transform transition duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="faq-content px-4 border-t border-gray-100">
                        <p class="pb-4">Tu gasto total mensual se muestra automáticamente en la sección de **Estadísticas**. También puedes filtrar por moneda y por periodo de tiempo para un análisis más detallado.</p>
                    </div>
                </div>

            </div>
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
                                <a href="ayuda.php" class="text-sm">
                                    Ayuda
                                </a>
                            </li>
                            <li>
                                <a href="contact.php" class="text-sm">
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

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                
                const faqToggles = document.querySelectorAll('.faq-toggle');

                faqToggles.forEach(button => {
                    button.addEventListener('click', function() {
                        const content = this.nextElementSibling; // El div .faq-content
                        const arrow = this.querySelector('.faq-arrow'); // El icono SVG de la flecha
                        
                        // Toggle (alternar) la clase 'active' en el contenido
                        content.classList.toggle('active');

                        // Alternar la rotación de la flecha
                        // Queremos rotarla 180 grados (hacia arriba) cuando está abierta.
                        if (content.classList.contains('active')) {
                            arrow.style.transform = 'rotate(180deg)';
                        } else {
                            arrow.style.transform = 'rotate(0deg)';
                        }
                    });
                });

                const filterButtons = document.querySelectorAll('.filter-btn');
                const faqItems = document.querySelectorAll('.faq-item');

                filterButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const filterValue = this.getAttribute('data-filter');
                        
                        // Resetea la apariencia de los botones
                        filterButtons.forEach(btn => {
                            btn.classList.remove('bg-blue-800', 'text-white', 'hover:bg-cyan-600');
                            btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                        });
                        // Marca el botón activo
                        this.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                        this.classList.add('bg-blue-800', 'text-white');

                        // Ocultar/Mostrar elementos FAQ según el filtro
                        faqItems.forEach(item => {
                            const itemCategory = item.getAttribute('data-category');
                            
                            // Si el filtro es 'todos' O la categoría del elemento coincide
                            if (filterValue === 'todos' || itemCategory === filterValue) {
                                item.style.display = 'block';
                            } else {
                                item.style.display = 'none';
                            }
                        });
                        
                        // Resetear el campo de búsqueda cuando se hace clic en un filtro
                        document.getElementById('search-input').value = '';
                    });
                });

                const searchInput = document.getElementById('search-input');

                // Escuchar el evento 'input' (cada vez que se escribe o borra algo)
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    
                    // Desactivar todos los chips cuando se comienza a buscar
                    filterButtons.forEach(btn => {
                        btn.classList.remove('bg-blue-800', 'text-white', 'hover:bg-cyan-600');
                        btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    });
                    
                    // Si el campo de búsqueda está vacío, reactivar el chip "Todos"
                    if (searchTerm === '') {
                        document.querySelector('[data-filter="todos"]').classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                        document.querySelector('[data-filter="todos"]').classList.add('bg-blue-800', 'text-white');
                    }


                    faqItems.forEach(item => {
                        // Obtenemos el texto del título (la pregunta) para buscar
                        const titleElement = item.querySelector('.faq-title');
                        const itemTitle = titleElement ? titleElement.textContent.toLowerCase() : '';
                        
                        // Ocultar/Mostrar el elemento basado en si el título incluye el término de búsqueda
                        if (itemTitle.includes(searchTerm)) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });
        </script>
    </body>
</html>