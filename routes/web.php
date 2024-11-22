<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return redirect()->route('home');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Profile
Route::prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        // Education Area
        Route::get('/show_education_area', [AdminController::class, 'show_education_area'])->name('show_education_area');
        Route::post('/education_area/edit', [AdminController::class, 'education_area_edit'])->name('education.area.edit');
        Route::post('/education_area/update', [AdminController::class, 'education_area_update'])->name('education.area.update');
        Route::get('/education_area/delete', [AdminController::class, 'education_area_delete'])->name('education.area.delete');

        // Subjects CRUD
        Route::get('/subjects/index', [AdminController::class, 'subjects_index'])->name('subjects.index');
        Route::post('/subjects', [AdminController::class, 'subjects_store'])->name('subjects.store');
        Route::put('/subjects/{id}', [AdminController::class, 'subjects_update'])->name('subjects.update');
        Route::delete('/subjects/{id}', [AdminController::class, 'subjects_destroy'])->name('subjects.destroy');
        // Subjects Rounds
        Route::get('/create_rounds', [AdminController::class, 'create_rounds'])->name('create.rounds');
        Route::post('/rounds/create', [AdminController::class, 'subjects_rounds_create'])->name('subjects.rounds.create');

        Route::get('/subjects/rounds', [AdminController::class, 'subjects_rounds_index'])->name('subjects.rounds.index');
        Route::get('/subjects/rounds/{roundYear}/{educationAreaId}/{roundNumber}/', [AdminController::class, 'subjects_rounds_show'])->name('subjects.rounds.show');
        Route::get('/subjects/rounds/{roundYear}/{educationAreaId}/{roundNumber}/delete', [AdminController::class, 'subjects_rounds_delete'])->name('subjects.rounds.delete');

        Route::get('/rounds/{roundYear}/{educationAreaId}/{roundNumber}/edit', [AdminController::class, 'subjects_rounds_edit'])->name('subjects.rounds.edit');

        Route::put('/rounds/update', [AdminController::class, 'subjects_rounds_update'])->name('subjects.rounds.update');

        Route::get('/subjects/rounds/next/{year}/{area}/{round}', [AdminController::class, 'subjects_rounds_next'])->name('subjects.rounds.next');

        // Profile
        Route::get('/profile/edit', [AdminController::class, 'profile_edit'])->name('profile.edit');
        Route::post('/profile/update', [AdminController::class, 'profile_update'])->name('profile.update');
        // Change Password
        Route::get('/change-password', [AdminController::class, 'changePassword'])->name('change-password');
        Route::post('/change-password/update', [AdminController::class, 'changePassword_update'])->name('change-password.update');
    });
