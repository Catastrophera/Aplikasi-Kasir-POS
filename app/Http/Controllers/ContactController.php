<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::latest()->get();
        return view('management.contacts', compact('contacts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:customer,supplier,both',
        ]);

        Contact::create($request->all());

        return back()->with('success', 'Kontak berhasil ditambahkan!');
    }

    public function update(Request $request, Contact $contact)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:customer,supplier,both',
        ]);

        $contact->update($request->all());

        return back()->with('success', 'Kontak berhasil diperbarui!');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return back()->with('success', 'Kontak berhasil dihapus!');
    }
}
