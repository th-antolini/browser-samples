<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TodoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Todo API routes
Route::apiResource('todos', TodoController::class);
Route::patch('todos/{todo}/toggle', [TodoController::class, 'toggle']);