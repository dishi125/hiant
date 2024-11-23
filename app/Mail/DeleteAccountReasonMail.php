<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeleteAccountReasonMail extends Mailable
{
    use Queueable, SerializesModels;
    private $mailContent;
    private $subjectTitle;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailContent)
    {
        $this->subjectTitle = "[Juju] Delete Account";
        $this->mailContent = $mailContent;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.reason-mail', ['subjectTitle' => $this->subjectTitle, 'mailContent' => $this->mailContent])->subject($this->subjectTitle);
    }
}
