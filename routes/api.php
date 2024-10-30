<?php

// use App\Http\Controllers\AdminController;
// use App\Http\Controllers\AuthController;
// use App\Http\Controllers\DepartmentController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;

Route::get('/users', [AdminController::class, 'getUsers']);


// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);
// Route::get('/department', [DepartmentController::class, 'index']);
// Route::get('/api/doctors', [AdminController::class, 'docIndex']); -->