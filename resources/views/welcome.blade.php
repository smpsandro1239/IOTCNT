<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IOTCNT - Sistema de Arrefecimento Industrial</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="gradient-bg min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <h1 class="text-3xl font-bold text-gray-900">IOTCNT</h1>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Sistema de Arrefecimento Industrial</p>
                            <p class="text-xs text-blue-600 font-semibold">EmpresaX - Prevenção de Legionela</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="/login-working.html" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            Entrar
                        </a>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Sistema Online
                        </span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <!-- Hero Section -->
            <div class="text-center mb-12">
                <h2 class="text-4xl font-extrabold text-white mb-4">
                    Sistema de Arrefecimento de Condensadores
                </h2>
                <p class="text-xl text-blue-100 mb-8">
                    Prevenção Automática de Legionela em Centrais de Frio Industriais
                </p>
                <div class="bg-white bg-opacity-20 backdrop-blur-lg rounded-lg p-6 max-w-2xl mx-auto">
                    <p class="text-white text-lg">
                        Desenvolvido para <strong>EmpresaX</strong> com <strong>Empatia</strong> no cuidado
                        com a saúde dos clientes e <strong>Melhoria Contínua</strong> na excelência operacional.
                    </p>
                </div>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                <!-- Sistema de Performance -->
                <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                    <div class="flex items-center mb-4">
                        <div class="bg-blue-500 rounded-lg p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="ml-4 text-xl font-semibold text-gray-900">Sistema de Performance</h3>
                    </div>
                    <p class="text-gray-600 mb-4">
                        Monitorização avançada com métricas em tempo real, optimização automática e detecção de problemas.
                    </p>
                    <a href="/admin/performance" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                        Aceder ao Sistema
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>

                <!-- Prevenção de Legionela -->
                <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                    <div class="flex items-center mb-4">
                        <div class="bg-green-500 rounded-lg p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="ml-4 text-xl font-semibold text-gray-900">Prevenção de Legionela</h3>
                    </div>
                    <p class="text-gray-600 mb-4">
                        Eliminação automática do risco através de circulação controlada de água nos condensadores.
                    </p>
                    <div class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 rounded-lg">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Sistema Activo
                    </div>
                </div>

                <!-- Monitorização -->
                <div class="bg-white rounded-lg shadow-lg p-6 card-hover">
                    <div class="flex items-center mb-4">
                        <div class="bg-purple-500 rounded-lg p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="ml-4 text-xl font-semibold text-gray-900">Monitorização</h3>
                    </div>
                    <p class="text-gray-600 mb-4">
                        Dashboard completo com estado dos condensadores, temperaturas e alertas em tempo real.
                    </p>
                    <a href="/dashboard" class="inline-flex items-center px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors">
                        Ver Dashboard
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Status Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <div class="text-3xl font-bold text-blue-600">5</div>
                    <div class="text-sm text-gray-600">Condensadores</div>
                </div>
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <div class="text-3xl font-bold text-green-600">100%</div>
                    <div class="text-sm text-gray-600">Sistema Operacional</div>
                </div>
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <div class="text-3xl font-bold text-purple-600">24/7</div>
                    <div class="text-sm text-gray-600">Monitorização</div>
                </div>
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <div class="text-3xl font-bold text-red-600">0</div>
                    <div class="text-sm text-gray-600">Alertas Activos</div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">Acesso Rápido</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="/admin/performance" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <div>
                            <div class="font-semibold text-gray-900">Performance</div>
                            <div class="text-sm text-gray-600">Métricas e Optimização</div>
                        </div>
                    </a>

                    <a href="/admin/dashboard" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <svg class="w-8 h-8 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                        </svg>
                        <div>
                            <div class="font-semibold text-gray-900">Admin</div>
                            <div class="text-sm text-gray-600">Painel de Controlo</div>
                        </div>
                    </a>

                    <a href="/admin/logs" class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                        <svg class="w-8 h-8 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <div>
                            <div class="font-semibold text-gray-900">Logs</div>
                            <div class="text-sm text-gray-600">Histórico de Operações</div>
                        </div>
                    </a>

                    <a href="/admin/settings" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                        <svg class="w-8 h-8 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <div>
                            <div class="font-semibold text-gray-900">Configurações</div>
                            <div class="text-sm text-gray-600">Parâmetros do Sistema</div>
                        </div>
                    </a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white bg-opacity-10 backdrop-blur-lg mt-12">
            <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <p class="text-white text-lg font-semibold mb-2">
                        IOTCNT - Sistema de Arrefecimento Industrial
                    </p>
                    <p class="text-blue-100 mb-4">
                        Desenvolvido com <span class="text-red-300">❤️</span> para EmpresaX
                    </p>
                    <div class="flex justify-center space-x-6 text-sm text-blue-200">
                        <span>✅ Prevenção de Legionela</span>
                        <span>✅ Monitorização 24/7</span>
                        <span>✅ Optimização Automática</span>
                        <span>✅ Saúde Pública</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
