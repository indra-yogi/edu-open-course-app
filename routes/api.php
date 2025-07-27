<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;

Route::prefix('course')->name('api.course.')->group(function () {
    Route::get('/approved', [CourseController::class, 'approvedCourses'])->name('approved');
    Route::get('/courses', [CourseController::class, 'courses'])->name('index');
    Route::get('/show/{id}', [CourseController::class, 'courseDetail'])->name('show');
    Route::post('/store', [CourseController::class, 'store'])->name('store');
    Route::post('/{id}/comment', [CourseController::class, 'postComment']);
});



