<?php

namespace App\Console\Commands;

use App\Models\AppNotification;
use App\Models\Vaccination;
use App\Services\NotificationService;
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
    protected $description = 'Gửi nhắc tẩy giun cho thú cưng đến hạn sau 3 ngày';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notifications): void
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

            $notifications->create([
                'user_id' => $owner->id,
                'appointment_id' => null,
                'type' => 'deworming_reminder',
                'title' => 'Nhắc lịch tẩy giun',
                'message' => "Đã đến hạn tẩy giun cho {$record->pet->name} vào ngày {$record->next_due_on->format('d/m/Y')}. Vui lòng đặt lịch sớm.",
            ]);

            $count++;
        }

        $this->info("Đã tạo {$count} nhắc nhở tẩy giun.");
    }
}
