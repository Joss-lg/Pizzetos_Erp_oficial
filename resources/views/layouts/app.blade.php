<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pizzetos - ERP</title>
    
{{-- FAVICON ESTÁNDAR --}}
    <link rel="icon" type="image/png" href="{{ asset('logocelular.png') }}">

    {{-- CONFIGURACIÓN PARA IPHONE / IPAD (iOS) --}}
    <link rel="apple-touch-icon" href="{{ asset('logocelular.png') }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Pizzetos">

    {{-- CONFIGURACIÓN PARA ANDROID (PWA) --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        (() => {
            const savedTheme = localStorage.getItem('appDarkMode');
            const darkMode = savedTheme === null ? true : savedTheme === 'true';
            document.documentElement.classList.toggle('app-dark', darkMode);
        })();
    </script>
    
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        svg { flex-shrink: 0; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Hacemos la scrollbar general más profesional para PC */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        .logo-container:hover img { transform: rotate(-5deg) scale(1.1); }
        .logo-container img { transition: all 0.3s ease; }

        /* ==========================================================================
           SOLUCIÓN GLOBAL PARA TABLAS RESPONSIVAS
           Envuelve automáticamente el desbordamiento en móviles y suaviza el touch en iOS
           ========================================================================== */
        .responsive-table-container {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }


        .theme-toggle {
            position: relative;
            width: 3.25rem;
            height: 2rem;
            border-radius: 999px;
            background: #fbbf24;
            box-shadow: inset 0 0 0 2px rgba(255,255,255,0.45), 0 8px 18px rgba(15,23,42,0.12);
            transition: background-color 220ms ease, box-shadow 220ms ease, transform 160ms ease;
        }
        .theme-toggle:hover { transform: translateY(-1px); }
        .theme-toggle:active { transform: translateY(1px) scale(0.98); }
        .theme-toggle__orb {
            position: absolute;
            top: 0.25rem;
            left: 0.25rem;
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 999px;
            background: #fff7cc;
            color: #f59e0b;
            display: grid;
            place-items: center;
            box-shadow: 0 4px 10px rgba(15,23,42,0.18);
            transition: transform 240ms ease, background-color 220ms ease, color 220ms ease;
        }
        .app-dark .theme-toggle {
            background: #111827;
            box-shadow: inset 0 0 0 2px rgba(148,163,184,0.28), 0 8px 18px rgba(0,0,0,0.32);
        }
        .app-dark .theme-toggle__orb {
            transform: translateX(1.25rem) rotate(-28deg);
            background: #1f2937;
            color: #94a3b8;
        }

        html.app-dark,
        .app-dark {
            --app-dark-bg: #0b1220;
            --app-dark-surface: #131c2e;
            --app-dark-surface-soft: #182235;
            --app-dark-surface-strong: #1d2940;
            --app-dark-border: #2f3c53;
            --app-dark-text: #e5e7eb;
            --app-dark-text-soft: #e2e8f0;
            --app-dark-text-muted: #cbd5e1;
            --app-dark-muted: #94a3b8;
            background: #0f172a !important;
            color: #e5e7eb !important;
            color-scheme: dark;
        }
        html.app-dark body,
        .app-dark body {
            background: var(--app-dark-bg) !important;
            color: var(--app-dark-text) !important;
        }
        html.app-dark header,
        .app-dark header {
            background-color: #151e2f !important;
            border-color: #263246 !important;
        }
        html.app-dark main,
        html.app-dark .bg-\[\#f8fafc\],
        html.app-dark .bg-\[\#f8f9fa\],
        html.app-dark .bg-gray-50,
        html.app-dark .bg-slate-50,
        .app-dark main,
        .app-dark .bg-\[\#f8fafc\],
        .app-dark .bg-\[\#f8f9fa\],
        .app-dark .bg-gray-50,
        .app-dark .bg-slate-50 {
            background-color: var(--app-dark-bg) !important;
        }
        .app-dark .bg-slate-100,
        .app-dark .bg-gray-100,
        .app-dark .bg-slate-100\/50,
        .app-dark .bg-slate-50\/50,
        .app-dark .bg-gray-50\/50,
        .app-dark .bg-slate-50\/80,
        .app-dark .bg-slate-100\/70,
        .app-dark .bg-slate-100\/60,
        .app-dark .bg-gray-50\/80 {
            background-color: var(--app-dark-surface) !important;
        }
        .app-dark .bg-white {
            background-color: var(--app-dark-surface-soft) !important;
        }
        .app-dark .bg-white\/90,
        .app-dark .bg-white\/95,
        .app-dark .bg-white\/80,
        .app-dark .bg-white\/70 {
            background-color: rgba(24, 34, 53, 0.94) !important;
        }
        .app-dark .bg-white\/60,
        .app-dark .bg-white\/50 {
            background-color: rgba(19, 28, 46, 0.9) !important;
        }
        .app-dark .border,
        .app-dark .border-2 {
            border-color: var(--app-dark-border) !important;
        }
        .app-dark .rounded-\[45px\],
        .app-dark .rounded-\[35px\],
        .app-dark .rounded-\[30px\],
        .app-dark .rounded-2xl,
        .app-dark .rounded-xl {
            box-shadow: 0 18px 36px rgba(0, 0, 0, 0.18) !important;
        }
        .app-dark .text-gray-900,
        .app-dark .text-slate-900,
        .app-dark .text-\[\#212529\],
        .app-dark .text-\[\#1e293b\],
        .app-dark .text-\[\#0f172a\] {
            color: #f9fafb !important;
        }
        .app-dark .bg-white .text-black,
        .app-dark .bg-\[\#f8f9fa\] .text-black,
        .app-dark .bg-\[\#f8fafc\] .text-black {
            color: #f9fafb !important;
        }
        .app-dark .text-slate-600,
        .app-dark .text-gray-700,
        .app-dark .text-\[\#495057\] {
            color: var(--app-dark-text-soft) !important;
        }
        .app-dark .text-slate-400,
        .app-dark .text-gray-500,
        .app-dark .text-\[\#6c757d\] {
            color: var(--app-dark-text-soft) !important;
        }
        .app-dark .text-gray-300,
        .app-dark .text-slate-300,
        .app-dark .text-gray-400 {
            color: var(--app-dark-text-muted) !important;
        }

        /* Regla global para que los tonos neutros en dark mode se lean como blancos suaves */
        .app-dark [class*="text-gray-"],
        .app-dark [class*="text-slate-"],
        .app-dark [class*="text-zinc-"],
        .app-dark [class*="text-neutral-"],
        .app-dark [class*="text-stone-"] {
            color: var(--app-dark-text-soft) !important;
        }
        .app-dark .border-slate-200,
        .app-dark .border-gray-200,
        .app-dark .border-gray-100,
        .app-dark .border-slate-100 {
            border-color: var(--app-dark-border) !important;
        }
        .app-dark .border-slate-300,
        .app-dark .border-gray-300,
        .app-dark .border-slate-400,
        .app-dark .border-gray-400 {
            border-color: #44526b !important;
        }
        .app-dark input,
        .app-dark select,
        .app-dark textarea {
            background-color: #101828 !important;
            color: #f9fafb !important;
            border-color: #334155 !important;
        }
        .app-dark input:focus,
        .app-dark select:focus,
        .app-dark textarea:focus {
            border-color: #60a5fa !important;
            box-shadow: 0 0 0 1px rgba(96, 165, 250, 0.28) !important;
        }
        .app-dark input::placeholder,
        .app-dark textarea::placeholder {
            color: #64748b !important;
        }
        .app-dark table,
        .app-dark th,
        .app-dark td {
            border-color: #334155 !important;
        }
        .app-dark th {
            background-color: #111827 !important;
            color: var(--app-dark-muted) !important;
        }
        .app-dark td {
            background-color: var(--app-dark-surface-soft) !important;
        }
        .app-dark tr:hover td,
        .app-dark tr:hover .bg-white {
            background-color: var(--app-dark-surface-strong) !important;
        }
        .app-dark .shadow-inner {
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.02), inset 0 0 0 1px rgba(47,60,83,0.52) !important;
        }
        .app-dark .shadow-sm,
        .app-dark .shadow-md,
        .app-dark .shadow-lg,
        .app-dark .shadow-xl,
        .app-dark .shadow-2xl {
            --tw-shadow-color: rgba(0, 0, 0, 0.35) !important;
        }
        .app-dark .bg-black\/40,
        .app-dark .bg-black\/50,
        .app-dark .bg-black\/60,
        .app-dark .bg-slate-900\/80 {
            background-color: rgba(2, 6, 23, 0.78) !important;
        }
        .app-dark .backdrop-blur-sm,
        .app-dark .backdrop-blur-md {
            backdrop-filter: blur(10px);
        }
        .app-dark .bg-amber-50,
        .app-dark .bg-amber-100,
        .app-dark .bg-yellow-50,
        .app-dark .bg-yellow-100 {
            background-color: rgba(245, 158, 11, 0.12) !important;
        }
        .app-dark .bg-green-50,
        .app-dark .bg-green-100 {
            background-color: rgba(34, 197, 94, 0.1) !important;
        }
        .app-dark .bg-red-50,
        .app-dark .bg-red-100,
        .app-dark .bg-red-50\/50 {
            background-color: rgba(239, 68, 68, 0.1) !important;
        }
        .app-dark .bg-blue-50,
        .app-dark .bg-blue-100,
        .app-dark .bg-cyan-50,
        .app-dark .bg-cyan-100 {
            background-color: rgba(59, 130, 246, 0.1) !important;
        }
        .app-dark .ring-1,
        .app-dark .ring-2,
        .app-dark .ring,
        .app-dark .outline,
        .app-dark .outline-1 {
            --tw-ring-color: rgba(71, 85, 105, 0.45) !important;
        }
        .app-dark .text-amber-600,
        .app-dark .text-amber-700,
        .app-dark .text-yellow-600,
        .app-dark .text-yellow-700 {
            color: #fbbf24 !important;
        }
        .app-dark .text-green-600,
        .app-dark .text-green-700 {
            color: #4ade80 !important;
        }
        .app-dark .text-red-500,
        .app-dark .text-red-600,
        .app-dark .text-red-700 {
            color: #f87171 !important;
        }
        .app-dark .text-blue-500,
        .app-dark .text-blue-600,
        .app-dark .text-cyan-600 {
            color: #60a5fa !important;
        }
        .app-dark .bg-amber-400,
        .app-dark .bg-amber-500,
        .app-dark .bg-\[\#ffc107\] {
            background-color: #d97706 !important;
            color: #fff7ed !important;
        }
        .app-dark .bg-green-500,
        .app-dark .bg-green-600,
        .app-dark .bg-\[\#28a745\] {
            background-color: #15803d !important;
            color: #f0fdf4 !important;
        }
        .app-dark .bg-red-500,
        .app-dark .bg-\[\#dc3545\] {
            background-color: #b91c1c !important;
            color: #fef2f2 !important;
        }
        .app-dark .bg-blue-600,
        .app-dark .bg-\[\#17a2b8\],
        .app-dark .bg-cyan-500 {
            background-color: #0f766e !important;
            color: #ecfeff !important;
        }
        .app-dark .bg-\[\#fd7e14\] {
            background-color: #c2410c !important;
            color: #fff7ed !important;
        }
        .app-dark .bg-\[\#343a40\],
        .app-dark .bg-black {
            background-color: #0b1120 !important;
            color: #f8fafc !important;
        }
        .app-dark .hover\:bg-white:hover,
        .app-dark .hover\:bg-slate-50:hover,
        .app-dark .hover\:bg-gray-50:hover,
        .app-dark .hover\:bg-white\/90:hover {
            background-color: var(--app-dark-surface-strong) !important;
        }
        .app-dark .hover\:bg-gray-50:hover,
        .app-dark .hover\:bg-gray-100:hover,
        .app-dark .hover\:bg-slate-100:hover,
        .app-dark .hover\:bg-slate-200:hover {
            background-color: var(--app-dark-surface-strong) !important;
        }
        .app-dark [class*='rounded-'][class*='shadow'][class*='bg-white'],
        .app-dark [class*='rounded-'][class*='border'][class*='bg-white'] {
            background-color: var(--app-dark-surface-soft) !important;
        }
        html.app-dark .pizzetos-card,
        html.app-dark .resume-surface,
        html.app-dark .resume-chip,
        html.app-dark .gastos-card,
        html.app-dark .modal-gasto__panel > div,
        html.app-dark [class*='card'],
        html.app-dark [class*='Card'],
        html.app-dark [class*='surface'],
        html.app-dark [class*='Surface'],
        html.app-dark [class*='panel'],
        html.app-dark [class*='Panel'],
        html.app-dark [class*='modal'],
        html.app-dark [class*='Modal'] {
            background-color: var(--app-dark-surface-soft) !important;
            border-color: var(--app-dark-border) !important;
            color: var(--app-dark-text) !important;
        }
        html.app-dark [style*='background: #fff'],
        html.app-dark [style*='background:#fff'],
        html.app-dark [style*='background: #ffffff'],
        html.app-dark [style*='background:#ffffff'],
        html.app-dark [style*='background: #fafafa'],
        html.app-dark [style*='background:#fafafa'],
        html.app-dark [style*='background-color: #fff'],
        html.app-dark [style*='background-color:#fff'],
        html.app-dark [style*='background-color: white'],
        html.app-dark [style*='background-color:white'] {
            background: var(--app-dark-surface-soft) !important;
            background-color: var(--app-dark-surface-soft) !important;
        }
        html.app-dark [class*='shadow-amber-'],
        html.app-dark [class*='shadow-blue-'],
        html.app-dark [class*='shadow-purple-'],
        html.app-dark [class*='shadow-emerald-'],
        html.app-dark [class*='shadow-red-'] {
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.22) !important;
        }
        html.app-dark .bg-white .bg-slate-50,
        html.app-dark .bg-white .bg-gray-50,
        html.app-dark .bg-white .bg-slate-100,
        html.app-dark .bg-white .bg-gray-100 {
            background-color: var(--app-dark-surface) !important;
        }
        html.app-dark .border-white\/70,
        html.app-dark .border-white\/60,
        html.app-dark .border-white\/50 {
            border-color: rgba(71, 85, 105, 0.45) !important;
        }
        html.app-dark .text-black,
        html.app-dark .text-\[\#1e293b\],
        html.app-dark .text-slate-800 {
            color: var(--app-dark-text) !important;
        }
        html.app-dark .text-slate-500,
        html.app-dark .text-gray-500,
        html.app-dark .text-gray-400 {
            color: var(--app-dark-text-soft) !important;
        }
        .app-dark aside .bg-white {
            background-color: #ffffff !important;
        }
        .app-dark aside .text-black,
        .app-dark aside .text-slate-900 {
            color: #0f172a !important;
        }

        .app-dark aside .logo-container .bg-white,
        html.app-dark aside .logo-container .bg-white {
            background-color: #ffffff !important;
        }
        .app-dark aside a:not(.bg-black),
        .app-dark aside button:not(.bg-black),
        .app-dark aside a:not(.bg-black) span,
        .app-dark aside button:not(.bg-black) span,
        .app-dark aside [x-show] a,
        .app-dark aside [x-show] button,
        .app-dark aside [x-show] span {
            color: #0f172a !important;
        }
        .app-dark aside a.bg-black,
        .app-dark aside a.bg-black span,
        .app-dark aside button.bg-black,
        .app-dark aside button.bg-black span {
            color: #fbbf24 !important;
        }
        .app-dark aside form button.bg-black,
        .app-dark aside form button.bg-black span {
            color: #ffffff !important;
        }
        .app-dark aside p {
            color: rgba(15, 23, 42, 0.55) !important;
        }
        .app-dark aside nav div a:not(.bg-black),
        .app-dark aside nav div a:not(.bg-black) span,
        .app-dark aside nav div a:not(.bg-black) svg {
            color: #ffffff !important;
            stroke: currentColor !important;
        }
        html.app-dark aside .sidebar-logo-badge,
        .app-dark aside .sidebar-logo-badge {
            background: #ffffff !important;
            background-color: #ffffff !important;
            border: 1px solid rgba(15, 23, 42, 0.08) !important;
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.14) !important;
        }
        html.app-dark aside .sidebar-logo-badge img,
        .app-dark aside .sidebar-logo-badge img {
            filter: none !important;
            opacity: 1 !important;
            mix-blend-mode: normal !important;
        }
        html.app-dark aside .sidebar-brand-text,
        .app-dark aside .sidebar-brand-text {
            color: #0f172a !important;
        }

        /* =====================================================
           POS — SLOTS DE PAQUETES (fondo amarillo seleccionado)
           ===================================================== */
        .app-dark .bg-\[\#fff9c4\] {
            background-color: rgba(180, 130, 10, 0.22) !important;
            border-color: #d97706 !important;
        }
        .app-dark .bg-\[\#fff9c4\] .text-\[\#212529\],
        .app-dark .bg-\[\#fff9c4\] span,
        .app-dark .bg-\[\#fff9c4\] .text-\[10px\] {
            color: #fde68a !important;
        }

        /* Botones deshabilitados en POS */
        .app-dark .bg-\[\#ced4da\] {
            background-color: #1e2a3a !important;
            color: #4b5563 !important;
        }

        /* Slots de Rectangular y Barra (fondo naranja) */
        .app-dark .bg-orange-50 {
            background-color: rgba(194, 65, 12, 0.15) !important;
        }
        .app-dark .border-\[\#fd7e14\] {
            border-color: #c2410c !important;
        }

        /* =====================================================
           MODAL COBRAR PEDIDO — selector de descuento y footer
           ===================================================== */
        /* Contenedor de botones Sin Desc / 40% / 100% */
        .app-dark .bg-gray-100.p-1\.5.rounded-lg,
        .app-dark [class*="bg-gray-100"][class*="rounded-lg"] {
            background-color: var(--app-dark-surface) !important;
        }
        /* Botón activo dentro del selector (bg-white shadow) */
        .app-dark .bg-gray-100 .bg-white,
        .app-dark [class*="bg-gray-100"] .bg-white {
            background-color: var(--app-dark-surface-strong) !important;
            color: #f9fafb !important;
        }
        /* Botones inactivos del selector de descuento */
        .app-dark .bg-gray-100 .text-gray-500,
        .app-dark [class*="bg-gray-100"] .text-gray-500 {
            color: var(--app-dark-text-muted) !important;
        }
        /* Footer del modal (bg-gray-50 border-t) */
        .app-dark .bg-gray-50.border-t {
            background-color: var(--app-dark-surface) !important;
            border-color: var(--app-dark-border) !important;
        }
        /* Botón Volver en footer */
        .app-dark .bg-gray-50.border-t .bg-white {
            background-color: var(--app-dark-surface-strong) !important;
            border-color: var(--app-dark-border) !important;
            color: var(--app-dark-text-soft) !important;
        }
        /* Bloque "Falta Cobrar / Completado / Excede" */
        .app-dark .bg-orange-50.border-orange-200 {
            background-color: rgba(194, 65, 12, 0.12) !important;
            border-color: rgba(194, 65, 12, 0.35) !important;
        }
        .app-dark .text-orange-700 {
            color: #fb923c !important;
        }
        /* Labels de métodos de pago */
        .app-dark .text-gray-700 {
            color: var(--app-dark-text-soft) !important;
        }
        /* Texto "Total a Cobrar" encabezado gris */
        .app-dark .bg-gray-50.rounded-xl {
            background-color: var(--app-dark-surface) !important;
        }
        .app-dark .bg-gray-50.rounded-xl .text-gray-400 {
            color: var(--app-dark-muted) !important;
        }

        /* =====================================================
           PAQUETE 1 — slots de Hawaiana/Pepperoni
           ===================================================== */
        .app-dark .bg-gray-100.border.border-gray-200.rounded {
            background-color: var(--app-dark-surface) !important;
            border-color: var(--app-dark-border) !important;
        }

    </style>
</head>

{{-- APPSHELL: Congelamos el body (h-screen overflow-hidden) para que solo el main tenga scroll --}}
<body class="bg-[#f8fafc] font-sans antialiased text-slate-900 h-screen overflow-hidden"
      :class="darkMode ? 'app-dark' : ''"
      x-data="{ 
          darkMode: localStorage.getItem('appDarkMode') === null ? true : localStorage.getItem('appDarkMode') === 'true',
          sidebarOpen: false, 
          sidebarExpanded: $persist(false), /* Magia: Guardamos el estado en LocalStorage */

          freezeSidebarScrollSave: false,
          sidebarNavScrollTop: parseInt(localStorage.getItem('sidebarNavScrollTop') || '0', 10),
          toggleDarkMode() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('appDarkMode', this.darkMode ? 'true' : 'false');
              document.documentElement.classList.toggle('app-dark', this.darkMode);
          },
          restoreSidebarScroll() {
              this.$nextTick(() => {
                  if (this.$refs.sidebarNav) {
                      const nav = this.$refs.sidebarNav;
                      nav.scrollTop = this.sidebarNavScrollTop || 0;

                      const activeLink = nav.querySelector('a.bg-black');
                      if (activeLink) {
                          const navRect = nav.getBoundingClientRect();
                          const activeRect = activeLink.getBoundingClientRect();
                          const centeredTop = nav.scrollTop + (activeRect.top - navRect.top) - Math.floor(nav.clientHeight * 0.35);
                          nav.scrollTop = Math.max(0, centeredTop);
                          this.sidebarNavScrollTop = nav.scrollTop;
                          localStorage.setItem('sidebarNavScrollTop', String(this.sidebarNavScrollTop));
                      }
                  }
              });
          },
          saveSidebarScroll(event) {
              if (this.freezeSidebarScrollSave) {
                  return;
              }
              this.sidebarNavScrollTop = event.target.scrollTop;
              localStorage.setItem('sidebarNavScrollTop', String(this.sidebarNavScrollTop));
          },
          handleSidebarNavigation(event) {
              if (this.$refs.sidebarNav) {
                  const nav = this.$refs.sidebarNav;
                  const target = event.currentTarget;
                  const navRect = nav.getBoundingClientRect();
                  const targetRect = target.getBoundingClientRect();
                  const offsetTop = targetRect.top - navRect.top;
                  const desiredTop = nav.scrollTop + offsetTop - Math.floor(nav.clientHeight * 0.35);

                  this.sidebarNavScrollTop = Math.max(0, desiredTop);
                  localStorage.setItem('sidebarNavScrollTop', String(this.sidebarNavScrollTop));
              }

              if (window.innerWidth < 1024) {
                  this.sidebarOpen = false;
                  return;
              }

              if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                  return;
              }

              const href = event.currentTarget.getAttribute('href');
              if (!href) {
                  return;
              }

              event.preventDefault();
              this.freezeSidebarScrollSave = true;
              this.sidebarExpanded = false;

              setTimeout(() => {
                  window.location.href = href;
              }, 320);
          }
      }">


    {{-- Overlay con Blur dinámico (Solo visible en móviles) --}}
    <div x-show="sidebarOpen" 
         x-cloak
         x-transition:opacity.duration.300ms
         @click="sidebarOpen = false" 
         class="fixed inset-0 bg-slate-900/60 z-40 backdrop-blur-sm lg:hidden">
    </div>

    {{-- CONTENEDOR MAESTRO: Ajusta el padding izquierdo dependiendo de la barra --}}
    <div class="h-screen flex flex-col transition-all duration-300 w-full" :class="sidebarExpanded ? 'lg:pl-60' : 'lg:pl-20'">
        

{{-- SIDEBAR LATERAL (Amarillo) --}}
        <aside 
            x-cloak

            :class="[
                sidebarOpen ? 'translate-x-0' : '-translate-x-full',
                sidebarExpanded ? 'w-60' : 'w-60 lg:w-20'
            ]"
            @keydown.window.escape="sidebarOpen = false"

            class="fixed inset-y-0 left-0 z-50 bg-amber-400 text-slate-900 transition-all duration-300 ease-in-out transform lg:translate-x-0 flex flex-col justify-between shadow-2xl border-r border-amber-500/20 overflow-hidden">
            
            {{-- Contenedor superior: Agrupa Logo y Navegación con Scroll propio --}}
            <div class="flex flex-col flex-1 min-h-0 overflow-hidden">
                
                {{-- Logo Area con Botón Integrado --}}
                <div class="h-20 lg:h-24 flex px-4 border-b border-black/5 shrink-0 transition-all items-center justify-between" :class="sidebarExpanded ? '' : 'lg:flex-col lg:items-center lg:justify-center lg:gap-2 lg:py-3'">
                    
                    <div class="flex items-center gap-2 logo-container" :class="sidebarExpanded ? '' : 'lg:justify-center'">
                        <div class="sidebar-logo-badge bg-white p-1.5 rounded-xl shadow-sm shrink-0" style="background: #ffffff !important; background-color: #ffffff !important;">
                            <img src="{{ asset('pizzetos.png') }}" alt="Logo" class="h-7 w-7 lg:h-8 lg:w-8 object-contain bg-white rounded-lg" style="background: #ffffff !important; background-color: #ffffff !important;">
                        </div>
                        <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="sidebar-brand-text text-lg font-black italic tracking-tighter uppercase whitespace-nowrap transition-opacity">Pizzetos</span>
                    </div>

                    {{-- Botón Toggle Escritorio --}}
                    <button @click="sidebarExpanded = !sidebarExpanded; if (sidebarExpanded) { freezeSidebarScrollSave = false; restoreSidebarScroll(); setTimeout(() => restoreSidebarScroll(), 220); }" class="hidden lg:flex p-1.5 bg-black/5 hover:bg-black/10 rounded-xl transition-colors text-slate-800 shrink-0" title="Expandir/Contraer">
                        <svg x-show="sidebarExpanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                        <svg x-show="!sidebarExpanded" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                    </button>

                    {{-- Botón de cerrar (Oculto en PC, para móviles) --}}
                    <button @click="sidebarOpen = false" class="lg:hidden p-2 hover:bg-black/5 rounded-xl transition-colors text-black">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Navegación Principal (Scrollable de forma segura) --}}
                <nav class="flex-1 overflow-y-auto overflow-x-hidden p-3 space-y-0.5 content-start" x-ref="sidebarNav" x-init="restoreSidebarScroll(); setTimeout(() => restoreSidebarScroll(), 220)" @scroll.passive="saveSidebarScroll($event)">

                    @php
                        $navItem = fn($active) => 'flex items-center px-3 py-2.5 rounded-xl transition-all border ' . ($active ? 'bg-black text-amber-400 shadow-xl border-black/40' : 'text-slate-900 font-bold border-transparent hover:bg-black/8 hover:border-black/[.14] hover:shadow-sm');
                        $iconWrap = fn($active) => 'w-8 h-8 rounded-xl flex items-center justify-center shrink-0 ' . ($active ? 'bg-amber-400/20' : 'bg-black/[.07]');
                    @endphp

                    <p :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="px-3 pt-2 pb-1.5 text-[9px] font-black text-black/35 uppercase tracking-[0.25em]">Operación</p>
                    <div :class="sidebarExpanded ? 'hidden' : 'hidden lg:block'" class="h-px w-6 bg-black/10 mx-auto my-3"></div>

                    {{-- INICIO --}}
                    <a href="{{ route('dashboard') }}" @click="handleSidebarNavigation($event)"
                       class="{{ $navItem(request()->routeIs('dashboard')) }}"
                       :class="sidebarExpanded ? 'gap-3 justify-start' : 'justify-start lg:justify-center'">
                        <div class="{{ $iconWrap(request()->routeIs('dashboard')) }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                            </svg>
                        </div>
                        <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-[13px] font-black uppercase italic tracking-tight whitespace-nowrap">Inicio</span>
                    </a>

                    {{-- VENTA POS --}}
                    <a href="{{ route('ventas.pos') }}" @click="handleSidebarNavigation($event)"
                       class="{{ $navItem(request()->routeIs('ventas.pos')) }}"
                       :class="sidebarExpanded ? 'gap-3 justify-start' : 'justify-start lg:justify-center'">
                        <div class="{{ $iconWrap(request()->routeIs('ventas.pos')) }}">
                            {{-- Computadora / monitor POS --}}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25A2.25 2.25 0 015.25 3h13.5A2.25 2.25 0 0121 5.25z"/>
                            </svg>
                        </div>
                        <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-[13px] font-black uppercase italic tracking-tight whitespace-nowrap">Venta POS</span>
                    </a>

                    {{-- MESAS --}}
                    <a href="{{ route('mesas.index') }}" @click="handleSidebarNavigation($event)"
                       class="{{ $navItem(request()->routeIs('mesas.*')) }}"
                       :class="sidebarExpanded ? 'gap-3 justify-start' : 'justify-start lg:justify-center'">
                        <div class="{{ $iconWrap(request()->routeIs('mesas.*')) }}">
                            {{-- Mesa de restaurante --}}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9h16.5M3.75 9a.75.75 0 01-.75-.75V6.75A.75.75 0 013.75 6h16.5a.75.75 0 01.75.75v1.5a.75.75 0 01-.75.75M3.75 9l1.5 9h13.5l1.5-9M9 12v3m6-3v3"/>
                            </svg>
                        </div>
                        <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-[13px] font-black uppercase italic tracking-tight whitespace-nowrap">Mesas</span>
                    </a>

                    {{-- REPARTIDOR --}}
                    <a href="{{ route('ventas.pedidos') }}" @click="handleSidebarNavigation($event)"
                       class="{{ $navItem(request()->routeIs('ventas.pedidos')) }}"
                       :class="sidebarExpanded ? 'gap-3 justify-start' : 'justify-start lg:justify-center'">
                        <div class="{{ $iconWrap(request()->routeIs('ventas.pedidos')) }}">
                            {{-- Moto / scooter de delivery --}}
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 7c0-1.1-.9-2-2-2h-3l2 4h3c1.1 0 2-.9 2-2zM4.5 11H2v2h2.09A5.004 5.004 0 002 17c0 2.76 2.24 5 5 5s5-2.24 5-5c0-.34-.04-.67-.1-1H14v-1.5l-1-3H9.5L8 11H4.5zm2.5 8c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm10-1c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm0-6c-2.21 0-4 1.79-4 4h2c0-1.1.9-2 2-2v-2z"/>
                            </svg>
                        </div>
                        <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-[13px] font-black uppercase italic tracking-tight whitespace-nowrap">Repartidor</span>
                    </a>

                    {{-- ESPECIALES --}}
                    <a href="{{ route('especiales.index') }}" @click="handleSidebarNavigation($event)"
                       class="{{ $navItem(request()->routeIs('especiales.*')) }}"
                       :class="sidebarExpanded ? 'gap-3 justify-start' : 'justify-start lg:justify-center'">
                        <div class="{{ $iconWrap(request()->routeIs('especiales.*')) }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                            </svg>
                        </div>
                        <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-[13px] font-black uppercase italic tracking-tight whitespace-nowrap">Especiales</span>
                    </a>

                    {{-- FLUJO CAJA --}}
                    <a href="{{ route('flujo.caja.index') }}" @click="handleSidebarNavigation($event)"
                       class="{{ $navItem(request()->routeIs('flujo.caja.*')) }}"
                       :class="sidebarExpanded ? 'gap-3 justify-start' : 'justify-start lg:justify-center'">
                        <div class="{{ $iconWrap(request()->routeIs('flujo.caja.*')) }}">
                            {{-- Caja registradora --}}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
                            </svg>
                        </div>
                        <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-[13px] font-black uppercase italic tracking-tight whitespace-nowrap">Flujo Caja</span>
                    </a>

                    {{-- GASTOS --}}
                    <a href="{{ route('gastos.index') }}" @click="handleSidebarNavigation($event)"
                       class="{{ $navItem(request()->routeIs('gastos.*')) }}"
                       :class="sidebarExpanded ? 'gap-3 justify-start' : 'justify-start lg:justify-center'">
                        <div class="{{ $iconWrap(request()->routeIs('gastos.*')) }}">
                            {{-- Recibo / comprobante --}}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                            </svg>
                        </div>
                        <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-[13px] font-black uppercase italic tracking-tight whitespace-nowrap">Gastos</span>
                    </a>

                    {{-- HISTORIAL --}}
                    <a href="{{ route('ventas.resume') }}" @click="handleSidebarNavigation($event)"
                       class="{{ $navItem(request()->routeIs('ventas.resume')) }}"
                       :class="sidebarExpanded ? 'gap-3 justify-start' : 'justify-start lg:justify-center'">
                        <div class="{{ $iconWrap(request()->routeIs('ventas.resume')) }}">
                            {{-- Reloj con historial --}}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-[13px] font-black uppercase italic tracking-tight whitespace-nowrap">Historial</span>
                    </a>

                    {{-- CLIENTES --}}
                    <a href="{{ route('clientes.index') }}" @click="handleSidebarNavigation($event)"
                       class="{{ $navItem(request()->routeIs('clientes.*')) }}"
                       :class="sidebarExpanded ? 'gap-3 justify-start' : 'justify-start lg:justify-center'">
                        <div class="{{ $iconWrap(request()->routeIs('clientes.*')) }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                            </svg>
                        </div>
                        <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-[13px] font-black uppercase italic tracking-tight whitespace-nowrap">Clientes</span>
                    </a>

                    {{-- SECCIÓN DE ADMINISTRACIÓN RESTRINGIDA --}}
                    @if(Auth::user()->id_ca == 1)
                        @php
                            $catActive = request()->routeIs('categorias.*') || request()->routeIs('pizzas.*') || request()->routeIs('especialidades.*');
                            $prodActive = request()->routeIs('alitas.*') || request()->routeIs('costillas.*') || request()->routeIs('hamburguesas.*') || request()->routeIs('papas.*') || request()->routeIs('mariscos.*') || request()->routeIs('rectangular.*') || request()->routeIs('spaguetty.*') || request()->routeIs('refrescos.*') || request()->routeIs('barra.*');
                            $ajustesActive = request()->routeIs('empleados.*') || request()->routeIs('cargos.*') || request()->routeIs('sucursales.*') || request()->routeIs('ventas.configuracion') || request()->is('Conf*');
                        @endphp
                        <p :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="px-3 pt-4 pb-1.5 text-[9px] font-black text-black/35 uppercase tracking-[0.25em]">Administración</p>
                        <div :class="sidebarExpanded ? 'hidden' : 'hidden lg:block'" class="h-px w-6 bg-black/10 mx-auto my-3"></div>

                        {{-- CORTE MENSUAL --}}
                        <a href="{{ route('corte.index') }}" @click="handleSidebarNavigation($event)"
                           class="{{ $navItem(request()->routeIs('corte.*')) }}"
                           :class="sidebarExpanded ? 'gap-3 justify-start' : 'justify-start lg:justify-center'">
                            <div class="{{ $iconWrap(request()->routeIs('corte.*')) }}">
                                {{-- Gráfica de barras / corte --}}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                                </svg>
                            </div>
                            <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-[13px] font-black uppercase italic tracking-tight whitespace-nowrap">Corte Mensual</span>
                        </a>

                        {{-- 1. MENÚ DESPLEGABLE: CATEGORÍAS --}}
                        <div x-data="{ openCat: @js($catActive) }">
                            <button @click="if(window.innerWidth >= 1024 && !sidebarExpanded) { sidebarExpanded = true; openCat = true; } else { openCat = !openCat; }"
                                    class="w-full text-left {{ $navItem($catActive) }}"
                                    :class="sidebarExpanded ? 'gap-3 justify-between' : 'justify-start lg:justify-center'">
                                <div class="flex items-center" :class="sidebarExpanded ? 'gap-3' : 'gap-3 lg:gap-0'">
                                    <div class="{{ $iconWrap($catActive) }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                                        </svg>
                                    </div>
                                    <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-[13px] font-black uppercase italic tracking-tight whitespace-nowrap">Categorías</span>
                                </div>
                                <svg :class="[sidebarExpanded ? 'block' : 'block lg:hidden', openCat ? 'rotate-180' : '']" class="w-3.5 h-3.5 transition-transform duration-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openCat && (sidebarExpanded || window.innerWidth < 1024)" x-cloak x-collapse class="pl-11 pr-3 space-y-0.5 pb-1 mt-0.5" :class="sidebarExpanded ? '' : 'lg:hidden'">
                                <a href="{{ route('categorias.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center gap-2 py-2 px-3 rounded-lg text-[11px] font-black uppercase tracking-widest transition-all {{ request()->routeIs('categorias.*') ? 'bg-black text-amber-400' : 'text-black/70 hover:bg-black/8 hover:translate-x-0.5' }}">General</a>
                                <a href="{{ route('pizzas.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center gap-2 py-2 px-3 rounded-lg text-[11px] font-black uppercase tracking-widest transition-all {{ request()->routeIs('pizzas.*') ? 'bg-black text-amber-400' : 'text-black/70 hover:bg-black/8 hover:translate-x-0.5' }}">Pizzas</a>
                                <a href="{{ route('especialidades.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center gap-2 py-2 px-3 rounded-lg text-[11px] font-black uppercase tracking-widest transition-all {{ request()->routeIs('especialidades.*') ? 'bg-black text-amber-400' : 'text-black/70 hover:bg-black/8 hover:translate-x-0.5' }}">Especialidades</a>
                            </div>
                        </div>

                        {{-- 2. MENÚ DESPLEGABLE: PRODUCTOS --}}
                        <div x-data="{ openProd: @js($prodActive) }">
                            <button @click="if(window.innerWidth >= 1024 && !sidebarExpanded) { sidebarExpanded = true; openProd = true; } else { openProd = !openProd; }"
                                    class="w-full text-left {{ $navItem($prodActive) }}"
                                    :class="sidebarExpanded ? 'gap-3 justify-between' : 'justify-start lg:justify-center'">
                                <div class="flex items-center" :class="sidebarExpanded ? 'gap-3' : 'gap-3 lg:gap-0'">
                                    <div class="{{ $iconWrap($prodActive) }}">
                                        {{-- Pizza --}}
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9-4.03-9-9-9zm0 0l9 9M12 3v18M3 12h18"/>
                                        </svg>
                                    </div>
                                    <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-[13px] font-black uppercase italic tracking-tight whitespace-nowrap">Productos</span>
                                </div>
                                <svg :class="[sidebarExpanded ? 'block' : 'block lg:hidden', openProd ? 'rotate-180' : '']" class="w-3.5 h-3.5 transition-transform duration-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openProd && (sidebarExpanded || window.innerWidth < 1024)" x-cloak x-collapse class="pl-11 pr-3 space-y-0.5 pb-1 mt-0.5" :class="sidebarExpanded ? '' : 'lg:hidden'">
                                <a href="{{ route('alitas.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center gap-2 py-2 px-3 rounded-lg text-[11px] font-black uppercase tracking-widest transition-all {{ request()->routeIs('alitas.*') ? 'bg-black text-amber-400' : 'text-black/70 hover:bg-black/8 hover:translate-x-0.5' }}">Alitas</a>
                                <a href="{{ route('costillas.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center gap-2 py-2 px-3 rounded-lg text-[11px] font-black uppercase tracking-widest transition-all {{ request()->routeIs('costillas.*') ? 'bg-black text-amber-400' : 'text-black/70 hover:bg-black/8 hover:translate-x-0.5' }}">Costillas</a>
                                <a href="{{ route('hamburguesas.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center gap-2 py-2 px-3 rounded-lg text-[11px] font-black uppercase tracking-widest transition-all {{ request()->routeIs('hamburguesas.*') ? 'bg-black text-amber-400' : 'text-black/70 hover:bg-black/8 hover:translate-x-0.5' }}">Hamburguesas</a>
                                <a href="{{ route('papas.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center gap-2 py-2 px-3 rounded-lg text-[11px] font-black uppercase tracking-widest transition-all {{ request()->routeIs('papas.*') ? 'bg-black text-amber-400' : 'text-black/70 hover:bg-black/8 hover:translate-x-0.5' }}">Papas</a>
                                <a href="{{ route('mariscos.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center gap-2 py-2 px-3 rounded-lg text-[11px] font-black uppercase tracking-widest transition-all {{ request()->routeIs('mariscos.*') ? 'bg-black text-amber-400' : 'text-black/70 hover:bg-black/8 hover:translate-x-0.5' }}">Mariscos</a>
                                <a href="{{ route('rectangular.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center gap-2 py-2 px-3 rounded-lg text-[11px] font-black uppercase tracking-widest transition-all {{ request()->routeIs('rectangular.*') ? 'bg-black text-amber-400' : 'text-black/70 hover:bg-black/8 hover:translate-x-0.5' }}">Rectangular</a>
                                <a href="{{ route('spaguetty.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center gap-2 py-2 px-3 rounded-lg text-[11px] font-black uppercase tracking-widest transition-all {{ request()->routeIs('spaguetty.*') ? 'bg-black text-amber-400' : 'text-black/70 hover:bg-black/8 hover:translate-x-0.5' }}">Spaguetty</a>
                                <a href="{{ route('refrescos.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center gap-2 py-2 px-3 rounded-lg text-[11px] font-black uppercase tracking-widest transition-all {{ request()->routeIs('refrescos.*') ? 'bg-black text-amber-400' : 'text-black/70 hover:bg-black/8 hover:translate-x-0.5' }}">Refrescos</a>
                                <a href="{{ route('barra.index') }}" @click="handleSidebarNavigation($event)" class="flex items-center gap-2 py-2 px-3 rounded-lg text-[11px] font-black uppercase tracking-widest transition-all {{ request()->routeIs('barra.*') ? 'bg-black text-amber-400' : 'text-black/70 hover:bg-black/8 hover:translate-x-0.5' }}">Barra</a>
                            </div>
                        </div>

                        {{-- 3. MENÚ DESPLEGABLE: AJUSTES --}}
                        <div x-data="{ openAjustes: @js($ajustesActive) }">
                            <button @click="if(window.innerWidth >= 1024 && !sidebarExpanded) { sidebarExpanded = true; openAjustes = true; } else { openAjustes = !openAjustes; }"
                                    class="w-full text-left {{ $navItem($ajustesActive) }}"
                                    :class="sidebarExpanded ? 'gap-3 justify-between' : 'justify-start lg:justify-center'">
                                <div class="flex items-center" :class="sidebarExpanded ? 'gap-3' : 'gap-3 lg:gap-0'">
                                    <div class="{{ $iconWrap($ajustesActive) }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'" class="text-[13px] font-black uppercase italic tracking-tight whitespace-nowrap">Ajustes</span>
                                </div>
                                <svg :class="[sidebarExpanded ? 'block' : 'block lg:hidden', openAjustes ? 'rotate-180' : '']" class="w-3.5 h-3.5 transition-transform duration-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="openAjustes && (sidebarExpanded || window.innerWidth < 1024)" x-cloak x-collapse class="pl-11 pr-3 space-y-0.5 pb-1 mt-0.5" :class="sidebarExpanded ? '' : 'lg:hidden'">
                                <a href="{{ route('empleados.index') }}" @click="handleSidebarNavigation($event)" class="block py-2 px-3 rounded-lg text-[11px] font-black uppercase tracking-widest transition-all {{ request()->routeIs('empleados.*') ? 'bg-black text-amber-400' : 'text-black/70 hover:bg-black/8 hover:translate-x-0.5' }}">Personal</a>
                                <a href="{{ route('cargos.index') }}" @click="handleSidebarNavigation($event)" class="block py-2 px-3 rounded-lg text-[11px] font-black uppercase tracking-widest transition-all {{ request()->routeIs('cargos.*') ? 'bg-black text-amber-400' : 'text-black/70 hover:bg-black/8 hover:translate-x-0.5' }}">Cargos</a>
                                <a href="{{ route('sucursales.index') }}" @click="handleSidebarNavigation($event)" class="block py-2 px-3 rounded-lg text-[11px] font-black uppercase tracking-widest transition-all {{ request()->routeIs('sucursales.*') ? 'bg-black text-amber-400' : 'text-black/70 hover:bg-black/8 hover:translate-x-0.5' }}">Sucursales</a>
                                <a href="{{ route('ventas.configuracion') }}" @click="handleSidebarNavigation($event)" class="block py-2 px-3 rounded-lg text-[11px] font-black uppercase tracking-widest transition-all {{ (request()->routeIs('ventas.configuracion') || request()->is('Conf*')) ? 'bg-black text-amber-400' : 'text-black/70 hover:bg-black/8 hover:translate-x-0.5' }}">Sistema</a>
                            </div>
                        </div>
                    @endif
                </nav>
            </div>

            {{-- Logout Footer --}}
            <div class="sticky bottom-0 z-10 p-3 border-t border-black/5 shrink-0 bg-amber-400" style="padding-bottom: calc(0.75rem + env(safe-area-inset-bottom, 0px));">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-3 py-3.5 rounded-xl bg-black text-white hover:bg-slate-800 transition-all font-black text-[10px] uppercase tracking-widest italic shadow-xl" :class="sidebarExpanded ? 'gap-2' : 'gap-2 lg:gap-0'">
                        <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span :class="sidebarExpanded ? 'block' : 'block lg:hidden'">Cerrar Sesión</span>

                    </button>
                </form>
            </div>
        </aside>


        {{-- Contenedor Principal Derecho (con el Header y el @yield) --}}
        <div class="flex-1 flex flex-col h-screen overflow-hidden transition-all duration-300 relative w-full">
            
            {{-- HEADER (Blanco) --}}
            <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-4 lg:px-10 shrink-0 sticky top-0 z-30 shadow-sm transition-all duration-300 w-full">
                <div class="flex items-center gap-4">
                    
                    {{-- Botón Hamburger Solo para Móviles (Abre el sidebar flotante) --}}
                    <button @click="sidebarOpen = true" class="lg:hidden p-2.5 bg-amber-400 rounded-2xl text-slate-900 shadow-sm hover:scale-105 transition-all active:scale-95">
                        <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M4 6h16M4 12h16m-7 6h7"/></svg>
                    </button>

                    <div class="hidden md:block">
                        <h2 class="text-[10px] font-black text-slate-400 tracking-[0.3em] italic leading-none uppercase">Pizzetos Management</h2>
                        <p class="text-xs font-bold text-slate-600 mt-1 italic tracking-tighter">By Ollintem Sistema POS</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button
                        type="button"
                        @click="toggleDarkMode()"
                        class="theme-toggle shrink-0"
                        :aria-label="darkMode ? 'Activar modo claro' : 'Activar modo oscuro'"
                        :title="darkMode ? 'Activar modo claro' : 'Activar modo oscuro'"
                    >
                        <span class="theme-toggle__orb">
                            <svg x-show="!darkMode" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 18a6 6 0 100-12 6 6 0 000 12Zm0 4a1 1 0 01-1-1v-1a1 1 0 112 0v1a1 1 0 01-1 1Zm0-18a1 1 0 01-1-1V2a1 1 0 112 0v1a1 1 0 01-1 1Zm10 8a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1ZM4 12a1 1 0 01-1 1H2a1 1 0 110-2h1a1 1 0 011 1Zm14.95 6.95a1 1 0 01-1.42 0l-.7-.7a1 1 0 111.41-1.42l.71.71a1 1 0 010 1.41ZM7.17 7.17a1 1 0 01-1.41 0l-.71-.71a1 1 0 011.41-1.41l.71.71a1 1 0 010 1.41Zm11.78-2.12a1 1 0 010 1.41l-.71.71a1 1 0 11-1.41-1.41l.7-.71a1 1 0 011.42 0ZM7.17 16.83a1 1 0 010 1.41l-.71.71a1 1 0 01-1.41-1.41l.71-.71a1 1 0 011.41 0Z"/>
                            </svg>
                            <svg x-show="darkMode" x-cloak class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M21 14.8A8.7 8.7 0 019.2 3 9 9 0 1012 21a8.9 8.9 0 009-6.2Z"/>
                            </svg>
                        </span>
                    </button>
                    <div class="hidden sm:block text-right">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Usuario Activo</p>
                        <p class="text-sm font-black text-gray-900 uppercase italic leading-none tracking-tighter">{{ Auth::user()->nombre ?? 'Admin' }}</p>
                    </div>
                    <div class="h-11 w-11 bg-amber-400 rounded-2xl flex items-center justify-center font-black text-lg text-slate-900 border-2 border-white shadow-md">
                        {{ substr(Auth::user()->nombre ?? 'A', 0, 1) }}
                    </div>
                </div>
            </header>

            {{-- MAIN CONTENT: Aquí ocurre el scroll interno --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-[#f8fafc] relative w-full">
                <div class="p-4 lg:p-8 max-w-[1600px] mx-auto w-full">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <style>
        html.app-dark main [class*="text-gray-"],
        html.app-dark main [class*="text-slate-"],
        html.app-dark main [class*="text-zinc-"],
        html.app-dark main [class*="text-neutral-"],
        html.app-dark main [class*="text-stone-"] {
            color: var(--app-dark-text-soft) !important;
        }

        html.app-dark main [class*="text-gray-900"],
        html.app-dark main [class*="text-gray-800"],
        html.app-dark main [class*="text-slate-900"],
        html.app-dark main [class*="text-slate-800"],
        html.app-dark main [class*="text-slate-700"],
        html.app-dark main [class*="text-black"] {
            color: var(--app-dark-text) !important;
        }

        html.app-dark main .text-\[\#212529\],
        html.app-dark main .text-\[\#1e293b\],
        html.app-dark main .text-\[\#0f172a\] {
            color: var(--app-dark-text) !important;
        }

        html.app-dark main .text-\[\#495057\],
        html.app-dark main .text-\[\#6c757d\] {
            color: var(--app-dark-text-soft) !important;
        }

        html.app-dark main [class*="text-gray-500"],
        html.app-dark main [class*="text-gray-400"],
        html.app-dark main [class*="text-gray-300"],
        html.app-dark main [class*="text-slate-500"],
        html.app-dark main [class*="text-slate-400"],
        html.app-dark main [class*="text-slate-300"],
        html.app-dark main [class*="text-zinc-500"],
        html.app-dark main [class*="text-zinc-400"],
        html.app-dark main [class*="text-neutral-500"],
        html.app-dark main [class*="text-neutral-400"],
        html.app-dark main [class*="text-stone-500"],
        html.app-dark main [class*="text-stone-400"] {
            color: var(--app-dark-text-soft) !important;
        }

        body aside,
        body aside nav a:not(.bg-black),
        body aside nav a:not(.bg-black) *,
        body aside nav button:not(.bg-black),
        body aside nav button:not(.bg-black) *,
        body aside .sidebar-brand-text {
            color: #0f172a !important;
            stroke: currentColor !important;
        }

        body aside nav a,
        body aside nav button,
        body aside nav span,
        body aside nav p {
            transition-property: background-color, border-color, box-shadow, transform, opacity !important;
        }

        body aside nav p {
            color: rgba(15, 23, 42, 0.62) !important;
        }

        body aside nav a.bg-black,
        body aside nav a.bg-black *,
        body aside nav button.bg-black,
        body aside nav button.bg-black * {
            color: #fbbf24 !important;
            stroke: currentColor !important;
        }

        body aside form button.bg-black,
        body aside form button.bg-black * {
            color: #ffffff !important;
            stroke: currentColor !important;
        }

        html body aside nav div[x-collapse] a:not(.bg-black),
        html body aside nav div[x-collapse] a:not(.bg-black) *,
        html.app-dark body aside nav div[x-collapse] a:not(.bg-black),
        html.app-dark body aside nav div[x-collapse] a:not(.bg-black) * {
            color: #0f172a !important;
            -webkit-text-fill-color: #0f172a !important;
            stroke: currentColor !important;
        }

        html body aside nav div[x-collapse] a.bg-black,
        html body aside nav div[x-collapse] a.bg-black *,
        html.app-dark body aside nav div[x-collapse] a.bg-black,
        html.app-dark body aside nav div[x-collapse] a.bg-black * {
            color: #fbbf24 !important;
            -webkit-text-fill-color: #fbbf24 !important;
            stroke: currentColor !important;
        }

        html.app-dark main .pos-product-card-pizza {
            border-left-color: #ffc107 !important;
        }

        html.app-dark main .pos-product-card-drink {
            border-left-color: #17a2b8 !important;
        }

        html.app-dark main .pos-product-card-direct {
            border-left-color: #60a5fa !important;
        }
    </style>
</body>
</html>