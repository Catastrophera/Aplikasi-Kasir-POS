@extends('layouts.app')
@section('title', 'Pelanggan & Supplier - Cheframa Kebab')

@section('content')
<div x-data="{ showModal: false, editContact: null }">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-bold">Pelanggan & Supplier</h2>
            <p class="text-[var(--color-kebab-text-muted)] mt-1">Kelola data kontak bisnis Anda.</p>
        </div>
        <button @click="showModal = true; editContact = null" class="bg-[var(--color-kebab-red)] hover:bg-[var(--color-kebab-red-dark)] text-white px-4 py-2.5 rounded-xl font-bold transition-all shadow-lg text-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Kontak
        </button>
    </div>

    @if(session('success'))
    <div class="bg-green-500/20 text-green-400 p-4 rounded-xl mb-6 border border-green-500/40 font-medium">{{ session('success') }}</div>
    @endif

    <div class="bg-[var(--color-kebab-dark-card)] rounded-2xl border border-[var(--color-kebab-dark-hover)] overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-[var(--color-kebab-dark)] text-xs text-[var(--color-kebab-text-muted)] uppercase tracking-wider">
                    <th class="p-4">Nama</th>
                    <th class="p-4">Tipe</th>
                    <th class="p-4">Telepon</th>
                    <th class="p-4">Alamat</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--color-kebab-dark-hover)]">
                @forelse($contacts as $contact)
                <tr class="hover:bg-[var(--color-kebab-dark-hover)] transition-colors">
                    <td class="p-4 font-bold text-white">{{ $contact->name }}</td>
                    <td class="p-4">
                        <span class="px-2 py-1 rounded text-xs font-bold {{ $contact->type == 'customer' ? 'bg-blue-500/20 text-blue-400' : ($contact->type == 'supplier' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-purple-500/20 text-purple-400') }}">
                            {{ ucfirst($contact->type) }}
                        </span>
                    </td>
                    <td class="p-4 text-sm text-[var(--color-kebab-text-muted)]">{{ $contact->phone ?: '-' }}</td>
                    <td class="p-4 text-sm text-[var(--color-kebab-text-muted)] truncate max-w-[200px]">{{ $contact->address ?: '-' }}</td>
                    <td class="p-4 text-right flex justify-end gap-2">
                        <button @click="editContact = {{ $contact->toJson() }}; showModal = true" class="text-blue-400 hover:text-blue-300">Edit</button>
                        <form action="/management/contacts/{{ $contact->id }}" method="POST" onsubmit="return confirm('Hapus kontak ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-300">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-12 text-center text-[var(--color-kebab-text-muted)]">Belum ada data kontak.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal --}}
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-[var(--color-kebab-dark-card)] p-6 rounded-2xl border border-[var(--color-kebab-dark-hover)] shadow-2xl w-full max-w-md mx-4" @click.away="showModal = false">
            <h3 class="text-xl font-bold mb-4 border-b border-[var(--color-kebab-dark-hover)] pb-3" x-text="editContact ? 'Edit Kontak' : 'Tambah Kontak Baru'"></h3>
            <form :action="editContact ? '/management/contacts/' + editContact.id : '/management/contacts'" method="POST">
                @csrf
                <template x-if="editContact"><input type="hidden" name="_method" value="PUT"></template>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase mb-1">Nama</label>
                        <input type="text" name="name" required :value="editContact?.name" class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white focus:border-[var(--color-kebab-red)] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase mb-1">Tipe</label>
                        <select name="type" required class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white focus:border-[var(--color-kebab-red)] outline-none">
                            <option value="customer" :selected="editContact?.type == 'customer'">Pelanggan</option>
                            <option value="supplier" :selected="editContact?.type == 'supplier'">Supplier</option>
                            <option value="both" :selected="editContact?.type == 'both'">Keduanya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase mb-1">Telepon</label>
                        <input type="text" name="phone" :value="editContact?.phone" class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white focus:border-[var(--color-kebab-red)] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[var(--color-kebab-text-muted)] uppercase mb-1">Alamat</label>
                        <textarea name="address" rows="3" class="w-full bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] rounded-xl p-3 text-white focus:border-[var(--color-kebab-red)] outline-none" x-text="editContact?.address"></textarea>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" @click="showModal = false" class="flex-1 bg-[var(--color-kebab-dark)] border border-[var(--color-kebab-dark-hover)] text-white font-bold py-2.5 rounded-xl">Batal</button>
                    <button type="submit" class="flex-1 bg-[var(--color-kebab-red)] hover:bg-[var(--color-kebab-red-dark)] text-white font-bold py-2.5 rounded-xl transition-colors shadow-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
