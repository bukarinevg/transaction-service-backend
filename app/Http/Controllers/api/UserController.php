<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return User::with(['projects', 'balance'])->get();
    }

    public function show()
    {
        $user = User::find(auth()->id());
        return $user->load(['projects', 'balance']);
    }
}
