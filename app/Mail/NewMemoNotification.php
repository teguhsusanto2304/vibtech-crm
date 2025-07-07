<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewMemoNotification extends Mailable
{
    use Queueable, SerializesModels;
    public $post;

    /**
     * Create a new message instance.
     */
    public function __construct($post)
    {
        $this->post = $post;
    }

    public function build()
    {
        if($this->post['method']=='insert'){
            $method ='New';
        } else {
            $method = 'Updated';
        }
        return $this->from(env('MAIL_FROM_ADDRESS'))
            ->subject('Vibtech Genesis Staff Portal :: '.$method.' Memo')
            ->view('emails.new_memo')
            ->with(['post' => $this->post]);
    }
}
