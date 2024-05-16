<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);


Route::resource('products',ProductController::class);

