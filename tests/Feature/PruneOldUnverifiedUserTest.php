<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class PruneOldUnverifiedUserTest extends TestCase
{
    use RefreshDatabase;

    public function testPruneCondition() : void
    {
        $this->getUsers();
        $this->artisan('model:prune')->assertSuccessful();
        $this->assertDatabaseHas('users', ['id' => 1]);
        $this->assertDatabaseHas('users', ['id' => 3]);
        $this->assertDatabaseMissing('users', ['id' => 2]);
    }

    public function getUsers() : void
    {
        User::insert([
            [
                'id' => 1,
                'name' => 'something',
                'email' => 'abc@gmail.com',
                'password' => 'password',
                'email_verified_at' => now(),
                'created_at' => now()->subDays(8),
            ],
            [
                'id' => 2,
                'name' => 'something',
                'email' => 'abc2@gmail.com',
                'password' => 'password',
                'email_verified_at' => null,
                'created_at' => now()->subDays(7),
            ],
            [
                'id' => 3,
                'name' => 'something',
                'email' => 'abc3@gmail.com',
                'password' => 'password',
                'email_verified_at' => null,
                'created_at' => now()->subDays(3),
            ],
        ]);
    }
}
