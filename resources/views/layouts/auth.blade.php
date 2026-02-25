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
    <title>@yield("title", "IOTCNT - Autenticação")</title>
    
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
<body class="font-sans bg-gradient-to-br from-indigo-600 to-purple-700 min-h-screen flex items-center justify-center p-4">
    <!-- Main Content -->
    <main>
        @yield(content)
    </main>

    <!-- JavaScript -->
    <script src="{{ asset(js/bootstrap.js) }}"></script>
    <script src="{{ asset(js/app.js) }}"></script>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</body>
</html>
