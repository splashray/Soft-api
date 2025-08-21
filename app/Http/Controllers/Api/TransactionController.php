<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public function __construct(private WalletService $walletService)
    {
        $this->middleware('auth:sanctum');
    }

    public function balance(Request $request)
    {
        $userId = Auth::id();
        return response()->json([
            'balance' => $this->walletService->getBalance($userId),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference' => ['required', 'string', 'max:64'],
            'type' => ['required', Rule::in(['credit', 'debit'])],
            'amount' => ['required', 'numeric', 'gt:0'],
            'meta' => ['nullable', 'array'],
        ]);

        $userId = Auth::id();

        if ($validated['type'] === 'credit') {
            $tx = $this->walletService->credit($userId, $validated['reference'], (string) $validated['amount'], $validated['meta'] ?? []);
        } else {
            $tx = $this->walletService->debit($userId, $validated['reference'], (string) $validated['amount'], $validated['meta'] ?? []);
        }

        return response()->json([
            'transaction' => $tx,
        ], 201);
    }

    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        return response()->json(['transaction' => $transaction]);
    }
}


