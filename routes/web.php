<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;



Route::get('/{any}' , [AppController::class, 'index'])->where('any', '.*');

//Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin
Route::post('/add-doctor', [AdminController::class, 'docStore']);
Route::delete('/doc/{id}', [AdminController::class, 'docDestroy']);
Route::post('/doctors', [AdminController::class, 'docIndex']);
Route::post('/doctor', [AdminController::class, 'docIndex']);

Route::post('/add-department', [AdminController::class, 'depStore']);
Route::post('/departments', [AdminController::class, 'depIndex']);
Route::delete('/depdlt/{id}', [AdminController::class, 'depDestroy']);

Route::post('/analyse', [AdminController::class, 'getAppointmentStats']);


//doc
Route::post('/create-sessions', [DoctorController::class, 'sessionstore']);
Route::post('/doctors-by-email/{email}', [DoctorController::class, 'getDoctorByEmail']);
Route::post('/upcsessions/{doctorId}', [DoctorController::class, 'getSessions']);

Route::post('/appointments/{doctorId}', [DoctorController::class, 'getAppointmentsByDoctorId']);
Route::post('/appointments/today/{doctorId}', [DoctorController::class, 'getTodaysAppointmentsByDoctorId']);

Route::post('/treatment/{id}', [DoctorController::class, 'show']);
Route::post('/submit-prescription', [DoctorController::class, 'submitPrescription']);


// patient
Route::post('/sessions/{doctorId}', [PatientController::class, 'getSessionsByDoctorId']);
Route::post('/appointments', [PatientController::class, 'appStore']);
Route::post('/my-appointments', [PatientController::class, 'myAppointments']);

Route::post('/getprescription', [PatientController::class, 'getDoneAppointments']);
Route::post('/appointments/{id}', [PatientController::class, 'getAppointmentById']);

Route::post('/user/update-profile', [PatientController::class, 'updateProfile']);
Route::post('/user/profile', [PatientController::class, 'getProfile']); 
Route::post('/getProfilePic/{userId}', [PatientController::class, 'getProfilePic']);





Route::delete('/appointments/{id}', [PatientController::class, 'destroyApp']);
