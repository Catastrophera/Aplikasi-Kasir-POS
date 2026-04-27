@extends('layouts.app')

@section('title', 'Dashboard - Cheframa Kebab')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-3xl font-bold">Dashboard</h2>
        <p class="text-[var(--color-kebab-text-muted)] mt-1">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>
    <a href="/pos" class="bg-[var(--color-kebab-red)] hover:bg-[var(--color-kebab-red-dark)] text-white font-bold px-5 py-2.5 rounded-xl transition-colors shadow-[0_0_15px_rgba(230,57,70,0.35)] flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        Buka Kasir
    </a>
</div>

{{-- Stats Grid --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-[var(--color-kebab-dark)] to-[var(--color-kebab-dark-card)] p-5 rounded-2xl border border-[var(--color-kebab-dark-hover)] shadow-lg hover:border-[var(--color-kebab-red)] transition-all">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider">Pendapatan Hari Ini</span>
            <div class="p-2 bg-[var(--color-kebab-red)]/20 rounded-lg text-[var(--color-kebab-red)]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <p class="text-2xl font-black text-white">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
        <p class="text-xs text-[var(--color-kebab-text-muted)] mt-1">{{ $todayOrders }} transaksi</p>
    </div>

    <div class="bg-gradient-to-br from-[var(--color-kebab-dark)] to-[var(--color-kebab-dark-card)] p-5 rounded-2xl border border-[var(--color-kebab-dark-hover)] shadow-lg hover:border-blue-500 transition-all">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider">Pendapatan Bulan Ini</span>
            <div class="p-2 bg-blue-500/20 rounded-lg text-blue-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
        </div>
        <p class="text-2xl font-black text-white">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</p>
        <p class="text-xs text-[var(--color-kebab-text-muted)] mt-1">{{ $monthOrders }} transaksi</p>
    </div>

    <div class="bg-gradient-to-br from-[var(--color-kebab-dark)] to-[var(--color-kebab-dark-card)] p-5 rounded-2xl border border-[var(--color-kebab-dark-hover)] shadow-lg hover:border-green-500 transition-all">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider">Avg. per Transaksi</span>
            <div class="p-2 bg-green-500/20 rounded-lg text-green-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
        </div>
        <p class="text-2xl font-black text-white">Rp {{ number_format($avgTransaction, 0, ',', '.') }}</p>
        <p class="text-xs text-[var(--color-kebab-text-muted)] mt-1">rata-rata hari ini</p>
    </div>

    <div class="bg-gradient-to-br from-[var(--color-kebab-dark)] to-[var(--color-kebab-dark-card)] p-5 rounded-2xl border border-[var(--color-kebab-dark-hover)] shadow-lg hover:border-yellow-500 transition-all">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider">Total Porsi Terjual</span>
            <div class="p-2 bg-yellow-500/20 rounded-lg text-yellow-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
        </div>
        <p class="text-2xl font-black text-white">{{ number_format($todayItems) }}</p>
        <p class="text-xs text-[var(--color-kebab-text-muted)] mt-1">porsi hari ini</p>
    </div>
</div>

{{-- Chart + Top Items --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- 7-day chart --}}
    <div class="lg:col-span-2 bg-[var(--color-kebab-dark-card)] p-6 rounded-2xl border border-[var(--color-kebab-dark-hover)] shadow-lg">
        <h3 class="font-bold text-lg mb-5">Tren Pendapatan 7 Hari Terakhir</h3>
        @php $maxDay = $recentDays->max('total') ?: 1; @endphp
        <div class="flex items-end gap-3 h-40">
            @forelse($recentDays->reverse() as $day)
            @php $pct = ($day->total / $maxDay) * 100; @endphp
            <div class="flex-1 flex flex-col items-center gap-2 group">
                <span class="text-[10px] text-[var(--color-kebab-red)] font-bold opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Rp {{ number_format($day->total/1000, 0, ',', '.') }}k</span>
                <div class="w-full bg-[var(--color-kebab-dark)] rounded-t-lg relative overflow-hidden" style="height: {{ max(8, $pct * 1.3) }}px">
                    <div class="absolute inset-0 bg-gradient-to-t from-[var(--color-kebab-red-dark)] to-[var(--color-kebab-red)] opacity-80 group-hover:opacity-100 transition-opacity rounded-t-lg"></div>
                </div>
                <span class="text-[10px] text-[var(--color-kebab-text-muted)]">{{ \Carbon\Carbon::parse($day->date)->format('d/m') }}</span>
            </div>
            @empty
            <p class="text-[var(--color-kebab-text-muted)] text-sm m-auto">Belum ada data 7 hari terakhir.</p>
            @endforelse
        </div>
    </div>

    {{-- Top 5 menu --}}
    <div class="bg-[var(--color-kebab-dark-card)] p-6 rounded-2xl border border-[var(--color-kebab-dark-hover)] shadow-lg">
        <h3 class="font-bold text-lg mb-5">Top 5 Menu Terlaris</h3>
        @php $maxQty = $topItems->max('total_qty') ?: 1; @endphp
        <div class="space-y-4">
            @forelse($topItems as $item)
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium text-white truncate pr-2">{{ $item->menu->name ?? 'Menu Dihapus' }}</span>
                    <span class="font-black text-[var(--color-kebab-red)] shrink-0">{{ $item->total_qty }}x</span>
                </div>
                <div class="w-full bg-[var(--color-kebab-dark)] rounded-full h-2">
                    <div class="bg-[var(--color-kebab-red)] h-2 rounded-full" style="width: {{ ($item->total_qty / $maxQty) * 100 }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-[var(--color-kebab-text-muted)] text-sm text-center py-4">Belum ada data.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Recent Transactions --}}
<div class="bg-[var(--color-kebab-dark-card)] rounded-2xl border border-[var(--color-kebab-dark-hover)] overflow-hidden shadow-lg">
    <div class="p-5 border-b border-[var(--color-kebab-dark-hover)] flex justify-between items-center">
        <h3 class="font-bold text-lg">Transaksi Terbaru</h3>
        <a href="/history" class="text-sm text-[var(--color-kebab-red)] hover:underline font-bold">Lihat Semua →</a>
    </div>
    <table class="w-full text-left">
        <thead>
            <tr class="bg-[var(--color-kebab-dark)] text-xs text-[var(--color-kebab-text-muted)] uppercase tracking-wider">
                <th class="p-4">ID</th>
                <th class="p-4">Waktu</th>
                <th class="p-4">Kanal</th>
                <th class="p-4">Bayar</th>
                <th class="p-4 text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentTransactions as $trx)
            <tr class="border-t border-[var(--color-kebab-dark-hover)] hover:bg-[var(--color-kebab-dark-hover)] transition-colors">
                <td class="p-4 font-mono text-sm text-[var(--color-kebab-text-muted)]">#{{ str_pad($trx->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td class="p-4 text-sm text-[var(--color-kebab-text-muted)]">{{ $trx->created_at->format('H:i') }}</td>
                <td class="p-4"><span class="bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] px-2 py-1 rounded-lg text-xs font-bold">{{ $trx->channel }}</span></td>
                <td class="p-4 text-sm">{{ $trx->payment_method }}</td>
                <td class="p-4 text-right font-black text-[var(--color-kebab-red)]">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="p-8 text-center text-[var(--color-kebab-text-muted)]">Belum ada transaksi hari ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
