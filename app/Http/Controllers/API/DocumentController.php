<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductDocuments;
use App\Models\ProductTracking;
use App\Models\User;
use App\Services\HistoryLogger;
use Aws\S3\S3Client;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\Catch_;

class DocumentController extends Controller
{
    #initialize s3
    public function S3Client()
    {
        return new S3Client([
            'region' => env('AWS_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    public function uploadChunk(Request $request)
    {
        try {
            $request->validate([
                'chunk' => 'required|file',
                'upload_id' => 'required|string',
                'chunk_index' => 'required|integer',
            ]);

            $uploadId = $request->upload_id;
            $index = $request->chunk_index;

            $chunkPath = storage_path("app/tmp_uploads/{$uploadId}");
            if (!File::isDirectory($chunkPath)) {
                File::makeDirectory($chunkPath, 0775, true);
            }
            $request->file('chunk')->move($chunkPath, "chunk_{$index}");
            return response()->json(['status' => true, 'message' => "Chunk $index uploaded", 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::channel('exception')->error('uploadChunk: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'alert-type' => 'error']);
        }
    }

    public function mergeChunks(Request $request)
    {
        try {
            set_time_limit(300);
            $request->validate([
                'upload_id'     => 'required|string',
                'filename'      => 'required|string',
                'total_chunks'  => 'required|integer',
                'product_id'    => 'nullable|integer',
                'stage_id'      => 'nullable|integer',
                'mime_type'     => 'nullable|string',
                'identifier'    => 'nullable|string',
            ]);
            $userId = $request->user_id;
            $uploadId    = $request->upload_id;
            $fileName    = $request->filename;
            $totalChunks = $request->total_chunks;
            $productId   = $request->product_id;
            $stageId     = $request->stage_id ?? null;
            $fileMimeType = $request->mime_type ?? 'application/octet-stream';
            $docIdentifier = $request->identifier;
            $chunkDir = storage_path("app/tmp_uploads/{$uploadId}");
            if (!File::isDirectory($chunkDir)) {
                return response()->json(['status' => false, 'message' => 'Chunks not found'], 404);
            }
            $mergedPath = storage_path("app/tmp_uploads/{$uploadId}_merged.tmp");
            $output = fopen($mergedPath, 'wb');
            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkFile = "{$chunkDir}/chunk_{$i}";
                if (!file_exists($chunkFile)) {
                    fclose($output);
                    return response()->json(['status' => false, 'message' => "Missing chunk $i"], 400);
                }
                fwrite($output, file_get_contents($chunkFile));
            }
            fclose($output);
            // Upload to S3
            $uniqueName = uniqid($docIdentifier . '_') . '_' . preg_replace('/[^A-Za-z0-9_\.\-]/', '_', $fileName);
            $s3Path = "product_documents/{$uniqueName}";
            Storage::disk('s3')->put($s3Path, fopen($mergedPath, 'r+'));
            // Clean up temporary files
            File::deleteDirectory($chunkDir);
            File::delete($mergedPath);
            $s3Url = Storage::url($s3Path);
            // Save in database
            $productDoc = ProductDocuments::create([
                'user_id'     => $userId,
                'product_id'  => $productId,
                'stage_id'    => $stageId ?? null,
                'file_name'   => $fileName,
                'mime_type'   => $fileMimeType,
                'doc_type'    => $docIdentifier,
                'file_path'   => $s3Path,
                'is_uploaded' => 1,
            ]);

            if ($productDoc && !empty($productId) && !empty($userId)) {
                $productDetails = Product::with('stages')->where('id', $productId)->first();
                $userName = User::select('name')->where('id', $userId)->first();
                if (!empty($stageId)) {
                    $productStageName = ProductTracking::select('product_stage')->where('id',$stageId)->first();
                    $stageName =  isset($productStageName) ? $productStageName->product_stage : '';
                    if (!empty($stageName)) {
                        $comment = 'New File Uploaded in product stage ' . $stageName . ' by the user: ' . $userName->name . ' for the product: ' . $productDetails->product_code;
                    } else {
                        $comment = 'New File Uploaded in product stage by the user: ' . $userName->name . ' for the product: ' . $productDetails->product_code;
                    }
                } else {
                    $comment = 'New File Uploaded by the user: ' . $userName->name . ' for the product: ' . $productDetails->product_code;
                }
                $changes = [
                    'file_name' => $fileName,
                    'file_type' => $fileMimeType,
                    'product_type' => $productDetails->product_type == 1 ? 'Size Chart' : 'Tech Pack',
                ];
                HistoryLogger::historyLog(
                    $productDetails,
                    'Product File Upload',
                    $changes,
                    $userId,
                    $comment,
                );
            }

            return response()->json([
                'status'  => true,
                'message' => 'Upload complete',
                'url'     => $s3Path,
                'docId' =>  $productDoc->id ?? '',
            ]);
        } catch (Exception $e) {
            Log::channel('exception')->error('mergeChunks: ' . $e->getMessage());
            return response()->json(['status'  => false, 'message' => 'Upload Failed']);
        }
    }

    public function cancelUpload(Request $request)
    {
        $request->validate([
            'upload_id' => 'required|string',
        ]);
        $uploadId = $request->upload_id;
        $chunkDir = storage_path("app/tmp_uploads/{$uploadId}");
        $mergedFile = storage_path("app/tmp_uploads/{$uploadId}_merged.tmp");
        try {
            // Delete all chunk files
            if (File::exists($chunkDir)) {
                $files = File::files($chunkDir);
                foreach ($files as $file) {
                    File::delete($file->getPathname());
                }
                File::deleteDirectory($chunkDir);
            }
            // Delete merged file if created
            if (File::exists($mergedFile)) {
                File::delete($mergedFile);
            }

            return response()->json([
                'status' => true,
                'message' => 'Upload canceled and temporary files removed'
            ]);
        } catch (\Exception $e) {
            Log::channel('exception')->error("cancelUpload : " . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to cancel upload'], 500);
        }
    }

    public function deleteFileApi(Request $request)
    {
        try {
            $file = ProductDocuments::findOrFail($request->docId);

            if ($file) {
                if (Storage::disk('s3')->exists($file->file_path)) {
                    $file->is_deleted = 1;
                    $file->deleted_by = $request->user_id;
                    if ($file && !empty($file->product_id) && !empty($file->user_id)) {
                        $productDetails = Product::where('id', $file->product_id)->first();
                        $userName = User::select('name')->where('id', $file->user_id)->first();
                        $changes = [
                            'file_name' => $file->file_name,
                            'file_type' => $file->mime_type,
                            'product_type' => $productDetails->product_type == 1 ? 'Size Chart' : 'Tech Pack',
                        ];
                        HistoryLogger::historyLog(
                            $productDetails,
                            'Delete Product File',
                            $changes,
                            $file->user_id,
                            'File Deleted by the user: ' . $userName->name . ' for the product: ' . $productDetails->product_code,
                        );
                    }
                }
                $file->save();
                return response()->json(['status' => true, 'message' => 'File deleted successfully', 'alert-type' => 'success'], 200);
            }
        } catch (Exception $e) {
            Log::channel('exception')->error('deleteFile: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Unable to delete file', 'alert-type' => 'error'], 500);
        }
    }
}
