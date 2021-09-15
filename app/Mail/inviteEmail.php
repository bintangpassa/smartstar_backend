<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class inviteEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->view('view.name');
        return $this->from('bintangpassa@smartstar.com')
            ->view('email.inviteEmail')
            ->with(
            [
                'email' => $this->data['email']??null,
                'role'  => $this->data['role']??null,
                'password'   => $this->data['password']??null,
                'sch'   => $this->data['sch']??null,
                'schid'   => $this->data['schid']??null
            ]);
    }
}
