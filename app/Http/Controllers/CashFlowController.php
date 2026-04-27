<?php

namespace App\Http\Controllers;

use App\Models\CashFlowEntry;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CashFlowController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'today');

        [$start, $end] = $this->getPeriodDates($period, $request);

        // Pemasukan dari transaksi (penjualan)
        $pemasukan = Transaction::whereBetween('created_at', [$start->startOfDay(), $end->copy()->endOfDay()])->sum('total_price');

        // Entri manual
        $entries = CashFlowEntry::whereBetween('date', [$start->toDateString(), $end->toDateString()])->get();

        $pemasukanLain   = $entries->where('type', 'pemasukan_lain')->sum('amount');
        $pengeluaran     = $entries->where('type', 'pengeluaran')->sum('amount');
        $pengeluaranLain = $entries->where('type', 'pengeluaran_lain')->sum('amount');

        $totalPemasukan  = $pemasukan + $pemasukanLain;
        $totalPengeluaran = $pengeluaran + $pengeluaranLain;
        $pendapatanNeto  = $totalPemasukan - $totalPengeluaran;

        return view('reports.cash-flow', compact(
            'period', 'start', 'end',
            'pemasukan', 'pemasukanLain', 'pengeluaran', 'pengeluaranLain',
            'totalPemasukan', 'totalPengeluaran', 'pendapatanNeto',
            'entries'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'        => 'required|in:pemasukan_lain,pengeluaran,pengeluaran_lain',
            'amount'      => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'date'        => 'required|date',
        ]);

        CashFlowEntry::create([
            'type'        => $request->type,
            'amount'      => $request->amount,
            'description' => $request->description,
            'date'        => $request->date,
            'shift_id'    => $request->shift_id,
            'created_by'  => session('pos_user', 'Kasir'),
        ]);

        return back()->with('success', 'Entri berhasil ditambahkan!');
    }

    public function destroy(CashFlowEntry $cashFlowEntry)
    {
        $cashFlowEntry->delete();
        return back()->with('success', 'Entri dihapus.');
    }

    private function getPeriodDates(string $period, Request $request): array
    {
        return match ($period) {
            'today'   => [Carbon::today(), Carbon::today()],
            'week'    => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'month'   => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'custom'  => [
                Carbon::parse($request->get('date_from', today())),
                Carbon::parse($request->get('date_to', today())),
            ],
            default   => [Carbon::today(), Carbon::today()],
        };
    }
}
