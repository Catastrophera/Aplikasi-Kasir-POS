<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Cheframa Kebab')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- PWA / Native App Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#121212">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Cheframa POS">
    <link rel="apple-touch-icon" href="/icon.png">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        /* Fallback if Tailwind fails */
        body { background-color: #121212; color: #F3F4F6; }
        /* Hide scrollbar for a more native feel */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--color-kebab-dark); }
        ::-webkit-scrollbar-thumb { background: var(--color-kebab-dark-hover); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--color-kebab-text-muted); }
        /* Prevent AlpineJS FOUC (Flash of Unstyled Content) */
        [x-cloak] { display: none !important; }
    </style>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then(registration => {
                    console.log('SW registered: ', registration);
                }).catch(registrationError => {
                    console.log('SW registration failed: ', registrationError);
                });
            });
        }
    </script>
</head>
<body class="bg-[var(--color-kebab-dark)] text-[var(--color-kebab-text)] font-sans antialiased h-screen flex flex-col md:flex-row overflow-hidden">
    
    <!-- Mobile Header -->
    <div class="md:hidden bg-[var(--color-kebab-dark-card)] p-4 flex justify-between items-center border-b border-[var(--color-kebab-dark-hover)] shrink-0 z-50">
        <h1 class="text-xl font-bold text-[var(--color-kebab-red)]">Cheframa<span class="text-white">Kebab</span></h1>
    </div>

    <!-- Sidebar / Bottom Nav -->
    <nav class="bg-[var(--color-kebab-dark-card)] border-t md:border-t-0 md:border-r border-[var(--color-kebab-dark-hover)] fixed bottom-0 w-full md:relative md:w-64 md:flex md:flex-col shrink-0 z-40">
        <div class="hidden md:block p-6 border-b border-[var(--color-kebab-dark-hover)]">
            <h1 class="text-2xl font-bold text-[var(--color-kebab-red)]">Cheframa<span class="text-white">Kebab</span></h1>
            <p class="text-[var(--color-kebab-text-muted)] text-sm mt-1">Management POS</p>
        </div>
        {{-- Desktop Sidebar Nav --}}
        <div x-data="{
            manajemen: {{ request()->is('menus','management/*') ? 'true' : 'false' }},
            laporan: {{ request()->is('history','reports*') ? 'true' : 'false' }}
        }" class="hidden md:flex flex-col flex-1 p-3 overflow-y-auto gap-1">

            {{-- Dashboard --}}
            <a href="/" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors font-medium text-sm {{ request()->is('/') ? 'bg-[var(--color-kebab-red)]/15 text-[var(--color-kebab-red)]' : 'text-[var(--color-kebab-text-muted)] hover:bg-[var(--color-kebab-dark-hover)] hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>

            {{-- Transaksi --}}
            <a href="/pos" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors font-medium text-sm {{ request()->is('pos') ? 'bg-[var(--color-kebab-red)]/15 text-[var(--color-kebab-red)]' : 'text-[var(--color-kebab-text-muted)] hover:bg-[var(--color-kebab-dark-hover)] hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Transaksi
            </a>

            {{-- Manajemen Accordion --}}
            <div>
                <button @click="manajemen = !manajemen" class="w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl transition-colors font-medium text-sm {{ request()->is('menus','management/*') ? 'bg-[var(--color-kebab-red)]/15 text-[var(--color-kebab-red)]' : 'text-[var(--color-kebab-text-muted)] hover:bg-[var(--color-kebab-dark-hover)] hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        Manajemen
                    </div>
                    <svg class="w-4 h-4 transition-transform" :class="manajemen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="manajemen" x-transition class="mt-1 ml-4 pl-3 border-l border-[var(--color-kebab-dark-hover)] space-y-1">
                    <a href="/menus" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors {{ request()->is('menus') ? 'text-[var(--color-kebab-red)] font-bold' : 'text-[var(--color-kebab-text-muted)] hover:text-white hover:bg-[var(--color-kebab-dark-hover)]' }}">Barang atau Jasa</a>
                    <a href="/management/stock" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors {{ request()->is('management/stock') ? 'text-[var(--color-kebab-red)] font-bold' : 'text-[var(--color-kebab-text-muted)] hover:text-white hover:bg-[var(--color-kebab-dark-hover)]' }}">Manajemen Stok</a>
                    <a href="/management/purchases" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors {{ request()->is('management/purchases') ? 'text-[var(--color-kebab-red)] font-bold' : 'text-[var(--color-kebab-text-muted)] hover:text-white hover:bg-[var(--color-kebab-dark-hover)]' }}">Pembelian dari Supplier</a>
                    <a href="/management/contacts" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors {{ request()->is('management/contacts') ? 'text-[var(--color-kebab-red)] font-bold' : 'text-[var(--color-kebab-text-muted)] hover:text-white hover:bg-[var(--color-kebab-dark-hover)]' }}">Pelanggan & Supplier</a>
                </div>
            </div>

            {{-- Laporan Accordion --}}
            <div>
                <button @click="laporan = !laporan" class="w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl transition-colors font-medium text-sm {{ request()->is('history','reports*') ? 'bg-[var(--color-kebab-red)]/15 text-[var(--color-kebab-red)]' : 'text-[var(--color-kebab-text-muted)] hover:bg-[var(--color-kebab-dark-hover)] hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Laporan
                    </div>
                    <svg class="w-4 h-4 transition-transform" :class="laporan ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="laporan" x-transition class="mt-1 ml-4 pl-3 border-l border-[var(--color-kebab-dark-hover)] space-y-1">
                    <a href="/reports/cash-flow" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors {{ request()->is('reports/cash-flow') ? 'text-[var(--color-kebab-red)] font-bold' : 'text-[var(--color-kebab-text-muted)] hover:text-white hover:bg-[var(--color-kebab-dark-hover)]' }}">Arus Keuangan</a>
                    <a href="/history" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors {{ request()->is('history') ? 'text-[var(--color-kebab-red)] font-bold' : 'text-[var(--color-kebab-text-muted)] hover:text-white hover:bg-[var(--color-kebab-dark-hover)]' }}">Transaksi</a>
                    <a href="/reports" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors {{ request()->is('reports') && !request()->is('reports/*') ? 'text-[var(--color-kebab-red)] font-bold' : 'text-[var(--color-kebab-text-muted)] hover:text-white hover:bg-[var(--color-kebab-dark-hover)]' }}">Penjualan Barang</a>
                    <a href="/reports/categories" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors {{ request()->is('reports/categories') ? 'text-[var(--color-kebab-red)] font-bold' : 'text-[var(--color-kebab-text-muted)] hover:text-white hover:bg-[var(--color-kebab-dark-hover)]' }}">Penjualan Kategori</a>
                </div>
            </div>

            {{-- Shift --}}
            <a href="/shift" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors font-medium text-sm {{ request()->is('shift') ? 'bg-[var(--color-kebab-red)]/15 text-[var(--color-kebab-red)]' : 'text-[var(--color-kebab-text-muted)] hover:bg-[var(--color-kebab-dark-hover)] hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Shift
            </a>

            {{-- Pengaturan --}}
            <a href="/settings" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors font-medium text-sm {{ request()->is('settings') ? 'bg-[var(--color-kebab-red)]/15 text-[var(--color-kebab-red)]' : 'text-[var(--color-kebab-text-muted)] hover:bg-[var(--color-kebab-dark-hover)] hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Pengaturan
            </a>

            <div class="mt-auto pt-4 border-t border-[var(--color-kebab-dark-hover)]">
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-red-500/10 transition-colors text-[var(--color-kebab-text-muted)] hover:text-red-400 text-sm font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>

        {{-- Mobile Bottom Nav (5 ikon utama) --}}
        <div class="flex md:hidden justify-around p-2">
            <a href="/" class="flex flex-col items-center p-2 {{ request()->is('/') ? 'text-[var(--color-kebab-red)]' : 'text-[var(--color-kebab-text-muted)]' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="text-[10px] mt-1">Home</span>
            </a>
            <a href="/pos" class="flex flex-col items-center p-2 {{ request()->is('pos') ? 'text-[var(--color-kebab-red)]' : 'text-[var(--color-kebab-text-muted)]' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span class="text-[10px] mt-1">Kasir</span>
            </a>
            <a href="/menus" class="flex flex-col items-center p-2 {{ request()->is('menus','management/*') ? 'text-[var(--color-kebab-red)]' : 'text-[var(--color-kebab-text-muted)]' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span class="text-[10px] mt-1">Manajemen</span>
            </a>
            <a href="/shift" class="flex flex-col items-center p-2 {{ request()->is('shift') ? 'text-[var(--color-kebab-red)]' : 'text-[var(--color-kebab-text-muted)]' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-[10px] mt-1">Shift</span>
            </a>
            <a href="/reports" class="flex flex-col items-center p-2 {{ request()->is('history','reports*') ? 'text-[var(--color-kebab-red)]' : 'text-[var(--color-kebab-text-muted)]' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span class="text-[10px] mt-1">Laporan</span>
            </a>
        </div>
    </nav>

    <!-- Global Print Area -->
    <div id="global-print-area" class="hidden">
        <style>
            @media print {
                body * { visibility: hidden !important; }
                #global-print-area, #global-print-area * { visibility: visible !important; }
                #global-print-area {
                    position: fixed !important; top: 0; left: 0;
                    width: 80mm; font-family: 'Courier New', monospace;
                    font-size: 12px; color: #000; background: #fff;
                    padding: 8px; margin: 0;
                }
            }
        </style>
        <div id="print-content"></div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 p-4 md:p-8 pb-24 md:pb-8 overflow-y-auto">
        <div class="max-w-7xl mx-auto">
            @yield('content')
        </div>
    </main>

    <!-- Toast Notification -->
    <div id="toast" class="fixed top-5 right-5 z-[9999] flex flex-col gap-2 pointer-events-none"></div>

    @if(session('success'))
    <script>window._flashSuccess = {{ json_encode(session('success')) }};</script>
    @endif

    <script>
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const el = document.createElement('div');
        const colors = {
            success: 'bg-green-500 border-green-400',
            error:   'bg-red-600 border-red-500',
            info:    'bg-blue-500 border-blue-400',
        };
        el.className = `pointer-events-auto flex items-center gap-3 px-5 py-3 rounded-xl border shadow-2xl text-white font-bold text-sm max-w-sm transition-all duration-300 opacity-0 translate-x-4 ${colors[type] || colors.success}`;
        el.innerHTML = `<svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span>${message}</span>`;
        toast.appendChild(el);
        requestAnimationFrame(() => { el.classList.remove('opacity-0', 'translate-x-4'); });
        setTimeout(() => {
            el.classList.add('opacity-0', 'translate-x-4');
            setTimeout(() => el.remove(), 300);
        }, 3500);
    }
    if (window._flashSuccess) showToast(window._flashSuccess);
    </script>
</body>
</html>
