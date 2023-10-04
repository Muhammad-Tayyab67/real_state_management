<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserManagementController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', [AuthController::Class, 'register']);
Route::post('/login', [AuthController::Class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserManagementController::class, 'list']);
    Route::get('/user/show/{id}', [UserManagementController::class, 'show']);
    Route::post('/user/update/{id}', [UserManagementController::class, 'update']);
});

//Public API to display images
Route::get('/images/{filename}', function ($filename) {
    $path = storage_path('app/public/profilePictures' . $filename);
    if (!File::exists($path)) {
        abort(402);
    }
    $file = File::get($path);
    $type = File::mimeType($path);
    $response = Response::make($file, 200);
    $response->header('Content-Type', $type);
    return $response;
});

