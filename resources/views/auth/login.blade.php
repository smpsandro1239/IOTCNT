// Autor: Sandro Pereira (smpsandro1239)
// Projeto: IOTCNT – Sistema de Gestão Industrial IoT para Condensadores
@extends("layouts.auth")

@section("title", "IOTCNT - Login")

@section("content")
<div class="min-h-screen bg-gradient-to-br from-indigo-600 to-purple-700 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">IOTCNT</h1>
            <p class="text-gray-600">Sistema IoT de Arrefecimento Industrial</p>
        </div>
        
        <form action="{{ route(auth.login) }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                    placeholder="seu@empresa.com"
                >
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Senha
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                    placeholder="••••••••"
                >
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input
                        id="remember"
                        name="remember"
                        type="checkbox"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        Lembrar-me
                    </label>
                </div>
                
                <a href="#" class="text-sm text-indigo-600 hover:text-indigo-500">
                    Esqueceu a senha?
                </a>
            </div>
            
            <button
                type="submit"
                class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors font-medium"
            >
                Entrar no Sistema
            </button>
        </form>
        
        <div class="mt-8 text-center text-sm text-gray-600">
            <p>
                Ainda não tem acesso? 
                <a href="#" class="text-indigo-600 hover:text-indigo-500 font-medium">
                    Solicitar acesso
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
