<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AccountCategoryController;
use App\Http\Controllers\TaskCategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\DailyStandupReportController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PipelineController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectCategoryController;
use App\Http\Controllers\MeetingMinuteController;

Route::get('/', [DashboardController::class, 'index']);
Route::resource('users', UserController::class);

Route::resource('tasks', TaskController::class);
Route::resource('projects', ProjectController::class);
Route::resource('tags', TagController::class);
Route::resource('standup-reports', DailyStandupReportController::class);
Route::resource('meeting-minutes', MeetingMinuteController::class);




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
Route::resource('Task_category', TaskCategoryController::class)
    ->middleware('can:manage-task-categories');


Route::resource('Project_category', ProjectCategoryController::class)
    ->middleware('can:manage-project-categories');

Route::get('category/select', function (Request $request) {
    $user = $request->user();
    $categoryId = $request->query('category_id');

    if ($user?->hasRole('admin')) {
        if ($categoryId !== null && $categoryId !== '' && ctype_digit((string) $categoryId)) {
            $request->session()->put('current_category_id', (int) $categoryId);
        } else {
            $request->session()->forget('current_category_id');
        }
    }

    return redirect()->to(url()->previous() ?: route('dashboard'));
})->name('category.select')->middleware('auth');



   
