<?php

use App\Http\Controllers\LabController as Lab;
use App\Http\Middleware\ActiveUser;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', ActiveUser::class])->group(function () {
    Route::get('/', [Lab::class, 'dashboard'])->name('dashboard');
    Route::get('/samples', [Lab::class, 'samples'])->name('samples.index');
    Route::post('/samples', [Lab::class, 'saveSample'])->name('samples.store');
    Route::get('/samples/export', [Lab::class, 'export'])->name('samples.export');
    Route::get('/samples/{id}', [Lab::class, 'sample'])->whereNumber('id')->name('samples.show');
    Route::post('/samples/{id}', [Lab::class, 'saveSample'])->whereNumber('id')->name('samples.update');
    Route::get('/samples/{id}/document', [Lab::class, 'downloadDocument'])->whereNumber('id')->name('samples.document.download');
    Route::get('/validations', [Lab::class, 'validations'])->name('validations.index');
    Route::post('/validations', [Lab::class, 'saveValidation'])->name('validations.store');
    Route::get('/materials/{id}/reference', [Lab::class, 'downloadReference'])->whereNumber('id')->name('materials.reference.download');
    Route::get('/materials', [Lab::class, 'materials'])->name('materials.index');
    Route::post('/materials', [Lab::class, 'saveMaterial'])->name('materials.store');
    Route::post('/materials/{id}', [Lab::class, 'saveMaterial'])->whereNumber('id')->name('materials.update');
    Route::get('/users', [Lab::class, 'users'])->name('users.index');
    Route::post('/users', [Lab::class, 'saveUser'])->name('users.store');
    Route::post('/users/{id}', [Lab::class, 'saveUser'])->whereNumber('id')->name('users.update');
    Route::post('/users/{id}/delete', [Lab::class, 'deleteUser'])->whereNumber('id')->name('users.destroy');
});
