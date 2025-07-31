<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConfirmationMail;
use App\Models\EmailBatch; // Import EmailBatch model
use Illuminate\Support\Facades\Log; // For logging

class BatchSendBulkEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $recipientEmail;
    protected $emailData;
    protected $emailBatchId; // New property to hold the batch ID

    /**
     * Create a new job instance.
     *
     * @param string $recipientEmail
     * @param array $emailData
     * @param int $emailBatchId // Accept batch ID in constructor
     * @return void
     */
    public function __construct(string $recipientEmail, array $emailData, int $emailBatchId)
    {
        $this->recipientEmail = $recipientEmail;
        $this->emailData = $emailData;
        $this->emailBatchId = $emailBatchId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            Mail::to($this->recipientEmail)->send(new ConfirmationMail($this->emailData));

            // Increment sent_count for the batch
            EmailBatch::where('id', $this->emailBatchId)->increment('sent_count');

            Log::info("Email sent to {$this->recipientEmail} for batch {$this->emailBatchId}");

            // Check if batch is completed after incrementing
            $batch = EmailBatch::find($this->emailBatchId);
            if ($batch && $batch->sent_count + $batch->failed_count >= $batch->total_recipients) {
                $batch->update(['status' => 'completed']);
                Log::info("Email batch {$this->emailBatchId} completed.");
            }

        } catch (\Exception $e) {
            // Increment failed_count for the batch if an error occurs
            EmailBatch::where('id', $this->emailBatchId)->increment('failed_count');
            Log::error("Failed to send email to {$this->recipientEmail} for batch {$this->emailBatchId}: " . $e->getMessage());

            // Re-throw the exception so Laravel's queue system can handle retries/failed_jobs
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     * This method is called if the job fails after all retries.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        // If you want to decrement sent_count or update status specifically on final failure,
        // you might do it here. However, `failed_count` is already incremented in handle().
        Log::error("Job for batch {$this->emailBatchId} failed permanently: " . $exception->getMessage());
        $batch = EmailBatch::find($this->emailBatchId);
        if ($batch && $batch->status !== 'completed') { // Only mark as failed if not already completed
             $batch->update(['status' => 'failed']);
        }
    }
}