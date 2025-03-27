<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\ProjectController;
use App\Http\Controllers\api\PaymentController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


// User registration and login routes
Route::post('/register', function (Request $request) {
    try {
        $fields = $request->validate([
            'name' => 'required|string|unique:users,name',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user]);
    } catch (\Exception $e) {
        Log::error('User registration failed', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'User registration failed', 'error' => $e->getMessage()], 400);
    }
});

Route::post('/login', function (Request $request) {
    try {
        $fields = $request->validate([
            'email' => 'required|email', 
            'password' => 'required|string',
        ]);

        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user]);
    } catch (\Exception $e) {
        Log::error('User login failed', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'User login failed', 'error' => $e->getMessage()], 400);
    }
})->name('login');

// User logout route
Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->tokens()->delete();
    return response()->json(['message' => 'Logged out']);
});


// Protected routes
try{
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::get('/projects', [ProjectController::class, 'index']);
        Route::post('/projects', [ProjectController::class, 'store']);
        Route::get('/payments', [PaymentController::class, 'index']);
        //verify HMAC middleware
        Route::post('/payments', [PaymentController::class, 'store'])->middleware('verify.hmac');
        Route::get('/payments/export', [PaymentController::class, 'export']);
        Route::patch('/payments/{payment}', [PaymentController::class, 'update']);
    });
}
catch (\Exception $e) {
    Log::error('Route error', ['error' => $e->getMessage()]);

    if($e instanceof \Illuminate\Auth\AuthenticationException){
        return response()->json(['message' => 'Unauthorized access'], 401);
    }

    return response()->json(['message' => 'Route error', 'error' => $e->getMessage()], 400);
};


Route::fallback(function(){
    return response()->json(['message' => 'Route not found or unauthorized access'], 404);
});