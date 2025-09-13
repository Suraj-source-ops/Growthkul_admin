<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProductAssignedMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $productDetail;
    public $currentUser;
    public $message;
    /**
     * Create a new message instance.
     */
    public function __construct($user, $productDetail, $currentUser, $message)
    {
        $this->user = $user;
        $this->productDetail = $productDetail;
        $this->currentUser = $currentUser;
        $this->message = $message;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Letune - Product Assigned Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'Mail.product-assign-template',
            with: [
                'user' => $this->user,
                'product' => $this->productDetail,
                'currentUser' => $this->currentUser,
                'customMessage' => $this->message,
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
