<?php

namespace App\Console\Commands;

use App\Models\AppNotification;
use App\Models\Vaccination;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendVaccinationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-vaccination-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder notifications to pet owners a few days before expected vaccination dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Bắt đầu quét danh sách tiêm phòng...");

        // Nhắc trước 3 ngày
        $targetDate = Carbon::today()->addDays(3);

        $vaccinations = Vaccination::with(['pet.owner'])
            ->whereDate('next_due_on', $targetDate->toDateString())
            ->get();

        $count = 0;

        foreach ($vaccinations as $vaccination) {
            $owner = $vaccination->pet->owner ?? null;
            if (!$owner) {
                continue;
            }

            // Có thể dùng 1 key tạm để tránh gửi lặp dựa vào custom payload trong title.
            // Do bảng notifications đang ko lưu vaccination_id nên ta check message/title
            $title = 'Đến lịch tiêm phòng mũi mới';
            $exists = AppNotification::where('user_id', $owner->id)
                ->where('type', 'vaccination_reminder')
                ->where('title', $title)
                ->whereDate('created_at', Carbon::today())
                ->exists();

            if (!$exists) {
                // Tạo Record trên DB table
                AppNotification::create([
                    'user_id' => $owner->id,
                    'appointment_id' => null,
                    'type' => 'vaccination_reminder',
                    'title' => $title,
                    'message' => "Đến lịch tiêm lại {$vaccination->vaccine_name} cho {$vaccination->pet->name} vào ngày " . $vaccination->next_due_on->format('d/m/Y'),
                ]);

                $count++;
            }
        }

        $this->info("Đã tạo {$count} thông báo tiêm phòng.");
    }
}
