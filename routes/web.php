<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
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
        Route::post('/teacher/student/{username}/stage/{n}/reset', [TeacherController::class, 'resetStage'])->name('teacher.student.stage.reset');
    });
});

// Diagnostic route (development only) to help debug asset URLs and storage files.
Route::get('/__diag', function () {
    if (!app()->environment(['local', 'testing'])) {
        abort(404);
    }

    $path = request('path');
    $result = [
        'app_url' => config('app.url'),
        'env_app_url' => env('APP_URL'),
        'request_scheme' => request()->getScheme(),
        'host' => request()->getHost(),
        'headers' => request()->header(),
    ];

    if ($path) {
        // Normalize leading slash for storage path
        $trim = preg_replace('#^/+#', '', $path);

        // If user supplied a full URL, extract the path component
        if (preg_match('#^https?://#', $trim)) {
            $parts = parse_url($trim);
            $trim = isset($parts['path']) ? ltrim($parts['path'], '/') : '';
        }

        // If path points to /storage/..., map to storage/app/public
        $publicPrefix = 'storage/';
        if (str_starts_with($trim, $publicPrefix)) {
            $rel = substr($trim, strlen($publicPrefix));
            $fsPath = storage_path('app/public/' . $rel);
            $result['filesystem_path'] = $fsPath;
            $result['file_exists'] = file_exists($fsPath);
            $result['storage_url'] = Storage::url($rel);
            $result['disk_exists'] = Storage::disk('public')->exists($rel);
        } else {
            // Treat as disk-relative path
            $rel = $trim;
            $result['storage_url'] = Storage::url($rel);
            $result['filesystem_path_candidate'] = storage_path('app/public/' . $rel);
            $result['file_exists_candidate'] = file_exists(storage_path('app/public/' . $rel));
            $result['disk_exists_candidate'] = Storage::disk('public')->exists($rel);
        }
    }

    return response()->json($result);
});
