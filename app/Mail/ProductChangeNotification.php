<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProductChangeNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $product;
    public $changes;
    public $action;
    public $customMessage;


    /**
     * Create a new message instance.
     */
    public function __construct(Product $product, array $changes, string $action, string $message)
    {
        $this->product = $product;
        $this->changes = $changes;
        $this->action = $action;
        $this->customMessage = $message;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Growthkul - Product Change Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'Mail.product-change-notification-template',
            with: [
                'product' => $this->product,
                'changes' => $this->changes,
                'action' => $this->action,
                'customMessage' => $this->customMessage,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
