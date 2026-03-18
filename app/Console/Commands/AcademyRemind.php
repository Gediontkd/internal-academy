<?php

namespace App\Console\Commands;

use App\Models\Registration;
use App\Models\Workshop;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class AcademyRemind extends Command
{
    protected $signature = 'academy:remind';

    protected $description = 'Send reminder emails to all confirmed participants of workshops scheduled for tomorrow.';

    public function handle(): int
    {
        $tomorrow = now()->addDay();

        $workshops = Workshop::with(['confirmedRegistrations.user'])
            ->whereDate('start_time', $tomorrow->toDateString())
            ->get();

        if ($workshops->isEmpty()) {
            $this->info('No workshops scheduled for tomorrow. No emails sent.');
            return Command::SUCCESS;
        }

        $emailsSent = 0;

        foreach ($workshops as $workshop) {
            foreach ($workshop->confirmedRegistrations as $registration) {
                $user = $registration->user;

                Mail::send([], [], function ($message) use ($user, $workshop) {
                    $message
                        ->to($user->email, $user->name)
                        ->subject("Reminder: \"{$workshop->title}\" is tomorrow!")
                        ->html(
                            "<p>Hi {$user->name},</p>" .
                            "<p>Just a friendly reminder that you are registered for:</p>" .
                            "<p><strong>{$workshop->title}</strong><br>" .
                            "📅 {$workshop->start_time->format('l, d F Y')} at {$workshop->start_time->format('H:i')}</p>" .
                            "<p>See you there!</p>" .
                            "<p>— The Internal Academy Team</p>"
                        );
                });

                $emailsSent++;
                $this->line("  ✉ Sent reminder to {$user->email} for \"{$workshop->title}\"");
            }
        }

        $this->info("Done. {$emailsSent} reminder(s) sent for {$workshops->count()} workshop(s).");

        return Command::SUCCESS;
    }
}
