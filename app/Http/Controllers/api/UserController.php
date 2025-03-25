<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return User::with(['projects', 'balance'])->get();
    }

    public function show(User $user)
    {
        return $user->load(['projects', 'balance']);
    }
}
