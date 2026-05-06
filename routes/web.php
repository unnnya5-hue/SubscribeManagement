<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BodyRecordController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\WorkoutController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::resource('machines', MachineController::class);
    Route::resource('menus', MenuController::class);
    Route::get('/menus/{menu}/suggest', [MenuController::class, 'suggest'])->name('menus.suggest');
    Route::post('/menus/{menu}/suggest', [MenuController::class, 'applySuggestion'])->name('menus.suggest.apply');

    Route::resource('schedules', ScheduleController::class)->except(['show']);
    Route::post('/schedules/{schedule}/activate', [ScheduleController::class, 'activate'])->name('schedules.activate');

    Route::resource('workouts', WorkoutController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::resource('body-records', BodyRecordController::class)->except(['show']);

    Route::get('/charts', [ChartController::class, 'index'])->name('charts.index');
    Route::get('/charts/body', [ChartController::class, 'body'])->name('charts.body');
    Route::get('/charts/progress', [ChartController::class, 'progress'])->name('charts.progress');
    Route::get('/charts/body-parts', [ChartController::class, 'bodyParts'])->name('charts.body-parts');
    Route::get('/calendar', CalendarController::class)->name('calendar.index');
});
