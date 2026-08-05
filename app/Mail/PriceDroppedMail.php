<?php

namespace App\Mail;

use App\Models\PriceAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PriceDroppedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $alert;

    public function __construct(PriceAlert $alert)
    {
        $this->alert = $alert;
    }

    public function build()
    {
        return $this->subject('Price Alert: Price Dropped!')
                    ->view('emails.price-dropped');
    }
}