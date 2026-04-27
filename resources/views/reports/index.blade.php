@extends('layouts.app')

@section('title', 'Laporan - Cheframa Kebab')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-3xl font-bold">Laporan Analisis</h2>
        <p class="text-[var(--color-kebab-text-muted)] mt-1">Ringkasan penjualan lengkap berdasarkan kanal, pembayaran, dan waktu.</p>
    </div>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-[var(--color-kebab-dark-card)] p-5 rounded-2xl border border-[var(--color-kebab-dark-hover)]">
        <p class="text-xs text-[var(--color-kebab-text-muted)] uppercase tracking-wider mb-2">Total Semua Waktu</p>
        <p class="text-xl font-black text-white">Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
        <p class="text-xs text-[var(--color-kebab-text-muted)] mt-1">{{ $grandCount }} transaksi</p>
    </div>
    <div class="bg-[var(--color-kebab-dark-card)] p-5 rounded-2xl border border-[var(--color-kebab-dark-hover)]">
        <p class="text-xs text-[var(--color-kebab-text-muted)] uppercase tracking-wider mb-2">Bulan Ini</p>
        <p class="text-xl font-black text-white">Rp {{ number_format($monthTotal, 0, ',', '.') }}</p>
        <p class="text-xs text-[var(--color-kebab-text-muted)] mt-1">{{ $monthCount }} transaksi</p>
    </div>
    <div class="bg-[var(--color-kebab-dark-card)] p-5 rounded-2xl border border-[var(--color-kebab-dark-hover)]">
        <p class="text-xs text-[var(--color-kebab-text-muted)] uppercase tracking-wider mb-2">Hari Terbaik</p>
        <p class="text-xl font-black text-white">Rp {{ number_format($bestDay->total ?? 0, 0, ',', '.') }}</p>
        <p class="text-xs text-[var(--color-kebab-text-muted)] mt-1">{{ $bestDay ? \Carbon\Carbon::parse($bestDay->date)->format('d M Y') : '-' }}</p>
    </div>
    <div class="bg-[var(--color-kebab-dark-card)] p-5 rounded-2xl border border-[var(--color-kebab-dark-hover)]">
        <p class="text-xs text-[var(--color-kebab-text-muted)] uppercase tracking-wider mb-2">Avg. / Hari (Bulan Ini)</p>
        <p class="text-xl font-black text-white">Rp {{ number_format($avgPerDay, 0, ',', '.') }}</p>
        <p class="text-xs text-[var(--color-kebab-text-muted)] mt-1">dari {{ $activeDays }} hari aktif</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Kanal --}}
    <div class="bg-[var(--color-kebab-dark-card)] rounded-2xl border border-[var(--color-kebab-dark-hover)] overflow-hidden">
        <div class="p-5 border-b border-[var(--color-kebab-dark-hover)]">
            <h3 class="font-bold text-lg">Pendapatan per Kanal</h3>
        </div>
        <div class="p-5 space-y-5">
            @php $maxChannel = $revenueByChannel->max('total') ?: 1; @endphp
            @forelse($revenueByChannel as $data)
            @php $pct = ($data->total / $maxChannel) * 100; @endphp
            <div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="font-bold text-white">{{ $data->channel }}</span>
                    <div class="text-right">
                        <span class="font-black text-[var(--color-kebab-red)]">Rp {{ number_format($data->total, 0, ',', '.') }}</span>
                        <span class="text-[var(--color-kebab-text-muted)] ml-2 text-xs">({{ $data->count }} trx)</span>
                    </div>
                </div>
                <div class="w-full bg-[var(--color-kebab-dark)] rounded-full h-3 border border-[var(--color-kebab-dark-hover)]">
                    <div class="bg-gradient-to-r from-[var(--color-kebab-red-dark)] to-[var(--color-kebab-red)] h-3 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-center text-[var(--color-kebab-text-muted)] py-4">Belum ada data.</p>
            @endforelse
        </div>
    </div>

    {{-- Metode Pembayaran --}}
    <div class="bg-[var(--color-kebab-dark-card)] rounded-2xl border border-[var(--color-kebab-dark-hover)] overflow-hidden">
        <div class="p-5 border-b border-[var(--color-kebab-dark-hover)]">
            <h3 class="font-bold text-lg">Metode Pembayaran</h3>
        </div>
        <div class="p-5 space-y-5">
            @php $maxPayment = $revenueByPayment->max('total') ?: 1; @endphp
            @forelse($revenueByPayment as $data)
            @php $pct = ($data->total / $maxPayment) * 100; @endphp
            <div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="font-bold text-white">{{ $data->payment_method }}</span>
                    <div class="text-right">
                        <span class="font-black text-blue-400">Rp {{ number_format($data->total, 0, ',', '.') }}</span>
                        <span class="text-[var(--color-kebab-text-muted)] ml-2 text-xs">({{ $data->count }} trx)</span>
                    </div>
                </div>
                <div class="w-full bg-[var(--color-kebab-dark)] rounded-full h-3 border border-[var(--color-kebab-dark-hover)]">
                    <div class="bg-gradient-to-r from-blue-700 to-blue-400 h-3 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-center text-[var(--color-kebab-text-muted)] py-4">Belum ada data.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Top Menu per Kategori --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-[var(--color-kebab-dark-card)] rounded-2xl border border-[var(--color-kebab-dark-hover)] overflow-hidden">
        <div class="p-5 border-b border-[var(--color-kebab-dark-hover)]">
            <h3 class="font-bold text-lg">Top 10 Menu Terlaris</h3>
        </div>
        <div class="p-5 space-y-3">
            @php $maxMenu = $topMenus->max('total_qty') ?: 1; @endphp
            @forelse($topMenus as $item)
            <div class="flex items-center gap-3">
                <span class="w-6 text-center font-black text-sm text-[var(--color-kebab-red)]">{{ $loop->iteration }}</span>
                <div class="flex-1">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-white truncate pr-2">{{ $item->menu->name ?? 'Dihapus' }}</span>
                        <span class="font-black text-[var(--color-kebab-text-muted)] shrink-0">{{ $item->total_qty }}x</span>
                    </div>
                    <div class="w-full bg-[var(--color-kebab-dark)] rounded-full h-1.5">
                        <div class="bg-[var(--color-kebab-red)] h-1.5 rounded-full" style="width: {{ ($item->total_qty / $maxMenu) * 100 }}%"></div>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-center text-[var(--color-kebab-text-muted)] py-4">Belum ada data.</p>
            @endforelse
        </div>
    </div>

    {{-- 30 hari terakhir --}}
    <div class="bg-[var(--color-kebab-dark-card)] rounded-2xl border border-[var(--color-kebab-dark-hover)] overflow-hidden">
        <div class="p-5 border-b border-[var(--color-kebab-dark-hover)]">
            <h3 class="font-bold text-lg">Tren Harian (30 Hari Terakhir)</h3>
        </div>
        <div class="p-5 space-y-3 max-h-96 overflow-y-auto">
            @forelse($recentDays as $data)
            @php
                $maxDay = $recentDays->max('total') ?: 1;
                $pct = ($data->total / $maxDay) * 100;
            @endphp
            <div class="flex items-center gap-3">
                <span class="text-xs text-[var(--color-kebab-text-muted)] w-16 shrink-0">{{ \Carbon\Carbon::parse($data->date)->format('d M') }}</span>
                <div class="flex-1 bg-[var(--color-kebab-dark)] rounded-full h-5 relative overflow-hidden border border-[var(--color-kebab-dark-hover)]">
                    <div class="bg-gradient-to-r from-[var(--color-kebab-red-dark)] to-[var(--color-kebab-red)] h-full rounded-r-full" style="width: {{ $pct }}%"></div>
                    <span class="absolute left-2 top-0.5 text-xs font-bold {{ $pct > 20 ? 'text-white' : 'text-gray-400' }}">{{ $data->count }} trx</span>
                </div>
                <span class="text-xs font-black text-green-400 w-24 text-right shrink-0">Rp {{ number_format($data->total/1000, 0) }}k</span>
            </div>
            @empty
            <p class="text-center text-[var(--color-kebab-text-muted)] py-4">Belum ada data.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
