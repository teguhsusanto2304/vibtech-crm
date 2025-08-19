<?php

namespace App\Services;
use App\Models\Country;
use App\Models\IndustryCategory;
use App\Models\ProductCategory;
use App\Models\User;
use App\Models\KanbanStage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CommonService
{
    /**
     * Marks specific client-database list notifications as read for the authenticated user.
     *
     * @return int The number of notifications marked as read.
     */
    public function getSortedCountries()
    {
            $countries = Country::all();

            // 2. Separate Singapore from the rest
            $singapore = $countries->filter(function ($country) {
                return strtolower($country->name) === 'singapore';
            })->first(); // Get the first (and hopefully only) Singapore entry

            // 3. Get the remaining countries (excluding Singapore)
            $otherCountries = $countries->filter(function ($country) use ($singapore) {
                return $singapore ? $country->id !== $singapore->id : true; // Exclude if Singapore was found
            })->sortBy('name'); // Sort the remaining countries alphabetically by name

            // 4. Combine them: Singapore first, then other sorted countries
            if ($singapore) {
                $sortedCountries = collect([$singapore])->merge($otherCountries);
            } else {
                // If Singapore isn't found, just use the sorted list of all countries
                $sortedCountries = $otherCountries; // $otherCountries is already sorted and contains all if Singapore wasn't found
            }
            return $sortedCountries;
    }

    public function getIndustryCategories()
    {
        return IndustryCategory::orderBy('name','ASC')->get();
    }

    public function getProductCategories()
    {
        return ProductCategory::where('data_status',1)->orderBy('name','ASC')->get();
    }

    public function getUsers()
    {
        return User::where('user_status', 1)->orderBy('name','ASC')->get();
    }

    public function getKanbanStages()
    {
        return KanbanStage::orderBy('id','asc')->get();
    }

    /**
     * Dynamically send any email.
     *
     * @param array $data          // Example: ['email' => 'recipient@example.com']
     * @param string $mailableClass // Example: App\Mail\WelcomeMail::class
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEmail($data, $mailableClass)
    {
        try {
            // Dynamically create the Mailable instance
            // It's generally safer to check if $data['email'] exists before using it
            if (!isset($data['email'])) {
                Log::error('Email data missing "email" key.', ['data' => $data]);
                return response()->json(['message' => 'Error: Recipient email is missing.'], 400);
            }

            Mail::to($data['email'])->later(now()->addMinutes(5),new $mailableClass($data));
            // Or if you prefer to send immediately (though queue is usually better for emails)
            //Mail::to($data['email'])->send(new $mailableClass($data));

            // 2. Log successful queuing/sending
            Log::info('Email successfully queued/sent.', [
                'recipient' => $data['email'],
                'mailable_class' => $mailableClass
            ]);

            return response()->json(['message' => 'Email has been sent successfully.']);

        } catch (\Exception $e) {
            // 3. Log any exceptions that occur during the process
            Log::error('Failed to send email.', [
                'recipient' => $data['email'] ?? 'N/A',
                'mailable_class' => $mailableClass,
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(), // Include stack trace for detailed debugging
            ]);

            // You might want to return an error response here
            return response()->json(['message' => 'Failed to send email. Please try again later.'], 500);
        }
    }
}