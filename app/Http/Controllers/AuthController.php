<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Service\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(RegistrationRequest $request) : RedirectResponse
    {
        $validated = $request->validated();
        $validated['password'] = bcrypt($validated['password']);
        $user = $this->userService->createUser($validated);
        if ($user) {
            Auth::login($user);
        }

        return redirect()->route('verification.notice');
    }

    public function logout(Request $request) : RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        if (Auth::attempt($validated)) {
            return redirect('/');
        }

        return redirect()->back()->withErrors(['email' => 'Email or password is incorrect']);
    }

    public function verifyEmail(EmailVerificationRequest $request) : RedirectResponse
    {
        $request->fulfill();

        return redirect('/');
    }
}
