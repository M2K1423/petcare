<?php

namespace App\Console\Commands;

use App\Models\AppNotification;
use App\Models\Vaccination;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendDewormingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-deworming-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send deworming reminders for pets with deworming due in 3 days';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $targetDate = Carbon::today()->addDays(3);

        $records = Vaccination::with(['pet.owner'])
            ->whereDate('next_due_on', $targetDate->toDateString())
            ->where(function ($query) {
                $query->where('vaccine_name', 'like', '%tay giun%')
                    ->orWhere('vaccine_name', 'like', '%deworm%');
            })
            ->get();

        $count = 0;

        foreach ($records as $record) {
            $owner = $record->pet->owner ?? null;
            if (!$owner) {
                continue;
            }

            $exists = AppNotification::where('user_id', $owner->id)
                ->where('type', 'deworming_reminder')
                ->whereDate('created_at', Carbon::today())
                ->where('message', 'like', '%' . $record->pet->name . '%')
                ->exists();

            if ($exists) {
                continue;
            }

            AppNotification::create([
                'user_id' => $owner->id,
                'appointment_id' => null,
                'type' => 'deworming_reminder',
                'title' => 'Nhac lich tay giun',
                'message' => "Da den han tay giun cho {$record->pet->name} vao ngay {$record->next_due_on->format('d/m/Y')}. Vui long dat lich som.",
            ]);

            $count++;
        }

        $this->info("Created {$count} deworming reminders.");
    }
}
