<?php

namespace App\Service;

use App\Models\User;
use Illuminate\Auth\Events\Registered;

class UserService
{
    public function createUser(array $user) : User {
        $user = User::create($user);

        event(new Registered($user));

        return $user;
    }
}
