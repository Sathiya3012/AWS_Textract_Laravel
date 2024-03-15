<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TextractController;

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

Route::get('/', function () {
    return view('welcome');
});

// Route::get('textract/detect-text', [TextractController::class, 'detectText']);

Route::get('/upload-form', [TextractController::class, 'showUploadForm'])->name('upload.form');
Route::post('/extract-text', [TextractController::class, 'extractText'])->name('text.extract');

