@extends('layouts.app')

@section('title', 'Manajemen Menu - Cheframa Kebab')

@section('content')
<div x-data="{ showAddMenuModal: false, editMenu: null, showCatModal: false }">

    {{-- Header --}}
    <div class="mb-6 flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
        <div>
            <h2 class="text-3xl font-bold">Manajemen Menu</h2>
            <p class="text-[var(--color-kebab-text-muted)] mt-1">Kelola daftar menu, harga, dan kategori.</p>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            <button @click="showCatModal = true"
                class="flex-1 sm:flex-none justify-center bg-[var(--color-kebab-dark-card)] border border-[var(--color-kebab-dark-hover)] hover:border-[var(--color-kebab-red)] text-white px-3 lg:px-4 py-2.5 rounded-xl font-bold flex items-center transition-colors text-sm lg:text-base">
                <svg class="w-5 h-5 lg:mr-2 text-[var(--color-kebab-red)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                <span class="hidden lg:inline">Kelola Kategori</span>
                <span class="lg:hidden ml-2">Kategori</span>
            </button>
            <button @click="showAddMenuModal = true"
                class="flex-1 sm:flex-none justify-center bg-[var(--color-kebab-red)] hover:bg-[var(--color-kebab-red-dark)] text-white px-3 lg:px-4 py-2.5 rounded-xl font-bold flex items-center transition-colors shadow-lg text-sm lg:text-base">
                <svg class="w-5 h-5 lg:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span class="hidden lg:inline">Tambah Menu</span>
                <span class="lg:hidden ml-2">Tambah</span>
            </button>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="bg-green-500/20 text-green-400 p-4 rounded-xl mb-6 border border-green-500/40 font-medium">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-red-500/20 text-red-400 p-4 rounded-xl mb-6 border border-red-500/40 font-medium">{{ session('error') }}</div>
    @endif
    @if($errors->any())
    <div class="bg-red-500/20 text-red-400 p-4 rounded-xl mb-6 border border-red-500/40">
        <ul class="list-disc pl-5 space-y-1 text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Menu Cards grouped by category --}}
    @foreach($menus->groupBy(fn($m) => $m->category?->name ?? 'Umum') as $cat => $items)
    <div class="mb-8">
        <h3 class="text-xl font-bold mb-4 flex items-center">
            <span class="w-8 h-8 bg-[var(--color-kebab-red)] text-white flex items-center justify-center rounded-lg mr-3 shadow-lg text-sm font-black">{{ $loop->iteration }}</span>
            {{ $cat }}
            <span class="ml-3 text-sm font-normal text-[var(--color-kebab-text-muted)]">({{ $items->count() }} menu)</span>
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($items as $menu)
            <div class="bg-gradient-to-br from-[var(--color-kebab-dark)] to-[var(--color-kebab-dark-card)] rounded-xl border border-[var(--color-kebab-dark-hover)] p-5 hover:border-[var(--color-kebab-red)] transition-all shadow-lg flex flex-col relative group">
                @if($menu->is_active)
                    <span class="w-2.5 h-2.5 rounded-full bg-green-500 shadow-[0_0_8px_#22c55e] absolute top-5 right-5" title="Aktif"></span>
                @else
                    <span class="w-2.5 h-2.5 rounded-full bg-gray-600 absolute top-5 right-5" title="Nonaktif"></span>
                @endif
                <h4 class="font-bold text-white text-base pr-5 leading-snug mb-1">{{ $menu->name }}</h4>
                <p class="text-[var(--color-kebab-red)] font-black text-xl mb-5">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                <div class="flex gap-2 mt-auto">
                    <button @click="editMenu = {{ $menu->toJson() }}"
                        class="flex-1 bg-[var(--color-kebab-dark-hover)] hover:bg-blue-500 text-white font-bold py-2 rounded-lg transition-colors text-sm border border-[var(--color-kebab-dark-hover)] hover:border-blue-500">Edit</button>
                    <form action="/menus/{{ $menu->id }}" method="POST" class="flex-1" onsubmit="return confirm('Hapus menu ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full bg-[var(--color-kebab-dark-hover)] hover:bg-red-600 text-white font-bold py-2 rounded-lg transition-colors text-sm border border-[var(--color-kebab-dark-hover)] hover:border-red-600">Hapus</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    @if($menus->isEmpty())
    <div class="bg-[var(--color-kebab-dark-card)] rounded-xl border border-[var(--color-kebab-dark-hover)] p-12 text-center">
        <p class="text-[var(--color-kebab-text-muted)] text-lg">Belum ada menu. Tambahkan menu pertama Anda!</p>
    </div>
    @endif

    {{-- ═══ MODAL: Tambah Menu ═══ --}}
    <div x-show="showAddMenuModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-[var(--color-kebab-dark-card)] p-6 rounded-2xl border border-[var(--color-kebab-dark-hover)] shadow-2xl w-full max-w-md mx-4" @click.away="showAddMenuModal = false">
            <h3 class="text-xl font-bold mb-4 border-b border-[var(--color-kebab-dark-hover)] pb-3">Tambah Menu Baru</h3>
            <form action="/menus" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider mb-1">Nama Menu</label>
                    <input type="text" name="name" required class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white focus:outline-none focus:border-[var(--color-kebab-red)]" placeholder="Misal: Kebab Sapi L">
                </div>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider mb-1">Kategori</label>
                        <select name="category_id" required class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white focus:outline-none focus:border-[var(--color-kebab-red)]">
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider mb-1">Harga (Rp)</label>
                        <input type="number" name="price" required min="0" class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white focus:outline-none focus:border-[var(--color-kebab-red)]">
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="showAddMenuModal = false" class="flex-1 bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] text-white font-bold py-2.5 rounded-xl transition-colors">Batal</button>
                    <button type="submit" class="flex-1 bg-[var(--color-kebab-red)] hover:bg-[var(--color-kebab-red-dark)] text-white font-bold py-2.5 rounded-xl transition-colors shadow-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══ MODAL: Edit Menu ═══ --}}
    <div x-show="editMenu !== null" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-[var(--color-kebab-dark-card)] p-6 rounded-2xl border border-[var(--color-kebab-dark-hover)] shadow-2xl w-full max-w-md mx-4" @click.away="editMenu = null">
            <h3 class="text-xl font-bold mb-4 border-b border-[var(--color-kebab-dark-hover)] pb-3">Edit Menu</h3>
            <form :action="'/menus/' + (editMenu?.id || '')" method="POST">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider mb-1">Nama Menu</label>
                    <input type="text" name="name" :value="editMenu?.name" required class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white focus:outline-none focus:border-[var(--color-kebab-red)]">
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider mb-1">Kategori</label>
                        <select name="category_id" required class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white focus:outline-none focus:border-[var(--color-kebab-red)]"
                            x-init="$watch('editMenu', v => { if(v) $el.value = v.category_id })">
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider mb-1">Harga (Rp)</label>
                        <input type="number" name="price" :value="editMenu?.price" required min="0" class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white focus:outline-none focus:border-[var(--color-kebab-red)]">
                    </div>
                </div>
                <div class="mb-5 flex items-center gap-2">
                    <input type="checkbox" id="is_active_edit" name="is_active" value="1" :checked="editMenu?.is_active" class="w-4 h-4 accent-[var(--color-kebab-red)]">
                    <label for="is_active_edit" class="text-sm font-medium text-white">Menu Aktif (tampil di Kasir)</label>
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="editMenu = null" class="flex-1 bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] text-white font-bold py-2.5 rounded-xl transition-colors">Batal</button>
                    <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2.5 rounded-xl transition-colors shadow-lg">Perbarui</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══ MODAL: Kelola Kategori ═══ --}}
    <div x-show="showCatModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-[var(--color-kebab-dark-card)] p-6 rounded-2xl border border-[var(--color-kebab-dark-hover)] shadow-2xl w-full max-w-lg mx-4" @click.away="showCatModal = false">
            <h3 class="text-xl font-bold mb-4 border-b border-[var(--color-kebab-dark-hover)] pb-3">Kelola Kategori</h3>

            {{-- Tambah Kategori Baru --}}
            <form action="/categories" method="POST" class="flex gap-3 mb-5">
                @csrf
                <input type="text" name="name" required placeholder="Nama kategori baru..."
                    class="flex-1 bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white text-sm focus:outline-none focus:border-[var(--color-kebab-red)]">
                <button type="submit" class="bg-[var(--color-kebab-red)] hover:bg-[var(--color-kebab-red-dark)] text-white font-bold px-5 py-3 rounded-xl transition-colors shadow-lg whitespace-nowrap">+ Tambah</button>
            </form>

            {{-- Daftar Kategori --}}
            <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                @forelse($categories as $cat)
                <div class="flex items-center justify-between bg-[var(--color-kebab-dark)] p-3 rounded-xl border border-[var(--color-kebab-dark-hover)]">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 flex items-center justify-center bg-[var(--color-kebab-red)]/20 text-[var(--color-kebab-red)] rounded-lg text-xs font-black">{{ $loop->iteration }}</span>
                        <span class="font-medium text-white text-sm">{{ $cat->name }}</span>
                        @php $menuCount = $menus->where('category_id', $cat->id)->count(); @endphp
                        <span class="text-xs text-[var(--color-kebab-text-muted)]">({{ $menuCount }} menu)</span>
                    </div>
                    @if($menuCount === 0)
                    <form action="/categories/{{ $cat->id }}" method="POST" onsubmit="return confirm('Hapus kategori {{ $cat->name }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-400 text-sm font-bold transition-colors px-2">Hapus</button>
                    </form>
                    @else
                    <span class="text-xs text-[var(--color-kebab-text-muted)] italic">digunakan</span>
                    @endif
                </div>
                @empty
                <p class="text-center text-[var(--color-kebab-text-muted)] py-4 text-sm">Belum ada kategori.</p>
                @endforelse
            </div>

            <div class="mt-5 pt-4 border-t border-[var(--color-kebab-dark-hover)]">
                <button @click="showCatModal = false" class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] text-white font-bold py-2.5 rounded-xl transition-colors hover:bg-[var(--color-kebab-dark-hover)]">Selesai</button>
            </div>
        </div>
    </div>

</div>
@endsection
