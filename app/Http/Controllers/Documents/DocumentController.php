<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Models\Files;
use Exception;
use Illuminate\Support\Facades\Log;
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
            $CustomFilePath = '';
            foreach ($files as $key => $file) {
                $docIdentifier = $file['docIdentifier'];
                $file = $file['file'];
                $fileName = $docIdentifier . '_' . $file->hashName();
                $fileMimeType = $file->getMimeType();
                $CustomFilePath = $file->storeAs('uploaded_files', $fileName, 'customupload');
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
                if (Storage::disk('customupload')->exists($profileImage->file_path)) {
                    Storage::disk('customupload')->delete($profileImage->file_path);
                }
                $profileImage->delete();
                return true;
            }
        } catch (Exception $e) {
            Log::channel('exception')->error('deleteCommentFiles: ' . $e->getMessage());
            return false;
        }
    }
}
