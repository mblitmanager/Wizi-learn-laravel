<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Stagiaire;

class ResetStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-stats {--force : Force the operation to run without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all statistics, histories, rankings, and participations data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('This will DELETE all historical data, rankings, and participations. Are you sure you want to proceed?')) {
            $this->info('Operation cancelled.');
            return;
        }

        $this->info('Starting data reset...');

        // Tables to truncate (ordered by dependencies if possible, or using foreign key checks disable)
        $tablesToTruncate = [
            'login_histories',
            'user_app_usages',
            'user_activity_log',
            'classements',
            'progressions',
            'user_achievements',
            'participation_answers', // Truncate child before parent
            'participations',
            'quiz_participation_answers', // Truncate child before parent
            'quiz_participations',
            'quiz_statistics',
            'media_stagiaire',
            'demande_inscriptions',
        ];

        DB::beginTransaction();

        try {
            Schema::disableForeignKeyConstraints();

            foreach ($tablesToTruncate as $tableName) {
                if (Schema::hasTable($tableName)) {
                    DB::table($tableName)->truncate();
                    $this->line("Truncated table: <comment>{$tableName}</comment>");
                } else {
                    $this->warn("Table not found: {$tableName}");
                }
            }

            // Reset student-specific fields
            $count = Stagiaire::query()->update([
                'login_streak' => 0,
                'last_login_at' => null,
            ]);
            $this->line("Reset <comment>{$count}</comment> stagiaires' login fields.");

            Schema::enableForeignKeyConstraints();
            DB::commit();

            $this->info('✅ Data reset completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Schema::enableForeignKeyConstraints();
            $this->error('❌ An error occurred during reset: ' . $e->getMessage());
        }
    }
}
