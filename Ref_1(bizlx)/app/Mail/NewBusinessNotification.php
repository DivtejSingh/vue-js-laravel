<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewBusinessNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $business; // Declare public variable

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($business)
    {
        $this->business = $business; // Assign variable
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('no-reply@bizlx.com', 'Bizlx')
                    ->subject('New Business Notification')
                    ->view('emails.newBusinessNotification')
                    ->with(['business' => $this->business]);
    }
    
}
