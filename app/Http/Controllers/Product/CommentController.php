<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Product;
use App\Models\User;
use App\Services\HistoryLogger;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class CommentController extends Controller
{
    public function postComment(CommentRequest $request)
    {
        DB::beginTransaction();
        try {
            $commentData = [
                'product_id' => $request->productId,
                'product_type' => $request->product_type,
                'comment' => $request->comment,
                'comment_by' => Auth::user()->id,
            ];
            //create product
            $commentCreated = Comment::create($commentData);
            if (!$commentCreated) {
                DB::rollBack();
                return response()->json(['status' => false, 'message' => 'Failed to add comment', 'alert-type' => 'error']);
            }
            // Upload product files
            $hasAttachments = false;
            if ($request->hasFile('comment_files')) {
                $fileUploadController = new DocumentController();
                $uploadSuccess = $fileUploadController->uploadCommentFile(Auth::user()->id, $request->productId, $commentCreated->id, $request->file('comment_files', []), 'commentfiles');
                if (!$uploadSuccess) {
                    DB::rollBack();
                    return response()->json(['status' => false, 'message' => 'Failed to upload comment files', 'alert-type' => 'error']);
                }
                $hasAttachments = true;
            }
            DB::commit();
            $comment = $commentCreated->fresh(['user']);
            $user = Auth::user()->name;
            #comment notification
            $product = Product::with('assignedMembers')->find($request->productId);
            $note = "{$user} posted a new comment on product " . $product->product_code . ($hasAttachments ? ' with attachments.' : '.');
            $changes = [
                'comment' => [
                    'text' => $request->comment,
                    'by' => $user,
                    'attachments' => $hasAttachments,
                ],
            ];

            HistoryLogger::commentHistoryLog(
                $product,
                'comment',
                $changes,
                Auth::user()->id,
                $note,
                $user
            );
            #end comment notification

            # Render HTML partial of new comment
            $commentHtml = view('products.viewpartials.commentAndHistory.comment.single-comment', compact('comment', 'user'))->render();
            return response()->json([
                'status' => true,
                'html' => $commentHtml,
                'message' => 'Comment added successfully!'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::channel('exception')->error('postComment: ' . $e->getMessage() . '| With The Request Details: ' . json_encode($request->all()));
            return response()->json(['status' => false, 'message' => 'Failed to add comment', 'alert-type' => 'error']);
        }
    }
}
