<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail; // Import Mail facade
use App\Mail\ConfirmationMail; // Import your Mailable

class SendBulkEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $recipientEmail;
    protected $emailData;

    /**
     * Create a new job instance.
     *
     * @param string $recipientEmail
     * @param array $emailData
     * @return void
     */
    public function __construct(string $recipientEmail, array $emailData)
    {
        $this->recipientEmail = $recipientEmail;
        $this->emailData = $emailData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        // Send the email using your Mailable
        Mail::to($this->recipientEmail)->send(new ConfirmationMail($this->emailData));
    }

    // Optional: Define properties for retries, timeout, etc.
    // public $tries = 3;
    // public $timeout = 120; // seconds
}