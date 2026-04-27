<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Contact;
use App\Models\CashFlowEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('contact')->latest()->get();
        $suppliers = Contact::whereIn('type', ['supplier', 'both'])->get();
        return view('management.purchases', compact('purchases', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $purchase = Purchase::create([
                'contact_id' => $request->contact_id,
                'total_amount' => $request->total_amount,
                'date' => $request->date,
                'notes' => $request->notes,
                'created_by' => session('pos_user', 'Kasir'),
            ]);

            foreach ($request->items as $item) {
                $purchase->items()->create([
                    'item_name' => $item['name'],
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                ]);
            }

            // Otomatis masukkan ke Arus Kas sebagai Pengeluaran
            CashFlowEntry::create([
                'type' => 'pengeluaran',
                'amount' => $request->total_amount,
                'description' => 'Pembelian stok: ' . ($request->notes ?: 'Tanpa catatan'),
                'date' => $request->date,
                'created_by' => session('pos_user', 'Kasir'),
            ]);
        });

        return back()->with('success', 'Pembelian berhasil dicatat!');
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();
        return back()->with('success', 'Data pembelian dihapus.');
    }
}
