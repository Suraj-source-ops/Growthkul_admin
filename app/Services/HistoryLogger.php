<?php

namespace App\Services;

use App\Mail\ProductChangeNotification;
use App\Mail\ProductCommentNotification;
use App\Models\History;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class HistoryLogger
{

    /**
     * Log any action to the product history and notify assigned members.
     */
    public static function historyLog(Product $product, string $action, array $changes = [], ?int $userId = null, string $notes)
    {
        try {
            $userId = $userId ?? Auth::user()->id;
            History::create([
                'product_id' => $product->id,
                'user_id' => $userId,
                'action' => $action,
                'changes' => $changes,
                'note' => $notes,
            ]);

            // Send emails to assigned members
            $delayMinute = Carbon::now()->addMinutes(env('S3_UPLOAD_ENABLED'), 2);
            $assignedMembers = $product->assignedMembers()->get();
            foreach ($assignedMembers as $member) {
                // Mail::to($member->email)->send(new ProductChangeNotification($product, $changes, $action, $notes));
                Mail::to($member->email)->queue(new ProductChangeNotification($product, $changes, $action, $notes, $userId));
            }
        } catch (\Throwable $th) {
            Log::channel('exception')->error('historyLog: ' . $th->getMessage());
            return false;
        }
    }


    public static function commentHistoryLog(Product $product, string $action, array $changes = [], ?int $userId = null, string $notes, string $userName)
    {
        try {
            $userId = $userId ?? Auth::user()->id;
            History::create([
                'product_id' => $product->id,
                'user_id' => $userId,
                'action' => $action,
                'changes' => $changes,
                'note' => $notes,
            ]);

            // Send emails to assigned members
            $assignedMembers = $product->assignedMembers()->get();
            foreach ($assignedMembers as $member) {
                Mail::to($member->email)->queue(new ProductCommentNotification($product, $changes, $action, $notes, $userName));
            }
        } catch (\Throwable $th) {
            Log::channel('exception')->error('historyLog: ' . $th->getMessage());
            return false;
        }
    }
}
