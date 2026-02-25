// Autor: Sandro Pereira (smpsandro1239)
// Projeto: IOTCNT – Sistema de Gestão Industrial IoT para Condensadores
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="IOTCNT - Sistema IoT de Arrefecimento Industrial">
    <meta name="keywords" content="IoT, industrial, condensadores, arrefecimento, monitorização">
    <meta name="author" content="Sandro Pereira">
    <title>@yield("title", "IOTCNT")</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset(css/app.css) }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset(favicon.ico) }}">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        industrial-primary: #0F172A,
                        industrial-secondary: #1E293B,
                        industrial-accent: #3B82F6,
                        industrial-success: #10B981,
                        industrial-error: #EF4444,
                        industrial-warning: #F59E0B
                    },
                    fontFamily: {
                        sans: [Inter, sans-serif],
                        mono: [Roboto Mono, monospace]
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo and Brand -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-xl font-bold text-industrial-accent">IOTCNT</h1>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <a href="{{ url(/) }}" class="text-gray-900 hover:text-industrial-accent px-3 py-2 rounded-md text-sm font-medium">Início</a>
                            <a href="{{ url(/dashboard-user) }}" class="text-gray-500 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                            <a href="{{ url(/esp32-dashboard) }}" class="text-gray-500 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">ESP32</a>
                            <a href="{{ url(/system-settings) }}" class="text-gray-500 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">Configurações</a>
                        </div>
                    </div>
                </div>
                
                <!-- User Menu -->
                <div class="hidden md:flex items-center space-x-4">
                    <button class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-bell"></i>
                    </button>
                    <button class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-cog"></i>
                    </button>
                    <div class="flex items-center">
                        <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name=Sandro&background=3B82F6&color=fff" alt="Avatar">
                        <span class="ml-2 text-sm font-medium text-gray-700">Sandro</span>
                    </div>
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield(content)
    </main>

    <!-- Footer -->
    <footer class="bg-industrial-primary text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <p class="text-sm">© 2026 IOTCNT - Sistema de Gestão Industrial IoT para Condensadores</p>
                <p class="text-xs mt-2 text-gray-400">Desenvolvido por Sandro Pereira (smpsandro1239)</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="{{ asset(js/bootstrap.js) }}"></script>
    <script src="{{ asset(js/app.js) }}"></script>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</body>
</html>
