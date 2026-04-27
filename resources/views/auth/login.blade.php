<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cheframa Kebab</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background-color: #0a0a0a; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-[#0a0a0a]">

    <div class="w-full max-w-sm px-6">
        {{-- Logo --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-[var(--color-kebab-red)] shadow-[0_0_40px_rgba(230,57,70,0.5)] mb-5">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-black text-white">Cheframa<span class="text-[var(--color-kebab-red)]">Kebab</span></h1>
            <p class="text-[var(--color-kebab-text-muted)] mt-2 text-sm">Sistem Manajemen POS</p>
        </div>

        {{-- Card --}}
        <div class="bg-[var(--color-kebab-dark-card)] border border-[var(--color-kebab-dark-hover)] rounded-2xl p-8 shadow-2xl">
            <h2 class="text-xl font-bold text-white mb-1">Masuk ke Sistem</h2>
            <p class="text-[var(--color-kebab-text-muted)] text-sm mb-6">Masukkan password untuk melanjutkan.</p>

            @if(session('success'))
            <div class="bg-green-500/20 border border-green-500/40 text-green-400 p-3 rounded-xl mb-4 text-sm font-medium">
                {{ session('success') }}
            </div>
            @endif

            <form method="POST" action="/login">
                @csrf
                <div class="mb-5">
                    <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider mb-2">Password</label>
                    <input type="password" name="password" autofocus
                        class="w-full bg-[var(--color-kebab-dark)] border @error('password') border-red-500 @else border-[var(--color-kebab-dark-hover)] @enderror rounded-xl p-3.5 text-white text-sm focus:outline-none focus:border-[var(--color-kebab-red)] transition-colors"
                        placeholder="Masukkan password...">
                    @error('password')
                    <p class="mt-2 text-red-500 text-xs font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-[var(--color-kebab-red)] hover:bg-[var(--color-kebab-red-dark)] text-white font-black py-3.5 rounded-xl transition-all shadow-[0_0_15px_rgba(230,57,70,0.4)] hover:shadow-[0_0_25px_rgba(230,57,70,0.6)] text-base">
                    Masuk →
                </button>
            </form>
        </div>

        <p class="text-center text-[var(--color-kebab-text-muted)] text-xs mt-6">
            Cheframa Kebab POS &copy; {{ date('Y') }}
        </p>
    </div>
</body>
</html>
