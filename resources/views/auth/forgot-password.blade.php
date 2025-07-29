<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'IOTCNT') }} - Recuperar Palavra-passe</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div>
            <a href="/">
                <div class="w-20 h-20 fill-current text-gray-500 flex items-center justify-center bg-blue-600 rounded-full text-white text-2xl font-bold">
                    IOT
                </div>
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-4 text-center">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    {{ __('Recuperar Palavra-passe') }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                    {{ __('Esqueceu-se da sua palavra-passe? Sem problema. Indique o seu endereço de email e enviaremos um link para definir uma nova palavra-passe.') }}
                </p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                        {{ __('Email') }}
                    </label>
                    <input id="email" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                           type="email" name="email" value="{{ old('email') }}" required autofocus />
                    @error('email')
                        <div class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex items-center justify-between mt-4">
                    <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                        {{ __('Voltar ao login') }}
                    </a>

                    <button type="submit" class="ml-4 inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        {{ __('Enviar Link') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
