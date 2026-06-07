@extends('layouts.app')

@section('title', 'Kasir (POS) - Cheframa Kebab')

@section('content')
<div class="flex flex-col sm:flex-row gap-2 lg:gap-4 sm:h-[calc(100vh-6rem)] min-h-screen sm:min-h-0" x-data="posSystem()">

    {{-- KIRI: Kolom Kategori --}}
    <div class="w-full sm:w-24 lg:w-44 shrink-0 flex sm:flex-col gap-2 overflow-x-auto sm:overflow-y-auto pb-2 sm:pb-0 pr-1 scrollbar-hide items-center sm:items-stretch">
        <p class="hidden lg:block text-[10px] uppercase font-bold tracking-widest text-[var(--color-kebab-text-muted)] px-1 mb-1">Kategori</p>
        
        <button @click="activeCategory = 'Semua'"
            :class="activeCategory === 'Semua'
                ? 'bg-[var(--color-kebab-red)] text-white shadow-[0_0_12px_rgba(230,57,70,0.4)] border-[var(--color-kebab-red)]'
                : 'bg-[var(--color-kebab-dark-card)] text-[var(--color-kebab-text-muted)] hover:text-white border-[var(--color-kebab-dark-hover)]'"
            class="whitespace-nowrap shrink-0 sm:w-full text-center sm:text-left px-4 sm:px-2 lg:px-4 py-2 lg:py-3 rounded-xl font-bold text-xs lg:text-sm transition-all border">
            Semua
        </button>

        @foreach($categories as $category)
        <button @click="activeCategory = '{{ $category }}'"
            :class="activeCategory === '{{ $category }}'
                ? 'bg-[var(--color-kebab-red)] text-white shadow-[0_0_12px_rgba(230,57,70,0.4)] border-[var(--color-kebab-red)]'
                : 'bg-[var(--color-kebab-dark-card)] text-[var(--color-kebab-text-muted)] hover:text-white border-[var(--color-kebab-dark-hover)]'"
            class="whitespace-nowrap shrink-0 sm:w-full text-center sm:text-left px-4 sm:px-2 lg:px-4 py-2 lg:py-3 rounded-xl font-bold text-xs lg:text-sm transition-all border">
            <span class="sm:hidden lg:inline">{{ $category }}</span>
            <span class="hidden sm:inline lg:hidden">{{ strtok($category, " ") }}</span>
        </button>
        @endforeach
    </div>

    {{-- TENGAH: Grid Menu --}}
    <div class="flex-1 flex flex-col min-h-[50vh] sm:min-h-0 overflow-hidden">
        <div class="mb-3 shrink-0 flex flex-col xl:flex-row xl:items-center justify-between gap-2">
            <div>
                <h2 class="text-lg lg:text-xl font-bold leading-tight" x-text="activeCategory === 'Semua' ? 'Semua Menu' : activeCategory"></h2>
                <p class="text-[10px] lg:text-xs text-[var(--color-kebab-text-muted)] hidden lg:block">Ketuk untuk menambah ke keranjang.</p>
            </div>
            <input type="text" x-model="searchQuery" placeholder="Cari menu..." 
                class="bg-[var(--color-kebab-dark-card)] border border-[var(--color-kebab-dark-hover)] rounded-xl px-3 py-1.5 lg:px-4 lg:py-2 text-sm w-full xl:w-48 focus:outline-none focus:border-[var(--color-kebab-red)]">
        </div>

        <div class="flex-1 overflow-y-auto scrollbar-hide pb-4">
            {{-- Low Stock Alerts --}}
            <template x-if="lowStockIngredients.length > 0">
                <div class="bg-amber-500/10 border border-amber-500/30 text-amber-400 px-4 py-3 rounded-xl mb-4 text-xs font-medium flex items-center gap-3">
                    <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <span class="font-bold">Peringatan Stok:</span> Beberapa bahan baku menipis: 
                        <span class="font-bold text-white" x-text="lowStockIngredients.map(i => i.name + ' (' + Number(i.stock) + ' ' + i.unit + ')').join(', ')"></span>.
                    </div>
                </div>
            </template>

            <div class="grid gap-2 lg:gap-4 pr-1 grid-cols-[repeat(auto-fill,minmax(130px,1fr))] lg:grid-cols-[repeat(auto-fill,minmax(160px,1fr))]">
                @foreach($menus as $menu)
                <button
                    x-show="(activeCategory === 'Semua' || activeCategory === '{{ $menu->category?->name }}') && matchesSearch('{{ addslashes(strtolower($menu->name)) }}')"
                    @click="addToCart({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }})"
                    class="bg-gradient-to-br from-[var(--color-kebab-dark-card)] to-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-2xl p-2.5 lg:p-4 flex flex-col items-start text-left hover:border-[var(--color-kebab-red)] hover:shadow-[0_0_15px_rgba(230,57,70,0.2)] hover:-translate-y-1 transition-all active:scale-95 h-24 lg:h-28 relative group overflow-hidden">

                    {{-- Decorative bg icon --}}
                    <div class="absolute -right-3 -bottom-3 opacity-[0.04] group-hover:opacity-10 transition-opacity">
                        <svg class="w-10 h-10 lg:w-16 lg:h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>

                    {{-- Badge qty --}}
                    <div x-show="getItemQuantity({{ $menu->id }}) > 0" x-cloak
                        class="absolute top-1 right-1 lg:top-2 lg:right-2 bg-[var(--color-kebab-red)] text-white w-5 h-5 lg:w-6 lg:h-6 rounded-full flex items-center justify-center font-black text-[10px] lg:text-xs shadow-[0_0_10px_rgba(230,57,70,0.7)] z-10"
                        x-text="getItemQuantity({{ $menu->id }})">
                    </div>

                    <h3 class="font-bold text-[11px] lg:text-sm text-white leading-tight line-clamp-2 pr-4 z-10">{{ $menu->name }}</h3>
                    <p class="text-[var(--color-kebab-red)] font-black text-[13px] lg:text-lg mt-auto z-10">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- KANAN: Keranjang --}}
    <div class="w-full sm:w-60 lg:w-80 shrink-0 bg-[var(--color-kebab-dark-card)] rounded-2xl border border-[var(--color-kebab-dark-hover)] flex flex-col shadow-2xl h-[500px] sm:h-auto">
        {{-- Header --}}
        <div class="p-3 lg:p-4 border-b border-[var(--color-kebab-dark-hover)] flex justify-between items-center shrink-0">
            <h2 class="font-bold text-sm lg:text-base flex items-center">
                <svg class="w-5 h-5 mr-2 text-[var(--color-kebab-red)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                Keranjang
            </h2>
            <button @click="cart = []" x-show="cart.length > 0" x-cloak
                class="text-xs text-red-500 hover:text-red-400 transition-colors font-bold">Kosongkan</button>
        </div>

        {{-- Cart Items --}}
        <div class="flex-1 overflow-y-auto p-3 space-y-2 scrollbar-hide">
            <template x-if="cart.length === 0">
                <div class="h-full flex flex-col items-center justify-center text-center opacity-40 py-10">
                    <div class="w-16 h-16 bg-[var(--color-kebab-dark)] rounded-full flex items-center justify-center mb-3">
                        <svg class="w-8 h-8 text-[var(--color-kebab-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <p class="text-sm font-medium">Keranjang kosong</p>
                    <p class="text-xs mt-1">Pilih menu di sebelah kiri</p>
                </div>
            </template>

            <template x-for="(item, index) in cart" :key="item.id">
                <div class="flex items-center bg-[var(--color-kebab-dark)] p-3 rounded-xl border border-[var(--color-kebab-dark-hover)]">
                    <div class="flex-1 overflow-hidden pr-2">
                        <h4 class="font-bold text-sm text-white leading-tight" x-text="item.name"></h4>
                        <p class="text-xs text-[var(--color-kebab-red)] font-bold mt-0.5" x-text="formatRupiah(item.price)"></p>
                    </div>
                    <div class="flex items-center bg-[var(--color-kebab-dark-card)] rounded-lg border border-[var(--color-kebab-dark-hover)] shrink-0">
                        <button @click="decreaseQuantity(index)" class="w-8 h-8 flex items-center justify-center rounded-l-lg hover:bg-red-500/20 hover:text-red-400 text-[var(--color-kebab-text-muted)] transition-colors font-bold text-base">−</button>
                        <span class="w-7 text-center font-black text-sm text-white" x-text="item.quantity"></span>
                        <button @click="increaseQuantity(index)" class="w-8 h-8 flex items-center justify-center rounded-r-lg hover:bg-green-500/20 hover:text-green-400 text-[var(--color-kebab-text-muted)] transition-colors font-bold text-base">+</button>
                    </div>
                </div>
            </template>
        </div>

        {{-- Order Config + Total + Button --}}
        <div class="p-4 border-t border-[var(--color-kebab-dark-hover)] shrink-0 space-y-3">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] uppercase font-bold text-[var(--color-kebab-text-muted)] mb-1 tracking-wider">Kanal</label>
                    <select x-model="channel" class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-lg p-2 text-xs focus:outline-none focus:border-[var(--color-kebab-red)] cursor-pointer">
                        <option value="Langsung">Langsung (Booth)</option>
                        <option value="WhatsApp">WhatsApp</option>
                        <option value="GoFood">GoFood</option>
                        <option value="GrabFood">GrabFood</option>
                        <option value="ShopeeFood">ShopeeFood</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-[var(--color-kebab-text-muted)] mb-1 tracking-wider">Bayar</label>
                    <select x-model="paymentMethod" class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-lg p-2 text-xs focus:outline-none focus:border-[var(--color-kebab-red)] cursor-pointer">
                        <option value="Tunai">Tunai / Cash</option>
                        <option value="QRIS">QRIS</option>
                        <option value="Transfer">Transfer Bank</option>
                        <option value="GoFood">Saldo GoFood</option>
                        <option value="ShopeeFood">Saldo Shopee</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-between items-center px-1">
                <span class="text-sm text-[var(--color-kebab-text-muted)] font-medium">Total</span>
                <span class="text-2xl font-black text-white" x-text="formatRupiah(totalPrice)"></span>
            </div>

            <button @click="confirmOrder()" :disabled="cart.length === 0 || isSubmitting"
                class="w-full bg-[var(--color-kebab-red)] hover:bg-[var(--color-kebab-red-dark)] disabled:opacity-40 disabled:cursor-not-allowed text-white font-black py-3.5 rounded-xl transition-all shadow-[0_0_15px_rgba(230,57,70,0.35)] hover:shadow-[0_0_20px_rgba(230,57,70,0.55)] flex items-center justify-center text-base">
                <template x-if="!isSubmitting">
                    <span>Konfirmasi Pesanan →</span>
                </template>
                <template x-if="isSubmitting">
                    <span class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
                        Memproses...
                    </span>
                </template>
            </button>
        </div>
    </div>

    {{-- Modal Konfirmasi Pesanan --}}
    <div x-show="isConfirming" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-[var(--color-kebab-dark-card)] p-6 rounded-2xl border border-[var(--color-kebab-dark-hover)] shadow-2xl w-full max-w-md mx-4" @click.away="isConfirming = false">
            <h3 class="text-xl font-black mb-1 text-center">Tinjau Pesanan</h3>
            <p class="text-xs text-center text-[var(--color-kebab-text-muted)] mb-5">Pastikan pesanan sudah benar sebelum diproses.</p>

            <div class="space-y-3 mb-4 max-h-[40vh] overflow-y-auto pr-1">
                <template x-for="item in cart" :key="item.id">
                    <div class="flex justify-between items-center border-b border-[var(--color-kebab-dark-hover)] pb-3">
                        <div>
                            <p class="font-bold text-sm text-white" x-text="item.name"></p>
                            <p class="text-xs text-[var(--color-kebab-text-muted)] mt-0.5" x-text="item.quantity + ' × ' + formatRupiah(item.price)"></p>
                        </div>
                        <p class="font-black text-[var(--color-kebab-red)] text-sm" x-text="formatRupiah(item.price * item.quantity)"></p>
                    </div>
                </template>
            </div>

            <div class="bg-[var(--color-kebab-dark)] p-4 rounded-xl border border-[var(--color-kebab-dark-hover)] mb-5 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-[var(--color-kebab-text-muted)]">Kanal</span>
                    <span class="font-bold text-white" x-text="channel"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-[var(--color-kebab-text-muted)]">Pembayaran</span>
                    <span class="font-bold text-white" x-text="paymentMethod"></span>
                </div>
                <div class="flex justify-between text-lg font-black border-t border-[var(--color-kebab-dark-hover)] pt-2 mt-1">
                    <span>Total</span>
                    <span class="text-[var(--color-kebab-red)]" x-text="formatRupiah(totalPrice)"></span>
                </div>
            </div>

            <div class="flex gap-3">
                <button @click="isConfirming = false" class="flex-1 bg-[var(--color-kebab-dark)] hover:bg-[var(--color-kebab-dark-hover)] border border-[var(--color-kebab-dark-hover)] text-white font-bold py-3 rounded-xl transition-colors">Batal</button>
                <button @click="processOrder()" class="flex-1 bg-[var(--color-kebab-red)] hover:bg-[var(--color-kebab-red-dark)] text-white font-black py-3 rounded-xl transition-colors shadow-lg">Proses Sekarang</button>
            </div>
        </div>
    </div>

    {{-- Modal Cetak Struk Setelah Transaksi --}}
    <div x-show="showPrintModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-[var(--color-kebab-dark-card)] p-6 rounded-2xl border border-[var(--color-kebab-dark-hover)] shadow-2xl w-full max-w-sm mx-4">
            <div class="text-center mb-5">
                <div class="w-16 h-16 bg-green-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="text-xl font-black text-white mb-1">Pesanan Berhasil!</h3>
                <p class="text-sm text-[var(--color-kebab-text-muted)]">Transaksi telah tersimpan ke sistem.</p>
            </div>
            <div class="flex gap-3">
                <button @click="showPrintModal = false"
                    class="flex-1 bg-[var(--color-kebab-dark)] hover:bg-[var(--color-kebab-dark-hover)] border border-[var(--color-kebab-dark-hover)] text-white font-bold py-3 rounded-xl transition-colors text-sm">
                    Lewati
                </button>
                <button @click="showPrintModal = false; printReceipt(lastOrder)"
                    class="flex-1 bg-green-500 hover:bg-green-600 text-white font-black py-3 rounded-xl transition-colors shadow-lg flex items-center justify-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak Struk
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<script>
function posSystem() {
    return {
        cart: [],
        activeCategory: 'Semua',
        channel: 'Langsung',
        paymentMethod: 'Tunai',
        isSubmitting: false,
        isConfirming: false,
        showPrintModal: false,
        searchQuery: '',
        lastOrder: null,
        lowStockIngredients: [],

        init() {
            this.fetchLowStockAlerts();
        },

        async fetchLowStockAlerts() {
            try {
                const res = await fetch('/management/stock/low-alerts');
                const data = await res.json();
                this.lowStockIngredients = data.items || [];
            } catch (e) {
                console.error('Failed to fetch stock alerts:', e);
            }
        },

        get totalPrice() {
            return this.cart.reduce((t, i) => t + (i.price * i.quantity), 0);
        },

        getItemQuantity(id) {
            const item = this.cart.find(i => i.id === id);
            return item ? item.quantity : 0;
        },

        matchesSearch(name) {
            return this.searchQuery.trim() === '' || name.includes(this.searchQuery.toLowerCase());
        },

        addToCart(id, name, price) {
            const idx = this.cart.findIndex(i => i.id === id);
            if (idx > -1) this.cart[idx].quantity++;
            else this.cart.push({ id, name, price, quantity: 1 });
        },

        increaseQuantity(i) { this.cart[i].quantity++; },
        decreaseQuantity(i) {
            if (this.cart[i].quantity > 1) this.cart[i].quantity--;
            else this.cart.splice(i, 1);
        },

        formatRupiah(n) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);
        },

        confirmOrder() {
            if (this.cart.length === 0) return;
            this.isConfirming = true;
        },

        async processOrder() {
            this.isConfirming = false;
            this.isSubmitting = true;
            try {
                const res = await fetch('/transactions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ channel: this.channel, payment_method: this.paymentMethod, cart: this.cart })
                });
                const data = await res.json();
                if (data.success) {
                    // Simpan data order terakhir untuk print struk
                    this.lastOrder = {
                        id: data.transaction_id,
                        channel: this.channel,
                        payment: this.paymentMethod,
                        total: this.totalPrice,
                        items: [...this.cart],
                        date: new Date().toLocaleString('id-ID')
                    };
                    this.cart = [];
                    this.channel = 'Langsung';
                    this.paymentMethod = 'Tunai';
                    showToast('✅ Pesanan berhasil disimpan!');
                    this.showPrintModal = true;
                    this.fetchLowStockAlerts();
                } else {
                    showToast('❌ Gagal: ' + data.message, 'error');
                }
            } catch (e) {
                showToast('❌ Terjadi kesalahan sistem.', 'error');
            } finally {
                this.isSubmitting = false;
            }
        },

        printReceipt(order) {
            const sep = '--------------------------------';
            const itemsHtml = order.items.map(i =>
                `<tr><td>${i.name}<br><small>${i.quantity} x Rp ${i.price.toLocaleString('id-ID')}</small></td><td style="text-align:right;white-space:nowrap">Rp ${(i.price*i.quantity).toLocaleString('id-ID')}</td></tr>`
            ).join('');

            const id = String(order.id).padStart(5, '0');
            const html = `
                <div style="text-align:center;margin-bottom:8px">
                    <div style="font-size:18px;font-weight:900;letter-spacing:1px">CHEFRAMA KEBAB</div>
                    <div style="font-size:11px">Struk Pembelian</div>
                    <div style="font-size:10px;margin-top:2px">${order.date}</div>
                </div>
                <div style="border-top:1px dashed #000;margin:6px 0"></div>
                <div style="font-size:10px;margin-bottom:4px">No: #${id} | Kanal: ${order.channel}</div>
                <div style="border-top:1px dashed #000;margin:4px 0"></div>
                <table style="width:100%;font-size:11px;border-collapse:collapse">${itemsHtml}</table>
                <div style="border-top:1px dashed #000;margin:6px 0"></div>
                <div style="display:flex;justify-content:space-between;font-weight:900;font-size:13px">
                    <span>TOTAL</span><span>Rp ${order.total.toLocaleString('id-ID')}</span>
                </div>
                <div style="font-size:10px;margin-top:4px">Bayar: ${order.payment}</div>
                <div style="border-top:1px dashed #000;margin:6px 0"></div>
                <div style="text-align:center;font-size:10px;margin-top:8px">
                    Terima kasih atas pesanan Anda!<br>Semoga hari Anda menyenangkan :)
                </div>
            `;

            const printArea = document.getElementById('global-print-area');
            document.getElementById('print-content').innerHTML = html;
            printArea.classList.remove('hidden');
            window.print();
            printArea.classList.add('hidden');
        }
    }
}
</script>
@endsection
