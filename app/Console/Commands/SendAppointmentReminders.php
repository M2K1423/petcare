<?php

namespace App\Console\Commands;

use App\Models\AppNotification;
use App\Models\Appointment;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-appointment-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder notifications to pet owners 24 hours before their appointments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Bắt đầu quét danh sách lịch hẹn cần nhắc nhở...");

        // Chọn khoảng thời gian 24 giờ tới
        $start = Carbon::now();
        $end = Carbon::now()->addDay();

        $appointments = Appointment::with(['owner', 'pet'])
            ->where('status', 'confirmed')
            ->whereBetween('appointment_at', [$start, $end])
            ->get();

        $count = 0;

        foreach ($appointments as $appointment) {
            $owner = $appointment->owner;
            if (!$owner) {
                continue;
            }

            // Kiểm tra xem đã gửi nhắc nhở cho lịch hẹn này chưa
            $exists = AppNotification::where('appointment_id', $appointment->id)
                ->where('type', 'appointment_reminder')
                ->exists();

            if (!$exists) {
                // Tạo Record trong CSDL
                AppNotification::create([
                    'user_id' => $owner->id,
                    'appointment_id' => $appointment->id,
                    'type' => 'appointment_reminder',
                    'title' => 'Nhắc lịch khám sắp tới!',
                    'message' => "Lịch khám cho {$appointment->pet->name} diễn ra vào lúc " . $appointment->appointment_at->format('H:i d/m/Y'),
                ]);

                $count++;
            }
        }

        $this->info("Đã gửi {$count} nhắc nhở lịch khám.");
    }
}
