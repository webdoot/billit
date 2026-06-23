<?php

namespace App\Console\Commands;

use App\Services\AlertService;
use Illuminate\Console\Command;

class ServicesCheckExpiryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'services:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically checks for service expiry, marks them expired, and generates dashboard alerts.';

    /**
     * Execute the console command.
     */
    public function handle(AlertService $alertService)
    {
        $this->info('Starting service expiry and alert generation check...');
        
        $results = $alertService->runDailyCheck();
        
        $this->info("Done! Expired services marked: {$results['expired_services']}");
        $this->info("New expiry alerts created: {$results['alerts_created']}");
        
        return Command::SUCCESS;
    }
}
