<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::view('/', 'welcome');
Route::view('register', 'auth.register')->name('register');
Route::view('login', 'auth.login')->name('login');

Route::prefix('auth')->name('auth.')->controller(AuthController::class)->group(function(){
    Route::post('register', 'register')->name('register');
    Route::post('login', 'login')->name('login');
});

Route::prefix('verify')->name('verification.')->controller(AuthController::class)->middleware('auth')->group(function(){
    Route::view('email', 'auth.verify-email')->name('notice');
    Route::middleware('signed')->group(function() {
        Route::get('email/{id}/{hash}', 'verifyEmail')->name('verify');
    });
});

Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
