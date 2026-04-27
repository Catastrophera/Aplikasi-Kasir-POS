@extends('layouts.app')
@section('title', 'Pembelian dari Supplier - Cheframa Kebab')
@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
    <div class="bg-[var(--color-kebab-dark-card)] rounded-3xl border border-[var(--color-kebab-dark-hover)] p-12 max-w-md w-full">
        <div class="w-20 h-20 bg-[var(--color-kebab-red)]/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-[var(--color-kebab-red)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        </div>
        <h2 class="text-2xl font-black text-white mb-2">Pembelian dari Supplier</h2>
        <p class="text-[var(--color-kebab-text-muted)] mb-6">Fitur ini sedang dalam pengembangan dan akan segera hadir.</p>
        <span class="inline-block bg-yellow-500/20 text-yellow-400 border border-yellow-500/40 px-4 py-1.5 rounded-full text-sm font-bold">🚧 Coming Soon</span>
    </div>
</div>
@endsection
