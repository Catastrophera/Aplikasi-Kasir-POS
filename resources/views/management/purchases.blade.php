@extends('layouts.app')
@section('title', 'Pembelian dari Supplier - Cheframa Kebab')

@section('content')
<div x-data="{ 
    showModal: false, 
    items: [{ name: '', qty: 1, price: 0 }],
    calculateTotal() {
        return this.items.reduce((sum, item) => sum + (item.qty * item.price), 0);
    }
}">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-bold">Pembelian dari Supplier</h2>
            <p class="text-[var(--color-kebab-text-muted)] mt-1">Catat belanja stok dan bahan baku.</p>
        </div>
        <button @click="showModal = true" class="bg-[var(--color-kebab-red)] hover:bg-[var(--color-kebab-red-dark)] text-white px-4 py-2.5 rounded-xl font-bold transition-all shadow-lg text-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Catat Pembelian
        </button>
    </div>

    @if(session('success'))
    <div class="bg-green-500/20 text-green-400 p-4 rounded-xl mb-6 border border-green-500/40 font-medium">{{ session('success') }}</div>
    @endif

    <div class="bg-[var(--color-kebab-dark-card)] rounded-2xl border border-[var(--color-kebab-dark-hover)] overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-[var(--color-kebab-dark)] text-xs text-[var(--color-kebab-text-muted)] uppercase tracking-wider">
                    <th class="p-4">Tanggal</th>
                    <th class="p-4">Supplier</th>
                    <th class="p-4">Total</th>
                    <th class="p-4">Catatan</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--color-kebab-dark-hover)]">
                @forelse($purchases as $purchase)
                <tr class="hover:bg-[var(--color-kebab-dark-hover)] transition-colors">
                    <td class="p-4 text-sm font-bold text-white">{{ $purchase->date->format('d M Y') }}</td>
                    <td class="p-4 text-sm text-white">{{ $purchase->contact->name ?? 'Umum' }}</td>
                    <td class="p-4 font-black text-[var(--color-kebab-red)]">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
                    <td class="p-4 text-xs text-[var(--color-kebab-text-muted)]">{{ $purchase->notes ?: '-' }}</td>
                    <td class="p-4 text-right">
                        <form action="/management/purchases/{{ $purchase->id }}" method="POST" onsubmit="return confirm('Hapus data pembelian ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-300 text-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-12 text-center text-[var(--color-kebab-text-muted)]">Belum ada data pembelian.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Tambah Pembelian --}}
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-[var(--color-kebab-dark-card)] p-6 rounded-2xl border border-[var(--color-kebab-dark-hover)] shadow-2xl w-full max-w-2xl mx-4" @click.away="showModal = false">
            <h3 class="text-xl font-bold mb-4 border-b border-[var(--color-kebab-dark-hover)] pb-3">Catat Pembelian Baru</h3>
            <form action="/management/purchases" method="POST">
                @csrf
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase mb-1">Tanggal</label>
                        <input type="date" name="date" required value="{{ today()->toDateString() }}" class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase mb-1">Supplier</label>
                        <select name="contact_id" class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white outline-none">
                            <option value="">Pilih Supplier (Opsional)</option>
                            @foreach($suppliers as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase mb-2">Item Barang</label>
                    <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="flex gap-2 items-center">
                                <input type="text" :name="'items['+index+'][name]'" required placeholder="Nama barang" x-model="item.name" class="flex-1 bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-lg p-2 text-sm text-white">
                                <input type="number" :name="'items['+index+'][qty]'" required min="1" placeholder="Qty" x-model="item.qty" class="w-16 bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-lg p-2 text-sm text-white text-center">
                                <input type="number" :name="'items['+index+'][price]'" required min="0" placeholder="Harga" x-model="item.price" class="w-28 bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-lg p-2 text-sm text-white">
                                <button type="button" @click="items.length > 1 ? items.splice(index, 1) : null" class="text-red-400">✕</button>
                            </div>
                        </template>
                    </div>
                    <button type="button" @click="items.push({ name: '', qty: 1, price: 0 })" class="mt-2 text-xs font-bold text-[var(--color-kebab-red)]">+ Tambah Baris</button>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase mb-1">Total Pembelian</label>
                    <input type="number" name="total_amount" readonly :value="calculateTotal()" class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-xl font-black text-[var(--color-kebab-red)] outline-none">
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase mb-1">Catatan</label>
                    <textarea name="notes" rows="2" class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white outline-none"></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" @click="showModal = false" class="flex-1 bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] text-white font-bold py-2.5 rounded-xl">Batal</button>
                    <button type="submit" class="flex-1 bg-[var(--color-kebab-red)] hover:bg-[var(--color-kebab-red-dark)] text-white font-bold py-2.5 rounded-xl transition-colors shadow-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
