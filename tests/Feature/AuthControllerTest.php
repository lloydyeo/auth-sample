<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Auth\VerifyEmail;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testRegistration() : void
    {
        Notification::fake();

        $this->followingRedirects()->post(route('auth.register'), [
            'name' => 'Tester',
            'email' => 'lloyd@lloydyeo.com',
            'password' => 'Password123!@#',
            'password_confirmation' => 'Password123!@#'
        ])->assertStatus(200)->assertSee('Click on the link inside to verify your account.');

        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'name' => 'Tester',
            'email' => 'lloyd@lloydyeo.com',
            'email_verified_at' => null,
        ]);

        Notification::assertSentTo(User::first(), VerifyEmail::class);
    }

    public function testInvalidRegistration() : void
    {
        User::create([
            'name' => 'Tester',
            'email' => 'lloyd@lloydyeo.com',
            'password' => 'hashed-password'
        ]);

        // Existing user
        $this->from('register')->post(route('auth.register'), [
            'name' => 'Test',
            'email' => 'lloyd@lloydyeo.com',
            'password' => 'Password123!@#',
            'password_confirmation' => 'Password123!@#'
        ])->assertRedirect('register')->assertSessionHasErrors(['email']);

        // Invalid Email
        $this->from('register')->post(route('auth.register'), [
            'name' => 'Test',
            'email' => 'lloyd',
            'password' => 'password123!@#',
            'password_confirmation' => 'Password123!@#'
        ])->assertRedirect('register')->assertSessionHasErrors(['email']);

        // Passwords don't match
        $this->from('register')->post(route('auth.register'), [
            'name' => 'Test',
            'email' => 'lloyd@lloydyeo2.com',
            'password' => 'password123!@#',
            'password_confirmation' => 'Password123!@#'
        ])->assertRedirect('register')->assertSessionHasErrors(['password']);

        // Weak Password
        $this->from('register')->post(route('auth.register'), [
            'name' => 'Test',
            'email' => 'lloyd',
            'password' => 'password123!@#',
            'password_confirmation' => 'password123!@#'
        ])->assertRedirect('register')->assertSessionHasErrors(['password']);
    }

    public function testLogin() : void
    {
        $user = User::create([
            'name' => 'Tester',
            'email' => 'lloyd@lloydyeo.com',
            'password' => bcrypt('Password123!@#')
        ]);

        $this->post(route('auth.login'), [
            'email' => 'lloyd@lloydyeo.com',
            'password' => 'Password123!@#'
        ])->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
    }

    public function testLogout() : void
    {
        $user = User::create([
            'name' => 'Tester',
            'email' => 'lloyd@lloydyeo.com',
            'password' => bcrypt('Password123!@#')
        ]);

        $this->actingAs($user)->post(route('logout'))->assertRedirect('/');

        $this->assertGuest();
    }
}
