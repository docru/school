<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Hooks\ApproveController;

// Утверждение
Route::any('approve', [ApproveController::class, 'handler'])->name('approve');
