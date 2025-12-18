<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Announcement;
use App\Models\User;
use App\Models\Formateur;
use App\Models\Commercial;
use App\Jobs\SendFcmNotificationJob;
use Illuminate\Support\Facades\Log;

class ProcessScheduledAnnouncements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'announcements:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process and send scheduled announcements';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for scheduled announcements...');

        $announcements = Announcement::where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->get();

        if ($announcements->isEmpty()) {
            $this->info('No pending announcements found.');
            return 0;
        }

        foreach ($announcements as $announcement) {
            $this->info("Processing Announcement ID: {$announcement->id}");
            
            try {
                // Fetch recipients similar to Controller logic
                $recipients = $this->getRecipients($announcement);
                
                if ($recipients->isEmpty()) {
                    $this->warn("No recipients found for Announcement ID: {$announcement->id}");
                }

                foreach ($recipients as $recipient) {
                    SendFcmNotificationJob::dispatch(
                        $recipient,
                        $announcement->title,
                        $announcement->message,
                        ['type' => 'announcement', 'announcement_id' => $announcement->id]
                    );
                }

                // Update status
                $announcement->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                $this->info("Sent to {$recipients->count()} recipients.");

            } catch (\Exception $e) {
                Log::error("Failed to process scheduled announcement {$announcement->id}: " . $e->getMessage());
                $this->error("Error processing ID {$announcement->id}: {$e->getMessage()}");
            }
        }

        $this->info('Done.');
        return 0;
    }

    private function getRecipients($announcement)
    {
        $recipients = collect();
        $targetAudience = $announcement->target_audience;
        
        // We need the creator to scope properly usually, or just use the saved recipient_ids for specific
        // For dynamic groups (stagiaires), we re-evaluate scope based on creator
        $creator = $announcement->creator; 

        if ($targetAudience === 'all') {
            $recipients = User::whereNotNull('fcm_token')->get();
        } 
        elseif ($targetAudience === 'formateurs') {
            $recipients = User::whereIn('role', ['formateur', 'formatrice'])->whereNotNull('fcm_token')->get();
        }
        elseif ($targetAudience === 'autres') {
            $recipients = User::whereIn('role', ['commercial', 'admin'])->whereNotNull('fcm_token')->get();
        }
        elseif ($targetAudience === 'stagiaires') {
             // Re-evaluate scope for creator
             $recipients = $this->getScopedStagiaireUsers($creator);
        } 
        elseif ($targetAudience === 'specific_users') {
             if (!empty($announcement->recipient_ids)) {
                 $recipients = User::whereIn('id', $announcement->recipient_ids)->whereNotNull('fcm_token')->get();
             }
        }

        return $recipients;
    }

    private function getScopedStagiaireUsers($sender)
    {
        if (!$sender) return collect(); // Should not happen if foreign key integrity
        
        if ($sender->role === 'admin') {
            return User::where('role', 'stagiaire')->get();
        }

        if ($sender->role === 'formateur' || $sender->role === 'formatrice') {
            $formateur = Formateur::where('user_id', $sender->id)->first();
            if (!$formateur) return collect();

            return User::whereHas('stagiaire', function($q) use ($formateur) {
                $q->whereHas('formateurs', function($formateurQ) use ($formateur) {
                    $formateurQ->where('formateurs.id', $formateur->id);
                });
            })->get();
        }

        if ($sender->role === 'commercial') {
            $commercial = Commercial::where('user_id', $sender->id)->first();
            if (!$commercial) return collect();
             
             return User::whereHas('stagiaire', function($q) use ($commercial) {
                $q->whereHas('commercials', function($commQ) use ($commercial) {
                    $commQ->where('commercials.id', $commercial->id);
                });
            })->get();
        }

        return collect();
    }
}
