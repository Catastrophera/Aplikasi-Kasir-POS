@extends('layouts.app')
@section('title', 'Laporan Arus Keuangan - Cheframa Kebab')

@section('content')
<div x-data="{ showAddModal: false, addType: 'pengeluaran' }" class="space-y-5">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-bold">Arus Keuangan</h2>
            <p class="text-[var(--color-kebab-text-muted)] mt-1">Pantau pemasukan dan pengeluaran bisnis.</p>
        </div>
        <button @click="showAddModal = true"
            class="bg-[var(--color-kebab-red)] hover:bg-[var(--color-kebab-red-dark)] text-white px-4 py-2.5 rounded-xl font-bold flex items-center gap-2 transition-colors shadow-lg text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Entri
        </button>
    </div>

    @if(session('success'))
    <div class="bg-green-500/20 text-green-400 p-4 rounded-xl border border-green-500/40 font-medium">{{ session('success') }}</div>
    @endif

    {{-- Filter Periode --}}
    <form method="GET" action="/reports/cash-flow" class="flex flex-wrap gap-3 items-center">
        <div class="flex bg-[var(--color-kebab-dark-card)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-1 gap-1">
            @foreach(['today' => 'Hari Ini', 'week' => 'Minggu Ini', 'month' => 'Bulan Ini', 'custom' => 'Custom'] as $key => $label)
            <a href="?period={{ $key }}"
               class="px-4 py-2 rounded-lg text-sm font-bold transition-all {{ $period === $key ? 'bg-[var(--color-kebab-red)] text-white shadow' : 'text-[var(--color-kebab-text-muted)] hover:text-white' }}">
               {{ $label }}
            </a>
            @endforeach
        </div>
        @if($period === 'custom')
        <input type="date" name="date_from" value="{{ request('date_from', today()->toDateString()) }}"
            class="bg-[var(--color-kebab-dark-card)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-2.5 text-white text-sm focus:outline-none focus:border-[var(--color-kebab-red)]">
        <span class="text-[var(--color-kebab-text-muted)]">→</span>
        <input type="date" name="date_to" value="{{ request('date_to', today()->toDateString()) }}"
            class="bg-[var(--color-kebab-dark-card)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-2.5 text-white text-sm focus:outline-none focus:border-[var(--color-kebab-red)]">
        <button type="submit" class="bg-[var(--color-kebab-red)] text-white px-4 py-2.5 rounded-xl font-bold text-sm">Terapkan</button>
        @endif
    </form>

    {{-- Summary Card --}}
    <div class="bg-[var(--color-kebab-dark-card)] rounded-2xl border border-[var(--color-kebab-dark-hover)] overflow-hidden">
        <div class="grid grid-cols-2 divide-x divide-[var(--color-kebab-dark-hover)]">
            {{-- Pemasukan --}}
            <div class="p-5 space-y-4">
                <div>
                    <p class="text-xs text-[var(--color-kebab-text-muted)] mb-1">Pemasukan (Penjualan)</p>
                    <p class="text-xl font-black text-green-400">Rp {{ number_format($pemasukan, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-xs text-[var(--color-kebab-text-muted)] mb-1">Pemasukan Lain</p>
                    <p class="text-xl font-black text-green-400">Rp {{ number_format($pemasukanLain, 0, ',', '.') }}</p>
                </div>
                <div class="pt-3 border-t border-[var(--color-kebab-dark-hover)]">
                    <p class="text-xs text-[var(--color-kebab-text-muted)] mb-1">Total Pemasukan</p>
                    <p class="text-xl font-black text-green-400">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
                </div>
            </div>
            {{-- Pengeluaran --}}
            <div class="p-5 space-y-4">
                <div>
                    <p class="text-xs text-[var(--color-kebab-text-muted)] mb-1">Pengeluaran</p>
                    <p class="text-xl font-black text-red-400">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-xs text-[var(--color-kebab-text-muted)] mb-1">Pengeluaran Lain</p>
                    <p class="text-xl font-black text-red-400">Rp {{ number_format($pengeluaranLain, 0, ',', '.') }}</p>
                </div>
                <div class="pt-3 border-t border-[var(--color-kebab-dark-hover)]">
                    <p class="text-xs text-[var(--color-kebab-text-muted)] mb-1">Total Pengeluaran</p>
                    <p class="text-xl font-black text-red-400">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        {{-- Pendapatan Neto --}}
        <div class="border-t border-[var(--color-kebab-dark-hover)] bg-gradient-to-br from-[var(--color-kebab-dark)] to-[#0d1f0d] p-5 text-center">
            <p class="text-sm text-[var(--color-kebab-text-muted)] mb-1">Pendapatan Neto</p>
            <p class="text-4xl font-black {{ $pendapatanNeto >= 0 ? 'text-green-400' : 'text-red-400' }}">
                {{ $pendapatanNeto < 0 ? '-' : '' }}Rp {{ number_format(abs($pendapatanNeto), 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Daftar Entri Manual --}}
    @if($entries->count() > 0)
    <div class="bg-[var(--color-kebab-dark-card)] rounded-2xl border border-[var(--color-kebab-dark-hover)] overflow-hidden">
        <div class="p-5 border-b border-[var(--color-kebab-dark-hover)]">
            <h3 class="font-bold">Entri Manual</h3>
        </div>
        <div class="divide-y divide-[var(--color-kebab-dark-hover)]">
            @foreach($entries as $entry)
            <div class="p-4 flex justify-between items-center hover:bg-[var(--color-kebab-dark-hover)] transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ str_contains($entry->type, 'pemasukan') ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if(str_contains($entry->type, 'pemasukan'))
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                            @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                            @endif
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-white text-sm">{{ $entry->description }}</p>
                        <p class="text-xs text-[var(--color-kebab-text-muted)]">
                            {{ match($entry->type) {
                                'pemasukan_lain'   => 'Pemasukan Lain',
                                'pengeluaran'      => 'Pengeluaran',
                                'pengeluaran_lain' => 'Pengeluaran Lain',
                                default            => $entry->type
                            } }} · {{ $entry->date->format('d M Y') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <p class="font-black {{ str_contains($entry->type, 'pemasukan') ? 'text-green-400' : 'text-red-400' }}">
                        {{ str_contains($entry->type, 'pengeluaran') ? '-' : '+' }}Rp {{ number_format($entry->amount, 0, ',', '.') }}
                    </p>
                    <form action="/reports/cash-flow/{{ $entry->id }}" method="POST" onsubmit="return confirm('Hapus entri ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="bg-[var(--color-kebab-dark-card)] rounded-2xl border border-[var(--color-kebab-dark-hover)] p-10 text-center text-[var(--color-kebab-text-muted)]">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        <p>Belum ada entri manual untuk periode ini.</p>
        <button @click="showAddModal = true" class="mt-3 text-[var(--color-kebab-red)] font-bold text-sm hover:underline">+ Tambah Entri</button>
    </div>
    @endif

    {{-- Modal Tambah Entri --}}
    <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-[var(--color-kebab-dark-card)] p-6 rounded-2xl border border-[var(--color-kebab-dark-hover)] shadow-2xl w-full max-w-md mx-4" @click.away="showAddModal = false">
            <h3 class="text-xl font-bold mb-4 border-b border-[var(--color-kebab-dark-hover)] pb-3">Tambah Entri Arus Kas</h3>
            <form action="/reports/cash-flow" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider mb-2">Jenis</label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['pemasukan_lain' => 'Pemasukan Lain', 'pengeluaran' => 'Pengeluaran', 'pengeluaran_lain' => 'Pengeluaran Lain'] as $val => $lbl)
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="{{ $val }}" x-model="addType" class="hidden peer" {{ $val === 'pengeluaran' ? 'checked' : '' }}>
                            <div class="border border-[var(--color-kebab-dark-hover)] peer-checked:border-[var(--color-kebab-red)] peer-checked:bg-[var(--color-kebab-red)]/10 rounded-xl p-2 text-center text-xs font-bold text-[var(--color-kebab-text-muted)] peer-checked:text-[var(--color-kebab-red)] transition-all">{{ $lbl }}</div>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider mb-2">Keterangan</label>
                    <input type="text" name="description" required placeholder="Misal: Bayar listrik" class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white focus:outline-none focus:border-[var(--color-kebab-red)]">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider mb-2">Jumlah (Rp)</label>
                        <input type="number" name="amount" required min="0" placeholder="0" class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white focus:outline-none focus:border-[var(--color-kebab-red)]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase tracking-wider mb-2">Tanggal</label>
                        <input type="date" name="date" required value="{{ today()->toDateString() }}" class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white focus:outline-none focus:border-[var(--color-kebab-red)]">
                    </div>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" @click="showAddModal = false" class="flex-1 bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] text-white font-bold py-2.5 rounded-xl">Batal</button>
                    <button type="submit" class="flex-1 bg-[var(--color-kebab-red)] hover:bg-[var(--color-kebab-red-dark)] text-white font-black py-2.5 rounded-xl transition-colors shadow-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
