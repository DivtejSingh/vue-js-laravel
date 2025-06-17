<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewAgentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $reseller; // Declare public variable

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($reseller)
    {
        $this->reseller = $reseller; // Assign variable
    }

    /**
     * Build the message.
     *
     * @return $this
     */

    public function build()
{
    return $this->from('no-reply@bizlx.com', 'Bizlx')
                ->subject('New Agent Notification')
                ->view('emails.newAgentNotification')
                ->with(['reseller' => $this->reseller]);
}

}
