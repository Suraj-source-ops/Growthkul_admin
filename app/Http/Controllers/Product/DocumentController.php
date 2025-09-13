<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\CommentDocument;
use App\Models\Product;
use App\Models\ProductDocuments;
use App\Models\User;
use App\Services\HistoryLogger;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    #uploadProductFiles
    public function uploadProductFiles($userId, $productId = null, $stageId = null, $files, $docIdentifier = null)
    {
        try {
            $uploadSuccess = false;
            $path = '';
            foreach ($files as $key => $file) {
                // $docIdentifier = $file['docIdentifier'];
                // $file = $file['file'];
                $fileName = $docIdentifier . '_' . $file->hashName();
                $fileMimeType = $file->getMimeType();
                if (env('S3_UPLOAD_ENABLED')) {
                    $fileContent = file_get_contents($file);
                    $path = 'file/' . $productId . '/' . $docIdentifier . '/' . $fileName;
                    $uploadSuccess = Storage::disk('s3')->put($path, $fileContent, 'public');
                } else {
                    $path = $file->storeAs('product_documents', $fileName, 'customupload');
                    $uploadSuccess = $path ? true : false;
                }
                if (!$uploadSuccess) {
                    return false;
                }
                ProductDocuments::create(
                    [
                        'user_id' => $userId,
                        'product_id' => $productId ?? null,
                        'stage_id' => $stageId ?? null,
                        'file_name' => $fileName,
                        'mime_type' => $fileMimeType,
                        'doc_type' => $docIdentifier,
                        'file_path' => $path,
                        'is_uploaded' => 1,
                    ]
                );
            }
            return true;
        } catch (Exception $e) {
            Log::channel('exception')->error('uploadProductFiles: ' . $e->getMessage());
            return false;
        }
    }

    #delete multiple files at once
    public function deleteProductFiles($id)
    {
        try {
            $productFiles = ProductDocuments::where('product_id', $id)->get();
            if ($productFiles) {
                if (env('S3_UPLOAD_ENABLED')) {
                    foreach ($productFiles as $key => $file) {
                        if (Storage::disk('s3')->exists($file->file_path)) {
                            Storage::disk('s3')->delete($file->file_path);
                        }
                        // $file->is_deleted = 1;
                        // $file->deleted_by = Auth::user()->id;
                        $file->delete(); //delete one by one
                    }
                } else {
                    foreach ($productFiles as $key => $file) {
                        if (Storage::disk('customupload')->exists($file->file_path)) {
                            Storage::disk('customupload')->delete($file->file_path);
                        }
                        // $file->is_deleted = 1;
                        // $file->deleted_by = Auth::user()->id;
                        $file->delete(); //delete one by one
                    }
                }
            }
        } catch (Exception $e) {
            Log::channel('exception')->error('deleteFiles: ' . $e->getMessage());
            return false;
        }
    }

    #delete single files one by one
    public function deleteFile($docId)
    {
        try {
            $file = ProductDocuments::findOrFail($docId);
            if ($file) {
                if (env('S3_UPLOAD_ENABLED')) {
                    if (Storage::disk('s3')->exists($file->file_path)) {
                        // Storage::disk('s3')->delete($file->file_path);
                        $file->is_deleted = 1;
                        $file->deleted_by = Auth::user()->id;
                    }
                } else {
                    if (Storage::disk('customupload')->exists($file->file_path)) {
                        // Storage::disk('customupload')->delete($file->file_path);
                        $file->is_deleted = 1;
                        $file->deleted_by = Auth::user()->id;
                    }
                }
                $file->save();
                return redirect()->back()->with(['message' => 'File deleted successfully', 'alert-type' => 'success']);
            }
        } catch (Exception $e) {
            Log::channel('exception')->error('deleteFile: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Unable to delete file', 'alert-type' => 'error']);
        }
    }

    #view file
    public function viewFile($docId)
    {
        try {
            $doc = ProductDocuments::findOrFail($docId);
            // For S3 Storage
            if (env('S3_UPLOAD_ENABLED')) {
                $downloadName = $doc->file_name;
                if (Storage::disk('s3')->exists($doc->file_path)) {
                    $temporaryUrl = Storage::disk('s3')->temporaryUrl(
                        $doc->file_path,
                        now()->addMinutes(5),
                        ['ResponseContentDisposition' => 'attachment; filename="' . $downloadName . '"']
                    );
                    return redirect($temporaryUrl);
                } else {
                    return back()->with('error', 'File does not exist on S3.');
                }
            }
            if (Storage::disk('customupload')->exists($doc->file_path)) {
                $downloadName = $doc->file_name;
                return response()->download(
                    Storage::disk('customupload')->path($doc->file_path),
                    $downloadName
                );
            }
            return redirect()->back()->with(['message' => 'File not found', 'alert-type' => 'error']);
        } catch (\Exception $e) {
            Log::channel('exception')->error('viewFile: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Unable to view file', 'alert-type' => 'error']);
        }
    }

    #upload comment file
    public function uploadCommentFile($userId, $productId = null, $commentId = null, $files, $docIdentifier = null)
    {
        try {
            $uploadSuccess = false;
            $path = '';
            foreach ($files as $key => $file) {
                $fileName = $docIdentifier . '_' . $file->hashName();
                $fileMimeType = $file->getMimeType();
                // if (env('S3_UPLOAD_ENABLED')) {
                $fileContent = file_get_contents($file);
                $path = 'file/' . $productId . '/' . $docIdentifier . '/' . $userId . '/' . $fileName;
                $uploadSuccess = Storage::disk('s3')->put($path, $fileContent);
                // } else {
                //     $path = $file->storeAs('product_documents', $fileName, 'customupload');
                //     $uploadSuccess = $path ? true : false;
                // }
                if (!$uploadSuccess) {
                    return false;
                }
                CommentDocument::create(
                    [
                        'user_id' => $userId,
                        'product_id' => $productId ?? null,
                        'comment_id' => $commentId ?? null,
                        'file_name' => $fileName,
                        'mime_type' => $fileMimeType,
                        'doc_type' => $docIdentifier,
                        'file_path' => $path,
                        'is_uploaded' => 1,
                    ]
                );
            }
            return true;
        } catch (Exception $e) {
            Log::channel('exception')->error('uploadCommentFile: ' . $e->getMessage());
            return false;
        }
    }

    #view Comments file
    public function viewCommentFile($docId)
    {
        try {
            $doc = CommentDocument::findOrFail($docId);
            // For S3 Storage
            // if (env('S3_UPLOAD_ENABLED')) {
                if (Storage::disk('s3')->exists($doc->file_path))
                    $url = Storage::disk('s3')->temporaryUrl(
                        $doc->file_path,
                        now()->addMinutes(5),
                        ['ResponseContentDisposition' => 'attachment; filename="' . $doc->file_name . '"']
                    );
                return redirect($url);
            // } else {
            //     return back()->with('error', 'File does not exist on S3.');
            // }
            // if (Storage::disk('customupload')->exists($doc->file_path)) {
            //     $downloadName = $doc->file_name;
            //     return response()->download(
            //         Storage::disk('customupload')->path($doc->file_path),
            //         $downloadName
            //     );
            // }
            return redirect()->back()->with(['message' => 'File not found', 'alert-type' => 'error']);
        } catch (\Exception $e) {
            Log::channel('exception')->error('viewCommentFile: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Unable to view file', 'alert-type' => 'error']);
        }
    }

    #delete multiple files at once
    public function deleteCommentFiles($id)
    {
        try {
            $productFiles = CommentDocument::where('product_id', $id)->get();
            if ($productFiles) {
                if (env('S3_UPLOAD_ENABLED')) {
                    foreach ($productFiles as $key => $file) {
                        if (Storage::disk('s3')->exists($file->file_path)) {
                            Storage::disk('s3')->delete($file->file_path);
                        }
                        $file->is_deleted = 1;
                        $file->deleted_by = Auth::user()->id;
                        // $file->delete();
                        $file->save();
                    }
                } else {
                    foreach ($productFiles as $key => $file) {
                        if (Storage::disk('customupload')->exists($file->file_path)) {
                            Storage::disk('customupload')->delete($file->file_path);
                        }
                        $file->is_deleted = 1;
                        $file->deleted_by = Auth::user()->id;
                        // $file->delete();
                        $file->save();
                    }
                }
            }
        } catch (Exception $e) {
            Log::channel('exception')->error('deleteCommentFiles: ' . $e->getMessage());
            return false;
        }
    }

    #chunck file save
    public function saveProductFileDetails($files, $productId)
    {
        try {
            foreach ($files as $key => $data) {
                $productFile = ProductDocuments::findOrFail($data->file_id);
                $productFile->product_id = $productId ?? null;
                $productFile->save();
            }
            return true;
        } catch (\Throwable $e) {
            Log::channel('exception')->error('saveProductFileDetails: ' . $e->getMessage());
            return false;
        }
    }
}
