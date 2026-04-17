<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Appointment $appointment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Add custom channels here if needed, e.g. Firebase ['mail', 'firebase']
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $petName = $this->appointment->pet->name;
        $appointmentTime = $this->appointment->appointment_at->format('d/m/Y H:i');

        return (new MailMessage)
            ->subject("Nhắc nhở lịch khám cho {$petName}")
            ->greeting("Chào {$notifiable->name},")
            ->line("Đây là lời nhắc từ PetCare. Thú cưng {$petName} của bạn có một lịch khám sắp tới.")
            ->line("Thời gian: {$appointmentTime}")
            ->action('Xem chi tiết', url(env('APP_FRONTEND_URL', 'http://localhost') . '/owner/appointments'))
            ->line('Cảm ơn bạn đã tin tưởng dịch vụ của phòng khám!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'message' => "Lịch khám của {$this->appointment->pet->name} vào lúc " . $this->appointment->appointment_at->format('d/m/Y H:i'),
        ];
    }
}
