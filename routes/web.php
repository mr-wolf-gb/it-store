<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SupportResourceController as AdminSupportResourceController;
use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResourceCatalogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ResourceCatalogController::class, 'index'])->name('library.index');
Route::get('/resources/{supportResource}/download', [ResourceCatalogController::class, 'download'])
    ->name('library.download');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function (): void {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('admin/resources')
        ->name('admin.resources.')
        ->middleware('can:resources.manage')
        ->group(function (): void {
            Route::get('/', [AdminSupportResourceController::class, 'index'])->name('index');
            Route::get('/create', [AdminSupportResourceController::class, 'create'])->name('create');
            Route::post('/', [AdminSupportResourceController::class, 'store'])->name('store');
            Route::get('/{supportResource}/edit', [AdminSupportResourceController::class, 'edit'])->name('edit');
            Route::put('/{supportResource}', [AdminSupportResourceController::class, 'update'])->name('update');
            Route::delete('/{supportResource}', [AdminSupportResourceController::class, 'destroy'])->name('destroy');
        });

    Route::prefix('admin/roles')
        ->name('admin.roles.')
        ->middleware('can:roles.manage')
        ->group(function (): void {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::get('/create', [RoleController::class, 'create'])->name('create');
            Route::post('/', [RoleController::class, 'store'])->name('store');
            Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
            Route::put('/{role}', [RoleController::class, 'update'])->name('update');
            Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        });

    Route::prefix('admin/user-roles')
        ->name('admin.user-roles.')
        ->middleware('can:users.manage_roles')
        ->group(function (): void {
            Route::get('/', [UserRoleController::class, 'index'])->name('index');
            Route::get('/{user}/edit', [UserRoleController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserRoleController::class, 'update'])->name('update');
        });
});

require __DIR__.'/auth.php';
