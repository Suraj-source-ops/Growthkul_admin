<?php

use App\Http\Controllers\API\DocumentController;
use Illuminate\Support\Facades\Route;


#chunk upload
Route::post('/upload-chunk', [DocumentController::class, 'uploadChunk'])->name('upload.chunk');
Route::post('/merge-chunks', [DocumentController::class, 'mergeChunks'])->name('merge.chunk');
Route::post('/cancel-upload', [DocumentController::class, 'cancelUpload']);
Route::get('/delete-product-file/{user_id}/{docId}', [DocumentController::class, 'deleteFileApi']);
