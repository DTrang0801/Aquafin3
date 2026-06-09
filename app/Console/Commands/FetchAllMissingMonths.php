<?php

namespace App\Console\Commands;

use App\Models\Neerslag;
use App\Services\FloodRiskService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;

#[Signature('app:fetch-all-missing-months')]
#[Description('Fetches and archives all missing months from Open-Meteo until now')]
class FetchAllMissingMonths extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(FloodRiskService $service): void
    {
        $this->info('Starting to fetch all missing months...');

        // Get the earliest and latest months we should have data for
        $earliest = Carbon::createFromDate(2004, 1, 1); // Start from 2004
        $now = Carbon::now('Europe/Berlin');

        // Get all months that should exist
        $this->info("Fetching data from {$earliest->format('Y-m')} to {$now->format('Y-m')}...");

        $bar = $this->output->createProgressBar(
            $earliest->diffInMonths($now) + 1
        );
        $bar->start();

        $current = $earliest->copy();
        $fetchedCount = 0;
        $skippedCount = 0;
        $failedCount = 0;
        $failedMonths = [];

        while ($current <= $now) {
            $exists = Neerslag::query()
                ->where('jaar', $current->year)
                ->where('maand', $current->month)
                ->exists();

            if (! $exists) {
                try {
                    $service->archiveHistoricalMonth($current->year, $current->month);
                    $fetchedCount++;
                } catch (ConnectionException) {
                    $failedCount++;
                    $failedMonths[] = $current->format('Y-m');
                }
            } else {
                $skippedCount++;
            }

            $current->addMonth();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('Complete!');
        $this->info("Fetched: {$fetchedCount} months");
        $this->info("Already existed: {$skippedCount} months");
        if ($failedCount > 0) {
            $this->warn("Failed: {$failedCount} months");
            $this->warn('Failed months: '.implode(', ', $failedMonths));
        }
    }
}
