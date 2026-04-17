<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Lịch hẹn -> Quét mỗi giờ để gửi nhắc nhở cho các lịch hẹn còn 24 giờ là tới
Schedule::command('app:send-appointment-reminders')->hourly();

// Lịch tiêm phòng -> Quét 1 lần/ngày vào lúc 8h sáng để báo tới lịch tiêm
Schedule::command('app:send-vaccination-reminders')->dailyAt('08:00');
