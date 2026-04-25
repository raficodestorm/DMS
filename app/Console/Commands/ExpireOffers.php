<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Offer;

class ExpireOffers extends Command
{
    protected $signature = 'offers:expire';
    protected $description = 'Deactivate expired offers automatically';

    public function handle()
    {
        Offer::where('status', 1)
            ->whereDate('end_date', '<', now()->toDateString())
            ->update([
                'status' => 0
            ]);

        $this->info('Expired offers deactivated successfully.');
    }
}
