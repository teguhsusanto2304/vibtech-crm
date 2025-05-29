<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Client; // Assuming you have a Client model
use Carbon\Carbon; // For date manipulation
use Illuminate\Support\Facades\Log;

class DeleteClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-client';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Define the duration for old data.
        // For example, delete data older than 1 year (365 days).
        $thresholdDate = Carbon::now()->subDays(365); // Adjust this as needed

        $this->info("Attempting to hard delete client data older than: {$thresholdDate->toDateString()}");

        try {
            // Find clients to delete (e.g., based on 'created_at' timestamp)
            $deletedCount = Client::where('data_status', 0)
                                  ->Delete(); // Use forceDelete for hard deletion if using soft deletes

            // If you are NOT using soft deletes, just use delete()
            // $deletedCount = Client::where('created_at', '<', $thresholdDate)->delete();

            $this->info("Successfully hard deleted {$deletedCount} client records.");

            // Optional: Log the success
            Log::info("Scheduler: Hard deleted {$deletedCount} old client records.");

        } catch (\Exception $e) {
            $this->error("Error hard deleting client data: " . $e->getMessage());

            // Optional: Log the error
            Log::error("Scheduler Error: Failed to hard delete old client data. " . $e->getMessage());
        }
    }
}
