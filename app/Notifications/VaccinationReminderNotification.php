<?php

namespace App\Notifications;

use App\Models\Vaccination;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VaccinationReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Vaccination $vaccination;

    /**
     * Create a new notification instance.
     */
    public function __construct(Vaccination $vaccination)
    {
        $this->vaccination = $vaccination;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Add custom Firebase channel here later if needed
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $petName = $this->vaccination->pet->name;
        $vaccineName = $this->vaccination->vaccine_name;
        $dueDate = $this->vaccination->next_due_on->format('d/m/Y');

        return (new MailMessage)
            ->subject("Nhắc nhở lịch tiêm phòng cho {$petName}")
            ->greeting("Chào {$notifiable->name},")
            ->line("Đây là lời nhắc từ PetCare. Thú cưng {$petName} của bạn sắp đến ngày cần tiêm nhắc lại mũi {$vaccineName}.")
            ->line("Ngày tiêm dự kiến: {$dueDate}")
            ->action('Đặt lịch ngay', url(env('APP_FRONTEND_URL', 'http://localhost') . '/owner/appointments'))
            ->line('Vui lòng sắp xếp thời gian để thú cưng dượcc bảo vệ tốt nhất!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'vaccination_id' => $this->vaccination->id,
            'message' => "Đến lịch tiêm {$this->vaccination->vaccine_name} cho {$this->vaccination->pet->name} vào ngày {$this->vaccination->next_due_on->format('d/m/Y')}",
        ];
    }
}
