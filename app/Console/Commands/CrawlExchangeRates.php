<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ExchangeCrawlerService;
use Illuminate\Support\Facades\Log;

class CrawlExchangeRates extends Command
{
    protected $signature = 'crawl:exchange-rates';
    protected $description = 'Crawl exchange rate data from external service';

    public function handle(ExchangeCrawlerService $crawler)
    {
        $rates = $crawler->fetchRates();

        if ($rates) {
            
            Log::info('Exchange rates fetched successfully', $rates);

            // Optional: Store in DB if needed
        } else {
            Log::error('Failed to fetch exchange rates.');
        }

        $this->info('Exchange rates crawling completed.');
    }
}
