<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WeeklyReport extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private array $stats
    ) {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre rapport hebdomadaire Thumos')
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line('Voici votre résumé de la semaine :')
            ->line('Objectifs complétés : ' . $this->stats['completed_goals'])
            ->line('Habitudes réalisées : ' . $this->stats['habits_completed'])
            ->line('Entrées de journal : ' . $this->stats['journal_entries'])
            ->action('Voir mon tableau de bord', url('/dashboard'))
            ->line('Continuez comme ça ! 🎉');
    }
}
