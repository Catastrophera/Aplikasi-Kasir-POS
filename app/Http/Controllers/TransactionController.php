<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'channel'        => 'required|string',
            'payment_method' => 'required|string',
            'cart'           => 'required|array|min:1',
            'cart.*.id'      => 'required|exists:menus,id',
            'cart.*.quantity'=> 'required|integer|min:1',
            'cart.*.price'   => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $totalPrice = collect($request->cart)->sum(fn($i) => $i['price'] * $i['quantity']);

            $transaction = Transaction::create([
                'channel'        => $request->channel,
                'payment_method' => $request->payment_method,
                'total_price'    => $totalPrice,
            ]);

            foreach ($request->cart as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'menu_id'        => $item['id'],
                    'quantity'       => $item['quantity'],
                    'price'          => $item['price'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success'        => true,
                'message'        => 'Pesanan berhasil disimpan!',
                'transaction_id' => $transaction->id,
                'total'          => $totalPrice,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->items()->delete();
        $transaction->delete();

        return redirect()->back()->with('success', 'Transaksi #' . str_pad($transaction->id, 5, '0', STR_PAD_LEFT) . ' berhasil dihapus.');
    }
}
