// Autor: Sandro Pereira (smpsandro1239)
// Projeto: IOTCNT – Sistema de Gestão Industrial IoT para Condensadores
@extends("layouts.app")

@section("title", "IOTCNT - Dashboard Administrativo")

@section("content")
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard Administrativo</h1>
            <p class="mt-2 text-sm text-gray-600">Monitorização e controlo completo do sistema IOTCNT</p>
        </div>
        
        <!-- Painel de Controlo Principal -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Sistemas Ativos</p>
                        <p class="text-2xl font-semibold text-gray-900">8/10</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Alertas Ativos</p>
                        <p class="text-2xl font-semibold text-red-600">3</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Manutenções</p>
                        <p class="text-2xl font-semibold text-yellow-600">2</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Performance</p>
                        <p class="text-2xl font-semibold text-green-600">98%</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sistema de Alertas -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Sistema de Alertas</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center p-4 bg-red-50 rounded-lg">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">Alerta Crítico</p>
                            <p class="text-sm text-red-700">Temperatura elevada no condensador C - Imediato</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center p-4 bg-yellow-50 rounded-lg">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-yellow-800">Alerta de Manutenção</p>
                            <p class="text-sm text-yellow-700">Manutenção prevista para condensador B - Amanhã</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Ações Rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="#" class="bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow">
                <h4 class="text-lg font-medium text-gray-900 mb-2">Gestão de Usuários</h4>
                <p class="text-sm text-gray-600">Gerir permissões e acesso ao sistema</p>
            </a>
            
            <a href="#" class="bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow">
                <h4 class="text-lg font-medium text-gray-900 mb-2">Configurações do Sistema</h4>
                <p class="text-sm text-gray-600">Ajustes gerais e parâmetros</p>
            </a>
            
            <a href="#" class="bg-white rounded-lg shadow p-6 hover:shadow-md transition-shadow">
                <h4 class="text-lg font-medium text-gray-900 mb-2">Relatórios e Logs</h4>
                <p class="text-sm text-gray-600">Acessar logs e gerar relatórios</p>
            </a>
        </div>
    </div>
</div>
@endsection
