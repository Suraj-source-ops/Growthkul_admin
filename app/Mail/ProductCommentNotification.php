<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProductCommentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $product;
    public $changes;
    public $action;
    public $notes;
    public $commentedBy;
    /**
     * Create a new message instance.
     */
    public function __construct($product, $changes, $action, $notes, $userName)
    {
        $this->product = $product;
        $this->changes = $changes;
        $this->action = $action;
        $this->notes = $notes;
        $this->commentedBy = $userName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Product Comment Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'Mail.product-comment-notification',
            with: [
                'product' => $this->product,
                'action' =>  $this->action,
                'changes' => $this->changes,
                'notes' => $this->notes,
                'commentedBy' => $this->commentedBy,
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
