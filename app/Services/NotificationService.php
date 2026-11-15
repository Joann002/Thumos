<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send notification to user
     */
    public function notify(int $userId, string $type, string $message, array $data = []): void
    {
        Log::info('Notification sent', [
            'user_id' => $userId,
            'type' => $type,
            'message' => $message,
            'data' => $data,
        ]);

        // TODO: Implement actual notification system
        // Could use database notifications, Pusher, etc.
    }

    /**
     * Send success notification
     */
    public function success(int $userId, string $message, array $data = []): void
    {
        $this->notify($userId, 'success', $message, $data);
    }

    /**
     * Send error notification
     */
    public function error(int $userId, string $message, array $data = []): void
    {
        $this->notify($userId, 'error', $message, $data);
    }

    /**
     * Send info notification
     */
    public function info(int $userId, string $message, array $data = []): void
    {
        $this->notify($userId, 'info', $message, $data);
    }

    /**
     * Send warning notification
     */
    public function warning(int $userId, string $message, array $data = []): void
    {
        $this->notify($userId, 'warning', $message, $data);
    }
}
