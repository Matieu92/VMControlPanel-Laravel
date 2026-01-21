<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    public function index()
    {
        $transactions = Transaction::where('user_id', Auth::id())->latest()->paginate(10);
        return view('finance.index', compact('transactions'));
    }

    public function deposit(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:10']);

        $user = Auth::user();
        $amount = $request->amount;

        $user->increment('balance', $amount);

        Transaction::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'type' => 'deposit',
            'description' => 'Doładowanie portfela'
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'BALANCE_DEPOSIT',
            'details' => "Doładowano konto o kwotę: {$amount} PLN",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Środki zostały dodane do Twojego konta.');
    }
}