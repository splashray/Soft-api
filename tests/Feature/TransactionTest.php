<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_credit_and_debit_with_locking(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $ref1 = (string) Str::uuid();
        $ref2 = (string) Str::uuid();

        $res1 = $this->postJson('/api/transactions', [
            'reference' => $ref1,
            'type' => 'credit',
            'amount' => 100.00,
        ]);
        $res1->assertCreated();

        $res2 = $this->postJson('/api/transactions', [
            'reference' => $ref2,
            'type' => 'debit',
            'amount' => 40.50,
        ]);
        $res2->assertCreated();

        $balance = $this->getJson('/api/balance')->json('balance');
        $this->assertSame('59.50', $balance);

        $this->assertDatabaseHas('transactions', [
            'reference' => $ref1,
            'type' => 'credit',
            'amount' => 100.00,
        ]);
        $this->assertDatabaseHas('transactions', [
            'reference' => $ref2,
            'type' => 'debit',
            'amount' => 40.50,
        ]);
    }

    public function test_cannot_overdraw(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $ref = (string) Str::uuid();
        $res = $this->postJson('/api/transactions', [
            'reference' => $ref,
            'type' => 'debit',
             'amount' => 10,
        ]);
        $res->assertStatus(422);
    }
}


