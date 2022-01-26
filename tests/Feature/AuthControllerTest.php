<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testRegistration() : void
    {
        $response = $this->followingRedirects()->post(route('auth.register'), [
            'name' => 'Tester',
            'email' => 'lloyd@lloydyeo.com',
            'password' => 'Password123!@#',
            'password_confirmation' => 'Password123!@#'
        ])->assertStatus(200)->assertSee('Click on the link inside to verify your account.');

        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'name' => 'Tester',
            'email' => 'lloyd@lloydyeo.com',
        ]);
    }

    public function testInvalidRegistration() : void
    {
        User::create([
            'name' => 'Tester',
            'email' => 'lloyd@lloydyeo.com',
            'password' => 'hashed-password'
        ]);

        // Existing user
        $response = $this->from('register')->post(route('auth.register'), [
            'name' => 'Test',
            'email' => 'lloyd@lloydyeo.com',
            'password' => 'Password123!@#',
            'password_confirmation' => 'Password123!@#'
        ])->assertRedirect('register')->assertSessionHasErrors(['email']);

        // Invalid Email
        $response = $this->from('register')->post(route('auth.register'), [
            'name' => 'Test',
            'email' => 'lloyd',
            'password' => 'password123!@#',
            'password_confirmation' => 'Password123!@#'
        ])->assertRedirect('register')->assertSessionHasErrors(['email']);

        // Passwords don't match
        $response = $this->from('register')->post(route('auth.register'), [
            'name' => 'Test',
            'email' => 'lloyd@lloydyeo2.com',
            'password' => 'password123!@#',
            'password_confirmation' => 'Password123!@#'
        ])->assertRedirect('register')->assertSessionHasErrors(['password']);

        // Weak Password
        $response = $this->from('register')->post(route('auth.register'), [
            'name' => 'Test',
            'email' => 'lloyd',
            'password' => 'password123!@#',
            'password_confirmation' => 'password123!@#'
        ])->assertRedirect('register')->assertSessionHasErrors(['password']);
    }
}
