@extends('layouts.app')

@section('title', 'Shift - Cheframa Kebab')

@section('content')
<div x-data="{ tab: '{{ $activeShift ? 'now' : 'now' }}', showDrawerModal: false, showCloseModal: false }" class="space-y-5">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-bold">Shift</h2>
            <p class="text-[var(--color-kebab-text-muted)] mt-1">Kelola shift kasir & pencatatan kas.</p>
        </div>
        <button @click="showDrawerModal = true"
            class="bg-[var(--color-kebab-dark-card)] border border-[var(--color-kebab-dark-hover)] hover:border-[var(--color-kebab-red)] text-white px-4 py-2.5 rounded-xl font-bold flex items-center gap-2 transition-colors text-sm">
            <svg class="w-4 h-4 text-[var(--color-kebab-red)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Tambah/Edit Cashdrawer
        </button>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-500/20 text-green-400 p-4 rounded-xl border border-green-500/40 font-medium">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-red-500/20 text-red-400 p-4 rounded-xl border border-red-500/40 font-medium">{{ session('error') }}</div>
    @endif

    {{-- Info banner --}}
    <div class="flex items-start gap-3 bg-[var(--color-kebab-dark-card)] border border-[var(--color-kebab-dark-hover)] p-4 rounded-xl">
        <svg class="w-5 h-5 text-[var(--color-kebab-red)] mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm text-[var(--color-kebab-text-muted)]">Fitur Cashdrawer dapat mengelola Kas Kecil (Petty Cash), staff dan pencatatan kasir.</p>
    </div>

    {{-- Tabs --}}
    <div class="flex bg-[var(--color-kebab-dark-card)] rounded-xl p-1 border border-[var(--color-kebab-dark-hover)] w-fit">
        <button @click="tab = 'now'" :class="tab === 'now' ? 'bg-[var(--color-kebab-red)] text-white shadow' : 'text-[var(--color-kebab-text-muted)] hover:text-white'"
            class="px-6 py-2 rounded-lg font-bold text-sm transition-all">Saat Ini</button>
        <button @click="tab = 'history'" :class="tab === 'history' ? 'bg-[var(--color-kebab-red)] text-white shadow' : 'text-[var(--color-kebab-text-muted)] hover:text-white'"
            class="px-6 py-2 rounded-lg font-bold text-sm transition-all">Riwayat</button>
    </div>

    {{-- ══════ TAB: SAAT INI ══════ --}}
    <div x-show="tab === 'now'" x-transition>

        {{-- Profil Kasir --}}
        <div class="bg-gradient-to-br from-[var(--color-kebab-dark-card)] to-[#1a2a1a] rounded-2xl border border-[var(--color-kebab-dark-hover)] p-5 mb-4 flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-[var(--color-kebab-red)] flex items-center justify-center shadow-lg shrink-0">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xl font-black text-white">{{ session('pos_user', 'Kasir') }}</p>
                <p class="text-sm text-[var(--color-kebab-text-muted)]">Owner</p>
            </div>
        </div>

        @if($activeShift)
        {{-- === SHIFT SEDANG AKTIF === --}}
        <div class="bg-green-500/10 border border-green-500/40 rounded-2xl p-5 mb-4">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-green-400 shadow-[0_0_8px_#4ade80] animate-pulse"></span>
                    <span class="text-green-400 font-bold">Shift Aktif</span>
                </div>
                <span class="text-xs text-[var(--color-kebab-text-muted)]">{{ $activeShift->opened_at->format('H:i, d M Y') }}</span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                <div class="bg-[var(--color-kebab-dark)] rounded-xl p-3 border border-[var(--color-kebab-dark-hover)]">
                    <p class="text-xs text-[var(--color-kebab-text-muted)] mb-1">Cash Drawer</p>
                    <p class="font-bold text-white text-sm">{{ $activeShift->cashDrawer->name }}</p>
                </div>
                <div class="bg-[var(--color-kebab-dark)] rounded-xl p-3 border border-[var(--color-kebab-dark-hover)]">
                    <p class="text-xs text-[var(--color-kebab-text-muted)] mb-1">Saldo Awal</p>
                    <p class="font-bold text-white text-sm">Rp {{ number_format($activeShift->opening_balance, 0, ',', '.') }}</p>
                </div>
                <div class="bg-[var(--color-kebab-dark)] rounded-xl p-3 border border-[var(--color-kebab-dark-hover)]">
                    <p class="text-xs text-[var(--color-kebab-text-muted)] mb-1">Total Penjualan</p>
                    <p class="font-bold text-[var(--color-kebab-red)] text-sm">Rp {{ number_format($activeShift->total_sales, 0, ',', '.') }}</p>
                </div>
                <div class="bg-[var(--color-kebab-dark)] rounded-xl p-3 border border-[var(--color-kebab-dark-hover)]">
                    <p class="text-xs text-[var(--color-kebab-text-muted)] mb-1">Durasi</p>
                    <p class="font-bold text-white text-sm">{{ $activeShift->duration }}</p>
                </div>
            </div>
            <div class="flex items-center justify-between bg-[var(--color-kebab-dark)] rounded-xl p-3 border border-[var(--color-kebab-dark-hover)] mb-4">
                <span class="text-sm text-[var(--color-kebab-text-muted)]">Total Transaksi</span>
                <span class="font-black text-white">{{ $activeShift->total_transactions }} transaksi</span>
            </div>
            <button @click="showCloseModal = true"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-black py-3.5 rounded-xl transition-colors shadow-lg flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Tutup Shift
            </button>
        </div>

        {{-- Transaksi dalam shift --}}
        @if($shiftTransactions->count() > 0)
        <div class="bg-[var(--color-kebab-dark-card)] rounded-2xl border border-[var(--color-kebab-dark-hover)] overflow-hidden">
            <div class="p-4 border-b border-[var(--color-kebab-dark-hover)]">
                <h3 class="font-bold">Transaksi Shift Ini</h3>
            </div>
            <div class="divide-y divide-[var(--color-kebab-dark-hover)] max-h-80 overflow-y-auto">
                @foreach($shiftTransactions as $trx)
                <div class="p-4 flex justify-between items-center hover:bg-[var(--color-kebab-dark-hover)] transition-colors">
                    <div>
                        <p class="font-mono text-sm font-bold">#{{ str_pad($trx->id, 5, '0', STR_PAD_LEFT) }}</p>
                        <p class="text-xs text-[var(--color-kebab-text-muted)]">{{ $trx->created_at->format('H:i') }} · {{ $trx->payment_method }}</p>
                    </div>
                    <p class="font-black text-[var(--color-kebab-red)]">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @else
        {{-- === MULAI SHIFT BARU === --}}
        <div class="bg-[var(--color-kebab-dark-card)] rounded-2xl border border-[var(--color-kebab-dark-hover)] p-6">
            <h3 class="font-bold text-lg mb-5">Mulai Shift Baru</h3>
            <form action="/shift" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider mb-2">Pilih Cash Drawer</label>
                    <div class="flex gap-3">
                        <select name="cash_drawer_id" required
                            class="flex-1 bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white focus:outline-none focus:border-[var(--color-kebab-red)] appearance-none">
                            @foreach($cashDrawers as $drawer)
                            <option value="{{ $drawer->id }}">{{ $drawer->name }}</option>
                            @endforeach
                        </select>
                        <button type="button"
                            class="bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] hover:border-[var(--color-kebab-red)] rounded-xl px-4 flex items-center gap-2 text-sm font-bold transition-colors">
                            <svg class="w-4 h-4 text-[var(--color-kebab-red)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Refresh
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider mb-2">Masukkan Saldo Awal</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--color-kebab-text-muted)] font-bold">Rp</span>
                        <input type="number" name="opening_balance" value="0" min="0" required
                            class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 pl-12 text-white focus:outline-none focus:border-[var(--color-kebab-red)] text-lg font-bold">
                    </div>
                    <p class="text-xs text-[var(--color-kebab-text-muted)] mt-2 cursor-pointer hover:text-[var(--color-kebab-red)] transition-colors">
                        ↺ Masukkan Data Terakhir
                    </p>
                </div>
                <button type="submit"
                    class="w-full bg-[var(--color-kebab-red)] hover:bg-[var(--color-kebab-red-dark)] text-white font-black py-4 rounded-xl transition-colors shadow-[0_0_20px_rgba(230,57,70,0.4)] text-lg mt-2">
                    Mulai Shift
                </button>
            </form>
        </div>
        @endif
    </div>

    {{-- ══════ TAB: RIWAYAT ══════ --}}
    <div x-show="tab === 'history'" x-transition x-cloak>
        <div class="bg-[var(--color-kebab-dark-card)] rounded-2xl border border-[var(--color-kebab-dark-hover)] overflow-hidden">
            <div class="p-5 border-b border-[var(--color-kebab-dark-hover)]">
                <h3 class="font-bold text-lg">Riwayat Shift</h3>
                <p class="text-xs text-[var(--color-kebab-text-muted)] mt-1">30 shift terakhir</p>
            </div>
            @forelse($shiftHistory as $sh)
            <div class="p-4 border-b border-[var(--color-kebab-dark-hover)] hover:bg-[var(--color-kebab-dark-hover)] transition-colors">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="font-bold text-white">{{ $sh->cashDrawer->name }}</p>
                        <p class="text-xs text-[var(--color-kebab-text-muted)]">{{ $sh->opened_at->format('d M Y, H:i') }} → {{ $sh->closed_at?->format('H:i') ?? '-' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-black text-[var(--color-kebab-red)]">Rp {{ number_format($sh->total_sales, 0, ',', '.') }}</p>
                        <p class="text-xs text-[var(--color-kebab-text-muted)]">{{ $sh->total_transactions }} transaksi</p>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex gap-4 text-xs text-[var(--color-kebab-text-muted)]">
                        <span>Buka: Rp {{ number_format($sh->opening_balance, 0, ',', '.') }}</span>
                        <span>Tutup: Rp {{ number_format($sh->closing_balance ?? 0, 0, ',', '.') }}</span>
                        <span>Durasi: {{ $sh->duration }}</span>
                    </div>
                    <a href="/shift/{{ $sh->id }}" class="text-xs text-[var(--color-kebab-red)] hover:underline font-bold shrink-0 ml-3">Lihat Detail →</a>
                </div>
                @if($sh->notes)
                <p class="text-xs text-yellow-400 mt-1">Catatan: {{ $sh->notes }}</p>
                @endif
            </div>
            @empty
            <div class="p-12 text-center text-[var(--color-kebab-text-muted)]">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>Belum ada riwayat shift.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ═══ MODAL: Tutup Shift ═══ --}}
    <div x-show="showCloseModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-[var(--color-kebab-dark-card)] p-6 rounded-2xl border border-[var(--color-kebab-dark-hover)] shadow-2xl w-full max-w-md mx-4" @click.away="showCloseModal = false">
            <h3 class="text-xl font-bold mb-1">Tutup Shift</h3>
            <p class="text-sm text-[var(--color-kebab-text-muted)] mb-5">Shift akan ditutup dan rekap akan disimpan.</p>
            @if($activeShift)
            <form action="/shift/{{ $activeShift->id }}/close" method="POST" class="space-y-4">
                @csrf
                <div class="bg-[var(--color-kebab-dark)] rounded-xl p-4 border border-[var(--color-kebab-dark-hover)] space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-[var(--color-kebab-text-muted)]">Saldo Awal</span>
                        <span class="font-bold">Rp {{ number_format($activeShift->opening_balance, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-[var(--color-kebab-text-muted)]">Total Penjualan</span>
                        <span class="font-bold text-[var(--color-kebab-red)]">Rp {{ number_format($activeShift->total_sales, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm border-t border-[var(--color-kebab-dark-hover)] pt-2">
                        <span class="font-bold">Estimasi Saldo Akhir</span>
                        <span class="font-black text-green-400">Rp {{ number_format($activeShift->opening_balance + $activeShift->total_sales, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider mb-2">Saldo Akhir Aktual (Rp)</label>
                    <input type="number" name="closing_balance" value="{{ $activeShift->opening_balance + $activeShift->total_sales }}" min="0"
                        class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white focus:outline-none focus:border-[var(--color-kebab-red)]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider mb-2">Catatan (opsional)</label>
                    <textarea name="notes" rows="2" placeholder="Catatan shift..."
                        class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white focus:outline-none focus:border-[var(--color-kebab-red)] resize-none text-sm"></textarea>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" @click="showCloseModal = false"
                        class="flex-1 bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] text-white font-bold py-2.5 rounded-xl">Batal</button>
                    <button type="submit"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-black py-2.5 rounded-xl transition-colors">Tutup Shift</button>
                </div>
            </form>
            @endif
        </div>
    </div>

    {{-- ═══ MODAL: Cash Drawer ═══ --}}
    <div x-show="showDrawerModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-[var(--color-kebab-dark-card)] p-6 rounded-2xl border border-[var(--color-kebab-dark-hover)] shadow-2xl w-full max-w-lg mx-4" @click.away="showDrawerModal = false">
            <h3 class="text-xl font-bold mb-4 border-b border-[var(--color-kebab-dark-hover)] pb-3">Tambah / Edit Cashdrawer</h3>
            <form action="/shift/cash-drawers" method="POST" class="flex gap-3 mb-5">
                @csrf
                <input type="text" name="name" required placeholder="Nama cash drawer..." class="flex-1 bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white text-sm focus:outline-none focus:border-[var(--color-kebab-red)]">
                <input type="text" name="description" placeholder="Deskripsi (opsional)" class="flex-1 bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white text-sm focus:outline-none focus:border-[var(--color-kebab-red)]">
                <button type="submit" class="bg-[var(--color-kebab-red)] hover:bg-[var(--color-kebab-red-dark)] text-white font-bold px-4 py-3 rounded-xl transition-colors whitespace-nowrap">+ Tambah</button>
            </form>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @foreach($cashDrawers as $drawer)
                <div class="flex items-center justify-between bg-[var(--color-kebab-dark)] p-3 rounded-xl border border-[var(--color-kebab-dark-hover)]">
                    <div>
                        <p class="font-bold text-white text-sm">{{ $drawer->name }}</p>
                        @if($drawer->description)<p class="text-xs text-[var(--color-kebab-text-muted)]">{{ $drawer->description }}</p>@endif
                    </div>
                    <form action="/shift/cash-drawers/{{ $drawer->id }}" method="POST" onsubmit="return confirm('Hapus cash drawer ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-400 text-sm font-bold px-2">Hapus</button>
                    </form>
                </div>
                @endforeach
            </div>
            <div class="mt-5 pt-4 border-t border-[var(--color-kebab-dark-hover)]">
                <button @click="showDrawerModal = false" class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] text-white font-bold py-2.5 rounded-xl hover:bg-[var(--color-kebab-dark-hover)] transition-colors">Selesai</button>
            </div>
        </div>
    </div>

</div>
@endsection
