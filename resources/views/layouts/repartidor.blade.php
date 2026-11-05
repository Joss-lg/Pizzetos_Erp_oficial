<!DOCTYPE html>
<html lang="es" class="app-dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Repartidor') — Pizzetos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        body { background: #0b1220; color: #f1f5f9; font-family: system-ui, -apple-system, sans-serif; }
        * { -webkit-tap-highlight-color: transparent; }
        .step-line { flex: 1; height: 2px; }
        .pulse-ring { animation: pulseRing 1.8s ease-out infinite; }
        @keyframes pulseRing {
            0%   { box-shadow: 0 0 0 0 rgba(253, 126, 20, .5); }
            70%  { box-shadow: 0 0 0 10px rgba(253, 126, 20, 0); }
            100% { box-shadow: 0 0 0 0 rgba(253, 126, 20, 0); }
        }
        .slide-up { animation: slideUp .35s ease; }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

{{-- Navbar mínimo --}}
<header class="sticky top-0 z-50 bg-[#0b1220]/95 backdrop-blur border-b border-white/[.06] px-4 py-3 flex items-center justify-between">
    <div class="flex items-center gap-2.5">
        <img src="{{ asset('pizzetos.png') }}" alt="Pizzetos" class="h-7 w-7 rounded-lg bg-white p-0.5">
        <span class="text-xs font-black uppercase tracking-widest text-amber-400">Pizzetos</span>
    </div>
    <div class="flex items-center gap-3">
        <span class="text-xs font-bold text-slate-400">{{ Auth::user()->nombre }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-8 h-8 rounded-xl bg-white/[.06] hover:bg-white/10 flex items-center justify-center transition-colors" title="Cerrar sesión">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </button>
        </form>
    </div>
</header>

@yield('content')

</body>
</html>
