<?php

namespace App\Console\Commands;

use App\Models\AppNotification;
use App\Models\Appointment;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendPostTreatmentFollowUps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-post-treatment-followups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send follow-up notifications 3 days after completed appointments';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $targetDate = Carbon::today()->subDays(3);

        $appointments = Appointment::with(['owner', 'pet', 'medicalRecord'])
            ->where('status', 'completed')
            ->whereDate('appointment_at', $targetDate->toDateString())
            ->whereHas('medicalRecord')
            ->get();

        $count = 0;

        foreach ($appointments as $appointment) {
            $owner = $appointment->owner;
            if (!$owner) {
                continue;
            }

            $exists = AppNotification::where('appointment_id', $appointment->id)
                ->where('type', 'aftercare_followup')
                ->exists();

            if ($exists) {
                continue;
            }

            AppNotification::create([
                'user_id' => $owner->id,
                'appointment_id' => $appointment->id,
                'type' => 'aftercare_followup',
                'title' => 'Theo doi sau dieu tri',
                'message' => "Phong kham nho ban cap nhat tinh trang cua {$appointment->pet->name} sau 3 ngay dieu tri. Neu co dau hieu bat thuong, vui long dat lich tai kham.",
            ]);

            $count++;
        }

        $this->info("Created {$count} post-treatment follow-up notifications.");
    }
}
