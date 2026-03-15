<?php

namespace App\Mail;

use App\Models\Connect;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConnectMail extends Mailable
{
    use Queueable, SerializesModels;

    public $query;

    public function __construct(Connect $query)
    {
        $this->query = $query;
    }

    public function build()
    {
        return $this->subject('New Query Received: ' . Str::title($this->query->subject))
            ->view('emails.connect');
    }
}