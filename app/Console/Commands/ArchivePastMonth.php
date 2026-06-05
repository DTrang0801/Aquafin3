<?php

namespace App\Console\Commands;

use App\Services\FloodRiskService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ArchivePastMonth extends Command
{
    // The name of the terminal command
    protected $signature = 'app:archive-past-month';

    protected $description = 'Berekent en archiveert de totale neerslag van de afgelopen afgesloten maand';

    public function handle(FloodRiskService $service)
    {
        // Get the date context for last month
        $lastMonth = Carbon::now('Europe/Berlin')->subMonth();
        $year = $lastMonth->year;
        $month = $lastMonth->month;

        $this->info("Controleren of neerslagdata voor {$year}-{$month} gearchiveerd moet worden...");

        // Run the archiver (Defaults to coordinates 50.75, 4.5)
        $service->archiveHistoricalMonth($year, $month);

        $this->info('Proces voltooid.');
    }
}
