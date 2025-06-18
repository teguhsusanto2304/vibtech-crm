<?php

namespace App\Services;
use App\Models\Country;
use App\Models\IndustryCategory;
use App\Models\User;
use App\Models\KanbanStage;

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

    public function getUsers()
    {
        return User::where('user_status', 1)->orderBy('name','ASC')->get();
    }

    public function getKanbanStages()
    {
        return KanbanStage::orderBy('id','asc')->get();
    }
}