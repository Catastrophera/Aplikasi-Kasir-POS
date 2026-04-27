<?php

namespace App\Http\Controllers;

use App\Models\CashDrawer;
use App\Models\Shift;
use App\Models\Transaction;
use App\Models\CashFlowEntry;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $cashDrawers  = CashDrawer::where('is_active', true)->get();
        $activeShift  = Shift::where('status', 'open')->with('cashDrawer')->latest('opened_at')->first();
        $shiftHistory = Shift::with('cashDrawer')->where('status', 'closed')->latest('closed_at')->take(30)->get();

        $shiftTransactions = collect();
        if ($activeShift) {
            $shiftTransactions = Transaction::where('created_at', '>=', $activeShift->opened_at)->latest()->get();
            $activeShift->total_sales        = $shiftTransactions->sum('total_price');
            $activeShift->total_transactions = $shiftTransactions->count();
        }

        return view('shift.index', compact('cashDrawers', 'activeShift', 'shiftHistory', 'shiftTransactions'));
    }

    public function show(Shift $shift)
    {
        $shift->load('cashDrawer');

        $shiftTransactions = Transaction::whereBetween('created_at', [
            $shift->opened_at,
            $shift->closed_at ?? now(),
        ])->latest()->get();

        $penjualanTunai = $shiftTransactions->sum('total_price');

        $cfEntries      = CashFlowEntry::where('shift_id', $shift->id)->get();
        $pemasukanLain  = $cfEntries->where('type', 'pemasukan_lain')->sum('amount');
        $pengeluaran    = $cfEntries->where('type', 'pengeluaran')->sum('amount');
        $pengeluaranLain = $cfEntries->where('type', 'pengeluaran_lain')->sum('amount');

        $subtotal = $penjualanTunai + $pemasukanLain - $pengeluaran - $pengeluaranLain;
        $penerimaanSistem = $shift->opening_balance + $subtotal;
        $selisih = ($shift->closing_balance ?? 0) - $penerimaanSistem;

        return view('shift.show', compact(
            'shift', 'shiftTransactions',
            'penjualanTunai', 'pemasukanLain', 'pengeluaran', 'pengeluaranLain',
            'subtotal', 'penerimaanSistem', 'selisih'
        ));
    }

    public function store(Request $request)
    {
        // Cek apakah ada shift aktif lain
        $existing = Shift::where('status', 'open')->first();
        if ($existing) {
            return back()->with('error', 'Masih ada shift yang sedang aktif! Tutup shift terlebih dahulu.');
        }

        $request->validate([
            'cash_drawer_id'  => 'required|exists:cash_drawers,id',
            'opening_balance' => 'required|numeric|min:0',
        ]);

        Shift::create([
            'cash_drawer_id'  => $request->cash_drawer_id,
            'opened_by'       => session('pos_user', 'Kasir'),
            'opening_balance' => $request->opening_balance,
            'opened_at'       => now(),
            'status'          => 'open',
        ]);

        return back()->with('success', 'Shift berhasil dimulai!');
    }

    public function close(Request $request, Shift $shift)
    {
        if ($shift->status === 'closed') {
            return back()->with('error', 'Shift ini sudah ditutup.');
        }

        // Hitung total transaksi selama shift
        $transactions = Transaction::where('created_at', '>=', $shift->opened_at)->get();
        $totalSales   = $transactions->sum('total_price');
        $totalTrx     = $transactions->count();

        $shift->update([
            'status'             => 'closed',
            'closed_at'          => now(),
            'total_sales'        => $totalSales,
            'total_transactions' => $totalTrx,
            'closing_balance'    => $request->closing_balance ?? ($shift->opening_balance + $totalSales),
            'notes'              => $request->notes,
        ]);

        return back()->with('success', 'Shift berhasil ditutup! Total pendapatan: Rp ' . number_format($totalSales, 0, ',', '.'));
    }

    // ─── Cash Drawer Management ────────────────────────────────────────────────

    public function storeCashDrawer(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);
        CashDrawer::create([
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => true,
        ]);
        return back()->with('success', 'Cash drawer berhasil ditambahkan!');
    }

    public function destroyCashDrawer(CashDrawer $cashDrawer)
    {
        if ($cashDrawer->shifts()->where('status', 'open')->exists()) {
            return back()->with('error', 'Cash drawer sedang digunakan dalam shift aktif!');
        }
        $cashDrawer->delete();
        return back()->with('success', 'Cash drawer dihapus.');
    }
}
