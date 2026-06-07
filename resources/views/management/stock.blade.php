@extends('layouts.app')
@section('title', 'Manajemen Stok - Cheframa Kebab')

@section('content')
<div x-data="{ 
    activeTab: 'materials',
    showAddMaterialModal: false,
    editMaterial: null,
    activeMenuId: {{ $menus->first()?->id ?? 'null' }},
    activeMenuName: '{{ $menus->first()?->name ?? '' }}',
    activeMenuRecipes: [],

    initRecipes() {
        // Map menus recipes into a JS lookup
        this.recipesLookup = {
            @foreach($menus as $menu)
            {{ $menu->id }}: [
                @foreach($menu->recipes as $r)
                {
                    id: {{ $r->id }},
                    raw_material_id: {{ $r->raw_material_id }},
                    name: '{{ addslashes($r->rawMaterial?->name) }}',
                    quantity: {{ $r->quantity }},
                    unit: '{{ $r->rawMaterial?->unit }}'
                },
                @endforeach
            ],
            @endforeach
        };
        this.updateActiveRecipes();
    },

    selectMenu(id, name) {
        this.activeMenuId = id;
        this.activeMenuName = name;
        this.updateActiveRecipes();
    },

    updateActiveRecipes() {
        if (this.activeMenuId && this.recipesLookup[this.activeMenuId]) {
            this.activeMenuRecipes = this.recipesLookup[this.activeMenuId];
        } else {
            this.activeMenuRecipes = [];
        }
    }
}" x-init="initRecipes()">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h2 class="text-3xl font-bold">Manajemen Stok & Resep</h2>
            <p class="text-[var(--color-kebab-text-muted)] mt-1">Pantau stok bahan baku dan konfigurasi porsi per menu.</p>
        </div>
        <div class="flex bg-[var(--color-kebab-dark-card)] p-1 rounded-xl border border-[var(--color-kebab-dark-hover)] self-stretch md:self-auto">
            <button @click="activeTab = 'materials'" 
                :class="activeTab === 'materials' ? 'bg-[var(--color-kebab-red)] text-white' : 'text-[var(--color-kebab-text-muted)] hover:text-white'"
                class="flex-1 md:flex-none px-4 py-2 rounded-lg font-bold text-sm transition-all">
                Stok Bahan Baku
            </button>
            <button @click="activeTab = 'recipes'" 
                :class="activeTab === 'recipes' ? 'bg-[var(--color-kebab-red)] text-white' : 'text-[var(--color-kebab-text-muted)] hover:text-white'"
                class="flex-1 md:flex-none px-4 py-2 rounded-lg font-bold text-sm transition-all">
                Resep Menu
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-500/20 text-green-400 p-4 rounded-xl mb-6 border border-green-500/40 font-medium">{{ session('success') }}</div>
    @endif

    {{-- TAB 1: STOK BAHAN BAKU --}}
    <div x-show="activeTab === 'materials'" class="space-y-4" x-cloak>
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-bold text-white">Daftar Bahan Baku</h3>
            <button @click="showAddMaterialModal = true" class="bg-[var(--color-kebab-red)] hover:bg-[var(--color-kebab-red-dark)] text-white px-4 py-2 rounded-xl font-bold transition-all shadow-lg text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Bahan Baku
            </button>
        </div>

        <div class="bg-[var(--color-kebab-dark-card)] rounded-2xl border border-[var(--color-kebab-dark-hover)] overflow-hidden shadow-xl">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[var(--color-kebab-dark)] text-xs text-[var(--color-kebab-text-muted)] uppercase tracking-wider">
                        <th class="p-4">Nama Bahan</th>
                        <th class="p-4">Stok Saat Ini</th>
                        <th class="p-4">Batas Minimum</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-kebab-dark-hover)]">
                    @forelse($rawMaterials as $rm)
                    @php $isLow = $rm->stock <= $rm->min_stock; @endphp
                    <tr class="hover:bg-[var(--color-kebab-dark-hover)] transition-colors {{ $isLow ? 'bg-amber-500/5' : '' }}">
                        <td class="p-4 text-sm font-bold text-white">
                            <div class="flex items-center gap-2">
                                @if($isLow)
                                <span class="w-2 h-2 rounded-full bg-amber-500 shadow-[0_0_8px_#f59e0b]"></span>
                                @else
                                <span class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_#10b981]"></span>
                                @endif
                                {{ $rm->name }}
                            </div>
                        </td>
                        <td class="p-4 text-sm font-black {{ $isLow ? 'text-amber-400' : 'text-white' }}">
                            {{ number_format($rm->stock, 2, ',', '.') }} {{ $rm->unit }}
                        </td>
                        <td class="p-4 text-sm text-[var(--color-kebab-text-muted)]">
                            {{ number_format($rm->min_stock, 2, ',', '.') }} {{ $rm->unit }}
                        </td>
                        <td class="p-4">
                            @if($isLow)
                            <span class="bg-amber-500/20 text-amber-400 border border-amber-500/40 px-2.5 py-0.5 rounded-full text-xs font-bold">Stok Menipis</span>
                            @else
                            <span class="bg-green-500/20 text-green-400 border border-green-500/40 px-2.5 py-0.5 rounded-full text-xs font-bold">Aman</span>
                            @endif
                        </td>
                        <td class="p-4 text-right flex justify-end gap-3">
                            <button @click="editMaterial = {{ $rm->toJson() }}" class="text-blue-400 hover:text-blue-300 text-sm font-bold">Edit</button>
                            <form action="/management/stock/raw-materials/{{ $rm->id }}" method="POST" onsubmit="return confirm('Hapus bahan baku ini? Resep yang menggunakannya juga akan terpengaruh.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 text-sm font-bold">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center text-[var(--color-kebab-text-muted)]">Belum ada bahan baku terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- TAB 2: RESEP MENU --}}
    <div x-show="activeTab === 'recipes'" class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-cloak>
        {{-- Kolom Kiri: Daftar Menu --}}
        <div class="lg:col-span-1 bg-[var(--color-kebab-dark-card)] rounded-2xl border border-[var(--color-kebab-dark-hover)] p-4 shadow-xl flex flex-col h-[550px]">
            <h3 class="text-lg font-bold mb-3">Pilih Menu</h3>
            <div class="flex-1 overflow-y-auto pr-1 space-y-1 scrollbar-hide">
                @foreach($menus as $menu)
                <button @click="selectMenu({{ $menu->id }}, '{{ addslashes($menu->name) }}')"
                    :class="activeMenuId === {{ $menu->id }} ? 'bg-[var(--color-kebab-red)]/15 text-[var(--color-kebab-red)] border-[var(--color-kebab-red)]' : 'bg-[var(--color-kebab-dark)] text-white hover:bg-[var(--color-kebab-dark-hover)] border-[var(--color-kebab-dark-hover)]'"
                    class="w-full text-left p-3 rounded-xl border font-bold text-sm transition-all flex justify-between items-center">
                    <span>{{ $menu->name }}</span>
                    <span class="text-xs font-normal text-[var(--color-kebab-text-muted)]">
                        ({{ $menu->recipes->count() }} bahan)
                    </span>
                </button>
                @endforeach
            </div>
        </div>

        {{-- Kolom Kanan: Resep Menu Terpilih --}}
        <div class="lg:col-span-2 bg-[var(--color-kebab-dark-card)] rounded-2xl border border-[var(--color-kebab-dark-hover)] p-6 shadow-xl flex flex-col h-[550px]">
            <template x-if="activeMenuId === null">
                <div class="flex-1 flex items-center justify-center text-center opacity-40">
                    <p>Silakan pilih menu di kolom sebelah kiri.</p>
                </div>
            </template>

            <template x-if="activeMenuId !== null">
                <div class="flex flex-col h-full">
                    <div class="border-b border-[var(--color-kebab-dark-hover)] pb-4 mb-4 flex justify-between items-center shrink-0">
                        <div>
                            <h3 class="text-xl font-black text-white" x-text="activeMenuName"></h3>
                            <p class="text-xs text-[var(--color-kebab-text-muted)] mt-0.5">Konfigurasi takaran bahan untuk menu ini.</p>
                        </div>
                    </div>

                    {{-- List of mapped ingredients --}}
                    <div class="flex-1 overflow-y-auto pr-1 mb-4 space-y-2">
                        <template x-if="activeMenuRecipes.length === 0">
                            <div class="h-full flex items-center justify-center text-center py-12">
                                <p class="text-sm text-[var(--color-kebab-text-muted)]">Resep untuk menu ini belum dikonfigurasi.<br>Silakan tambahkan bahan di bawah.</p>
                            </div>
                        </template>

                        <template x-for="recipe in activeMenuRecipes" :key="recipe.id">
                            <div class="flex items-center justify-between bg-[var(--color-kebab-dark)] p-4 rounded-xl border border-[var(--color-kebab-dark-hover)]">
                                <div>
                                    <h4 class="font-bold text-white text-sm" x-text="recipe.name"></h4>
                                    <p class="text-xs text-[var(--color-kebab-text-muted)] mt-0.5">Takaran konsumsi: <span class="font-bold text-[var(--color-kebab-red)]" x-text="Number(recipe.quantity) + ' ' + recipe.unit"></span> per porsi</p>
                                </div>
                                <form :action="'/management/stock/recipes/' + recipe.id" method="POST" onsubmit="return confirm('Hapus bahan ini dari resep menu?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 text-sm font-bold">✕ Hapus</button>
                                </form>
                            </div>
                        </template>
                    </div>

                    {{-- Form Add / Update Recipe Ingredient --}}
                    <div class="border-t border-[var(--color-kebab-dark-hover)] pt-4 shrink-0">
                        <h4 class="font-bold text-sm text-white mb-3">Tambah atau Update Bahan Resep</h4>
                        <form action="/management/stock/recipes" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                            @csrf
                            <input type="hidden" name="menu_id" :value="activeMenuId">
                            
                            <div>
                                <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase mb-1">Bahan Baku</label>
                                <select name="raw_material_id" required class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white text-sm focus:outline-none focus:border-[var(--color-kebab-red)]">
                                    <option value="">Pilih bahan...</option>
                                    @foreach($rawMaterials as $rm)
                                    <option value="{{ $rm->id }}">{{ $rm->name }} ({{ $rm->unit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase mb-1">Takaran / Jumlah</label>
                                <input type="number" name="quantity" step="0.01" min="0.01" required placeholder="Misal: 50 atau 1" class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white text-sm focus:outline-none focus:border-[var(--color-kebab-red)]">
                            </div>

                            <button type="submit" class="bg-[var(--color-kebab-red)] hover:bg-[var(--color-kebab-red-dark)] text-white font-bold p-3 rounded-xl transition-colors shadow-lg text-sm">
                                Simpan ke Resep
                            </button>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- MODAL: Tambah Bahan Baku --}}
    <div x-show="showAddMaterialModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-[var(--color-kebab-dark-card)] p-6 rounded-2xl border border-[var(--color-kebab-dark-hover)] shadow-2xl w-full max-w-md mx-4" @click.away="showAddMaterialModal = false">
            <h3 class="text-xl font-bold mb-4 border-b border-[var(--color-kebab-dark-hover)] pb-3">Tambah Bahan Baku Baru</h3>
            <form action="/management/stock/raw-materials" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase mb-1">Nama Bahan</label>
                    <input type="text" name="name" required class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white focus:outline-none focus:border-[var(--color-kebab-red)]" placeholder="Misal: Daging Sapi">
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase mb-1">Stok Awal</label>
                        <input type="number" name="stock" step="0.01" required min="0" class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white focus:outline-none focus:border-[var(--color-kebab-red)]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase mb-1">Satuan</label>
                        <input type="text" name="unit" required placeholder="Misal: gr, ml, pcs" class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white focus:outline-none focus:border-[var(--color-kebab-red)]">
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase mb-1">Batas Minimum (Alert)</label>
                    <input type="number" name="min_stock" step="0.01" required min="0" class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white focus:outline-none focus:border-[var(--color-kebab-red)]" placeholder="Batas stok menipis">
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="showAddMaterialModal = false" class="flex-1 bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] text-white font-bold py-2.5 rounded-xl">Batal</button>
                    <button type="submit" class="flex-1 bg-[var(--color-kebab-red)] hover:bg-[var(--color-kebab-red-dark)] text-white font-bold py-2.5 rounded-xl shadow-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: Edit Bahan Baku --}}
    <div x-show="editMaterial !== null" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-[var(--color-kebab-dark-card)] p-6 rounded-2xl border border-[var(--color-kebab-dark-hover)] shadow-2xl w-full max-w-md mx-4" @click.away="editMaterial = null">
            <h3 class="text-xl font-bold mb-4 border-b border-[var(--color-kebab-dark-hover)] pb-3">Edit Bahan Baku</h3>
            <form :action="'/management/stock/raw-materials/' + (editMaterial?.id || '')" method="POST">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase mb-1">Nama Bahan</label>
                    <input type="text" name="name" :value="editMaterial?.name" required class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white focus:outline-none focus:border-[var(--color-kebab-red)]">
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase mb-1">Stok Saat Ini</label>
                        <input type="number" name="stock" step="0.01" :value="editMaterial?.stock" required min="0" class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white focus:outline-none focus:border-[var(--color-kebab-red)]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase mb-1">Satuan</label>
                        <input type="text" name="unit" :value="editMaterial?.unit" required class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white focus:outline-none focus:border-[var(--color-kebab-red)]">
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase mb-1">Batas Minimum (Alert)</label>
                    <input type="number" name="min_stock" step="0.01" :value="editMaterial?.min_stock" required min="0" class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white focus:outline-none focus:border-[var(--color-kebab-red)]">
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="editMaterial = null" class="flex-1 bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] text-white font-bold py-2.5 rounded-xl">Batal</button>
                    <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2.5 rounded-xl shadow-lg">Perbarui</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
