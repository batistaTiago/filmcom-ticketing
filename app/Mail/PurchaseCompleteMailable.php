<?php

namespace App\Mail;

use App\Domain\DTO\Cart\CartDTO;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PurchaseCompleteMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private readonly CartDTO $cart_state)
    { }

    public function build()
    {
        return $this->subject('Your Email Subject')
            ->view('emails.purchase_complete', ['cart_state' => $this->cart_state]);
    }
}
