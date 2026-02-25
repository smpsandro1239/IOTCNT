@extends("layouts.app")

@section("title", "IOTCNT - Sistema IoT de Arrefecimento Industrial")

@section("content")
<div class="min-h-screen bg-gradient-to-br from-indigo-600 to-purple-700">
    <div class="container mx-auto px-4 py-8">
        <div class="text-center text-white">
            <h1 class="text-4xl md:text-6xl font-bold mb-4">IOTCNT</h1>
            <p class="text-xl md:text-2xl mb-8">Sistema IoT de Arrefecimento Industrial</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6">
                    <h3 class="text-xl font-semibold mb-2">Monitorização em Tempo Real</h3>
                    <p>Controlo completo de condensadores com dados IoT em tempo real</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6">
                    <h3 class="text-xl font-semibold mb-2">Automação Inteligente</h3>
                    <p>Sistema de agendamento e controlo automatizado de equipamentos</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6">
                    <h3 class="text-xl font-semibold mb-2">Segurança e Confiabilidade</h3>
                    <p>Monitorização preventiva para prevenção de legionela</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
