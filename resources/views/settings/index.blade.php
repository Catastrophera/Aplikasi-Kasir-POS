@extends('layouts.app')
@section('title', 'Pengaturan - Cheframa Kebab')
@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
    <div class="bg-[var(--color-kebab-dark-card)] rounded-3xl border border-[var(--color-kebab-dark-hover)] p-12 max-w-md w-full">
        <div class="w-20 h-20 bg-[var(--color-kebab-red)]/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-[var(--color-kebab-red)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <h2 class="text-2xl font-black text-white mb-2">Pengaturan</h2>
        <p class="text-[var(--color-kebab-text-muted)] mb-6">Fitur ini sedang dalam pengembangan dan akan segera hadir.</p>
        <span class="inline-block bg-yellow-500/20 text-yellow-400 border border-yellow-500/40 px-4 py-1.5 rounded-full text-sm font-bold">🚧 Coming Soon</span>
    </div>
</div>
@endsection
