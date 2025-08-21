<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_and_login_returns_token(): void
    {
        $res = $this->postJson('/api/register', [
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);
        $res->assertCreated();
        $this->assertNotEmpty($res->json('token'));

        $login = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);
        $login->assertOk();
        $this->assertNotEmpty($login->json('token'));
    }
}


