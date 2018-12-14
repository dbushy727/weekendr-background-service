<?php

namespace Weekendr\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TestEmail extends Mailable
{
    protected $data;

    use Queueable, SerializesModels;

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
        $address = 'danny@weekendr.io';
        $subject = 'This is a demo!';
        $name = 'Danny from Weekendr';

        return $this->view('emails.test')
            ->from($address, $name)
            ->replyTo($address, $name)
            ->subject($subject)
            ->with([ 'abc' => $this->data['abc'] ]);
    }
}
