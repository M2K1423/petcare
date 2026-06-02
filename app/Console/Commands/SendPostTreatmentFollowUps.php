<?php

namespace App\Console\Commands;

use App\Models\AppNotification;
use App\Models\Appointment;
use App\Services\NotificationService;
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
    protected $description = 'Gửi nhắc tái khám 3 ngày sau lịch hẹn hoàn thành';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notifications): void
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

            $notifications->create([
                'user_id' => $owner->id,
                'appointment_id' => $appointment->id,
                'type' => 'aftercare_followup',
                'title' => 'Theo dõi sau điều trị',
                'message' => "Phòng khám nhắc bạn cập nhật tình trạng của {$appointment->pet->name} sau 3 ngày điều trị. Nếu có dấu hiệu bất thường, vui lòng đặt lịch tái khám.",
            ]);

            $count++;
        }

        $this->info("Đã tạo {$count} thông báo theo dõi sau điều trị.");
    }
}
