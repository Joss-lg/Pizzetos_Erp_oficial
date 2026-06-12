<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizzetos - Iniciar Sesión</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex items-center justify-center bg-yellow-500 font-sans">

    <div class="w-full max-w-md px-6">
        <div class="bg-white rounded-3xl shadow-2xl p-10">

            {{-- Logo --}}
            <div class="text-center mb-8">
                <img src="{{ asset('pizzetos.png') }}"
                     alt="Pizzetos Logo"
                     class="h-14 mx-auto mb-4 object-contain">

                <h1 class="text-2xl font-bold text-gray-800">
                    Bienvenido de nuevo
                </h1>
                <p class="text-gray-500 text-sm mt-1">
                    Inicia sesión para continuar
                </p>
            </div>

            {{-- Errores --}}
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <p class="text-sm text-red-600 font-medium">
                        {{ $errors->first() }}
                    </p>
                </div>
            @endif

            {{-- Formulario --}}
            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Usuario --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">
                        Usuario
                    </label>
                    <input type="text"
                           name="nickName"
                           value="{{ old('nickName') }}"
                           required
                           class="w-full px-4 py-3 bg-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-yellow-500 outline-none transition"
                           placeholder="Ingresa tu usuario">
                </div>

                {{-- Contraseña --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-2">
                        Contraseña
                    </label>
                    <input type="password"
                           name="password"
                           required
                           class="w-full px-4 py-3 bg-gray-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-yellow-500 outline-none transition"
                           placeholder="••••••••">
                </div>

                {{-- Botón --}}
                <button type="submit"
                        class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-3 rounded-xl transition duration-200 shadow-lg shadow-yellow-500/30 active:scale-95">
                    Iniciar Sesión
                </button>
            </form>

            {{-- Footer --}}
            <div class="mt-8 text-center">
                <p class="text-xs text-gray-400">
                    Pizzetos &copy; {{ date('Y') }}
                </p>
            </div>

        </div>
    </div>

</body>
</html>
