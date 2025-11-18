<?php

use App\Http\Controllers\NoteController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/notes')->name('home');

Route::prefix('notes')->group(function () {
    Route::match(['get', 'post'], '', [NoteController::class, 'index'])->name('notes_index');
    Route::match(['get', 'post'], '{id}/edit', [NoteController::class, 'edit'])->name('notes_edit');
    Route::post('{id}/delete', [NoteController::class, 'delete'])->name('notes_delete');
    Route::post('mass-delete', [NoteController::class, 'massDelete'])->name('notes_mass_delete');
});
