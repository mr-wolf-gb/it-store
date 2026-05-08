<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SupportResourceController as AdminSupportResourceController;
use App\Http\Controllers\Admin\ResourceTaxonomyController;
use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResourceCatalogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ResourceCatalogController::class, 'index'])->name('library.index');
Route::get('/resources/{supportResource}/download', [ResourceCatalogController::class, 'download'])
    ->middleware('throttle:downloads')
    ->name('library.download');
Route::get('/resources/{supportResource}/files/{resourceFile}/download', [ResourceCatalogController::class, 'downloadFile'])
    ->middleware('throttle:downloads')
    ->name('library.files.download');
Route::get('/resources/{supportResource}/files/download-all', [ResourceCatalogController::class, 'downloadAllFiles'])
    ->middleware('throttle:downloads')
    ->name('library.files.download-all');
Route::post('/locale', [LocaleController::class, 'update'])->name('locale.update');

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

    Route::prefix('admin/taxonomy')
        ->name('admin.taxonomy.')
        ->middleware('can:resources.manage')
        ->group(function (): void {
            Route::get('/', [ResourceTaxonomyController::class, 'index'])->name('index');
            Route::post('/categories', [ResourceTaxonomyController::class, 'storeCategory'])->name('categories.store');
            Route::delete('/categories/{category}', [ResourceTaxonomyController::class, 'destroyCategory'])->name('categories.destroy');
            Route::post('/tags', [ResourceTaxonomyController::class, 'storeTag'])->name('tags.store');
            Route::delete('/tags/{tag}', [ResourceTaxonomyController::class, 'destroyTag'])->name('tags.destroy');
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
            Route::get('/create', [UserRoleController::class, 'create'])->name('create');
            Route::post('/', [UserRoleController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserRoleController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserRoleController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserRoleController::class, 'destroy'])->name('destroy');
        });

});

require __DIR__.'/auth.php';
