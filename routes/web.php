<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebController;
use App\Http\Controllers\Api\AuthController;

Route::get('/', [WebController::class, 'index'])->name('home');
Route::get('/course', [WebController::class, 'course'])->name('courses');
Route::get('/login', [WebController::class, 'login'])->name('login');
Route::get('/register', [WebController::class, 'register'])->name('register');
Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [WebController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/course/create', [WebController::class, 'createCourse'])->name('course.create');
    Route::get('/course/{id}', [WebController::class, 'detailCourse'])->name('course.detail');
    Route::get('/course/{id}/edit', [WebController::class, 'courseEdit'])->name('course.edit');
});

Route::post('/logout', [WebController::class, 'logout'])->name('logout');
