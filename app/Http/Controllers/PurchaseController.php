<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Contact;
use App\Models\CashFlowEntry;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('contact')->latest()->get();
        $suppliers = Contact::whereIn('type', ['supplier', 'both'])->get();
        $rawMaterials = RawMaterial::orderBy('name')->get();
        return view('management.purchases', compact('purchases', 'suppliers', 'rawMaterials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.raw_material_id' => 'nullable|exists:raw_materials,id',
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
                    'raw_material_id' => $item['raw_material_id'] ?: null,
                    'item_name' => $item['name'],
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                ]);

                if (!empty($item['raw_material_id'])) {
                    $rawMaterial = RawMaterial::find($item['raw_material_id']);
                    if ($rawMaterial) {
                        $rawMaterial->increment('stock', $item['qty']);
                    }
                }
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
        DB::transaction(function () use ($purchase) {
            foreach ($purchase->items as $item) {
                if ($item->raw_material_id) {
                    $rawMaterial = RawMaterial::find($item->raw_material_id);
                    if ($rawMaterial) {
                        $rawMaterial->decrement('stock', $item->quantity);
                    }
                }
            }
            $purchase->delete();
        });
        
        return back()->with('success', 'Data pembelian dihapus.');
    }
}
