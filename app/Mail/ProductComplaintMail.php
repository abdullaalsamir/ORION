<?php

namespace App\Mail;

use App\Models\ProductComplaint;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProductComplaintMail extends Mailable
{
    use Queueable, SerializesModels;

    public $complaint;

    public function __construct(ProductComplaint $complaint)
    {
        $this->complaint = $complaint;
    }

    public function build()
    {
        return $this->subject('New Product Complaint Received - ' . $this->complaint->product_name)
            ->view('emails.product-complaint');
    }
}