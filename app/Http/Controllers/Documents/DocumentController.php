<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\Files;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    #uploadFiles
    public function uploadFiles($userId, $files)
    {
        try {
            $response  = [
                'success' => false,
                'message' => 'unable to upload file',
                'file_id' => '',
                'status_code' => 400
            ];
            $s3fileuploadresponse = false;
            $CustomFilePath = '';
            $disk = env('S3_UPLOAD_ENABLED') ? 's3' : 'customupload';
            foreach ($files as $key => $file) {
                $docIdentifier = $file['docIdentifier'];
                $productId = isset($file['product_id']) && $file['product_id'] != '' ? $file['product_id'] : '';
                $file = $file['file'];
                $fileName = $docIdentifier . '_' . $file->hashName();
                $fileMimeType = $file->getMimeType();
                // if (env('S3_UPLOAD_ENABLED') == true) {
                //     $fileContent = file_get_contents($file);
                //     if (isset($productId) && $productId != '' && isset($docIdentifier) && $docIdentifier != '') {
                //         $path = 'file/' . $productId . '/' . $docIdentifier . '/' . $fileName;
                //     } else {
                //         $path = 'file/' . $userId . '/' . $docIdentifier . '/' . $fileName;
                //     }
                //     $s3fileuploadresponse = Storage::disk('s3')->put($path, $fileContent, 'public');
                // } else {
                $CustomFilePath = $file->storeAs('profile_pics', $fileName, 'customupload');
                // }

                // if (($s3fileuploadresponse && env('S3_UPLOAD_ENABLED') == true) || (isset($CustomFilePath) && env('S3_UPLOAD_ENABLED') == false)) {
                $createdFile = Files::create(
                    [
                        'user_id' => $userId,
                        'file_name' => $fileName,
                        'mime_type' => $fileMimeType,
                        'doc_type' => $docIdentifier,
                        'file_path' => $CustomFilePath,
                        'is_uploaded' => 1,
                    ]
                );
                $response = [
                    'success' => true,
                    'file_name' => $fileName,
                    'file_id' => $createdFile->id,
                    'message' => 'file uploaded successfully',
                    'status_code' => 200,
                ];
                // } else {
                //     $response;
                // }
            }
            return $response;
        } catch (Exception $e) {
            Log::channel('exception')->error('uploadFiles: ' . $e->getMessage());
            return $e->getMessage();
        }
    }

    public function deleteFile($id)
    {
        try {
            $profileImage = Files::where('id', $id)->first();
            if ($profileImage) {
                // if (env('S3_UPLOAD_ENABLED')) {
                //     if (Storage::disk('s3')->exists($profileImage->file_path)) {
                //         Storage::disk('s3')->delete($profileImage->file_path);
                //     }
                //     $profileImage->delete();
                //     return true;
                // } else {
                if (Storage::disk('customupload')->exists($profileImage->file_path)) {
                    Storage::disk('customupload')->delete($profileImage->file_path);
                }
                $profileImage->delete();
                return true;
                // }
            }
        } catch (Exception $e) {
            Log::channel('exception')->error('deleteCommentFiles: ' . $e->getMessage());
            return false;
        }
    }
}
