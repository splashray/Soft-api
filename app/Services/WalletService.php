<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WalletService
{
    public function getOrCreateWallet(int $userId): Wallet
    {
        return Wallet::firstOrCreate(['user_id' => $userId], ['balance' => 0]);
    }

    public function credit(int $userId, string $reference, string $amount, array $meta = []): Transaction
    {
        return DB::transaction(function () use ($userId, $reference, $amount, $meta) {
            $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->first();
            if (!$wallet) {
                $wallet = $this->getOrCreateWallet($userId);
                $wallet->refresh();
                $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->first();
            }

            if (Transaction::where('reference', $reference)->exists()) {
                return Transaction::where('reference', $reference)->first();
            }

            $balanceBefore = $wallet->balance;
            $wallet->balance = bcadd((string) $wallet->balance, (string) $amount, 2);
            $wallet->save();

            return Transaction::create([
                'user_id' => $userId,
                'reference' => $reference,
                'type' => 'credit',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'meta' => $meta,
            ]);
        }, 3);
    }

    public function debit(int $userId, string $reference, string $amount, array $meta = []): Transaction
    {
        return DB::transaction(function () use ($userId, $reference, $amount, $meta) {
            $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->first();
            if (!$wallet) {
                $wallet = $this->getOrCreateWallet($userId);
                $wallet->refresh();
                $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->first();
            }

            if (Transaction::where('reference', $reference)->exists()) {
                return Transaction::where('reference', $reference)->first();
            }

            if (bccomp((string) $wallet->balance, (string) $amount, 2) < 0) {
                throw ValidationException::withMessages([
                    'balance' => ['Insufficient balance.'],
                ])->status(422);
            }

            $balanceBefore = $wallet->balance;
            $wallet->balance = bcsub((string) $wallet->balance, (string) $amount, 2);
            $wallet->save();

            return Transaction::create([
                'user_id' => $userId,
                'reference' => $reference,
                'type' => 'debit',
                'amount' => $amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'meta' => $meta,
            ]);
        }, 3);
    }

    public function getBalance(int $userId): string
    {
        $wallet = $this->getOrCreateWallet($userId);
        return (string) $wallet->balance;
    }
}


