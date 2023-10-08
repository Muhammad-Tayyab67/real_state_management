<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\{UserManagementController, PlotManagementController, ShareManagementController};


Route::post('/register', [AuthController::Class, 'register']);
Route::post('/login', [AuthController::Class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserManagementController::class, 'list']);
    Route::get('/user/show/{id}', [UserManagementController::class, 'show']);
    Route::post('/user/update/{id}', [UserManagementController::class, 'update']);
    Route::get('/user/delete/{id}', [UserManagementController::class, 'delete']);
    Route::get('/user/plots', [UserManagementController::class, 'plots']);

    //PlotManagement
    Route::post('/plot/store', [PlotManagementController::class, 'store']);
    Route::get('/plot/list', [PlotManagementController::class, 'list']);
    Route::get('/plot/show/{id}', [PlotManagementController::class, 'show']);
    Route::post('/plot/update/{id}', [PlotManagementController::class, 'update']);
    Route::get('/plot/delete/{id}', [PlotManagementController::class, 'delete']);

    //ShareManagement
    Route::post('/share/buy', [ShareManagementController::class, 'BuyShare']);
});


