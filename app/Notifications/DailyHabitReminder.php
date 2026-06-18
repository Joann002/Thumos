<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DailyHabitReminder extends Notification
{
    use Queueable;

    /**
     * @param  array<int, string>  $pendingHabits  Names of habits still to complete today.
     */
    public function __construct(public array $pendingHabits)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $count = count($this->pendingHabits);

        $mail = (new MailMessage)
            ->subject('Thumos — '.$count.' habitude(s) à cocher aujourd\'hui')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('Il te reste '.$count.' habitude(s) à compléter aujourd\'hui :');

        foreach ($this->pendingHabits as $habit) {
            $mail->line('• '.$habit);
        }

        return $mail
            ->action('Cocher mes habitudes', url('/habits'))
            ->line('Continue ta série, tu peux le faire !');
    }
}
