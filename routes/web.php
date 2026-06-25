<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AccountCategoryController;
use App\Http\Controllers\TaskCategoryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PipelineController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\ProfileController;

Route::get('/', [DashboardController::class, 'index']);
Route::resource('users', UserController::class);

Route::resource('tasks', TaskController::class);
Route::resource('projects', ProjectController::class);




Route::get('task-board', [PipelineController::class, 'kanbanBoard']);
Route::post('task-update-status', [PipelineController::class, 'updateStatus']);

Route::get('/task-details/{id}', [PipelineController::class, 'taskDetails'])
    ->name('task.details');

    Route::get('/login', [SessionsController::class, 'create'])->name('login');
Route::post('/login', [SessionsController::class, 'store'])->name('login.store');

Route::get('/logout', [SessionsController::class, 'logout'])->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');


Route::get('/Profile', [ProfileController::class, 'index'])->name('Profile');

Route::post('/Profile/update', [ProfileController::class, 'update'])
    ->name('Profile.update');

Route::post('/change-password', [ProfileController::class, 'changePassword'])
    ->name('change.password');
Route::get('admin/setting', [ProfileController::class, 'index'])
    ->name('profile');

Route::resource(
    'Account_category',
    AccountCategoryController::class
);

Route::resource(
    'Task_category',
    TaskCategoryController::class
);
   



   
