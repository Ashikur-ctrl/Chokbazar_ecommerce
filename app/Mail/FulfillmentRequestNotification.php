<?php

namespace App\Mail;

use App\Models\FulfillmentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FulfillmentRequestNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public FulfillmentRequest $fulfillmentRequest;

    public function __construct(FulfillmentRequest $fulfillmentRequest)
    {
        $this->fulfillmentRequest = $fulfillmentRequest;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Fulfillment Request - ' . $this->fulfillmentRequest->fulfillment_request_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.fulfillment-request',
            with: [
                'fulfillmentRequest' => $this->fulfillmentRequest,
                'seller' => $this->fulfillmentRequest->seller,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
