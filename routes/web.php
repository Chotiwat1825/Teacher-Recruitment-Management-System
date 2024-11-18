<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return redirect()->route('login');
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
        
        // Subjects Rounds
        Route::get('/create_rounds', [AdminController::class, 'create_rounds'])->name('create.rounds');
        Route::post('/rounds/create', [AdminController::class, 'subjects_rounds_create'])->name('subjects.rounds.create');

        // Profile
        Route::get('/profile/edit', [AdminController::class, 'profile_edit'])->name('profile.edit');
        Route::post('/profile/update', [AdminController::class, 'profile_update'])->name('profile.update');
        // Change Password
        Route::get('/change-password', [AdminController::class, 'changePassword'])->name('change-password');
        Route::post('/change-password/update', [AdminController::class, 'changePassword_update'])->name('change-password.update');
    });
