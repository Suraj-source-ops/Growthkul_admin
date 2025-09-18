<?php

namespace App\Listeners;

use App\Events\UserCreation;
use App\Mail\UserCreationEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendUserNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserCreation $event): void
    {
        try {
            $email = env('APP_ENV') == 'local' ? env('customEmail') : $event->user['email'];
            Mail::to($email)->queue(new UserCreationEmail($event->user['name'], $event->user['email'], $event->user['tmpPassword']));
        } catch (\Throwable $th) {
            Log::channel('exception')->warning('SendUserNotification: Mail transport not configured. Skipping UserCreation event, Found error- ' . $th->getMessage());
        }
    }
}
