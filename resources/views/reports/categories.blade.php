@extends('layouts.app')
@section('title', 'Laporan Penjualan Per Kategori - Cheframa Kebab')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-3xl font-bold">Penjualan Per Kategori</h2>
        <p class="text-[var(--color-kebab-text-muted)] mt-1">Analisis kategori yang paling diminati pelanggan.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Tabel Data --}}
        <div class="bg-[var(--color-kebab-dark-card)] rounded-2xl border border-[var(--color-kebab-dark-hover)] overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[var(--color-kebab-dark)] text-xs text-[var(--color-kebab-text-muted)] uppercase tracking-wider">
                        <th class="p-4">Kategori</th>
                        <th class="p-4 text-center">Jumlah Item</th>
                        <th class="p-4 text-right">Total Penjualan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-kebab-dark-hover)]">
                    @php 
                        $grandTotal = 0;
                        $grandCount = 0;
                    @endphp
                    @forelse($categorySales as $sale)
                    @php 
                        $grandTotal += $sale->total_amount;
                        $grandCount += $sale->total_qty;
                    @endphp
                    <tr class="hover:bg-[var(--color-kebab-dark-hover)] transition-colors">
                        <td class="p-4 font-bold text-white">{{ $sale->category_name }}</td>
                        <td class="p-4 text-center text-white">{{ number_format($sale->total_qty) }}</td>
                        <td class="p-4 text-right font-black text-green-400">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="p-12 text-center text-[var(--color-kebab-text-muted)]">Belum ada data penjualan.</td></tr>
                    @endforelse
                </tbody>
                @if($categorySales->count() > 0)
                <tfoot class="bg-[var(--color-kebab-dark)]">
                    <tr class="font-bold text-white">
                        <td class="p-4">TOTAL</td>
                        <td class="p-4 text-center">{{ number_format($grandCount) }}</td>
                        <td class="p-4 text-right text-[var(--color-kebab-red)]">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        {{-- Visualisasi Sederhana (Bar) --}}
        <div class="bg-[var(--color-kebab-dark-card)] rounded-2xl border border-[var(--color-kebab-dark-hover)] p-6 space-y-4">
            <h3 class="font-bold mb-4">Persentase Penjualan</h3>
            @foreach($categorySales as $sale)
            @php 
                $percentage = $grandTotal > 0 ? ($sale->total_amount / $grandTotal) * 100 : 0;
            @endphp
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="text-white">{{ $sale->category_name }}</span>
                    <span class="text-[var(--color-kebab-text-muted)]">{{ number_format($percentage, 1) }}%</span>
                </div>
                <div class="w-full bg-[var(--color-kebab-dark)] rounded-full h-2">
                    <div class="bg-[var(--color-kebab-red)] h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
