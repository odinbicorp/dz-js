<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DropzoneController;
use App\Http\Controllers\MediaLibraryController;
use App\Http\Controllers\UploaderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [DropzoneController::class,'index']);
Route::post('/', [DropzoneController::class,'store'])->name('store');
Route::post('uploads', [DropzoneController::class,'uploads'])->name('uploads');

Route::post('/delete-chunk', [DropzoneController::class,'deleteChunk'])->name('deleteChunk');
Route::post('/cancel-upload', [DropzoneController::class,'cancelUpload'])->name('cancelUpload'); 

//Media library routes
Route::get('/medialibrary', [MediaLibraryController::class, 'mediaLibrary'])->name('media-library');

//FILE UPLOADS CONTROLER
Route::post('medialibrary/upload', [UploaderController::class, 'upload'])->name('file-upload');
Route::post('medialibrary/delete', [UploaderController::class, 'delete'])->name('file-delete');