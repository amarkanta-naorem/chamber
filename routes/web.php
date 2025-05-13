<?php

use App\Http\Controllers\ChamberController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', [ChamberController::class, 'index'])->name('chambers.index');
Route::get('/chambers/export', [ChamberController::class, 'export'])->name('chambers.export');
Route::get('/chambers/export-missing', [ChamberController::class, 'exportMissingData'])->name('chambers.exportMissing');