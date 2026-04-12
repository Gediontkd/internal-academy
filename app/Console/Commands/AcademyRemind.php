<?php

namespace App\Console\Commands;

use App\Mail\WorkshopReminder;
use App\Models\Workshop;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class AcademyRemind extends Command
{
    protected $signature = 'academy:remind';

    protected $description = 'Send reminder emails to all confirmed participants of workshops scheduled for tomorrow.';

    public function handle(): int
    {
        $workshops = Workshop::with(['confirmedRegistrations.user'])
            ->whereDate('start_time', now()->addDay()->toDateString())
            ->get();

        if ($workshops->isEmpty()) {
            $this->info('No workshops scheduled for tomorrow. No emails sent.');
            return Command::SUCCESS;
        }

        $emailsSent = 0;

        foreach ($workshops as $workshop) {
            foreach ($workshop->confirmedRegistrations as $registration) {
                $user = $registration->user;

                Mail::to($user->email, $user->name)
                    ->send(new WorkshopReminder($user, $workshop));

                $emailsSent++;
                $this->line("  ✉ Sent reminder to {$user->email} for \"{$workshop->title}\"");
            }
        }

        $this->info("Done. {$emailsSent} reminder(s) sent for {$workshops->count()} workshop(s).");

        return Command::SUCCESS;
    }
}
