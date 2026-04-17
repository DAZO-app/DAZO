<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:purge-old-logs')]
    #[Description('Purge app logs older than 1 year')]
    class PurgeOldLogs extends Command
    {
        /**
         * Execute the console command.
         */
        public function handle(): void
        {
            $count = \App\Models\AppLog::where('created_at', '<', now()->subYear())->delete();
            $this->info("Purged $count old logs.");
        }
}
