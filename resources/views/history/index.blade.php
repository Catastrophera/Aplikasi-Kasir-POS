@extends('layouts.app')

@section('title', 'Riwayat Pesanan - Cheframa Kebab')

@section('content')

{{-- ═══ PRINT RECEIPT AREA MOVED TO APP.BLADE.PHP ═══ --}}

<div class="mb-6 flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
    <div>
        <h2 class="text-3xl font-bold">Riwayat Pesanan</h2>
        <p class="text-[var(--color-kebab-text-muted)] mt-1">Total {{ $transactions->count() }} pesanan ditemukan.</p>
    </div>
</div>

{{-- Filter Bar --}}
<form method="GET" action="/history" class="bg-[var(--color-kebab-dark-card)] p-4 rounded-2xl border border-[var(--color-kebab-dark-hover)] mb-6 flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-36">
        <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider mb-1">Tanggal</label>
        <input type="date" name="date" value="{{ request('date') }}"
            class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-lg p-2 text-sm focus:outline-none focus:border-[var(--color-kebab-red)] text-white">
    </div>
    <div class="flex-1 min-w-36">
        <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider mb-1">Kanal</label>
        <select name="channel" class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-lg p-2 text-sm focus:outline-none focus:border-[var(--color-kebab-red)] text-white">
            <option value="">Semua Kanal</option>
            @foreach($channels as $ch)
            <option value="{{ $ch }}" {{ request('channel') == $ch ? 'selected' : '' }}>{{ $ch }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex-1 min-w-36">
        <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider mb-1">Pembayaran</label>
        <select name="payment" class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-lg p-2 text-sm focus:outline-none focus:border-[var(--color-kebab-red)] text-white">
            <option value="">Semua Metode</option>
            @foreach($paymentMethods as $pm)
            <option value="{{ $pm }}" {{ request('payment') == $pm ? 'selected' : '' }}>{{ $pm }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex gap-2">
        <button type="submit" class="bg-[var(--color-kebab-red)] hover:bg-[var(--color-kebab-red-dark)] text-white font-bold px-5 py-2 rounded-lg transition-colors text-sm">Filter</button>
        <a href="/history" class="bg-[var(--color-kebab-dark)] hover:bg-[var(--color-kebab-dark-hover)] border border-[var(--color-kebab-dark-hover)] text-[var(--color-kebab-text-muted)] font-bold px-4 py-2 rounded-lg transition-colors text-sm">Reset</a>
    </div>
</form>

@if($transactions->count() > 0)
<div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-[var(--color-kebab-dark-card)] p-4 rounded-xl border border-[var(--color-kebab-dark-hover)] text-center">
        <p class="text-xs text-[var(--color-kebab-text-muted)] mb-1">Total Pendapatan</p>
        <p class="text-xl font-black text-[var(--color-kebab-red)]">Rp {{ number_format($transactions->sum('total_price'), 0, ',', '.') }}</p>
    </div>
    <div class="bg-[var(--color-kebab-dark-card)] p-4 rounded-xl border border-[var(--color-kebab-dark-hover)] text-center">
        <p class="text-xs text-[var(--color-kebab-text-muted)] mb-1">Jumlah Transaksi</p>
        <p class="text-xl font-black text-white">{{ $transactions->count() }}</p>
    </div>
    <div class="bg-[var(--color-kebab-dark-card)] p-4 rounded-xl border border-[var(--color-kebab-dark-hover)] text-center col-span-2 sm:col-span-1">
        <p class="text-xs text-[var(--color-kebab-text-muted)] mb-1">Rata-rata per Transaksi</p>
        <p class="text-xl font-black text-green-400">Rp {{ number_format($transactions->avg('total_price'), 0, ',', '.') }}</p>
    </div>
</div>
@endif

{{-- Table / Mobile Cards --}}
<div class="bg-[var(--color-kebab-dark-card)] rounded-2xl border border-[var(--color-kebab-dark-hover)] overflow-hidden">
    
    {{-- Desktop Table --}}
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-[var(--color-kebab-dark)] border-b border-[var(--color-kebab-dark-hover)]">
                    <th class="p-4 text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider">ID</th>
                    <th class="p-4 text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider">Waktu</th>
                    <th class="p-4 text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider">Kanal</th>
                    <th class="p-4 text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider">Pembayaran</th>
                    <th class="p-4 text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider text-right">Total</th>
                    <th class="p-4 text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                <tr class="border-b border-[var(--color-kebab-dark-hover)] hover:bg-[var(--color-kebab-dark-hover)] transition-colors">
                    <td class="p-4 font-mono text-sm text-[var(--color-kebab-text-muted)]">#{{ str_pad($trx->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="p-4 text-sm text-[var(--color-kebab-text-muted)]">{{ $trx->created_at->format('d M Y, H:i') }}</td>
                    <td class="p-4">
                        <span class="bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] px-2 py-1 rounded-lg text-xs font-bold">{{ $trx->channel }}</span>
                    </td>
                    <td class="p-4 text-sm text-white">{{ $trx->payment_method }}</td>
                    <td class="p-4 font-black text-[var(--color-kebab-red)] text-right">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                    <td class="p-4 text-center">
                        <div class="flex items-center justify-center gap-3">
                            <button onclick="document.getElementById('modal-{{ $trx->id }}').showModal()"
                                class="text-blue-400 hover:text-blue-300 text-sm font-bold underline decoration-dotted">
                                {{ $trx->items->sum('quantity') }} item
                            </button>
                            <button onclick="printReceipt({{ $trx->id }})"
                                class="text-green-400 hover:text-green-300 transition-colors" title="Cetak Struk">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            </button>
                            @php $paddedId = str_pad($trx->id, 5, '0', STR_PAD_LEFT); @endphp
                            <form action="/transactions/{{ $trx->id }}" method="POST"
                                onsubmit="return confirm('Hapus transaksi #{{ $paddedId }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-400 text-sm font-bold transition-colors">Void</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-12 text-center text-[var(--color-kebab-text-muted)]">Belum ada riwayat transaksi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden flex flex-col">
        @forelse($transactions as $trx)
        <div class="p-4 border-b border-[var(--color-kebab-dark-hover)] hover:bg-[var(--color-kebab-dark-hover)] transition-colors">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <span class="font-mono text-sm font-bold text-white">#{{ str_pad($trx->id, 5, '0', STR_PAD_LEFT) }}</span>
                    <span class="text-xs text-[var(--color-kebab-text-muted)] ml-2">{{ $trx->created_at->format('d M, H:i') }}</span>
                </div>
                <span class="bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] px-2 py-1 rounded-lg text-[10px] font-bold uppercase">{{ $trx->channel }}</span>
            </div>
            
            <div class="flex justify-between items-end mb-3">
                <div class="text-sm">
                    <p class="text-[var(--color-kebab-text-muted)] mb-0.5">Metode: <span class="text-white font-medium">{{ $trx->payment_method }}</span></p>
                    <button onclick="document.getElementById('modal-{{ $trx->id }}').showModal()"
                        class="text-blue-400 text-xs font-bold underline decoration-dotted">Lihat Detail ({{ $trx->items->sum('quantity') }} item)</button>
                </div>
                <p class="font-black text-lg text-[var(--color-kebab-red)]">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</p>
            </div>

            <div class="flex gap-2 pt-3 border-t border-[var(--color-kebab-dark)]">
                <button onclick="printReceipt({{ $trx->id }})" class="flex-1 bg-[var(--color-kebab-dark)] hover:bg-[var(--color-kebab-dark-hover)] border border-[var(--color-kebab-dark-hover)] text-green-400 font-bold py-2 rounded-xl text-xs flex items-center justify-center gap-1 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak
                </button>
                <form action="/transactions/{{ $trx->id }}" method="POST" class="flex-1"
                    onsubmit="return confirm('Hapus transaksi #{{ str_pad($trx->id, 5, '0', STR_PAD_LEFT) }}? Tindakan ini tidak bisa dibatalkan.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full bg-[var(--color-kebab-dark)] hover:bg-red-900/40 border border-[var(--color-kebab-dark-hover)] hover:border-red-500/50 text-red-500 font-bold py-2 rounded-xl text-xs transition-colors">Void Transaksi</button>
                </form>
            </div>
        </div>
        @empty
        <div class="p-12 text-center text-[var(--color-kebab-text-muted)]">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Tidak ada transaksi ditemukan.
        </div>
        @endforelse
    </div>

    {{-- Modals for both views --}}
    @foreach($transactions as $trx)
    <dialog id="modal-{{ $trx->id }}" class="bg-[var(--color-kebab-dark-card)] text-white p-5 sm:p-6 rounded-2xl border border-[var(--color-kebab-dark-hover)] shadow-2xl w-[90%] max-w-md m-auto backdrop:bg-black/80 backdrop:backdrop-blur-sm">
        <div class="flex justify-between items-center mb-4 border-b border-[var(--color-kebab-dark-hover)] pb-3">
            <div>
                <h3 class="font-bold text-lg">Detail Pesanan</h3>
                <p class="text-xs text-[var(--color-kebab-text-muted)]">#{{ str_pad($trx->id, 5, '0', STR_PAD_LEFT) }} · {{ $trx->created_at->format('d M Y, H:i') }}</p>
            </div>
            <button onclick="document.getElementById('modal-{{ $trx->id }}').close()" class="text-gray-400 hover:text-white text-3xl font-bold leading-none p-2">&times;</button>
        </div>
        <ul class="space-y-3 mb-5 max-h-[50vh] overflow-y-auto pr-1">
            @foreach($trx->items as $item)
            <li class="flex justify-between items-center">
                <div>
                    <p class="font-bold text-sm text-white">{{ $item->menu->name ?? 'Menu Dihapus' }}</p>
                    <p class="text-xs text-[var(--color-kebab-text-muted)] mt-0.5">{{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                </div>
                <span class="font-black text-[var(--color-kebab-red)]">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</span>
            </li>
            @endforeach
        </ul>
        <div class="bg-[var(--color-kebab-dark)] p-4 rounded-xl border border-[var(--color-kebab-dark-hover)]">
            <div class="flex justify-between text-xs sm:text-sm mb-2">
                <span class="text-[var(--color-kebab-text-muted)]">Kanal: <span class="text-white font-bold ml-1">{{ $trx->channel }}</span></span>
                <span class="text-[var(--color-kebab-text-muted)]">Bayar: <span class="text-white font-bold ml-1">{{ $trx->payment_method }}</span></span>
            </div>
            <div class="flex justify-between items-center pt-2 border-t border-[var(--color-kebab-dark-hover)]">
                <span class="font-bold text-sm">TOTAL</span>
                <span class="font-black text-xl text-[var(--color-kebab-red)]">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</span>
            </div>
        </div>
        <div class="mt-4">
            <button onclick="printReceipt({{ $trx->id }})" class="w-full bg-green-500 hover:bg-green-600 text-white font-black py-3 rounded-xl transition-colors shadow-lg flex justify-center items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Struk
            </button>
        </div>
    </dialog>
    @endforeach
</div>

{{-- Receipt data embedded for JS --}}
<script>
const receipts = {
    @foreach($transactions as $trx)
    {{ $trx->id }}: {
        id: '{{ str_pad($trx->id, 5, '0', STR_PAD_LEFT) }}',
        date: '{{ $trx->created_at->format('d M Y, H:i') }}',
        channel: '{{ $trx->channel }}',
        payment: '{{ $trx->payment_method }}',
        total: '{{ number_format($trx->total_price, 0, ',', '.') }}',
        items: [
            @foreach($trx->items as $item)
            { name: '{{ addslashes($item->menu->name ?? 'Menu Dihapus') }}', qty: {{ $item->quantity }}, price: '{{ number_format($item->price, 0, ',', '.') }}', subtotal: '{{ number_format($item->quantity * $item->price, 0, ',', '.') }}' },
            @endforeach
        ]
    },
    @endforeach
};

function printReceipt(id) {
    const r = receipts[id];
    if (!r) return;

    const sep = '--------------------------------';
    let itemsHtml = r.items.map(i =>
        `<tr><td>${i.name}<br><small>${i.qty} x Rp ${i.price}</small></td><td style="text-align:right;white-space:nowrap">Rp ${i.subtotal}</td></tr>`
    ).join('');

    document.getElementById('print-content').innerHTML = `
        <div style="text-align:center;margin-bottom:8px">
            <div style="font-size:18px;font-weight:900;letter-spacing:1px">CHEFRAMA KEBAB</div>
            <div style="font-size:11px">Struk Pembelian</div>
            <div style="font-size:10px;margin-top:2px">${r.date}</div>
        </div>
        <div style="border-top:1px dashed #000;margin:6px 0"></div>
        <div style="font-size:10px;margin-bottom:4px">No: #${r.id} | Kanal: ${r.channel}</div>
        <div style="border-top:1px dashed #000;margin:4px 0"></div>
        <table style="width:100%;font-size:11px;border-collapse:collapse">${itemsHtml}</table>
        <div style="border-top:1px dashed #000;margin:6px 0"></div>
        <div style="display:flex;justify-content:space-between;font-weight:900;font-size:13px">
            <span>TOTAL</span><span>Rp ${r.total}</span>
        </div>
        <div style="font-size:10px;margin-top:4px">Bayar: ${r.payment}</div>
        <div style="border-top:1px dashed #000;margin:6px 0"></div>
        <div style="text-align:center;font-size:10px;margin-top:8px">
            Terima kasih atas pesanan Anda!<br>
            Semoga hari Anda menyenangkan :)
        </div>
    `;

    document.getElementById('global-print-area').classList.remove('hidden');
    window.print();
    document.getElementById('global-print-area').classList.add('hidden');
}
</script>

<style>
dialog::backdrop { background: rgba(0,0,0,0.75); backdrop-filter: blur(4px); }
</style>
@endsection
