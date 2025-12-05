<?php

namespace App\Console\Commands;

use App\Models\RefreshToken;
use Illuminate\Console\Command;

class CleanExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tokens:clean';

    /**
     * The console command description.
     */
    protected $description = 'Clean expired refresh tokens from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning expired refresh tokens...');
        
        $deleted = RefreshToken::cleanExpired();
        
        $this->info("✓ Deleted {$deleted} expired refresh tokens");
        
        return 0;
    }
}
