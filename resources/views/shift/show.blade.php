@extends('layouts.app')
@section('title', 'Detail Shift - Cheframa Kebab')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="/shift" class="w-10 h-10 bg-[var(--color-kebab-dark-card)] border border-[var(--color-kebab-dark-hover)] rounded-xl flex items-center justify-center hover:border-[var(--color-kebab-red)] transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold">Detail Riwayat Shift</h2>
            <p class="text-[var(--color-kebab-text-muted)] text-sm mt-0.5">{{ $shift->cashDrawer->name }}</p>
        </div>
    </div>

    {{-- Info Shift --}}
    <div class="bg-gradient-to-br from-[var(--color-kebab-dark-card)] to-[#0d1f0d] rounded-2xl border border-[var(--color-kebab-dark-hover)] overflow-hidden">
        <div class="divide-y divide-[var(--color-kebab-dark-hover)]">
            <div class="grid grid-cols-2 p-4 items-center">
                <span class="text-sm text-[var(--color-kebab-text-muted)]">Nama - Posisi</span>
                <span class="text-sm font-bold text-right">{{ $shift->opened_by }} - Kasir</span>
            </div>
            <div class="grid grid-cols-2 p-4 items-center bg-green-500/5">
                <span class="text-sm text-[var(--color-kebab-text-muted)]">Mulai Shift</span>
                <span class="text-sm font-bold text-right">{{ $shift->opened_at->format('d M Y H:i:s') }}</span>
            </div>
            <div class="grid grid-cols-2 p-4 items-center">
                <span class="text-sm text-[var(--color-kebab-text-muted)]">Shift Selesai</span>
                <span class="text-sm font-bold text-right">{{ $shift->closed_at ? $shift->closed_at->format('d M Y H:i:s') : '-' }}</span>
            </div>
        </div>
    </div>

    {{-- Rekap Keuangan Shift --}}
    <div class="bg-[var(--color-kebab-dark-card)] rounded-2xl border border-[var(--color-kebab-dark-hover)] overflow-hidden">
        <div class="divide-y divide-[var(--color-kebab-dark-hover)]">
            <div class="grid grid-cols-2 p-4 items-center">
                <span class="text-sm text-[var(--color-kebab-text-muted)]">Penjualan Tunai</span>
                <span class="text-sm font-bold text-right text-green-400">Rp {{ number_format($penjualanTunai, 0, ',', '.') }}</span>
            </div>
            <div class="grid grid-cols-2 p-4 items-center">
                <span class="text-sm text-[var(--color-kebab-text-muted)]">Pemasukan Lain</span>
                <span class="text-sm font-bold text-right text-green-400">Rp {{ number_format($pemasukanLain, 0, ',', '.') }}</span>
            </div>
            <div class="grid grid-cols-2 p-4 items-center">
                <span class="text-sm text-[var(--color-kebab-text-muted)]">Pengeluaran</span>
                <span class="text-sm font-bold text-right text-red-400">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</span>
            </div>
            <div class="grid grid-cols-2 p-4 items-center">
                <span class="text-sm text-[var(--color-kebab-text-muted)]">Pengeluaran Lain</span>
                <span class="text-sm font-bold text-right text-red-400">Rp {{ number_format($pengeluaranLain, 0, ',', '.') }}</span>
            </div>
            <div class="grid grid-cols-2 p-4 items-center bg-[var(--color-kebab-dark)]">
                <span class="text-sm font-black text-white">Subtotal</span>
                <span class="text-sm font-black text-right text-white">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Kas & Selisih --}}
    <div class="bg-[var(--color-kebab-dark-card)] rounded-2xl border border-[var(--color-kebab-dark-hover)] overflow-hidden">
        <div class="divide-y divide-[var(--color-kebab-dark-hover)]">
            <div class="grid grid-cols-2 p-4 items-center">
                <span class="text-sm text-[var(--color-kebab-text-muted)]">Kas Awal</span>
                <span class="text-sm font-bold text-right">Rp {{ number_format($shift->opening_balance, 0, ',', '.') }}</span>
            </div>
            <div class="grid grid-cols-2 p-4 items-center bg-yellow-500/5">
                <span class="text-sm font-bold text-yellow-400">Penerimaan Sistem</span>
                <span class="text-sm font-bold text-right text-yellow-400">Rp {{ number_format($penerimaanSistem, 0, ',', '.') }}</span>
            </div>
            <div class="grid grid-cols-2 p-4 items-center bg-green-500/5">
                <span class="text-sm font-bold text-green-400">Penerimaan Aktual</span>
                <span class="text-sm font-bold text-right text-green-400">Rp {{ number_format($shift->closing_balance ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="grid grid-cols-2 p-4 items-center {{ $selisih < 0 ? 'bg-red-500/10' : 'bg-green-500/10' }}">
                <span class="text-sm font-black {{ $selisih < 0 ? 'text-red-400' : 'text-green-400' }}">Selisih</span>
                <span class="text-sm font-black text-right {{ $selisih < 0 ? 'text-red-400' : 'text-green-400' }}">
                    {{ $selisih < 0 ? '-' : '+' }}Rp {{ number_format(abs($selisih), 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Catatan --}}
    @if($shift->notes)
    <div class="bg-[var(--color-kebab-dark-card)] rounded-2xl border border-[var(--color-kebab-dark-hover)] p-4">
        <p class="text-xs text-[var(--color-kebab-text-muted)] uppercase tracking-wider font-bold mb-2">Catatan</p>
        <p class="text-sm text-white">{{ $shift->notes }}</p>
    </div>
    @endif

    {{-- Transaksi dalam shift --}}
    @if($shiftTransactions->count() > 0)
    <div class="bg-[var(--color-kebab-dark-card)] rounded-2xl border border-[var(--color-kebab-dark-hover)] overflow-hidden">
        <div class="p-4 border-b border-[var(--color-kebab-dark-hover)] flex justify-between">
            <h3 class="font-bold">Rekap Transaksi</h3>
            <span class="text-xs text-[var(--color-kebab-text-muted)]">{{ $shiftTransactions->count() }} transaksi</span>
        </div>
        <div class="divide-y divide-[var(--color-kebab-dark-hover)] max-h-72 overflow-y-auto">
            @foreach($shiftTransactions as $trx)
            <div class="p-4 flex justify-between items-center hover:bg-[var(--color-kebab-dark-hover)] transition-colors">
                <div>
                    <p class="font-mono text-sm font-bold">#{{ str_pad($trx->id, 5, '0', STR_PAD_LEFT) }}</p>
                    <p class="text-xs text-[var(--color-kebab-text-muted)]">{{ $trx->created_at->format('H:i') }} · {{ $trx->payment_method }} · {{ $trx->channel }}</p>
                </div>
                <p class="font-black text-green-400">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
