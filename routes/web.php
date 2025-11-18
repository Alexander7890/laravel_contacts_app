<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::redirect('/', '/contacts');

Route::prefix('contacts')->group(function () {
    Route::get('', [ContactController::class, 'index'])->name('contacts_index');
    Route::match(['get', 'post'], 'new', [ContactController::class, 'create'])->name('contacts_new');
    Route::match(['get', 'post'], '{id}/edit', [ContactController::class, 'edit'])->name('contacts_edit');
    Route::post('{id}/delete', [ContactController::class, 'delete'])->name('contacts_delete');
});
