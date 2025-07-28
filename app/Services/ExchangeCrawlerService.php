<?php 
namespace App\Services;

use Illuminate\Support\Facades\Http;

class ExchangeCrawlerService
{
    public function fetchRates()
    {
        $response = Http::get('https://api.exchangerate-api.com/v4/latest/SGD'); // example API

        if ($response->successful()) {
            $rates = $response->json();
            $arr = [];
            foreach ($rates['rates'] as $currency => $rate) {
                \App\Models\ExchangeRate::updateOrCreate(
                    ['currency' => $currency],
                    [
                        'rate_date' => date('Y-m-d'),
                        'rate' => $rate
                    ]
                );
                $arr[$currency] = $rate;
            }
            dd($arr);
            return $rates; // You can also store this in DB
        }

        return null;
    }
}
