<?php

namespace App\Repositories\Interfaces;

use App\Models\Notification;

interface NotificationRepositoryInterface
{
    public function getNotificationsByStagiaire($stagiaireId);
    public function getUnreadNotifications($stagiaireId);
    public function markAsRead($notificationId): bool;
    public function markAllAsRead($stagiaireId): bool;
    public function createNotification(array $data): Notification;
    public function deleteNotification($id): bool;
} 