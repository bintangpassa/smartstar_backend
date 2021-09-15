<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UniversalController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/test', function (Request $request) {
        return $request->user();
    });

    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('invite-teacher', [UniversalController::class, 'inviteTeacher']);
    Route::post('invite-student', [UniversalController::class, 'inviteStudent']);
    Route::post('delete-teacher', [UniversalController::class, 'deleteTeacher']);
    Route::post('delete-student', [UniversalController::class, 'deleteStudent']);
    Route::get('teacher-list', [UniversalController::class, 'getTeacherList']);
    Route::get('student-list', [UniversalController::class, 'getStudentList']);

    Route::prefix('console')->group(function () {
    });
});

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');