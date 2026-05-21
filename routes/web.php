<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;

// Root redirect
Route::get('/', function () {
    if (session('user')) {
        return redirect(session('user')['role'] === 'guru' ? '/teacher' : '/student');
    }
    return redirect('/login');
});

// Auth
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',[AuthController::class, 'register']);
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');

// Student routes
Route::middleware('auth.user')->group(function () {
    Route::middleware('role:siswa')->group(function () {
        Route::get('/student',               [StudentController::class, 'dashboard'])->name('student.dashboard');
        Route::get('/student/stage/{n}',     [StudentController::class, 'showStage'])->name('student.stage');
        Route::post('/student/stage/{n}',    [StudentController::class, 'saveStage'])->name('student.stage.save');
    });

    // Teacher routes
    Route::middleware('role:guru')->group(function () {
        Route::get('/teacher',                        [TeacherController::class, 'dashboard'])->name('teacher.dashboard');
        Route::get('/teacher/student/{username}',     [TeacherController::class, 'detail'])->name('teacher.student');
    });
});
